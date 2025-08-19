<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProductTag
 * 
 * @property int $id
 * @property int $product_id
 * @property int $tag_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Product $product
 * @property Tag $tag
 *
 * @package App\Models
 */
class ProductTag extends Model
{
    protected $table = 'product_tags';

    protected $casts = [
        'product_id' => 'int',
        'tag_id' => 'int'
    ];

    protected $fillable = [
        'product_id',
        'tag_id'
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    // Scopes
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForTag($query, $tagId)
    {
        return $query->where('tag_id', $tagId);
    }
}