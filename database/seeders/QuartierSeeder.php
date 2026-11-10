<?php
namespace Database\Seeders;
use App\Models\Quartier;
use Illuminate\Database\Seeder;

class QuartierSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Agoè', 'Adidogomé', 'Djidjolé', 'Kégué', 'Tokoin', 'Hédzranawoé', 'Nyékonakpoè', 'Kodjoviakopé', 'Bè', 'Baguida'] as $n) {
            Quartier::firstOrCreate(['name' => $n]);
        }
    }
}
