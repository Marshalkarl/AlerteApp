<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuartierSeeder extends Seeder
{
    public function run(): void
    {
        $noms = ['Baguida', 'Agoè', 'Adidogomé', 'Bè', 'Tokoin', 'Hédzranawoé', 'Nyékonakpoè',
                 'Kodjoviakopé', 'Amoutivé', 'Djidjolé', 'Kégué', 'Avédji', 'Akodésséwa', 'Adakpamé', 'Totsi'];

        foreach ($noms as $nom) {
            DB::table('quartiers')->insertOrIgnore(['name' => $nom]);
        }
    }
}