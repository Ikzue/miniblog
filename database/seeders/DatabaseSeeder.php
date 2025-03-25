<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Enums\Role;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $roles = Role::cases();

        $users = [];

        // Create users
        foreach ($roles as $role) {
            $users[] = User::create([
                'name' => ucfirst($role->value),
                'email' => $role->value . '@example.com',
                'password' => Hash::make('password'),
                'role' => $role->value,
                'is_email_public' => $faker->boolean,
            ]);
        }

        $posts = [];

        // Create a post for each user
        foreach ($users as $user) {
            $posts[] = Post::create([
                'title' => ucfirst($user->role) . ' post',
                'content' => $faker->paragraph,
                'user_id' => $user->id,
            ]);
        }

        // Create a comment by each user for each post
        foreach ($posts as $post) {
            foreach ($users as $user) {
                Comment::create([
                    'content' => $faker->sentence,
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
