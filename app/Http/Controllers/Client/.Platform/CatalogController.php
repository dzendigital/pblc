<?php

namespace App\Http\Controllers\Client\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\SpecialistRepository;
use App\Models\Baseauto\Item as Item;
use App\Models\Map\Region;

class CatalogController extends Controller
{

    private SpecialistRepository $itemRepository;

    public function __construct(SpecialistRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }
    /**
     * Display a listing of the resource.
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $where = array(); 
        $whereOr = array();
        $url_raw = array();
        if ( $request->filled('city') ) {
            $where[] = array("city", "=", $request->input('city')); 
            $url_raw[] = "city=" . $request->input('city');
        }
        if ( $request->filled('region') ) {
            $where[] = array("region", "=", $request->input('region')); 
            $url_raw[] = "region=" . $request->input('region');
        }
        if ( !is_null($request->input('is_soleproprietor')) ) {
            $where[] = array("is_soleproprietor", "=", $request->input('is_soleproprietor')); 
            $url_raw[] = "is_soleproprietor=" . $request->input('is_soleproprietor');
        }
        if ( !is_null($request->input('is_organization')) ) {
            $where[] = array("is_organization", "=", $request->input('is_organization')); 
            $url_raw[] = "is_organization=" . $request->input('is_organization');
        }

        $order = null;
        if ( $request->filled('sort') ) {
            if ( $request->input('sort') == "По цене" ) {
                $order = "`price` ASC";
            }else if ( $request->input('sort') == "По рейтингу" ) {
                $order = "`rating` ASC";
            }
        }
       
        $response = $this->itemRepository->urlForFilterPagination($url_raw, $request);
        
        $response["region"] = Region::orderBy("title")->get();
        $response["region_selected"] = null;
        $response["request"] = $request->all();

        // $response['items'] = $this->itemRepository->paginate();
        $response['items'] = $this->itemRepository->where($where, $whereOr, $order);

        $response["template"]["render"] = view( "client/platform/catalog/paginated", $response)->render();

        return view( "client/platform/catalog/index", $response );
    }

    /**
     *
     * render filtered results
     *
     */
    public function filter(Request $request)
    {
        $where = array(); 
        $whereOr = array(); 
        if ( $request->filled('city') ) {
            $where[] = array("city", "=", $request->input('city')); 
        }
        if ( $request->filled('region') ) {
            $where[] = array("region", "=", $request->input('region')); 
        }
        if ( !is_null($request->input('is_soleproprietor')) ) {
            $where[] = array("is_soleproprietor", "=", $request->input('is_soleproprietor')); 
            // if ( !is_null($request->input('is_organization')) ) {
            //    $where[] = array("is_organization", "=", $request->input('is_organization')); 
            // }
        }
        if ( !is_null($request->input('is_organization')) ) {
            $where[] = array("is_organization", "=", $request->input('is_organization')); 
            // if ( !is_null($request->input('is_soleproprietor')) ) {
            //    $where[] = array("is_soleproprietor", "=", $request->input('is_soleproprietor')); 
            // }
        }


        $order = null;
        if ( $request->filled('sort') ) {
            if ( $request->input('sort') == "По цене" ) {
                $order = "`price` ASC";
            }else if ( $request->input('sort') == "По рейтингу" ) {
                $order = "`rating` ASC";
            }
        }
     
        $response = array(
            "items" => $this->itemRepository->where($where, $whereOr, $order),
        ); 
        $response["template"]["render"] = view( "client/platform/catalog/paginated", $response)->render();
        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $where = array(); 
        $whereOr = array(); 
        $url_raw = array();
        if ( $request->filled('city') ) {
            $where[] = array("city", "=", $request->input('city')); 
            $url_raw[] = "city=" . $request->input('city');
        }
        if ( $request->filled('region') ) {
            $where[] = array("region", "=", $request->input('region')); 
            $url_raw[] = "region=" . $request->input('region');
        }
        if ( !is_null($request->input('is_soleproprietor')) ) {
            $where[] = array("is_soleproprietor", "=", $request->input('is_soleproprietor')); 
            $url_raw[] = "is_soleproprietor=" . $request->input('is_soleproprietor');
        }
        if ( !is_null($request->input('is_organization')) ) {
            $where[] = array("is_organization", "=", $request->input('is_organization')); 
            $url_raw[] = "is_organization=" . $request->input('is_organization');
        }


        $order = null;
        if ( $request->filled('sort') ) {
            if ( $request->input('sort') == "По цене" ) {
                $order = "`price` ASC";
            }else if ( $request->input('sort') == "По рейтингу" ) {
                $order = "`rating` ASC";
            }
        }
     
        $response = $this->itemRepository->urlForFilterPagination($url_raw, $request);
        $response['items'] = $this->itemRepository->where($where, $whereOr, $order);


        $response["template"]["render"] = view( "client/platform/catalog/paginated", $response)->render();
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        $item = $this->itemRepository->find($slug);
        
        $meta = array(
            'meta_title' => $item['meta_title'],
            'meta_description' => $item['meta_description'],
            'meta_keywords' => $item['meta_keywords'],
            'meta_h1' => $item['meta_h1'],
        );
        $response = array(
            'item' => $item,
            'meta' => $meta,
        );
        # dd(__METHOD__, $response);
        return view('client/platform/catalog/show', $response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        dd(__METHOD__, $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        dd(__METHOD__, 123);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
