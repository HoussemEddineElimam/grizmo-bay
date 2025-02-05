<?php
namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartManagment{
    // add Item to cart
    static public function addItemToCart($product_id){
        $cart_items = self::getCartItemsFromCookie();
        $existing_item = null;
        foreach($cart_items as $key=>$item){
            if($item['product_id'] == $product_id){
                $existing_item = $key;
                break;
            }
        }
        if(!$existing_item){
            $cart_items[$existing_item]['quantity']++;
            $cart_item[$existing_item]['total_amount'] = $cart_items[$existing_item]['quantity']*$cart_items[$existing_item]['unit_amount'];
        }else{
            $product = Product::query()->where('id',$product_id)->get('id','name','price','images');
            if($product){
                $cart_items[] = [
                    'product_id' =>$product_id,
                    'name'=>$product->name,
                    'image'=>$product->images[0],
                    'unit_amount'=>$product->price,
                    'total_amount'=>$product->price,
                    'quantity'=>1,
                ];
            }
        }
        self::addCartItemsCookie($cart_items);
        return count($cart_items);

    }

    //remove item from cart
    static public function removeCartItem($product_id){
        $cart_items = self::getCartItemsFromCookie();
        foreach($cart_items as $key => $item){
            if($item['product_id'] == $product_id){
                unset($cart_items[$key]);
            }
        }
        self::addCartItemsCookie($cart_items);
    }
    // add cart item to cookie
    static public function addCartItemsCookie($cart_items){
        Cookie::queue('cart_items',json_encode($cart_items),60*24*30); 
    }

    //clear cart items from cookie
    static public function clearCartItemsCookie(){
        Cookie::queue(Cookie::forget('cart_items'));
    }
    // get all cart items from cookie
    static public function getCartItemsFromCookie(){
        $data =  json_decode(Cookie::get('cart_items'),true);
        if(!$data){
            $data = [];
        }
        return $data;
    }
    // increament item quantity 
    static public function increamentQuantityToCartItem($product_id){
        $cart_items = self::getCartItemsFromCookie();
        foreach($cart_items as $key=>$item){
            if($item['product_id']== $product_id){
                $cart_items[$key]['quantity'] ++;
                $cart_items[$key]['total_amount'] =  $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
            }
        }
        self::addCartItemsCookie($cart_items);
    }
    // decreament item quantity
    static public function decreamentQuantityToCartItem($product_id){
        $cart_items = self::getCartItemsFromCookie();
        foreach($cart_items as $key=>$item){
            if($item['product_id']== $product_id){
               if($item['quantity']>1){
                $cart_items[$key]['quantity'] --;
                $cart_items[$key]['total_amount'] =  $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
                self::addCartItemsCookie($cart_items);
               }else{
                self::removeCartItem($product_id);
               }
            }
        }
    }
    //  calculate grand total
        static public function calculateGrandTotal($items){
            $cart_items = self::getCartItemsFromCookie();
            return array_sum(array_column($items , 'total_amount'));
        }
}