<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id('purchase_id');
            $table->decimal('total_price', 13, 2);
            $table->timestamp('purchase_time');
            $table->integer('payment_id');
            $table->string('email', 320);

            $table->timestamps();

            // A purchase belongs to the user with the same email (1 user : N purchases).
            $table->foreign('email')
                ->references('email')
                ->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index('purchase_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
