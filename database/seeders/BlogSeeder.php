<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use App\Models\Page;

use App\Models\Blog\Category;
use App\Models\Blog\Item;
use App\Models\Blog\Tag;

use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = array(
            array(
                "title" => "Глубоководная культура",
                "slug" => "",
                "parent_id" => 0,
                "is_visible" => 1,
                "sort" => "",
            ),
            array(
                "title" => "Периодическое затопление",
                "slug" => "",
                "parent_id" => 0,
                "is_visible" => 1,
                "sort" => "",
            ),
            array(
                "title" => "Капельный полив",
                "slug" => "",
                "parent_id" => 0,
                "is_visible" => 1,
                "sort" => "",
            ),
            array(
                "title" => "Питательный слой",
                "slug" => "",
                "parent_id" => 0,
                "is_visible" => 1,
                "sort" => "",
            ),
            array(
                "title" => "Аэропоника",
                "slug" => "",
                "parent_id" => 0,
                "is_visible" => 1,
                "sort" => "",
            ),
            array(
                "title" => "Фитильная система",
                "slug" => "",
                "parent_id" => 0,
                "is_visible" => 1,
                "sort" => "",
            ),
        );
        foreach ( $category as $key => $value ) {
            $item = new Category();
            $item->title = $value["title"];
            $item->slug = Str::slug($value["title"], '-');
            $item->parent_id = $value["parent_id"];
            $item->is_visible = 1;
            $item->sort = $key;
            $item->save();
        }
        $tag = array(
            array(
                "title" => "Новинка",
                "slug" => "",
                "is_visible" => 1,
                "sort" => "",
            )
        );
        foreach ( $tag as $key => $value ) {
            $item = new Tag();
            $item->title = $value["title"];
            $item->slug = Str::slug($value["title"], '-');
            $item->is_visible = 1;
            $item->sort = $key;
            $item->save();
        }

        # наполнение базы данных информацией 
        # предыдущего сайта
        $path = 'resources/database/blog_category.sql';
        DB::unprepared(file_get_contents($path));
        
        $path = 'resources/database/blog.sql';
        DB::unprepared(file_get_contents($path));
    }
}
