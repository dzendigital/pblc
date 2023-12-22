<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # категория
        Schema::create("blog_category", function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('slug', 255)->nullable();

            $table->integer('parent_id')->default('0'); # родительский элемент этой же таблицы

            $table->integer('is_visible')->nullable(); # видимость
            $table->integer('sort')->nullable(); # сортировка

            $table->timestamps();
            $table->softDeletes();
        }); 
        # тег
        Schema::create("tag", function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('slug', 255)->nullable();

            $table->integer('is_visible')->nullable(); # видимость
            $table->integer('sort')->nullable(); # сортировка

            $table->timestamps();
            $table->softDeletes();
        }); 

        # основная таблица содержащая информацию о каталоге
        Schema::create("blog", function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable(); # наименование 
            $table->string('slug', 255)->nullable(); # slug

            # краткое и полное описание
            $table->longText("body_short")->nullable(); 
            $table->longText("body_long")->nullable(); 

            $table->foreignId('account_id')->nullable();
            $table->foreign('account_id')
                    ->references('id')
                    ->on('account')
                    ->onDelete('cascade'); # пользователь

            $table->foreignId('category_id')->nullable();
            $table->foreign('category_id')
                    ->references('id')
                    ->on('blog')
                    ->onDelete('cascade'); # категория

            $table->integer('is_visible')->nullable(); # видимость
            $table->integer('is_approve')->nullable(); # одобрение админа
            $table->integer('is_slider')->nullable(); # создавать слайдер в статье
            $table->integer('sort')->nullable(); # сортировка

            $table->timestamp('published_at');

            $table->timestamps();
            $table->softDeletes();
        }); # основной объект

        /* теги записи */
        Schema::create('blog_tag', function (Blueprint $table) 
        {
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('tag_id');
        
            $table->foreign('item_id')->references('id')->on('blog')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tag')->onDelete('cascade');
        
            $table->primary(['item_id', 'tag_id']);

        });
        /* таблица меню контент страниц */
        Schema::create('blog_meta', function (Blueprint $table) 
        {
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('meta_id');
        
            $table->foreign('item_id')->references('id')->on('blog')->onDelete('cascade');
            $table->foreign('meta_id')->references('id')->on('meta')->onDelete('cascade');
        
            $table->primary(['item_id', 'meta_id']);

        });
        # таблица содержит отношения между directions и transport в случае когда отношения belongsToMany 
        Schema::create('blog_gallery', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('gallery_id');
        
            $table->foreign('item_id')->references('id')->on('blog')->onDelete('cascade');
            $table->foreign('gallery_id')->references('id')->on('gallery')->onDelete('cascade');
        
            $table->primary(['item_id', 'gallery_id']);

        }); 
        # таблица содержит отношения между directions и transport в случае когда отношения belongsToMany 
        Schema::create('blog_video', function (Blueprint $table) {
            $table->unsignedBigInteger('blog_id');
            $table->unsignedBigInteger('video_id');
        
            $table->foreign('blog_id')->references('id')->on('blog')->onDelete('cascade');
            $table->foreign('video_id')->references('id')->on('video')->onDelete('cascade');

            $table->primary(['blog_id', 'video_id']);
        }); 

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        # сначала дропаем таблицы по foreign_id, потом основную
        
        Schema::dropIfExists('blog_category');
        Schema::dropIfExists('blog_tag');
        Schema::dropIfExists('blog');
        Schema::dropIfExists('blog_meta');
        Schema::dropIfExists('blog_tag');
        Schema::dropIfExists('blog_gallery');
        Schema::dropIfExists('blog_video');
        
    }
}
