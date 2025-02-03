<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class ProductsPage extends Component
{   
    use WithPagination;
    #[Url]
    public $selected_categories = [];
    #[Url]
    public $selected_brands = [];
    #[Url]
    public $featured;
    #[Url]
    public $sale;
    public function render()
    {   
        $products = Product::query()->where('is_active',1);

        if(!empty($this->selected_categories)){
            $products->whereIn('category_id',$this->selected_categories);
        }
        if(!empty($this->selected_brands)){
            $products->whereIn('brand_id',$this->selected_brands);
        }
        if($this->featured){
            $products->where('is_featured',1);
        }
        if($this->sale){
            $products->where('on_sale',1);
        }
        $brands = Brand::where('is_active',1)->get(['id','name','slug']);
        $categories = Category::where('is_active',1)->get(['id','name','slug']);
        return view('livewire.products-page',['products'=>$products->paginate(9),'brands'=>$brands,'categories'=>$categories]);
    }
}
