<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone_number' => '1234567890',
            'userid' => 'testuser',
        ]);

        $this->call(
            [
                CountriesTableSeeder::class,
                StatesTableSeeder::class,
                CitiesTableSeeder::class,
                CategoriesSeeder::class,
                SubCategorySeeder::class,
                ItemSeeder::class
            ]);
    }
}
