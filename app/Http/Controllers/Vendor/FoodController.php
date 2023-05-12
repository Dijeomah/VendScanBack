<?php

    namespace App\Http\Controllers\Vendor;

    use App\Http\Controllers\Controller;
    use App\Models\BusinessLink;
    use App\Models\Category;
    use App\Models\Food;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;

    class FoodController extends Controller
    {
        /**
         * View aall categories.
         *
         * @return \Illuminate\Http\JsonResponse
         * @throws \Illuminate\Validation\ValidationException
         */
        public function food()
        {
            $food = Food::where('userid', authUser()->userid)->get();
            return success('Food: ', $food, 200);
        }

        /**
         * Update the Authenticated User profile.
         *
         * @return \Illuminate\Http\JsonResponse
         * @throws \Illuminate\Validation\ValidationException
         */
        public function addFood(Request $request) : JsonResponse
        {
            $validated_data = $this->validate($request, config('validation.add_food'));

            $user_business_link = BusinessLink::where(['userid' => authUser()->userid,])->first();
//        $categoryCheck = Category::where('category_name',$validated_data['category_name')])->exists();
            $food = new Food();
            $food->uid = authUser()->id;
            $food->userid = authUser()->userid;
            $food->business_link = $user_business_link->business_link;
            $food->title = $validated_data['title'];
            $food->category_id = $validated_data['category_id'];
            $food->description = $validated_data['description'];
            $food->price = $validated_data['price'];
            $food->status = true;
            $food->save();
            return success('Food Created Successfully. ', $food, 200);

        }

        /**
         * Update the Authenticated User profile.
         *
         * @return \Illuminate\Http\JsonResponse
         * @throws \Illuminate\Validation\ValidationException
         */
        public function showFood(Request $request, $id)
        {
            $food = Food::find($id);
            return success('Food information: ', $food, 200);
        }

        /**
         * Update the Authenticated User profile.
         *
         * @return \Illuminate\Http\JsonResponse
         * @throws \Illuminate\Validation\ValidationException
         */
        public function editFood(Request $request, $id)
        {
            $food = Food::find($id);
            return success('Food information: ', $food, 200);
        }

        /**
         * Update the Authenticated User profile.
         *
         * @return \Illuminate\Http\JsonResponse
         * @throws \Illuminate\Validation\ValidationException
         */
        public function updateFood(Request $request, $id)
        {
            $food = Food::find($id);
            $food->uid = authUser()->id;
            if (authUser()->role == 'admin') {
                $food->userid = 'ADMIN001';
            }
            if (authUser()->role == 'vendor') {
                $food->userid = authUser()->userid;
            }
            $food->title = $validated_data['title'];
            $food->category_id = $validated_data['category_id'];
            $food->description = $validated_data['description'];
            $food->price = $validated_data['price'];
            $food->status = true;
            $food->save();
            return success('Food information updated.', $food, 200);
        }

        /**
         * Update the Authenticated User profile.
         *
         * @return \Illuminate\Http\JsonResponse
         * @throws \Illuminate\Validation\ValidationException
         */
        public function deleteFood($id)
        {
            $food = Food::find($id);
            $food->delete();
            return success('Food information deleted.', $food, 200);
        }


    }
