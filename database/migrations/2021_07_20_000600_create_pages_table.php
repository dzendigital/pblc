<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* информация о мета содержании объектов: страниц, блогов, пользователей */
        Schema::create('meta', function (Blueprint $table) {
            $table->id();
            
            $table->string('meta_title', 255)->nullable(); 
            $table->string('meta_description', 255)->nullable(); 
            $table->string('meta_keywords', 255)->nullable(); 
            $table->string('meta_canonical', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
        /* таблица меню контент страниц */
        Schema::create('pages_menu', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id'); # родительский элемент этой же таблицы
            $table->string('title', 255); # название
            $table->string('slug', 255); # slug
            $table->integer('sort')->nullable();; # сортировка
            $table->integer('is_visible')->nullable(); # страница доступна (или 404)
            $table->integer('is_onmenu')->nullable(); # ссылка на страницу отображается в меню
            $table->integer('is_managable')->nullable(); # можно изменить страницу
            $table->integer('is_deletable')->nullable(); # можно удлить страницу

            $table->timestamps();
            $table->softDeletes();
        });

        /* таблица контент страниц */
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_id');
            $table->foreign('menu_id')
                    ->references('id')
                    ->on('pages_menu')
                    ->onDelete('cascade'); # категория: меню контент страниц

            $table->text("body")->nullable(); # описание из редактора
            
            $table->integer('is_visible')->nullable(); # показатель видимости
            $table->integer('is_able_to_manage')->nullable(); # является ли страница управляемой

            $table->string('meta_title', 255)->nullable(); # meta: title 
            $table->string('meta_description', 255)->nullable(); # meta: description 
            $table->string('meta_keywords', 255)->nullable(); # meta: keywords 
            $table->string('meta_canonical', 255)->nullable(); # ссылка на canonical страницу 

            $table->timestamps();
            $table->softDeletes();
        });


        /* таблица меню контент страниц */
        Schema::create('pages_meta', function (Blueprint $table) 
        {
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('meta_id');
        
            $table->foreign('item_id')->references('id')->on('pages')->onDelete('cascade');
            $table->foreign('meta_id')->references('id')->on('meta')->onDelete('cascade');
        
            $table->primary(['item_id', 'meta_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
        Schema::dropIfExists('pages_menu');
        Schema::dropIfExists('pages_meta');
    }
}
