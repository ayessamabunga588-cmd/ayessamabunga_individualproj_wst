<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseProduct extends Model
{
    use HasFactory;

    protected $table = 'purchase_products';

    /**
     * The table uses the composite primary key (purchase_id, code).
     */
    protected $primaryKey = 'purchase_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'purchase_id',
        'code',
        'product_price',
        'product_quantity',
        'product_name',
    ];

    protected function casts(): array
    {
        return [
            'product_price' => 'decimal:2',
            'product_quantity' => 'integer',
        ];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'purchase_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'code', 'code');
    }
}
