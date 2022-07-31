@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" value="{{ request('title')??'' }}" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        <option value="">-- Select A Variant --</option>
                        @foreach($product_variants as $key=>$variants)
                        <optgroup label="{{ $key }}">
                            @foreach($variants as $variant)
                            <option value="{{ $variant }}" {{ request('variant')==$variant?'selected':'' }}>{{ $variant }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" value="{{ request('price_from')??'' }}" aria-label="Price From" placeholder="From" class="form-control">
                        <input type="text" name="price_to" value="{{ request('price_to')??'' }}" aria-label="Price To" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" value="{{ request('date')??'' }}" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $product->title }} <br> Created at : {{ date('d-M-Y',strtotime($product->created_at)) }}</td>
                        <td></td>
                        <td>
                            <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant-{{ $loop->iteration }}">
                                {{-- LOOPING OVER PRODUCT VARIANTS --}}
                                @foreach($product->productVariantPrices as $product_variant_price)
                                    {{-- RETRIVING VARIANT DATA --}}
                                    @php
                                        $_variant = $product_variant_price->productVariantOne->variant??'';
                                        $_variant .= '/ '.$product_variant_price->productVariantTwo->variant??'';
                                        $_variant .= '/ '.$product_variant_price->productVariantTwo->variant??'';

                                        $_price = $product_variant_price->price;
                                        $_stock = $product_variant_price->stock;
                                    @endphp

                                <dt class="col-sm-3 pb-0">
                                    {{ $_variant }}
                                </dt>
                                <dd class="col-sm-9">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 pb-0">Price : {{ number_format($_price,2) }}</dt>
                                        <dd class="col-sm-8 pb-0">InStock : {{ number_format($_stock,2) }}</dd>
                                    </dl>
                                </dd>
                                @endforeach
                            </dl>
                            <button onclick="$('#variant-{{ $loop->iteration }}').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.edit', 1) }}" class="btn btn-success">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} out of {{$products->total() }}</p>
                </div>
                <div class="col-md-3">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
