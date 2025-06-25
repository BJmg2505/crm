<?php

namespace Database\Seeders;

use App\Models\Clientetipo;
use Illuminate\Database\Seeder;

class ClientetipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Clientetipo::factory()->create([
            'nombre' => 'WIN Empresas - Activo',
        ]);
        Clientetipo::factory()->create([
            'nombre' => 'WIN Empresas - Potencial',
        ]);
        Clientetipo::factory()->create([
            'nombre' => 'WIN Negocios',
        ]);
        Clientetipo::factory()->create([
            'nombre' => 'Libre',
        ]);
    }
}
