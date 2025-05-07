<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryCreateRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mockery\Generator\StringManipulation\Pass\RemoveUnserializeForInternalSerializableClassesPass;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $item = Category::where('user_id', authUser()->id)->get();
        return success('Item: ', $item, Response::HTTP_OK);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryCreateRequest $categoryCreateRequest): JsonResponse
    {
        try {
            if (!Category::where('category_name', $categoryCreateRequest['category_name'])->exists()) {
                $category = new Category();
                $category->user_id = authUser()->id;
                $category->category_name = $categoryCreateRequest['category_name'];
                $category->category_code = Str::slug(authUser()->id . '-' . $categoryCreateRequest['category_name']);
                $category->save();
                return success('Category created successfully. ', $category, Response::HTTP_CREATED);
            }
        } catch (\Exception $exception) {
            Log::debug('Message: ' . $exception->getMessage() . '. On Line: ' . $exception->getLine());
        }
        return error('Category creation failed. ', [], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Category $category
     * @return JsonResponse
     */
    public function show(Category $category)
    {
        return success('Category fetched successfully. ', $category, Response::HTTP_CREATED);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Category $category
     * @return JsonResponse
     */
    public function edit(Category $category)
    {
        return success('Category fetched successfully. ', $category, Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }

    public function viewSubCategories(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id'
            ]);

            $subCategories = SubCategory::where([
                'user_id' => authUser()->id,
                'category_id' => $validated['category_id']
            ])->paginate(10);

            return $subCategories->isEmpty()
                ? error('Data not found', null, Response::HTTP_NO_CONTENT)
                : success('Sub Category', $subCategories, Response::HTTP_OK);

        } catch (Exception $exception) {
            return error('Error fetching subcategories', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createSubCategory(Request $request, SubCategory $subCategoryM)
    {
        $validated = $this->validate($request, [
            'category_id' => 'required',
            'sub_category_name' => 'required'
        ]);

        $data = new SubCategory();
        $data->user_id = authUser()->id;
        $data->category_id = $validated['category_id'];
        $data->sub_category_name = $validated['sub_category_name'];
        $data->save();
        return success('Sub Category created successfully. ', $data, Response::HTTP_CREATED);

    }
}