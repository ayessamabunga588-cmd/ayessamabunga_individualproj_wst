<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // The ER diagram defines users by email; the existing Laravel table already
            // has a unique email column plus the auth columns, so these are the extra
            // profile fields. They are nullable so the existing registration flow keeps
            // working without requiring a full profile up front.
            $table->string('username', 30)->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->timestamp('last_login_time')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'first_name',
                'last_name',
                'phone_number',
                'last_login_time',
                'address_id',
            ]);
        });
    }
};
