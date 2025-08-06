<?php

namespace Database\Seeders;

use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subcategories = [
            ['sub_category_name' => 'Appetizers', 'category_id' => 1, 'user_id' => 1],
            ['sub_category_name' => 'Main Courses', 'category_id' => 1, 'user_id' => 1],
            ['sub_category_name' => 'Desserts', 'category_id' => 1, 'user_id' => 1],
            ['sub_category_name' => 'Soft Drinks', 'category_id' => 2, 'user_id' => 1],
            ['sub_category_name' => 'Alcoholic Beverages', 'category_id' => 2, 'user_id' => 1],
            ['sub_category_name' => 'Breakfast', 'category_id' => 3, 'user_id' => 1],
            ['sub_category_name' => 'Lunch', 'category_id' => 3, 'user_id' => 1],
            ['sub_category_name' => 'Dinner', 'category_id' => 3, 'user_id' => 1],
        ];

        foreach ($subcategories as $subcategory) {
            SubCategory::create($subcategory);
        }
    }
}