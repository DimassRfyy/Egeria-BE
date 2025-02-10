<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryApiResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request) {
        $categories = Category::withCount(['cosmetics']);

        if ($request->has('limit')) {
            $categories->limit($request->input('limit'));
        }

        return CategoryApiResource::collection($categories->get());
    }

    public function show(Category $category) {
        $category->load(['cosmetics','popularCosmetics','cosmetics.brand']);
        $category->loadCount(['cosmetics']);

        return new CategoryApiResource($category);
    }
}
