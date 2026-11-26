<?php

namespace App\Http\Controllers\ExhibitionProducts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\exhibition\Product;
use Spatie\Permission\Models\Role;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if($request->user()->cannot('VIEW_PRODUCT_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_PRODUCT_SUBMENU'));
        }
        //
        $products=Product::get();
        return view('exhibition_products.products',compact('products'));


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if($request->user()->cannot('CREATE_PRODUCT',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_PRODUCT'));
        }

        try {
            $request->validate([
                'product_name'=>'required'
            ]);
            Product::create([
                'product_id'=>Str::uuid(),
                'product_name'=>$request->product_name,
                'descriptions'=>$request->descriptions,
            ]);
            return back()->with('success','Product registered successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong'.$th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->user()->cannot('UPDATE_PRODUCT',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_PRODUCT'));
        }
        //

        try {
            $request->validate([
                'product_name'=>'required'
            ]);
            $product=Product::find($id);
            $product->update([
                'product_name'=>$request->product_name,
                'descriptions'=>$request->descriptions,
            ]);
            return back()->with('success','Product updated successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong'.$th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()->cannot('DELETE_PRODUCT',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_PRODUCT'));
        }
        //

        try {
            Product::find($id)->delete();
            return back()->with('success','Product Deleted successfully!');
        } catch (\Throwable $th) {
           return back()->with('fail','Something went wrong!'.$th->getMessage());
        }
    }
}
