<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $primaryKey = 'purchase_id';

    protected $fillable = [
        'total_price',
        'purchase_time',
        'payment_id',
        'email',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'purchase_time' => 'datetime',
            'payment_id' => 'integer',
        ];
    }

    /**
     * The buyer, joined through the users.email column.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * The products bought in this purchase, read through the purchase_products table.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'purchase_products', 'purchase_id', 'code')
            ->withPivot(['product_price', 'product_quantity', 'product_name']);
    }

    /**
     * The raw line items of this purchase.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseProduct::class, 'purchase_id', 'purchase_id');
    }
}
