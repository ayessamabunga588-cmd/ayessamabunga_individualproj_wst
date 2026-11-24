<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->foreignId('purchase_id');
            $table->char('code', 12);
            $table->decimal('product_price', 7, 2);
            $table->integer('product_quantity');
            $table->string('product_name', 100);

            // Composite primary key straight from the ER diagram.
            $table->primary(['purchase_id', 'code']);

            $table->foreign('purchase_id')
                ->references('purchase_id')
                ->on('purchases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('code')
                ->references('code')
                ->on('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_products');
    }
};
