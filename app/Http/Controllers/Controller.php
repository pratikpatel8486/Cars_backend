<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendResponse($result, $message, $code = 200)
    {
        $response = [
            'success'    => true,
            'message'   => $message,
            'api_version' => config('app.api_latest'),
            'datetime' => time()
        ];

        if (!is_null($result)) {
            $response['data'] = $result;
        }

        return response($response, $code);
    }

    public function sendError($message, $data = null, $code = 404)
    {
        $res = [
            'success'    => false,
            'message'   => $message,
            'api_version' => config('app.api_latest'),
            'datetime' => time()
        ];
        if (!empty($data)) {
            $res['data'] = $data;
        } else {
            $res['data'] = json_decode("{}");
        }
        return response($res, $code);
    }
}
