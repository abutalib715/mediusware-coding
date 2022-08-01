<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
//        dd($request->input());
//        $request->validate([
//           ''
//        ]);
        DB::beginTransaction();
        try {
            $product = new Product();
            $product->title = $request->title;
            $product->sku = $request->sku;
            $product->description = $request->description;
            $product->save();
            $_new_product_id = $product->id;

            //SAVING DATA IN PRODUCT VARIANT TABLE
            foreach ($request->product_variant as $variant):
                foreach ($variant['tags'] as $tag):
                    $product_variant = new ProductVariant();
                    $product_variant->variant = $tag;
                    $product_variant->variant_id = $variant['option'];
                    $product_variant->product_id = $_new_product_id;
                    $product_variant->save();
                endforeach;
            endforeach;

            //SAVING DATA IN PRODUCT VARIANT PRICES TABLE
            foreach ($request->product_variant_prices as $pv_price):
                //EXPLODING COMBINATION OF VARIANTS
                $_titles = explode('/', $pv_price['title']);

                $pro_variant_price = new ProductVariantPrice();

                if(array_key_exists('0',$_titles))
                    $pro_variant_price->product_variant_one = ProductVariant::where('variant',$_titles[0])->where('product_id',$_new_product_id)->first()->id??NULL;
                if(array_key_exists('1',$_titles))
                    $pro_variant_price->product_variant_two = ProductVariant::where('variant',$_titles[1])->where('product_id',$_new_product_id)->first()->id??NULL;
                if(array_key_exists('2',$_titles))
                    $pro_variant_price->product_variant_three = ProductVariant::where('variant',$_titles[2])->where('product_id',$_new_product_id)->first()->id??NULL;

                $pro_variant_price->price = $pv_price['price'];
                $pro_variant_price->stock = $pv_price['stock'];
                $pro_variant_price->product_id = $_new_product_id;
                $pro_variant_price->save();
            endforeach;

            DB::commit();
            return response()->json(['status'=>'success','message'=>'Product Saved Successfully']);
        } catch (\Exception $e){
            DB::rollBack();
            return response()->json(['status'=>'error','message'=>$e->getMessage()??'Failed to save product info']);
        }
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
