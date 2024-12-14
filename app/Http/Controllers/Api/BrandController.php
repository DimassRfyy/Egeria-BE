<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BrandApiResource;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request) {
        $brands = Brand::withCount(['cosmetics']);

        if ($request->has('limit')) {
            $brands->limit($request->input('limit'));
        }

        return BrandApiResource::collection($brands->get());
    }

    public function show(Brand $brand) {
        $brand->load(['cosmetics','pupularCosmetics']);
        $brand->loadCount(['cosmetics']);

        return new BrandApiResource($brand);
    }
}
