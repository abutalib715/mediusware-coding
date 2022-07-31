<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $products = new Product();

        //SEARCH IF SEARCH PARAMETER EXIST
        if($request->title != ''){
            $products = $products->where('title','like','%'.$request->title.'%');
        }
        if($request->price_from != ''){
            $products = $products->whereHas('productVariantPrices',function($query) use($request){
                $query->where('price','>=',$request->price_from);
            });
        }
        if($request->price_to != ''){
            $products = $products->whereHas('productVariantPrices',function($query) use($request){
                $query->where('price','<=',$request->price_to);
            });
        }
        if($request->variant != ''){
            $products = $products->whereHas('productVariants',function($query) use($request){
                $query->where('variant',$request->variant);
            });
        }
        if($request->date != ''){
            $products = $products->whereDate('created_at',$request->date);
        }

        //FINALLY GET PRODUCTS
        $products = $products->paginate(2);

        //GET VARIANT GROUPS
        $_variants  = Variant::all();
        //RETRIEVE PRODUCT VARIANTS
        $product_variants = [];
        foreach ($_variants as $_variant):
            $product_variants[$_variant->title] = ProductVariant::where('variant_id',$_variant->id)
                ->groupBy('variant')->pluck('variant')->toArray();
        endforeach;

        return view('products.index',compact('products','product_variants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
