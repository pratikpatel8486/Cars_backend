<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Brands;
class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brandData = Brands::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'Brand fetched successfully.', 
            'data' => $brandData
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
            'brand' => 'required|string|max:100'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 401);       
        }
        $brand = Brands::create([
            'brand' => $request->brand
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Brand created successfully.', 
            'data' => $brand
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
        $brandData = Brands::find($brandId);
        if (is_null($brandData)) {
            return response()->json([
                'status' => false,
                'message' => 'Brand not found', 
                'data' => ''
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Brand fetched successfully.', 
            'data' => $brandData
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
    public function update(Request $request, $brandId)
    {
        $validator = Validator::make($request->all(),[
            'brand' => 'required|string|max:100'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 401);       
        }
        $brandData = Brands::find($brandId);
        if (is_null($brandData)) {
            return response()->json([
                'status' => false,
                'message' => 'Brand not found', 
                'data' => ''
            ], 404);
        }

        $brandData->brand = $request->brand;
        $brandData->save();
        return response()->json([
            'status' => true,
            'message' => 'Brand updated successfully.', 
            'data' => $brandData
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($brandId)
    {
        $brandData = Brands::find($brandId);
        if (is_null($brandData)) {
            return response()->json([
                'status' => false,
                'message' => 'Brand not found', 
                'data' => ''
            ], 404);
        }

        $brandData->delete();
        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully.', 
            'data' => $brandData
        ]);
    }
}
