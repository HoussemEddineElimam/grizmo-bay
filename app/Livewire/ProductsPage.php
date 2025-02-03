<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsPage extends Component
{   
    use WithPagination;
    public function render()
    {   
        $products = Product::query()->where('is_active',1);
        $brands = Brand::where('is_active',1)->get(['id','name','slug']);
        $categories = Category::where('is_active',1)->get(['id','name','slug']);
        return view('livewire.products-page',['products'=>$products->paginate(9),'brands'=>$brands,'categories'=>$categories]);
    }
}
