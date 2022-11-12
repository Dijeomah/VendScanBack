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
                array('id' => 1,'category_name' =>"Whisky"),
                array('id' => 2,'category_name' =>"Vodka"),
                array('id' => 3,'category_name' =>"Wine"),
                array('id' => 4,'category_name' =>"Aperitif"),
                array('id' => 5,'category_name' =>"Cocktail"),
                array('id' => 6,'category_name' =>"Smokes"),
                array('id' => 7,'category_name' =>"Beer"),
                array('id' => 8,'category_name' =>"Soft Drink"),
                array('id' => 9,'category_name' =>"Energy Drinks"),
                array('id' => 10,'category_name' =>"Brandy"),
                array('id' => 11,'category_name' =>"Champagne"),
                array('id' => 12,'category_name' =>"Starters"),
                array('id' => 13,'category_name' =>"Rice"),
                array('id' => 14,'category_name' =>"Salads"),
                array('id' => 15,'category_name' =>"Sides"),
                array('id' => 16,'category_name' =>"Africana"),
                array('id' => 17,'category_name' =>"Soups"),
                array('id' => 18,'category_name' =>"Specials"),
                array('id' => 19,'category_name' =>"Others"),
            );
            DB::table('categories')->insert($categories);
        }
    }
