<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('content', 400);
            $table->foreignId('user_id');
            $table->foreignId('post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
