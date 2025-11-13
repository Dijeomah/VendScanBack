<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Item;
use App\Models\User;
use App\Models\BusinessLink;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the vendor user
        $vendor = User::where('userid', 'YLROCZ')->first();
        
        if (!$vendor) {
            $this->command->error('Vendor with userid YLROCZ not found!');
            return;
        }

        // Get the business
        $business = BusinessLink::where('business_link', 'airvend3')->first();
        
        if (!$business) {
            $this->command->error('Business with business_link airvend3 not found!');
            return;
        }

        $this->command->info('Creating menu for Airvend3...');

        // Create Categories
        $categories = [
            [
                'user_id' => $vendor->id,
                'category_name' => 'Appetizers',
                'category_code' => $vendor->id . '-appetizers',
            ],
            [
                'user_id' => $vendor->id,
                'category_name' => 'Main Courses',
                'category_code' => $vendor->id . '-main-courses',
            ],
            [
                'user_id' => $vendor->id,
                'category_name' => 'Desserts',
                'category_code' => $vendor->id . '-desserts',
            ],
            [
                'user_id' => $vendor->id,
                'category_name' => 'Beverages',
                'category_code' => $vendor->id . '-beverages',
            ],
            [
                'user_id' => $vendor->id,
                'category_name' => 'Salads',
                'category_code' => $vendor->id . '-salads',
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $category = Category::firstOrCreate(
                ['category_code' => $categoryData['category_code']],
                $categoryData
            );
            $createdCategories[$category->category_name] = $category;
            $this->command->info("Created category: {$category->category_name}");
        }

        // Create Subcategories
        $subcategories = [
            ['category' => 'Appetizers', 'name' => 'Fried'],
            ['category' => 'Appetizers', 'name' => 'Grilled'],
            ['category' => 'Main Courses', 'name' => 'Pasta'],
            ['category' => 'Main Courses', 'name' => 'Grilled Meats'],
            ['category' => 'Main Courses', 'name' => 'Seafood'],
            ['category' => 'Desserts', 'name' => 'Ice Cream'],
            ['category' => 'Desserts', 'name' => 'Cakes'],
            ['category' => 'Beverages', 'name' => 'Soft Drinks'],
            ['category' => 'Beverages', 'name' => 'Juices'],
            ['category' => 'Beverages', 'name' => 'Hot Drinks'],
            ['category' => 'Salads', 'name' => 'Caesar'],
            ['category' => 'Salads', 'name' => 'Green'],
        ];

        $createdSubcategories = [];
        foreach ($subcategories as $subData) {
            $category = $createdCategories[$subData['category']];
            $subcategory = SubCategory::create([
                'user_id' => $vendor->id,
                'category_id' => $category->id,
                'sub_category_name' => $subData['name'],
            ]);
            $createdSubcategories["{$subData['category']}-{$subData['name']}"] = $subcategory;
            $this->command->info("Created subcategory: {$subcategory->sub_category_name}");
        }

        // Create Menu Items
        $items = [
            // Appetizers - Fried
            [
                'title' => 'Chicken Wings',
                'description' => 'Crispy buffalo chicken wings served with blue cheese dip',
                'price' => 8.99,
                'category' => 'Appetizers',
                'subcategory' => 'Fried'
            ],
            [
                'title' => 'Mozzarella Sticks',
                'description' => 'Golden fried mozzarella sticks with marinara sauce',
                'price' => 6.99,
                'category' => 'Appetizers',
                'subcategory' => 'Fried'
            ],
            [
                'title' => 'Onion Rings',
                'description' => 'Crispy beer-battered onion rings',
                'price' => 5.99,
                'category' => 'Appetizers',
                'subcategory' => 'Fried'
            ],
            // Appetizers - Grilled
            [
                'title' => 'Bruschetta',
                'description' => 'Grilled bread with tomatoes, garlic, and basil',
                'price' => 7.99,
                'category' => 'Appetizers',
                'subcategory' => 'Grilled'
            ],
            // Main Courses - Pasta
            [
                'title' => 'Spaghetti Carbonara',
                'description' => 'Classic Italian pasta with bacon, eggs, and parmesan cheese',
                'price' => 14.99,
                'category' => 'Main Courses',
                'subcategory' => 'Pasta'
            ],
            [
                'title' => 'Fettuccine Alfredo',
                'description' => 'Creamy pasta with parmesan cheese sauce',
                'price' => 13.99,
                'category' => 'Main Courses',
                'subcategory' => 'Pasta'
            ],
            [
                'title' => 'Penne Arrabbiata',
                'description' => 'Penne pasta in spicy tomato sauce',
                'price' => 12.99,
                'category' => 'Main Courses',
                'subcategory' => 'Pasta'
            ],
            // Main Courses - Grilled Meats
            [
                'title' => 'Grilled Ribeye Steak',
                'description' => '12oz ribeye steak grilled to perfection, served with vegetables',
                'price' => 24.99,
                'category' => 'Main Courses',
                'subcategory' => 'Grilled Meats'
            ],
            [
                'title' => 'BBQ Chicken',
                'description' => 'Half chicken with BBQ sauce, served with fries',
                'price' => 16.99,
                'category' => 'Main Courses',
                'subcategory' => 'Grilled Meats'
            ],
            [
                'title' => 'Lamb Chops',
                'description' => 'Grilled lamb chops with mint sauce',
                'price' => 22.99,
                'category' => 'Main Courses',
                'subcategory' => 'Grilled Meats'
            ],
            // Main Courses - Seafood
            [
                'title' => 'Grilled Salmon',
                'description' => 'Fresh Atlantic salmon with lemon butter sauce',
                'price' => 19.99,
                'category' => 'Main Courses',
                'subcategory' => 'Seafood'
            ],
            [
                'title' => 'Shrimp Scampi',
                'description' => 'Garlic butter shrimp over linguine',
                'price' => 18.99,
                'category' => 'Main Courses',
                'subcategory' => 'Seafood'
            ],
            // Desserts - Ice Cream
            [
                'title' => 'Vanilla Ice Cream',
                'description' => 'Premium vanilla ice cream with chocolate sauce',
                'price' => 4.99,
                'category' => 'Desserts',
                'subcategory' => 'Ice Cream'
            ],
            [
                'title' => 'Chocolate Sundae',
                'description' => 'Chocolate ice cream with whipped cream and cherry',
                'price' => 5.99,
                'category' => 'Desserts',
                'subcategory' => 'Ice Cream'
            ],
            // Desserts - Cakes
            [
                'title' => 'Chocolate Cake',
                'description' => 'Rich chocolate layer cake',
                'price' => 6.99,
                'category' => 'Desserts',
                'subcategory' => 'Cakes'
            ],
            [
                'title' => 'Cheesecake',
                'description' => 'New York style cheesecake with berry compote',
                'price' => 7.99,
                'category' => 'Desserts',
                'subcategory' => 'Cakes'
            ],
            // Beverages - Soft Drinks
            [
                'title' => 'Coca Cola',
                'description' => 'Classic Coca Cola',
                'price' => 2.99,
                'category' => 'Beverages',
                'subcategory' => 'Soft Drinks'
            ],
            [
                'title' => 'Sprite',
                'description' => 'Lemon-lime soda',
                'price' => 2.99,
                'category' => 'Beverages',
                'subcategory' => 'Soft Drinks'
            ],
            // Beverages - Juices
            [
                'title' => 'Orange Juice',
                'description' => 'Freshly squeezed orange juice',
                'price' => 3.99,
                'category' => 'Beverages',
                'subcategory' => 'Juices'
            ],
            [
                'title' => 'Apple Juice',
                'description' => 'Fresh apple juice',
                'price' => 3.99,
                'category' => 'Beverages',
                'subcategory' => 'Juices'
            ],
            // Beverages - Hot Drinks
            [
                'title' => 'Coffee',
                'description' => 'Freshly brewed coffee',
                'price' => 2.49,
                'category' => 'Beverages',
                'subcategory' => 'Hot Drinks'
            ],
            [
                'title' => 'Cappuccino',
                'description' => 'Italian cappuccino',
                'price' => 3.99,
                'category' => 'Beverages',
                'subcategory' => 'Hot Drinks'
            ],
            // Salads - Caesar
            [
                'title' => 'Caesar Salad',
                'description' => 'Classic Caesar salad with croutons and parmesan',
                'price' => 8.99,
                'category' => 'Salads',
                'subcategory' => 'Caesar'
            ],
            [
                'title' => 'Chicken Caesar Salad',
                'description' => 'Caesar salad with grilled chicken',
                'price' => 11.99,
                'category' => 'Salads',
                'subcategory' => 'Caesar'
            ],
            // Salads - Green
            [
                'title' => 'Garden Salad',
                'description' => 'Mixed greens with vegetables and vinaigrette',
                'price' => 7.99,
                'category' => 'Salads',
                'subcategory' => 'Green'
            ],
            [
                'title' => 'Greek Salad',
                'description' => 'Tomatoes, cucumbers, olives, and feta cheese',
                'price' => 9.99,
                'category' => 'Salads',
                'subcategory' => 'Green'
            ],
        ];

        foreach ($items as $itemData) {
            $category = $createdCategories[$itemData['category']];
            $subcategory = $createdSubcategories["{$itemData['category']}-{$itemData['subcategory']}"];

            Item::create([
                'uid' => $vendor->id,
                'userid' => $vendor->userid,
                'business_link' => $business->business_link,
                'title' => $itemData['title'],
                'description' => $itemData['description'],
                'price' => $itemData['price'],
                'category_id' => $category->id,
                'sub_category_id' => $subcategory->id,
                'status' => true,
            ]);

            $this->command->info("Created item: {$itemData['title']} - \${$itemData['price']}");
        }

        $this->command->info("\nMenu seeding completed successfully!");
        $this->command->info("Total Categories: " . count($createdCategories));
        $this->command->info("Total Subcategories: " . count($createdSubcategories));
        $this->command->info("Total Items: " . count($items));
    }
}
