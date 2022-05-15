<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BrandModal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class OtherController extends Controller
{
    public function load_brands()
    {
        $brand = Brand::all();
        if (!empty($brand)) {
            return $this->sendResponse($brand, Lang::get("success", array(), ''), 200);
        } else {
            return $this->sendError(Lang::get("common.country_not_found", array(), ''), null, 201);
        }
    }

    public function load_modal_by_brand(Request $request, $id)
    {
        if (!empty($id)) {
            $modals_name = BrandModal::where('brand_id', $id)->orderBy('brand_modal')->get();
            $totalModals = count($modals_name);
            if ($totalModals > 0) {
                return $this->sendResponse($modals_name, Lang::get("success", array(), ''), 200);
            } else {
                return $this->sendError(Lang::get("no_data_found", array(), ''), null, 201);
            }
        } else {
            return $this->sendError(Lang::get("brand_id_missing", array(), ''), null, 400);
        }
    }
}
