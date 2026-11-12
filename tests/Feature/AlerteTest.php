<?php

namespace Tests\Feature;

use App\Jobs\SendAlert;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AlerteTest extends TestCase
{
    use RefreshDatabase;

    private function quartier(string $nom = 'Baguida'): int
    {
        return DB::table('quartiers')->insertGetId(['name' => $nom]);
    }

    // Canal telegram sans chat_id : aucun SMS réel n'est envoyé pendant les tests.
    private function habitant(int $quartierId, array $extra = []): Subscriber
    {
        $id = DB::table('subscribers')->insertGetId(array_merge([
            'phone'       => '+22890000000',
            'quartier_id' => $quartierId,
            'channel'     => 'telegram',
            'verified_at' => now(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ], $extra));

        return Subscriber::find($id);
    }

    private function hashCode(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    /* ---------- Pages publiques ---------- */

    public function test_page_accueil_s_affiche(): void
    {
        $this->quartier();
        $this->get('/')->assertOk();
    }

    public function test_inscription_refuse_un_numero_invalide(): void
    {
        $q = $this->quartier();

        $this->post('/inscription', [
            'phone' => 'abc', 'quartier_id' => $q, 'channel' => 'telegram', 'consent' => 'on',
        ])->assertSessionHasErrors('phone');
    }

    public function test_inscription_exige_le_consentement(): void
    {
        $q = $this->quartier();

        $this->post('/inscription', [
            'phone' => '90000000', 'quartier_id' => $q, 'channel' => 'telegram',
        ])->assertSessionHasErrors('consent');
    }

    public function test_inscription_ajoute_l_indicatif_du_togo(): void
    {
        $q = $this->quartier();

        $this->post('/inscription', [
            'phone' => '90 00 00 00', 'quartier_id' => $q, 'channel' => 'telegram', 'consent' => 'on',
        ])->assertRedirect();

        $this->assertDatabaseHas('subscribers', ['phone' => '+22890000000', 'verified_at' => null]);
    }

    /* ---------- Connexion habitant ---------- */

    public function test_mon_espace_exige_une_connexion(): void
    {
        $this->get('/mon-espace')->assertRedirect('/connexion');
    }

    public function test_demande_de_code_ne_revele_pas_si_le_numero_existe(): void
    {
        $this->post('/connexion', ['phone' => '99999999'])
            ->assertRedirect('/connexion/code')
            ->assertSessionHas('ok');
    }

    public function test_connexion_avec_le_bon_code(): void
    {
        $q = $this->quartier();
        $s = $this->habitant($q, [
            'login_code'            => $this->hashCode('123456'),
            'login_code_expires_at' => now()->addMinutes(10),
        ]);

        $this->withSession(['login_phone' => $s->phone])
            ->post('/connexion/code', ['code' => '123456'])
            ->assertRedirect('/mon-espace');

        $this->assertSame($s->id, session('subscriber_id'));
    }

    public function test_connexion_refuse_un_mauvais_code(): void
    {
        $q = $this->quartier();
        $s = $this->habitant($q, [
            'login_code'            => $this->hashCode('123456'),
            'login_code_expires_at' => now()->addMinutes(10),
        ]);

        $this->withSession(['login_phone' => $s->phone])
            ->post('/connexion/code', ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertNull(session('subscriber_id'));
    }

    public function test_connexion_refuse_un_code_expire(): void
    {
        $q = $this->quartier();
        $s = $this->habitant($q, [
            'login_code'            => $this->hashCode('123456'),
            'login_code_expires_at' => now()->subMinute(),
        ]);

        $this->withSession(['login_phone' => $s->phone])
            ->post('/connexion/code', ['code' => '123456'])
            ->assertSessionHasErrors('code');
    }

    public function test_connexion_bloquee_apres_5_essais(): void
    {
        $q = $this->quartier();
        $s = $this->habitant($q, [
            'login_code'            => $this->hashCode('123456'),
            'login_code_expires_at' => now()->addMinutes(10),
            'login_attempts'        => 5,
        ]);

        // Même le bon code est refusé une fois la limite atteinte
        $this->withSession(['login_phone' => $s->phone])
            ->post('/connexion/code', ['code' => '123456'])
            ->assertSessionHasErrors('code');
    }

    /* ---------- Espace habitant ---------- */

    public function test_habitant_change_de_quartier(): void
    {
        $a = $this->quartier('Baguida');
        $b = $this->quartier('Agoè');
        $s = $this->habitant($a);

        $this->withSession(['subscriber_id' => $s->id])
            ->put('/mon-espace/quartier', ['quartier_id' => $b])
            ->assertRedirect('/mon-espace');

        $this->assertDatabaseHas('subscribers', ['id' => $s->id, 'quartier_id' => $b]);
    }

    public function test_habitant_se_desinscrit(): void
    {
        $q = $this->quartier();
        $s = $this->habitant($q);

        $this->withSession(['subscriber_id' => $s->id])
            ->delete('/mon-espace')
            ->assertRedirect('/');

        $this->assertDatabaseMissing('subscribers', ['id' => $s->id]);
    }

    public function test_habitant_voit_seulement_les_alertes_de_son_quartier(): void
    {
        $a = $this->quartier('Baguida');
        $b = $this->quartier('Agoè');
        $s = $this->habitant($a);

        foreach ([[$a, 'Alerte pour Baguida'], [$b, 'Alerte pour Agoè']] as [$qid, $msg]) {
            DB::table('alerts')->insert([
                'quartier_id' => $qid, 'type' => 'inond', 'message' => $msg,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $this->withSession(['subscriber_id' => $s->id])
            ->get('/mon-espace')
            ->assertOk()
            ->assertSee('Alerte pour Baguida')
            ->assertDontSee('Alerte pour Agoè');
    }

    /* ---------- Administration ---------- */

    public function test_admin_sans_session_voit_la_page_de_connexion(): void
    {
        $this->get('/admin')->assertOk()->assertViewIs('admin-login');
    }

    public function test_admin_refuse_une_mauvaise_cle(): void
    {
        config(['alerts.admin_key' => 'secret']);

        $this->post('/admin/connexion', ['key' => 'mauvaise'])->assertSessionHasErrors('key');
        $this->assertNull(session('admin'));
    }

    public function test_admin_refuse_une_cle_vide_si_non_configuree(): void
    {
        config(['alerts.admin_key' => '']);

        $this->post('/admin/connexion', ['key' => ''])->assertSessionHasErrors('key');
    }

    public function test_admin_se_connecte_avec_la_bonne_cle(): void
    {
        config(['alerts.admin_key' => 'secret']);

        $this->post('/admin/connexion', ['key' => 'secret'])->assertRedirect('/admin');
        $this->assertTrue(session('admin'));
    }

    public function test_dashboard_admin_s_affiche(): void
    {
        $q = $this->quartier();
        $this->habitant($q);

        $this->withSession(['admin' => true])->get('/admin')->assertOk()->assertSee('Baguida');
    }

    public function test_envoi_d_alerte_exige_la_session_admin(): void
    {
        Queue::fake();
        $q = $this->quartier();

        $this->post('/admin/alerte', ['quartier_id' => $q, 'type' => 'inond', 'message' => 'Test'])
            ->assertForbidden();

        Queue::assertNothingPushed();
    }

    public function test_envoi_d_alerte_lance_le_job(): void
    {
        Queue::fake();
        $q = $this->quartier();

        $this->withSession(['admin' => true])
            ->post('/admin/alerte', ['quartier_id' => $q, 'type' => 'inond', 'message' => 'Montée des eaux'])
            ->assertRedirect('/admin');

        $this->assertDatabaseHas('alerts', ['quartier_id' => $q, 'message' => 'Montée des eaux']);
        Queue::assertPushed(SendAlert::class);
    }

    public function test_message_d_alerte_limite_a_140_caracteres(): void
    {
        Queue::fake();
        $q = $this->quartier();

        $this->withSession(['admin' => true])
            ->post('/admin/alerte', ['quartier_id' => $q, 'type' => 'inond', 'message' => str_repeat('a', 141)])
            ->assertSessionHasErrors('message');
    }

    public function test_admin_ajoute_et_supprime_un_quartier(): void
    {
        $this->withSession(['admin' => true])
            ->post('/admin/quartiers', ['name' => 'Totsi'])
            ->assertRedirect();

        $this->assertDatabaseHas('quartiers', ['name' => 'Totsi']);

        $id = DB::table('quartiers')->where('name', 'Totsi')->value('id');

        $this->withSession(['admin' => true])->delete("/admin/quartiers/{$id}")->assertRedirect();
        $this->assertDatabaseMissing('quartiers', ['name' => 'Totsi']);
    }

    public function test_quartier_avec_habitants_ne_peut_pas_etre_supprime(): void
    {
        $q = $this->quartier();
        $this->habitant($q);

        $this->withSession(['admin' => true])->delete("/admin/quartiers/{$q}");
        $this->assertDatabaseHas('quartiers', ['id' => $q]);
    }
}