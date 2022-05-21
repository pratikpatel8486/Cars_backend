<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
class LoginController extends Controller
{
    // public function AdminLogin(Request $request)
    // {
    //     $requestData = $request->json()->all();
    //     if (count($requestData) > 0) {
    //         if (!empty($requestData['email']) && !empty($requestData['password'])) {
    //             $email = $requestData['email'];
    //             $password = $requestData['password'];
    //         } else {
    //             return $this->sendError(Lang::get("auth.require_email_password", array(), $this->selected_language), json_decode("{}"), 201);
    //         }
    //        $hasher = app()->make('hash');
    //     //    dd($hasher);
    //         $login = User::where('email', $email)->first();
    //         if (!$login) {
    //             return $this->sendError(Lang::get("auth.invalid_login", array(), $this->selected_language), json_decode("{}"), 201);
    //         } else {
    //             if ($login) {
    //                 if (!empty($login->deleted_at)) {
    //                     return $this->sendError(Lang::get("auth.account_deactivated", array(), $this->selected_language), json_decode("{}"), 201);
    //                 }
    //             }
    //             if ($hasher->check($password, $login->password)) {
    //                 if (!$create_token = Auth::attempt($requestData)) {
    //                     return $this->sendError(Lang::get("common.unauthorized", array(), $this->selected_language), json_decode("{}"), 401);
    //                 }
    //                 // Update device token
    //                 User::where('id', $login->id)->update(['api_token' => $create_token]);
    //                 if ($create_token) {
    //                     $User = $login;
    //                     $tokens = $this->respondWithToken($create_token);
    //                     $data['api_token'] = $tokens['api_token'];
    //                     $data['token_expires_in'] = $tokens['token_expires_in'];
    //                     $data['user_info'] = $User;
    //                     return $this->sendResponse($data, Lang::get("common.success", array(), $this->selected_language));
    //                 }
    //             } else {
    //                 return $this->sendError(Lang::get("auth.invalid_login", array(), $this->selected_language), json_decode("{}"), 201);
    //             }
    //         }
    //     } else {
    //         return $this->sendError(Lang::get("common.request_invalid", array(), $this->selected_language), json_decode("{}"), 400);
    //     }
    // }


    /*public function AdminLogin(Request $request)
    {
        $requestData = $request->json()->all();
        if (count($requestData) > 0) {
            if (!empty($requestData['email']) && !empty($requestData['password'])) {
                $email = $requestData['email'];
                $password = $requestData['password'];
            } else {
                return $this->sendError(Lang::get("auth.require_email_password", array(), ''), json_decode("{}"), 201);
            }
         //  $hasher = app()->make('hash');
          // dd($hasher);
            $login = User::where('email', $email)->first();
            if (!$login) {
                return response()->json(['success' => 'false', 'messae' => 'invalid_login']);
              //  return $this->sendError(Lang::get("auth.invalid_login", array(), ''), json_decode("{}"), 201);
            } else {
               // CHECK IF PAS AND ID ARE VALID
               $login_true = User::where('email', $email)->where('password', $password)->first();

               if($login_true){
                return response()->json(['success' => 'true', 'messae' => 'Login successfully']);
               }else{
                //return response()->json(['success' => 'false', 'messae' => 'invalid_login']);
                return $this->sendError("invalid_login", array(''), 400);
               }
             
            }
        } else {
            return response()->json(['success' => 'false', 'messae' => 'Request invalid']);
           // return $this->sendError(Lang::get("common.request_invalid", array(), ''), json_decode("{}"), 400);
        }
    }*/

    public function add_car(Request $request){
        $requestData = $request->json()->all();
        if (count($requestData) > 0) {
            $validator =  Validator::make($requestData, [
                'brand' => 'required',
                'modal' => 'required',
                'variant' => 'required',
                'make_year' => 'required',
                'reg_year' => 'required',
                'fuel_type' => 'required',
                'ownership' => 'required',
                'kms' => 'required',
                'rto' => 'required',
                'transmission' => 'required',
                'insurance' => 'required',
                'insurance_date' => 'required',
                'color' => 'required',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return $this->sendError($error, null, 400);
            }

            $brand = $requestData['brand'];
            $modal = $requestData['modal'];
            $variant = $requestData['variant'];
            $make_year = $requestData['make_year'];
            $reg_year = $requestData['reg_year'];
            $fuel_type = $requestData['fuel_type'];
            $ownership = $requestData['ownership'];
            $kms = $requestData['kms'];
            $rto = $requestData['rto'];
            $transmission = $requestData['transmission'];
            $insurance = $requestData['insurance'];
            $insurance_date = $requestData['insurance_date'];
            $color = $requestData['color'];


            $car = new Car;
            $car->brand = $brand;
            $car->modal = $modal;
            $car->variant = $variant;
            $car->make_year = $make_year;
            $car->reg_year = $reg_year;
            $car->fuel_type = $fuel_type;
            $car->ownership = $ownership;
            $car->kms = $kms;
            $car->rto = $rto;
            $car->transmission = $transmission;
            $car->insurance = $insurance;
            $car->insurance_date = $insurance_date;
            $car->color = $color;
            $car->save();

            if (!empty($car)) {
                return $this->sendResponse($car, Lang::get("success", array(), ''), 200);
            }else{
                return $this->sendError(Lang::get("Car not added", array(), ''), json_decode("{}"), 201);
            }
        }
    }
    public function AdminRegister(Request $request)
    {
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 401);       
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]); 
        return response()->json([
            'status' => true,
            'message' => 'Account registered successfully.', 
            'data' => $user
        ]);
    }
    public function AdminLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 401);       
        }
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
        return $credentials;
            return response()->json([
                    'success' => false,
                    'message' => 'Could not create token.',
                ], 500);
        }
        $user = Auth::user();
        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'message' => 'Login successfully',
            'data' => $user,
            'token' => $token,
            'type' => 'bearer'
        ]);
    }
}
