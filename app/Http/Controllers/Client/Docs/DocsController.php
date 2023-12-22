<?php

namespace App\Http\Controllers\Client\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\ServeyRepository as ItemRepository;

# работа с файлами
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Servey\Anketa;
use App\Models\File\Document;

class DocsController extends Controller
{
    private ItemRepository $itemRepository;

    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }  
    public function index()
    {
        $response = [
            'items' => $this->itemRepository->all(),
        ];

        return view("panel/docs/index", $response);
    }
    public function storage($id)
    {
        $document = Document::where("id", $id)->with(['anketa'])->first();
        $data = [
            "document" => $document->toArray(),
            "anketa" => $document->anketa->first()->toArray(),
        ];
        $response = [
            'data' => $data
        ];
        return view("docs/acceleration-contract", $response);
    }
}
