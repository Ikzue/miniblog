<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use DateInterval;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    public function definition(): array
    {
        $createdAt = fake()->dateTime();
        $updatedAt = clone $createdAt;
        $randomDays = new DateInterval("P".rand(0, 7)."D");
        $updatedAt->add($randomDays);

        return [
            'content' => fake()->sentence(10),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}
