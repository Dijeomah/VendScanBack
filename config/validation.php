<?php

return [
    'registration' => [
        'first_name' => 'required',
        'last_name' => 'required',
        'phone_number' => 'required|unique:users,phone_number',
        'email' => 'required|unique:users,email|max:50',
        'password' => 'required|min:8',
    ],
    'business_info' => [
        'business_name' => 'required|unique:user_data',
        'business_address' => 'required',
        'city_id' => 'required',
        'state_id' => 'required',
        'country_id' => 'required',
    ],
    'set_business_name' => [
        'business_name' => 'required|unique:business_links'
    ],
    'add_food' => [
        'category_id' => 'required',
        'title' => 'required',
        'description' => 'required',
        'price' => 'required',
    ],
    'update_user_info' => [
        'business_name' => 'required|min:4',
        'business_address' => 'required',
        'city_id' => 'required|unique:users,email|',
        'state_id' => 'required|unique:users,phone_number',
        'country_id' => 'required|min:8',
    ],
    'admin_add_food' => [
        'uid' => 'required',
        'userid' => 'required',
        'business_link' => 'required',
        'business_name' => 'required',
        'category_id' => 'required',
        'title' => 'required',
        'description' => 'required',
        'price' => 'required',
    ],
    'admin_set_vendor_media' => [
        'logo_file'=>'sometimes|mimes:jpg,jpeg,png',
        'hero_file'=>'sometimes|mimes:jpg,jpeg,png',
    ],
];
