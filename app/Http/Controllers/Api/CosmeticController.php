<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CosmeticApiResource;
use App\Models\Cosmetic;
use Illuminate\Http\Request;

class CosmeticController extends Controller
{
    public function index(Request $request) {
        $cosmetics = Cosmetic::with(['brand','category']);

        if($request->has('brand_id')) {
            $cosmetics->where('brand_id', $request->input('brand_id'));
        }
        if($request->has('category_id')) {
            $cosmetics->where('category_id', $request->input('category_id'));
        }
        if($request->has('is_popular')) {
            $cosmetics->where('is_popular', $request->input('is_popular'));
        }
        if($request->has('limit')) {
            $cosmetics->limit($request->input('limit'));
        }

        return CosmeticApiResource::collection($cosmetics->get());
    }

    public function show(Cosmetic $cosmetic) {
        $cosmetic->load(['category','benefits','photos','testimonials','brand']);

        return new CosmeticApiResource($cosmetic);
    }
}
