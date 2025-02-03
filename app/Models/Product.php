<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    protected $fillable = ['category_id','brand_id','name','slug','images','description','price','is_active','is_featured','in_stock','on_sale'];
    #images property is json type so we should create a casts for it
    protected $casts = [
        'images'=>'array'
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function brand(){
        return $this->belongsTo(Brand::class);
    }
    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }
}
