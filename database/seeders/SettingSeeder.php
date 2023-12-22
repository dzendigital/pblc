<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

use App\Models\Setting\Item;

class SettingSeeder extends Seeder
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
                "title" => "phone",
                "value" => "88000000000",
                "is_visible" => 1,
            ),
            array(
                "title" => "phone_format",
                "value" => "8 800 000-00-00",
                "is_visible" => 1,
            ),
            array(
                "title" => "email",
                "value" => "info@dzendigital.ru",
                "is_visible" => 1,
            ),
        );

        foreach( $items as $key => $value ){
            $item = new Item($value);
            $item->save();
        }
    }
}
