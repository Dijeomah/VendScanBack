<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->delete();
        $categories = array(
            array('id' => 1, 'user_id' => 1,'category_code'=>'1-Whisky', 'category_name' => "Whisky"),
            array('id' => 2, 'user_id' => 1,'category_code'=>'1-Vodka', 'category_name' => "Vodka"),
            array('id' => 3, 'user_id' => 1,'category_code'=>'1-Wine', 'category_name' => "Wine"),
            array('id' => 4, 'user_id' => 1,'category_code'=>'1-Aperitif', 'category_name' => "Aperitif"),
            array('id' => 5, 'user_id' => 1,'category_code'=>'1-Cocktail', 'category_name' => "Cocktail"),
            array('id' => 6, 'user_id' => 1,'category_code'=>'1-Smokes', 'category_name' => "Smokes"),
            array('id' => 7, 'user_id' => 1,'category_code'=>'1-Beer', 'category_name' => "Beer"),
            array('id' => 8, 'user_id' => 1,'category_code'=>'1-Soft Drinks', 'category_name' => "Soft Drink"),
            array('id' => 9, 'user_id' => 1,'category_code'=>'1-Energy Drinks', 'category_name' => "Energy Drinks"),
            array('id' => 10, 'user_id' => 1,'category_code'=>'1-Brandy', 'category_name' => "Brandy"),
            array('id' => 11, 'user_id' => 1,'category_code'=>'1-Champagne', 'category_name' => "Champagne"),
            array('id' => 12, 'user_id' => 1,'category_code'=>'1-Starters', 'category_name' => "Starters"),
            array('id' => 13, 'user_id' => 1,'category_code'=>'1-Rice', 'category_name' => "Rice"),
            array('id' => 14, 'user_id' => 1,'category_code'=>'1-Salads', 'category_name' => "Salads"),
            array('id' => 15, 'user_id' => 1,'category_code'=>'1-Sides', 'category_name' => "Sides"),
            array('id' => 16, 'user_id' => 1,'category_code'=>'1-Africana', 'category_name' => "Africana"),
            array('id' => 17, 'user_id' => 1,'category_code'=>'1-Soups', 'category_name' => "Soups"),
            array('id' => 18, 'user_id' => 1,'category_code'=>'1-Specials', 'category_name' => "Specials"),
            array('id' => 19, 'user_id' => 1,'category_code'=>'1-Others', 'category_name' => "Others"),
        );
        DB::table('categories')->insert($categories);
    }
}
