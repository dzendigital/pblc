<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\Menu;
use App\Models\Page;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        $items = array(
            array(
                "parent_id" => 0,
                "title" => "Политика конфиденциальности",
                "slug" => "policy",
                "is_visible" => 1,
                "is_onmenu" => null,
                "is_managable" => 1,
                "is_deletable" => null,
            ),
        );

        foreach ( $items as $key => $value ) {
            $item = new Menu();
            $item->slug = Str::slug($value["title"], '-');
            $item->title = $value["title"];
            $item->slug = $value["slug"];
            $item->parent_id = $value["parent_id"];
            $item->is_visible = $value["is_visible"];
            $item->is_deletable = $value["is_deletable"];
            $item->is_onmenu = $value["is_onmenu"];
            $item->is_managable = $value["is_managable"];

            $item->sort = $key;

            $result = $item->save();

            if( $result ){
                # создание страницы, привязка к пункту меню и сохранение
                $page = new Page();
                $page->meta_title = $item->title;

                if ( $item->slug == "policy" ) 
                {
                    $page->body = view('client/pages/policy/index')->render();
                }
                $item->pages()->save($page);
            }
        }  
    }
}
