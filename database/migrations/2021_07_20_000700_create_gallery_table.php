<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalleryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create("gallery", function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('src', 255)->nullable(); # путь до файал для View
            $table->string('url', 255)->nullable(); # путь до файла для Storage
            $table->integer('sort')->nullable(); # порядок сортировки
            $table->timestamps();
            $table->softDeletes();
        }); # карточка товара содержит ссылку на галерею
 
        if (!1) {
            # в остальных миграциях отношения между таблицами нужно определять через belongsToMany 
            Schema::create('gallery_course', function (Blueprint $table) {
                $table->unsignedBigInteger('gallery_id');
                $table->unsignedBigInteger('course_id');
            
                $table->foreign('gallery_id')->references('id')->on('gallery')->onDelete('cascade');
                $table->foreign('course_id')->references('id')->on('catalog_course')->onDelete('cascade');
            
                $table->primary(['gallery_id', 'course_id']);

            }); 
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        # сначала дропаем таблицы по foreign_id, потом основную
        
        Schema::dropIfExists('gallery');
        
    }
}
