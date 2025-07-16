<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Product
 * 
 * @property int $id
 * @property string $name
 * @property int|null $store_id
 * @property string|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Store|null $store
 * @property Collection|ProductCategory[] $product_categories
 * @property Collection|ProductReview[] $product_reviews
 * @property Collection|ProductVariant[] $product_variants
 *
 * @package App\Models
 */
class Product extends Model
{
	use SoftDeletes;
	protected $table = 'products';

	protected $casts = [
		'store_id' => 'int'
	];

	protected $fillable = [
		'name',
		'store_id',
		'description'
	];

	public function store()
	{
		return $this->belongsTo(Store::class);
	}

	public function product_categories()
	{
		return $this->hasMany(ProductCategory::class);
	}

	public function product_reviews()
	{
		return $this->hasMany(ProductReview::class);
	}

	public function product_variants()
	{
		return $this->hasMany(ProductVariant::class);
	}

	public function categories()
	{
		return $this->hasManyThrough(
			Category::class,
			ProductCategory::class,
			'product_id', 
			'id',
			'id',
			'category_id'
		);
	}

}
