<?php 
namespace App\Repositories\Panel;

use App\Interfaces\Panel\SpecialistDocumentInterface;

use App\Models\Specialist\Item as Specialist;


class SpecialistDocumentRepository implements SpecialistDocumentInterface 
{
    public function all()
    {
        $items = array();
        $items = Specialist::with(["document"])->orderBy('created_at', 'DESC')->get();
        return $items;
    }
    public function byId($id)
    {
        $items = array();
        $items = Specialist::where('id', $id)->first();
        return $items;
    }
    
}
?>