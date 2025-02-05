<?php

namespace App\Livewire;

use App\Helpers\CartManagment;
use App\Livewire\Partials\Navbar;
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
    #[Url]
    public $price_range = 50;
    #[Url]
    public $sort;

    // add product to cart
    public  function addToCart($product_id){
        $total_count = CartManagment::addItemToCart($product_id);
        $this->dispatch('update-cart-count',total_count:$total_count)->to(Navbar::class); 

    }
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
        if($this->price_range){
            $products->whereBetween('price',[0,$this->price_range]);
        }
        if($this->sort == 'latest'){
            $products->latest();
        }
        if($this->sort == 'price'){
            $products->orderBy('price');
        }
        $brands = Brand::where('is_active',1)->get(['id','name','slug']);
        $categories = Category::where('is_active',1)->get(['id','name','slug']);
        return view('livewire.products-page',['products'=>$products->paginate(9),'brands'=>$brands,'categories'=>$categories]);
    }
}
