<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Brands;
use App\Models\BrandModal;
use App\Models\BrandVariant;
use App\Models\CarImages;
use Validator;
use Illuminate\Support\Facades\DB;
use File;
class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $imageUrl = \URL::to('public/images/');
        $cars = Car::select("*",DB::raw('DATE_FORMAT(insurance_date, "%d-%m-%Y") as insurance_date'));

        if(isset($request->body_type) && !empty($request->body_type)){
            $cars = $cars->where('body_type', $request->body_type);
        }
        if(isset($request->brand) && !empty($request->brand)){
            $cars = $cars->where('brand', $request->brand);
        }
        if(isset($request->model) && !empty($request->model)){
            $cars = $cars->where('modal', $request->model);
        }
        if(isset($request->make_year) && !empty($request->make_year)){
            $cars = $cars->where('make_year', $request->make_year);
        }
        if(isset($request->registration_year) && !empty($request->registration_year)){
            if (strpos(strtolower($request->registration_year), 'above') !== false) {
                $regYear = str_replace('above', '', strtolower($request->registration_year));
                $regYear = trim($regYear);
                $cars = $cars->where('reg_year', '>=', DB::raw("$regYear"));    
            }
            else if (strpos(strtolower($request->registration_year), '-') !== false) {
                $regYear = explode('-', $request->registration_year);
                $start = isset($regYear[0]) ? trim($regYear[0]) : '';
                $end = isset($regYear[1]) ? trim($regYear[1]) : '';
                $cars = $cars->where('reg_year', '>=', DB::raw("$start"))->where('reg_year', '<=', DB::raw("$end"));
            }
        }
        if(isset($request->kms) && !empty($request->kms)){
            if (strpos(strtolower($request->kms), 'below') !== false) {
                $kms = str_replace('below', '', strtolower($request->kms));
                $kms = $this->formatPrice($kms);
                $cars = $cars->where('kms', '<=', DB::raw("$kms"));
            }
            else if (strpos(strtolower($request->kms), '-') !== false) {
                $kms = explode('-', $request->kms);
                $start = isset($kms[0]) ? trim($this->formatPrice($kms[0])) : '';
                $end = isset($kms[1]) ? trim($this->formatPrice($kms[1])) : '';
                $cars = $cars->where('kms', '>=', DB::raw("$start"))->where('kms', '<=', DB::raw("$end"));
            }
            else if (strpos(strtolower($request->kms), 'above') !== false) {
                $kms = str_replace('above', '', strtolower($request->kms));
                $kms = trim($this->formatPrice($kms));
                $cars = $cars->where('kms', '>=', DB::raw("$kms"));    
            }
        }
        if(isset($request->budget) && !empty($request->budget)){
            if (strpos(strtolower($request->budget), 'below') !== false) {
                $budget = str_replace('below', '', strtolower($request->budget));
                $budget = $this->formatPrice($budget);
                $cars = $cars->where('price', '<=', DB::raw("$budget"));
            }
            else if (strpos(strtolower($request->budget), '-') !== false) {
                $budget = explode('-', $request->budget);
                $start = isset($budget[0]) ? trim($this->formatPrice($budget[0])) : '';
                $end = isset($budget[1]) ? trim($this->formatPrice($budget[1])) : '';
                $cars = $cars->where('price', '>=', DB::raw("$start"))->where('price', '<=', DB::raw("$end"));
            }
            else if (strpos(strtolower($request->budget), 'above') !== false) {
                $budget = str_replace('above', '', strtolower($request->budget));
                $budget = trim($this->formatPrice($budget));
                $cars = $cars->where('price', '>=', DB::raw("$budget"));    
            }
        }
        if(isset($request->sort_by) && !empty($request->sort_by))
        {
            if(strtolower($request->sort_by) == 'price'){
                $cars = $cars->orderBy('cars.price');    
            }
            else if(strtolower($request->sort_by) == 'model year'){
                $cars = $cars->orderBy('cars.make_year');    
            }
        }
        else{
            $cars = $cars->orderBy('cars.created_at', 'DESC');
        }
        $cars = $cars->get();

        $carData = array();
        foreach($cars as $index => $car){
            $carData[$index] = $car;
            $carImages = CarImages::where("car_id", $car->id)->get();
            $images = array();
            foreach($carImages as $image){
                $images[] = $imageUrl.$image->image;
            }
            $carData[$index]->images = $images;
        }
        return response()->json([
            'status' => true,
            'message' => 'Cars fetched successfully.', 
            'data' => $carData
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
     * Store a newly added resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'brand' => 'required|max:100',
            'modal' => 'required|max:50',
            'variant' => 'required|max:50',
            'body_type' => 'required:max:50',
            'make_year' => 'required|numeric',
            'reg_year' => 'required|numeric',
            'fuel_type' => 'required|max:50',
            'ownership' => 'required|max:50',
            'kms' => 'required|max:50',
            'rto' => 'required|max:50',
            'transmission' => 'required|max:50',
            'insurance' => 'required|max:50',
            'insurance_date' => 'required|max:50',
            'color' => 'required|max:50',
            'images.*' => 'required|image|mimes:jpg,png,jpeg,gif',
            'price' => 'required|numeric|min:0'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 401);       
        }
        //validate brand
        $brandData = Brands::find($request->brand);
        if (is_null($brandData)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brand', 
                'data' => ''
            ], 404);
        }

        //validate brand model
        $brandModel = BrandModal::find($request->modal);
        if (is_null($brandModel)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brand model', 
                'data' => ''
            ], 404);
        }

        //validate brand variant
        $brandVariant = BrandVariant::find($request->variant);
        if (is_null($brandVariant)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brand variant', 
                'data' => ''
            ], 404);
        }
         
        $car = Car::create([
            'brand' => $request->brand,
            'modal' => $request->modal,
            'variant' => $request->variant,
            'body_type' => $request->body_type,
            'make_year' => $request->make_year,
            'reg_year' => $request->reg_year,
            'fuel_type' => $request->fuel_type,
            'ownership' => $request->ownership,
            'kms' => $request->kms,
            'rto' => $request->rto,
            'transmission' => $request->transmission,
            'insurance' => $request->insurance,
            'insurance_date' => date('Y-m-d', strtotime($request->insurance_date)),
            'color' => $request->color,
            'price' => $request->price
        ]);
        if($car){
            $path = public_path('images');
   
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            $images = $request->file('images');
            foreach ($images as $image) {      
                $imageName = time().rand(). "." . $image->getClientOriginalExtension();
                $image->move($path, $imageName);
                //store image file into directory and db
                $carImage = new CarImages();
                $carImage->car_id = $car->id;
                $carImage->image = $imageName;
                $carImage->save();
            }
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Car added successfully.', 
            'data' => $car
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($carId)
    {
        $imageUrl = \URL::to('public/images/');
        $car = Car::select("*",DB::raw('DATE_FORMAT(insurance_date, "%d-%m-%Y") as insurance_date'))->where("id", $carId)->get()->first();
        $carImages = CarImages::where("car_id", $carId)->get();


        if (is_null($car)) {
            return response()->json([
                'status' => false,
                'message' => 'Car not found', 
                'data' => ''
            ], 404);
        }
        $images = array();
        if($carImages->count() > 0){
            foreach($carImages as $image){
                $images[] = $imageUrl.$image->image;
            }
        }
        $car->images = $images;
        return response()->json([
            'status' => true,
            'message' => 'Car fetched successfully.', 
            'data' => $car
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
    public function update(Request $request, $carId)
    {
        $validator = Validator::make($request->all(),[
            'brand' => 'required|max:100',
            'modal' => 'required|max:50',
            'variant' => 'required|max:50',
            'body_type' => 'required:max:50',
            'make_year' => 'required|numeric',
            'reg_year' => 'required|numeric',
            'fuel_type' => 'required|max:50',
            'ownership' => 'required|max:50',
            'kms' => 'required|max:50',
            'rto' => 'required|max:50',
            'transmission' => 'required|max:50',
            'insurance' => 'required|max:50',
            'insurance_date' => 'required|max:50',
            'color' => 'required|max:50',
            'images.*' => 'image|mimes:jpg,png,jpeg,gif',
            'price' => 'required|numeric|min:0'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 401);       
        }
        //validate brand
        $brandData = Brands::find($request->brand);
        if (is_null($brandData)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brand', 
                'data' => ''
            ], 404);
        }

        //validate brand model
        $brandModel = BrandModal::find($request->modal);
        if (is_null($brandModel)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brand model', 
                'data' => ''
            ], 404);
        }

        //validate brand variant
        $brandVariant = BrandVariant::find($request->variant);
        if (is_null($brandVariant)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brand variant', 
                'data' => ''
            ], 404);
        }

        $car = Car::find($carId);
        if (is_null($car)) {
            return response()->json([
                'status' => false,
                'message' => 'Car not found', 
                'data' => ''
            ], 404);
        }
        $car->brand = $request->brand;
        $car->modal = $request->modal;
        $car->variant = $request->variant;
        $car->body_type = $request->body_type;
        $car->make_year = $request->make_year;
        $car->reg_year = $request->reg_year;
        $car->fuel_type = $request->fuel_type;
        $car->ownership = $request->ownership;
        $car->kms = $request->kms;
        $car->rto = $request->rto;
        $car->transmission = $request->transmission;
        $car->insurance = $request->insurance;
        $car->insurance_date = date('Y-m-d', strtotime($request->insurance_date));
        $car->color = $request->color;
        $car->price = $request->price;
        $car->save();

        if ($images = $request->file('images')) {
            $path = public_path('images');
   
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            CarImages::where("car_id", $carId)->delete();
            foreach ($images as $image) {      
                $imageName = time().rand() . "." . $image->getClientOriginalExtension();
                $image->move($path, $imageName);
                //store image file into directory and db
                $carImage = new CarImages();
                $carImage->car_id = $carId;
                $carImage->image = $imageName;
                $carImage->save();
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'Car updated successfully.', 
            'data' => $car
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($carId)
    {
        $car = Car::find($carId);
        if (is_null($car)) {
            return response()->json([
                'status' => false,
                'message' => 'Car not found', 
                'data' => ''
            ], 404);
        }

        $car->delete();

        CarImages::where("car_id", $carId)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Car deleted successfully.', 
            'data' => $car
        ]);
    }
    public function formatPrice($price)
    {
        $price = str_replace('.','',$price);
        if (strpos(strtolower($price), 'k') !== false) {
            return (float)$price.'000';
        }
        else if (strpos(strtolower($price), 'l') !== false) {
            return (float)$price.'00000';
        }
        else if (strpos(strtolower($price), 'cr') !== false) {
            return (float)$price.'00000000';
        }
    }
    public function getLatestCars()
    {
        $imageUrl = \URL::to('public/images/');
        $cars = Car::select("*",DB::raw('DATE_FORMAT(insurance_date, "%d-%m-%Y") as insurance_date'))->whereRaw("created_at BETWEEN NOW() - INTERVAL 30 DAY AND NOW()")->orderBy('cars.created_at', 'DESC')->get();
        $carData = array();
        foreach($cars as $index => $car){
            $carData[$index] = $car;
            $carImages = CarImages::where("car_id", $car->id)->get();
            $images = array();
            foreach($carImages as $image){
                $images[] = $imageUrl.$image->image;
            }
            $carData[$index]->images = $images;
        }
        return response()->json([
            'status' => true,
            'message' => 'Cars fetched successfully.', 
            'data' => $carData
        ]);
    }
}
