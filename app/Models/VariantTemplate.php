<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VariantTemplate
 * 
 * @property int $id
 * @property int $store_id
 * @property string $name
 * @property string|null $description
 * @property array $template_json
 * @property bool $is_default
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property Store $store
 * @property \Illuminate\Database\Eloquent\Collection|Product[] $products
 */
class VariantTemplate extends Model
{
    protected $fillable = [
        'store_id',
        'name', 
        'description',
        'template_json',
        'is_default'
    ];

    protected $casts = [
        'template_json' => 'array',
        'is_default' => 'boolean',
        'store_id' => 'int'
    ];

    /**
     * Relationships
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'variant_template_id');
    }

    /**
     * Scopes
     */
    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get attribute names from template
     * 
     * @return array
     */
    public function getAttributeNames()
    {
        return array_keys($this->template_json ?? []);
    }

    /**
     * Get options for specific attribute
     * 
     * @param string $attributeName
     * @return array
     */
    public function getAttributeOptions($attributeName)
    {
        return $this->template_json[$attributeName]['options'] ?? [];
    }

    /**
     * Add new attribute to template
     */
    public function addAttribute($name, $config)
    {
        $template = $this->template_json;
        $template[$name] = $config;
        $this->update(['template_json' => $template]);
    }

    /**
     * Remove attribute from template
     */
    public function removeAttribute($name)
    {
        $template = $this->template_json;
        unset($template[$name]);
        $this->update(['template_json' => $template]);
    }
}