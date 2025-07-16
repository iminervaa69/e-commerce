<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductCategory
 * 
 * @property int $id
 * @property int $product_id
 * @property int $category_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Product $product
 * @property Category $category
 *
 * @package App\Models
 */
class ProductCategory extends Model
{
	use SoftDeletes;
	protected $table = 'product_categories';

	protected $casts = [
		'product_id' => 'int',
		'category_id' => 'int'
	];

	protected $fillable = [
		'product_id',
		'category_id'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function category()
	{
		return $this->belongsTo(Category::class);
	}
}
