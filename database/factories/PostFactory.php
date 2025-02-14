<?php

namespace Database\Factories;

use DateInterval;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $createdAt = fake()->dateTime();
        $updatedAt = clone $createdAt;
        $randomDays = new DateInterval("P".rand(0, 7)."D");
        $updatedAt->add($randomDays);

        return [
            'title' => fake()->sentence(5),
            'content' => fake()->sentence(60),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}
