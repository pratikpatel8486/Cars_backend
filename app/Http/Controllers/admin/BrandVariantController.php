<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BrandVariant;
use App\Models\Brands;
use App\Models\BrandModal;
use Validator;
class BrandVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brandVariantData = BrandVariant::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'Brand variants fetched successfully.', 
            'data' => $brandVariantData
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'brand_id' => 'required',
            'model_id' => 'required',
            'brand_variant' => 'required|string|max:50'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 401);       
        }
        //validate brand
        $brandData = Brands::find($request->brand_id);
        if (is_null($brandData)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brand', 
                'data' => ''
            ], 404);
        }
        //validate brand model
        $brandModel = BrandModal::find($request->model_id);
        if (is_null($brandModel)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brand model', 
                'data' => ''
            ], 404);
        }
        $brandVariant = BrandVariant::create([
            'brand_id' => $request->brand_id,
            'model_id' => $request->model_id,
            'brand_variant' => $request->brand_variant,
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Brand variant created successfully.', 
            'data' => $brandVariant
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($brandVariantId)
    {
        $brandVariant = BrandVariant::find($brandVariantId);
        if (is_null($brandVariant)) {
            return response()->json([
                'status' => false,
                'message' => 'Brand variant not found', 
                'data' => ''
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Brand variant fetched successfully.', 
            'data' => $brandVariant
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $brandVariantId)
    {
        $validator = Validator::make($request->all(),[
            'brand_id' => 'required',
            'model_id' => 'required',
            'brand_variant' => 'required|string|max:50'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 401);       
        }
        $brandVariantData = BrandVariant::find($brandVariantId);
        if (is_null($brandVariantData)) {
            return response()->json([
                'status' => false,
                'message' => 'Brand variant not found', 
                'data' => ''
            ], 404);
        }

        $brandData = Brands::find($request->brand_id);
        if (is_null($brandData)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brand', 
                'data' => ''
            ], 404);
        }

        $brandVariantData->brand_id = $request->brand_id;
        $brandVariantData->model_id = $request->model_id;
        $brandVariantData->brand_variant = $request->brand_variant;
        $brandVariantData->save();
        return response()->json([
            'status' => true,
            'message' => 'Brand variant updated successfully.', 
            'data' => $brandVariantData
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($variantId)
    {
        $brandVariant = BrandVariant::find($variantId);
        if (is_null($brandVariant)) {
            return response()->json([
                'status' => false,
                'message' => 'Brand not found', 
                'data' => ''
            ], 404);
        }

        $brandVariant->delete();
        return response()->json([
            'status' => true,
            'message' => 'Brand variant deleted successfully.', 
            'data' => $brandVariant
        ]);
    }
    public function getVariantsByBrandModel(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'brand_id' => 'required',
            'model_id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 401);       
        }
        $brandVariants = BrandVariant::where("brand_id", $request->brand_id)->where("model_id", $request->model_id)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'Brand variants fetched successfully.', 
            'data' => $brandVariants
        ]);
    }
}
