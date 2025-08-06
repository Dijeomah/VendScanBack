<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->constrained('users');
            $table->integer('category_id')->constrained('categories');
            $table->integer('sub_category_id')->constrained('sub_categories');
            $table->integer('business_link_id')->constrained('business_links');
            $table->string('userid');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 12,2);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item');
    }
};
