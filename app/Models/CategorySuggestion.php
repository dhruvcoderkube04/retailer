<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorySuggestion extends Model
{
	protected $fillable = ['category_name','sub_category_name','wholesaler_id', 'retailer_id','is_approve'];
}
