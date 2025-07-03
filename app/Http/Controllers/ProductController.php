<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $user_id = auth()->user()->id;

        $products = Product::where("user_id", $user_id)->get()->map(function($product) {
            $product->banner_image = $product->banner_image ? asset("storage/" . $product->banner_image) : null;
            return $product;
        });

        return response()->json([
            "status" => true,
            "products" => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "title" => "required"
        ]);

        $data["description"] = $request->description;
        $data["cost"] = $request->cost;
        
        $data["user_id"] = auth()->user()->id;
        if ($request->hasFile("banner_image")) {
            $data["banner_image"] = $request->file("banner_image")->store("products", "public");
        }

        Product::create($data);

        return response()->json([
            "status" => true,
            "message" => "Product created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            "status" => true,
            "message" => "Product data retrieved successfully",
            "product" => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            "title" => "required",
        ]);

        $data["description"] = isset($request->description) ? $request->description : $product->description;
        $data["cost"] = isset($request->cost) ? $request->cost : $product->cost;
        
        
        if ($request->hasFile("banner_image")) {
            if ($product->banner_image) {
                Storage::disk("public")->delete($product->banner_image);
            }

            $data["banner_image"] = $request->file("banner_image")->store("products", "public");
        }

        $product->update($data);

        return response()->json([
            "status" => true,
            "message" => "Product data updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            "status" => true,
            "message" => "Product deleted successfully"
        ]);
    }
}
