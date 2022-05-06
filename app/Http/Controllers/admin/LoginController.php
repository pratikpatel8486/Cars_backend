<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    public function AdminLogin(Request $request)
    {
        $requestData = $request->json()->all();
        if (count($requestData) > 0) {
            if (!empty($requestData['email']) && !empty($requestData['password'])) {
                $email = $requestData['email'];
                $password = $requestData['password'];
            } else {
                return $this->sendError(Lang::get("auth.require_email_password", array(), $this->selected_language), json_decode("{}"), 201);
            }
            $hasher = app()->make('hash');
            $login = User::where('email', $email)->first();
            if (!$login) {
                return $this->sendError(Lang::get("auth.invalid_login", array(), $this->selected_language), json_decode("{}"), 201);
            } else {
                if ($login) {
                    if (!empty($login->deleted_at)) {
                        return $this->sendError(Lang::get("auth.account_deactivated", array(), $this->selected_language), json_decode("{}"), 201);
                    }
                }
                if ($hasher->check($password, $login->password)) {
                    if (!$create_token = Auth::attempt($requestData)) {
                        return $this->sendError(Lang::get("common.unauthorized", array(), $this->selected_language), json_decode("{}"), 401);
                    }
                    // Update device token
                    User::where('id', $login->id)->update(['api_token' => $create_token]);
                    if ($create_token) {
                        $User = $login;
                        $tokens = $this->respondWithToken($create_token);
                        $data['api_token'] = $tokens['api_token'];
                        $data['token_expires_in'] = $tokens['token_expires_in'];
                        $data['user_info'] = $User;
                        return $this->sendResponse($data, Lang::get("common.success", array(), $this->selected_language));
                    }
                } else {
                    return $this->sendError(Lang::get("auth.invalid_login", array(), $this->selected_language), json_decode("{}"), 201);
                }
            }
        } else {
            return $this->sendError(Lang::get("common.request_invalid", array(), $this->selected_language), json_decode("{}"), 400);
        }
    }
}
