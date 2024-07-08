<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Variant extends Model
{
    protected $fillable = [
        'title',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stocks(): BelongsToMany
    {
        return $this->belongsToMany(
            Stock::class,
            table: 'product_stock_variant_pivot',
            foreignPivotKey: 'variant_id',
            relatedPivotKey: 'stock_id',
        );
    }
}
