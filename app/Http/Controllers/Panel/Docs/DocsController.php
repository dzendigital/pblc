<?php

namespace App\Http\Controllers\Panel\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Panel\Docs\ServeyRequest;

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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = [
            'items' => $this->itemRepository->all(),
        ];

        return view("panel/docs/index", $response);
    }

    /**
     * Формирует список документов для анкеты (требуется доработка)
     */
    public function listdoc(Request $request, $id)
    {
        $request_raw = $request->all();
        $anketa = Anketa::where("id", $id)->firstOrFail();
        $anketa_array = $anketa->toArray();
        $hash = time();

        # $pdf = PDF::loadView('/docs/acceleration-contract');
        # $pdf_path = "public/docs/{$id}-acceleration-contract.pdf";
        $pdf_path = "";

        # $pdf_save = $pdf->save($pdf_path);

        $response = array(
            'result' => array(
                'status' => 1,
                'path' => $pdf_path
            ),
        );
        return $response;
    }
    /**
     * Формирует файлы документов разного типа
     */
    public function createdoc(Request $request)
    {

        $request_raw = $request->all();
        $anketa = Anketa::where("id", $request->input('_servey'))->firstOrFail();
            
        $anketa_array = $anketa->toArray();

        $hash = time();
        # create pdf
        $data = [
            "id" => "4A-{$anketa_array['id']}",
            "orgname" => $anketa_array['orgname'],
            "personname" => $anketa_array['lastname'] . " " . $anketa_array['firstname'] . " " . $anketa_array['secondname'],
            "personname_signatory" => $anketa_array['lastname'] . " " . mb_substr($anketa_array['firstname'],0,1,'UTF-8') . ". " . mb_substr($anketa_array['secondname'],0,1,'UTF-8') . ".", # подпись
            "signatoryposition" => $anketa_array['signatoryposition'],
            "signatoryreason" => $anketa_array['signatoryreason'],
            "lastname_genetive" => $anketa_array['lastname_genetive'],
            "firstname_genetive" => $anketa_array['firstname_genetive'],
            "secondname_genetive" => $anketa_array['secondname_genetive'],
            "inn" => $anketa_array['inn'],
            "kpp" => $anketa_array['kpp'],
            "ogrnip" => $anketa_array['ogrnip'],
            "bankname" => $anketa_array['bankname'],
            "bankbik" => $anketa_array['bankbik'],
            "bankcor" => $anketa_array['bankcor'],
            "bankcheck" => $anketa_array['bankcheck'],
            "orgadress" => $anketa_array['orgadress'],
            "phone" => $anketa_array['phone'],
            "email" => $anketa_array['email'],
            "created_at" => $anketa_array['created_at'],
        ];
        if ( !in_array(null, [@$data['lastname_genetive'], @$data['firstname_genetive'], @$data['secondname_genetive']]) ) 
        {
            $data["personname_genetive"] = $data['lastname_genetive'] . " " . $data['firstname_genetive'] . " " . $data['secondname_genetive'];
            $data["personname_signatory_genetive"] = $data['lastname_genetive'] . " " . mb_substr($data['firstname_genetive'],0,1,'UTF-8') . ". " . mb_substr($data['secondname_genetive'],0,1,'UTF-8') . ".";
        }
        $request->request->add($data);

        $is_validate = $request->validate([
            "orgname" => "required|min:3",
            "personname" => "required|min:3",
            "personname_signatory" => "required|min:3",
            "signatoryposition" => "required|min:3",
            "signatoryreason" => "required|min:3",
            "inn" => "required|min:3",
            "kpp" => "",
            "ogrnip" => "",
            "bankname" => "required|min:3",
            "bankbik" => "required|min:3",
            "bankcor" => "required|min:3",
            "bankcheck" => "required|min:3",
            "orgadress" => "required|min:3",
            "phone" => "required|min:3",
            "email" => "required|min:3",
            "created_at" => "required|min:3",

            "personname_genetive" => "required|min:3",
            "personname_signatory_genetive" => "required|min:3",
        ], [
                '*.required' => 'Все поля анкеты обязательны для создания документов.',
        ]);


        // dd(__METHOD__, $data);
        $pdf = PDF::loadView('/docs/acceleration-contract', compact('data'));
        // $pdf->setPaper('A4', 'landscape');
        $pdffile_path = "{$anketa_array['id']}-acceleration-contract.pdf";
        # $pdf_path = "storage/app/docs/{$pdffile_path}";
        $pdf_path = "public/docs/{$pdffile_path}";

        $pdf_save = $pdf->save($pdf_path);


        # if document exist
        $is_exist = Document::where("title", "$pdffile_path")->first();
        if ( !is_null($is_exist) ) 
        {
            # обновление, удалим старый документ
            $is_delete = $is_exist->forceDelete();
        }
        # create model
        $document = new Document();
        $document->title = $pdffile_path;
        $document->src = implode("", array_filter(explode("/home/d/dzendiye/dzendiye.beget.tech/public_html/", Storage::path($pdffile_path))));
        $document->url = $pdf_path;

        $is_save = $anketa->document()->save($document);

        # также доступны методы
        # Storage::copy('old/file.jpg', 'new/file.jpg');
        # Storage::move('old/file.jpg', 'new/file.jpg');
        # Storage::delete('path/file.jpg')
        # Storage::files($directory);

        switch ($request_raw['docslug']) {
            case 'accelerationcontract':
                
            break;
            
            default:
                dd(__METHOD__, "docslug not defined.");
            break;
        }


        $response = array(
            'result' => array(
                'status' => 1,
                'link' => $this->itemRepository->storagelink($is_save->id),
                'path' => $pdf_path,
                'items' => $this->itemRepository->all(),
            ),
        );
        return $response;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ServeyRequest $request, $id)
    {
        $validated = $request->validated();

        $item = Anketa::where("id", $id)->firstOrFail();
        $is_update = $item->update($validated);
        // dd(__METHOD__, $is_update, $item);
        
        $response = array(
            'result' => array(
                'status' => 1,
                'items' => $this->itemRepository->all(),
            ),
        );
        
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyall(Request $request, $id)
    {

        if( is_null($request->input('ids')) ){
            $result = Anketa::find($id)->delete();
            $response = array(
                'result' => array(
                    'status' => $result,
                    'items' => $this->itemRepository->all()
                ),
            );
        }else{
            # массовое удаление
            $result = array();
            foreach ( Anketa::whereIn('id', json_decode($request->input('ids'), 1) )->get() as $key => $value ) {
                $result[] = $value->delete();
            }
            $response = array(
                'result' => array(
                    'status' => !in_array(!1, $result),
                    'items' => $this->itemRepository->all()
                ),
            );
        }
        
        return $response;
    }
    public function destroy(Request $request, $id)
    {
        $result = Anketa::find($id)->delete();
        $response = array(
            'result' => array(
                'status' => $result,
                'items' => $this->itemRepository->all()
            ),
        );
        
        return $response;
    }
    public function destroydoc(Request $request, $id)
    {
        $result = Document::find($id)->forceDelete();
        $response = array(
            'result' => array(
                'status' => $result,
                'items' => $this->itemRepository->all()
            ),
        );
        
        return $response;
    }
}
