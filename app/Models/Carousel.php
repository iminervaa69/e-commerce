<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Carousel
 * 
 * @property int $id
 * @property string|null $title
 * @property string|null $subtitle
 * @property string $image_url
 * @property string|null $link_url
 * @property string|null $link_text
 * @property int $sort_order
 * @property bool $is_active
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class Carousel extends Model
{
    use SoftDeletes;

    protected $table = 'carousels';

    protected $casts = [
        'sort_order' => 'int',
        'is_active' => 'bool',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $fillable = [
        'title',
        'subtitle',
        'image_url',
        'link_url',
        'link_text',
        'status',
        'sort_order',
        'is_active',
        'start_date',
        'end_date'
    ];

    // Scopes for easy querying
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrentlyActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $now);
                    });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    // Helper methods
    public function getIsCurrentlyActiveAttribute()
    {
        if (!$this->is_active) return false;
        
        $now = now();
        
        if ($this->start_date && $this->start_date->gt($now)) return false;
        if ($this->end_date && $this->end_date->lt($now)) return false;
        
        return true;
    }

    public function getImageUrlAttribute()
    {
        return asset($this->image_path ?? 'storage/photos/1/placeholder.jpg');
    }
}
