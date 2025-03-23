<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\Role;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table){
            $table->enum(
                'role',
                [Role::MODERATOR->value, Role::WRITER->value, Role::READER->value]
            )->default(Role::READER->value);
            $table->boolean('is_email_public')->default(false);
        });
    }


    public function down(): void
    {
        Schema::table('users', function (Blueprint $table){
            $table->dropColumn('role');
        });
    }
};
