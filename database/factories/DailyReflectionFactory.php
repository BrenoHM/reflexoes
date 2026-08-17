<?php

namespace Database\Factories;

use App\Models\DailyReflection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyReflection>
 */
class DailyReflectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'paragrafo' => fake()->numberBetween(1, 1828),
            'descricao_paragrafo' => '[Texto fictício de teste] '.fake()->paragraphs(2, true),
            'reflexao' => '[Texto fictício de teste] '.fake()->paragraphs(2, true),
        ];
    }
}
