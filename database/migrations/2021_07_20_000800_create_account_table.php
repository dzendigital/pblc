<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255); # наименование 
            $table->string('slug', 255)->unique(); # slug

            $table->foreignId('user_id')->nullable();
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

            $table->integer('is_visible')->nullable();
            $table->integer('sort')->nullable(); # сортировка

            $table->timestamps();
            $table->softDeletes();
        });
        
        # таблица содержит отношения belongsToMany 
        Schema::create('gallery_account', function (Blueprint $table) {
            $table->unsignedBigInteger('gallery_id');
            $table->unsignedBigInteger('item_id');
        
            $table->foreign('gallery_id')->references('id')->on('gallery')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('account')->onDelete('cascade');
        
            $table->primary(['gallery_id', 'item_id']);

        }); 

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_account');
        Schema::dropIfExists('report_payment');
        Schema::dropIfExists('account');
        Schema::dropIfExists('account_gallery');
    }
}
