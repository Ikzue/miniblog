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
            'content' => fake()->sentence(60),
        ];
    }

    public function withUser($user): Factory {
        return $this->state(function () use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }

    public function withPost($post): Factory {
        return $this->state(function () use ($post) {
            return [
                'post_id' => $post->id,
            ];
        });
    }
}
