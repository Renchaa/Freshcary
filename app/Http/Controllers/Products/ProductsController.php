<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Product\Category;
use App\Models\Product\Cart;
use App\Models\Product\Order;
use Auth;
use Redirect;
use Session;


class ProductsController extends Controller
{
    public function singleCategory($id){
        $products = Product::select()->orderBy('id', 'desc')
        ->where('category_id',$id)->get();
        return view('products.singlecategory', compact('products'));
    }
    public function singleProduct($id){

        $product =Product::find($id);

        $relatedProducts = Product::where('category_id',$product->category_id)
        ->where('id','!=',$id)
        ->get();
    if(isset( auth::user()->id)){


            $checkInCart = Cart::where('pro_id',$id)
            ->where('user_id', Auth::user()->id)
            ->count();

            return view('products.singleproduct', compact('product','relatedProducts','checkInCart'));
    }
    else{
        return view('products.singleproduct', compact('product','relatedProducts'));

    }
}
    
    public function shop(){
        $categories = Category::select()->orderBy('id','desc')->get();

        $mostWanted=Product::select()->orderBy('name','desc')->take(5)->get();

        $vegetables = Product::select()->where('category_id', "=", 5)
        ->orderBy('id','desc')->take(5)
        ->get();

        $meats = Product::select()->where('category_id', "=", 1)
        ->orderBy('id','desc')->take(5)
        ->get();

        $fishes = Product::select()->where('category_id', "=", 2)
        ->orderBy('id','desc')->take(5)
        ->get();

        $fruits = Product::select()->where('category_id', "=", 4)
        ->orderBy('id','desc')->take(5)
        ->get();

        return view('products.shop', compact('categories', 'mostWanted', 'vegetables','meats','fishes','fruits'));
    }

    public function addToCart(Request $request){

        $addCart=Cart::create([

            "name"=>$request->name,
            "price"=>$request->price,
            "qty"=>$request->qty,
            "image"=>$request->image,
            "pro_id"=>$request->pro_id,
            "user_id"=> Auth::user()->id,
            "subtotal"=>$request->qty*$request->price
        ]);
        if($addCart){
            return Redirect::route("single.product",$request->pro_id)->with(['success' => 'Product added to cart successfully']);
        }
    }

    public function cart(){
        $cartProducts = Cart::select()->where('user_id',Auth::user()->id)
        ->get();
        $subtotal = Cart::where('user_id',Auth::user()->id)->sum('subtotal');
        return view('products.cart',compact('cartProducts','subtotal'));
    }
    
    public function deleteFromCart($id){
       $deleteCart  = Cart::find($id);
       $deleteCart->delete();
       if($deleteCart){
        return Redirect::route("products.cart")->with(['delete' => 'Product deleted from cart successfully ']);
        }
    }

    public function prepareCheckout(Request $request){
        $price= $request->price;
        $value=Session::put('value',$price);
        $newPrice=Session::get($value);

        if($newPrice>0){
            return Redirect::route("products.checkout");
         }
     }


     public function checkout(){
        $cartItems = Cart::select()->where('user_id',Auth::user()->id)->get();
        $checkoutSubtotal = Cart::select()->where('user_id',Auth::user()->id)
        ->sum('subtotal');

        return view('products.checkout',compact('cartItems','checkoutSubtotal'));
     }

     public function proccessCheckout(Request $request){

        $checkout=Order::create([

            "name"=>$request->name,
            "last_name"=>$request->last_name,
            "adress"=>$request->adress,
            "town"=>$request->town,
            "state"=>$request->state,
            "zip_code"=>$request->zip_code,
            "email"=>$request->email,
            "phone_number"=>$request->phone_number,
            "price"=>$request->price,
            "user_id"=> $request->user_id,
            "order_notes"=>$request->order_notes
        ]);

        $value=Session::put('value',$request->price);
        $newPrice=Session::get($value);


        if($checkout){
            return Redirect::route("products.pay");
        }
    }
    public function payWithPaypal(){
        
        return view('products.pay');
     }
     public function success(){
        $deleteItemsFromCart=Cart::where('user_id',Auth::user()->id);
        $deleteItemsFromCart->delete();

        if($deleteItemsFromCart){
            Session::forget('value');
            return view("products.success");
        }
        //return view('products.pay');
     }
}
