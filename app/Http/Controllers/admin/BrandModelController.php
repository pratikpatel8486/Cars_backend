<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BrandModal;
use App\Models\Brands;
use Validator;
class BrandModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brandModelData = BrandModal::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'Brand models fetched successfully.', 
            'data' => $brandModelData
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
            'brand_modal' => 'required|string|max:50'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 401);       
        }
        $brandData = Brands::find($request->brand_id);
        if (is_null($brandData)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brand', 
                'data' => ''
            ], 404);
        }
        $brandModel = BrandModal::create([
            'brand_id' => $request->brand_id,
            'brand_modal' => $request->brand_modal,
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Brand model created successfully.', 
            'data' => $brandModel
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($brandId)
    {
        $brandModel = BrandModal::find($brandId);
        if (is_null($brandModel)) {
            return response()->json([
                'status' => false,
                'message' => 'Brand model not found', 
                'data' => ''
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Brand model fetched successfully.', 
            'data' => $brandModel
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
    public function update(Request $request, $brandModelId)
    {
        $validator = Validator::make($request->all(),[
            'brand_id' => 'required',
            'brand_modal' => 'required|string|max:50'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 401);       
        }
        $brandModelData = BrandModal::find($brandModelId);
        if (is_null($brandModelData)) {
            return response()->json([
                'status' => false,
                'message' => 'Brand model not found', 
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

        $brandModelData->brand_id = $request->brand_id;
        $brandModelData->brand_modal = $request->brand_modal;
        $brandModelData->save();
        return response()->json([
            'status' => true,
            'message' => 'Brand model updated successfully.', 
            'data' => $brandModelData
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($modelId)
    {
        $brandModel = BrandModal::find($modelId);
        if (is_null($brandModel)) {
            return response()->json([
                'status' => false,
                'message' => 'Brand not found', 
                'data' => ''
            ], 404);
        }

        $brandModel->delete();
        return response()->json([
            'status' => true,
            'message' => 'Brand model deleted successfully.', 
            'data' => $brandModel
        ]);
    }
    public function getModelsByBrand($brandId)
    {
        $brandModelData = BrandModal::where("brand_id", $brandId)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'Brand models fetched successfully.', 
            'data' => $brandModelData
        ]);
    }
}
