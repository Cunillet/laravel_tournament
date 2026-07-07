<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ScoringSystem;
use Illuminate\Database\Seeder;

final class ScoringSystemSeeder extends Seeder
{
    public function run(): void
    {
        $systems = [
            ['name' => 'Puntos', 'slug' => 'points', 'description' => 'Puntuación numérica estándar. Se asigna una cantidad de puntos directa.'],
            ['name' => 'Tiempo', 'slug' => 'time', 'description' => 'Puntuación basada en tiempo (cronómetro, duración, velocidad).'],
            ['name' => 'Conteo', 'slug' => 'count', 'description' => 'Puntuación por cantidad de elementos (aciertos, objetivos, kills, etc.).'],
            ['name' => 'Personalizado', 'slug' => 'custom', 'description' => 'Sistema de puntuación libre sin validaciones predefinidas.'],
        ];

        foreach ($systems as $system) {
            ScoringSystem::create($system);
        }
    }
}
