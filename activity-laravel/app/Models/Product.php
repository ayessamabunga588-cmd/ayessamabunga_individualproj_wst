<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'description',
        'manufacturer',
        'photo',
        'price',
        'cost',
        'inventory_quantity',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
            'inventory_quantity' => 'integer',
        ];
    }

    /**
     * A product can appear in many purchases through the purchase_products table.
     */
    public function purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class, 'purchase_products', 'code', 'purchase_id')
            ->withPivot(['product_price', 'product_quantity', 'product_name']);
    }

    /**
     * The individual line items recorded for this product.
     */
    public function purchaseProducts(): HasMany
    {
        return $this->hasMany(PurchaseProduct::class, 'code', 'code');
    }
}
