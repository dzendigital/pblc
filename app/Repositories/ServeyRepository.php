<?php 
namespace App\Repositories;

use App\Interfaces\ServeyInterface;
use App\Models\Servey\Anketa;
use App\Models\File\Document;

class ServeyRepository implements ServeyInterface 
{
    public function all()
    {
        $items = Anketa::with(["document"])->get();        
        return $items;
    }
    public function storagelink($id)
    {
        $item = Document::where("id", $id)->first();
        $link = [
            '_web' => url('/') . "/" .$item->src,
            'web' => url('/') . "/storage/" . $item->id,
            'pdf' => url('/') . "/" .$item->url
        ];
        return $link;
    }
    public function save($validated)
    {
        $item = array();
        $item = new Anketa($validated);
        $is_save = $item->save();
        
        return $item->id;
    }
}
?>