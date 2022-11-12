<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * View aall categories.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function categories(){
        $categories = Category::get();
        return success('Categories: ', $categories, 200);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addCategory(Request $request){
        $this->validate($request, [
            'category_name' => 'required|unique:categories|min:4',
        ]);

        $categoryCheck = Category::where('category_name',$request->category_name )->exists();
        if (!$categoryCheck)
        {
            $categoryData = new Category();
            $categoryData->category_name = $request->category_name;
            $categoryData->save();
            return success('Category Created Successfully. ',$categoryData, 200);
        }
            return error('Failed in Creating Category. ',$categoryCheck, 400);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function showCategory(Request $request, $id){
        $category= Category::find($id);
        return success('Category information', $category, 200);
    }
    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editCategory(Request $request, $id){
        $category= Category::find($id);
        return success('Category information', $category, 200);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateCategory(Request $request, $id){
        $category= Category::find($id);
        $category->category_name = $request->category_name;
        $category->update();
        return success('Category information updated', $category, 200);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteCategory($id){
        $category= Category::find($id);
        $category->delete();
        return success('Category information updated', $category, 200);
    }

}
