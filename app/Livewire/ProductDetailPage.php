<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductDetailPage extends Component
{   
    public $slug;
    public function mount($slug){
        return $this->slug = $slug;
    }
    public function render()
    {
        return view('livewire.product-detail-page',["product"=>Product::query()->where('slug',$this->slug)->firstOrFail()]);
    }
}
