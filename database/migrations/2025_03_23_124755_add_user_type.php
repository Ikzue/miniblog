<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Enums\Role;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table){
            $table->enum(
                'role',
                ['moderator', 'writer', 'reader']
            )->default('reader');
            $table->boolean('is_email_public')->default(false);
        });
    }


    public function down(): void
    {
        Schema::table('users', function (Blueprint $table){
            $table->dropColumn('role');
            $table->dropColumn('is_email_public');
        });
    }
};
