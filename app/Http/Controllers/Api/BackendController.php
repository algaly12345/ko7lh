<?php

namespace App\Http\Controllers\Api;

use App\BookingTime;
use App\Category;
use App\Company;
use App\Http\Controllers\Controller;
use App\VendorPage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BackendController extends Controller
{
    public function __construct()
    {
        parent::__construct();

    }
    public function index(Request  $request)
    {
        $couponData = json_decode(request()->cookie('couponData'), true);
        if ($couponData) {
            setcookie("couponData", "", time() - 3600);
        }

        if ($request->ajax())
        {
            /* LOCATION */
            $location_id = $request->location_id;

            /* CATRGORIES */
            $categories = Category::active()->withoutGlobalScope('company')
                ->activeCompanyService()
                ->with(['services' => function ($query)  use($location_id) {
                    $query->active()->withoutGlobalScope('company')->where('location_id', $location_id);
                }])
                ->withCount(['services' => function ($query) use($location_id) {
                    $query->withoutGlobalScope('company')->where('location_id', $location_id);
                }]);

            $total_categories_count = $categories->count();
            $categories = $categories->take(8)->get();


            /* DEALS */
            $deals = Deal::withoutGlobalScope('company')
                ->active()
                ->activeCompany()
                ->with(['location', 'services', 'company'=> function($q) { $q->withoutGlobalScope('company'); } ])



                ->where('location_id', $location_id);

            $total_deals_count = $deals->count();
            $deals = $deals->take(10)->get();

            $spotlight = Spotlight::with(['deal', 'company'=> function($q) { $q->withoutGlobalScope('company'); } ])
                ->activeCompany()
                ->whereHas('deal', function($q) use($location_id){
                    $q->whereHas('location', function ($q) use($location_id) {
                        $q->where('location_id', $location_id);
                    });
                })
                ->orderBy('sequence', 'asc')->get();

            return Reply::dataOnly(['categories' => $categories, 'total_categories_count' => $total_categories_count, 'deals' => $deals, 'total_deals_count' => $total_deals_count, 'spotlight' => $spotlight]);
        }

        /* COUPON */



        return response()->json($this->data);



//        $this->sliderContents = Media::all();

        //return view('front.index', $this->data);
    }


    public function getCategories(Request  $request)
    {

            $categories = Category::active()->withoutGlobalScope('company')
                ->activeCompanyService()
                ->get();


        return response()->json($categories);


    }



    public function vendorPage(Request $request)
    {
        $this->company = Company::withoutGlobalScope('company')->whereSlug($request->slug)
            ->active()->verified()->firstOrFail();
        $this->vendorPage = VendorPage::withoutGlobalScope('company')->where('company_id',$this->company->id)->first();
        $this->bookingTimes = BookingTime::withoutGlobalScope('company')->where('company_id',$this->company->id)->get();
        $this->categories = Category::withoutGlobalScope('company')->has('services', '>', 0)->withCount(['services' => function($q) {
            $q->withoutGlobalScope('company');
        }])
            ->get();
        return response()->json($this->data);
        //return view('front.vendor', $this->data);
    }



    public function AllServices(Request $request)
    {

        if($request->ajax())
        {
            $services = BusinessService::withoutGlobalScope('company')
                ->activeCompany()
                ->with([
                    'location' => function($q) { $q->withoutGlobalScope('company'); } ,
                    'category' => function($q) { $q->withoutGlobalScope('company'); } ,
                    'company' => function($q) { $q->withoutGlobalScope('company'); }
                ])->active();

            if(!is_null($request->service_name)) {
                $services = $services->where('name', 'like', '%'.$request->service_name.'%');
            }

            if(is_null($request->company_id) && !is_null($request->term)) {
                $services = $services->where('name', 'like', '%'.$request->term.'%');
            }

            if(!is_null($request->company_id)) {
                $company_id = $request->company_id;
                $services = $services->whereHas('company', function($q) use($company_id){
                    $q->where('id', $company_id);
                });
            }

            if(!is_null($request->locations)) {
                $locations = explode(",",$request->locations);
                $services->whereIn('location_id', $locations);
            }

            if(!is_null($request->categories)) {
                $categories = explode(",",$request->categories);
                $services->whereIn('category_id', $categories);
            }

            if(!is_null($request->companies)) {
                $companies = explode(",",$request->companies);
                $services->whereIn('company_id', $companies);
            }

            if(!is_null($request->price)) {
                $prices = $request->price;

                $firstPrice = explode('-', array_shift($prices));
                $low = $firstPrice[0];
                $high = $firstPrice[1];

                $priceArr = [];
                foreach ($prices as $price) {
                    $priceArr[] = [
                        explode('-', $price)[0],
                        explode('-', $price)[1],
                    ];
                }

                $services = $services->whereBetween('price', [$low,$high]);

                foreach ($priceArr as $price) {
                    $services = $services->orWhereBetween('price', [$price[0], $price[1]]);
                }
            }

            if(!is_null($request->discounts)) {
                $discounts = $request->discounts;

                $firstDiscount = explode('-', array_shift($discounts));
                $low = $firstDiscount[0];
                $high = $firstDiscount[1];

                $discountArr = [];
                foreach ($discounts as $discount) {
                    $discountArr[] = [
                        explode('-', $discount)[0],
                        explode('-', $discount)[1],
                    ];
                }

                $services = $services->where('discount_type', 'percent')->whereBetween('discount', [$low,$high]);

                foreach ($discountArr as $discount) {
                    $services = $services->where('discount_type', 'percent')->orWhereBetween('discount', [$discount[0], $discount[1]]);
                }
            }

            if(!is_null($request->sort_by)) {
                if($request->sort_by=='newest') {
                    $services->orderBy('id', 'DESC');
                }
                elseif($request->sort_by=='low_to_high') {
                    $services->orderBy('net_price');
                }
                elseif($request->sort_by=='high_to_low') {
                    $services->orderBy('net_price', 'DESC');
                }
            }

            $services = $services->paginate(10);



            //$view = view('front.filtered_services', compact('services'))->render()
            //;
            return response()->json([$services->count(), 'service_total' => $services->total()]);

          //  return Reply::dataOnly(['view' => $view, 'service_count' => $services->count(), 'service_total' => $services->total()]);

        } /* end of ajax */

        $company_id = !is_null($request->company_id) ? $request->company_id : '';

        $category_id = '';
        if($request->category_id && $request->category_id != 'all'){
            $category_id = Category::where('slug', $request->category_id)->first();
            if(!$category_id) {
                abort(404);
            }

            $category_id = $category_id->id;
        }

        $categories = Category::withoutGlobalScope('company')->has('services', '>', 0)->withCount(['services' => function($q) {
            $q->withoutGlobalScope('company');
        }])
            ->get();
        return response()->json($this->categories);
       // return view('front.all_services', compact('categories', 'category_id', 'company_id'));
    }

}
