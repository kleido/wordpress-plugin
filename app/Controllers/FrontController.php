<?php
namespace WPRengine\Controllers;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Herbert\Framework\Models\Option;
use Herbert\Framework\Models\Post;
use WPRengine\Helper;
use Herbert\Framework\Http;

class FrontController
{

    private $timeout = 180;

    private $auth, $version, $googleKey, $company;

    private $client;

    public function __construct()
    {   
        $version  = Option::where('option_name', 'wpRengineApiVersion')->first();
        if (($version->option_value == 'v1')) {
            $base_uri = 'https://secure.reservationengine.net/rest/api/';
            $apiVersion = 'v1';
        } else {
             // $base_uri = 'https://979183d6.ngrok.io/workadu/public/api/';
            $base_uri = 'https://app.workadu.com/api/'; 
            // $base_uri = 'http://stage.reservationengine.net/api/';  
            $apiVersion = 'v2';
        }
        $this->setAuth();
        $this->version = $apiVersion;
        
        $key = Option::where('option_name', 'wpRengineMapsApiKey')->first();
        if ((!$key)) {
            $key = 'AIzaSyCH2aMLtEaJUC81UK4M8H_Vb4ePt8iti9c';
        } else {
            $key = $key->option_value;
        }

        $this->googleKey = $key; 

        $this->client = new Client([
            'base_uri' => $base_uri,
            'headers'  => ['Accept' => 'application/vnd.rengine.'.$apiVersion.'+json'],
            'timeout'  => $this->timeout,
            'auth'     => $this->auth,
        ]);
        if (($this->auth) && (!$this->company)) {
            try {
                 $response   = $this->client->request('GET', 'companies?sdfasdf123123');
                 $this->company            = json_decode($response->getBody());
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $this->company = false; 
            }
        }
        add_action('wp_enqueue_scripts', [ & $this, 'enqueueAssets']);

    }

    public function enqueueAssets()
    {
        // wp_register_style('prefix_bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
        // wp_enqueue_style('prefix_bootstrap');
        // wp_register_script('prefix_bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
        // wp_enqueue_script('prefix_bootstrap-js');
    }

    private function setAuth()
    {
        $wpRengineUser     = Option::where('option_name', 'wpRengineUser')->first();
        $wpRenginePassword = Option::where('option_name', 'wpRenginePassword')->first();
        if (($wpRengineUser) && ($wpRenginePassword)) {
            if (strpos($wpRengineUser, 'wk_') !== false) {
                $this->auth        = [$wpRengineUser->option_value];
            } else {
                 $this->auth        = false;
            }
        }  else {
            $wpRengineUser               = new Option();
            $wpRengineUser->option_name  = 'wpRengineUser';
            $wpRengineUser->option_value = 'wk_QBMV5c5d5440f0b14' ;
            $wpRengineUser->save();

            $wpRenginePassword                = new Option();
            $wpRenginePassword->option_name  = 'wpRenginePassword ';
            $wpRenginePassword->option_value = '' ;
            $wpRenginePassword->save();
            $this->auth        = [$wpRengineUser->option_value];
        } 
        

    }

    public function showWidgetForm($services ='', $type = 'rentals', $locations = true, $form='widget', $background='ffffff', $buttonBackground='0060ff', $buttonColor='ffffff', $buttonBorderColor='ffffff', $buttonText="Book now?", $greetingText='Hi how can i help you?', $locale='en_GB', $question=true)   {
        if ($this->company ) {
            $company          =  $this->company;
            $proffesion          = $company->data[0]->proffesion;
            $affiliate      = $company->data[0]->desks[0]->email;
            $resultsPage    = 'https://'.$company->data[0]->alias.'.workadu.com/search/services/s';
            $googlemapskey  = $this->googleKey;

            $view = '@WPRengine/front/widget.twig';

            return view($view, [
                'affiliate' => $affiliate,
                'resultsPage' => $resultsPage,
                'googlemapskey' => $googlemapskey,
                'type' => $type, 
                'locations' => $locations,
                'form' => $form, 
                'background' => $background,
                'buttonBackground' => $buttonBackground,
                'buttonColor' => $buttonColor,
                'buttonBorderColor' => $buttonBorderColor,
                'buttonText' => $buttonText,
                'greetingText' => $greetingText,
                'locale' =>$locale,
                'question' => $question
            ]);
        }
    }
  
    public function showReservationForm($services ='', $group = '', $locations = '', $type = '')   {
        if ($this->company ) {
            $company          =  $this->company;
            $proffesion          = $company->data[0]->proffesion;
           
            $parkings       = Option::where('option_name', 'wpRengineParkings')->first();
            // dd('unable to load form');
            $locale = get_locale();
            if ($locale == "el") {
                $locale = "el_GR";
                add_filter('locale', function($locale) {
                    return 'el_GR';
                });

            }
            $parkings = json_decode($parkings->option_value, true);
            if (!$parkings || (isset($parkings[0]) && ($parkings[0]['parking_coordinates'] == ''))) {
                $desks = $company->data[0]->desks;
                $parkings = [];
                foreach ($desks as $desk) {
                    $parkings[] = ['parking_name' => $desk->name, 'parking_coordinates' => $desk->coordinates ];
                }
            }
            $wpRengineCalendarMode          = Option::where('option_name', 'wpRengineCalendarMode')->first();
            $wpRengineCalendarView          = Option::where('option_name', 'wpRengineCalendarView')->first();
            $wpRengineCalendarStep          = Option::where('option_name', 'wpRengineCalendarStep')->first();
            $wpRengineCalendarLocation      = Option::where('option_name', 'wpRengineCalendarLocation')->first();
            $wpRengineCalendarHoursDisabled = Option::where('option_name', 'wpRengineCalendarHoursDisabled')->first();
            $wpRengineSlideMode             = Option::where('option_name', 'wpRengineSlideMode')->first();
            $wpRengineLocationsMode         = Option::where('option_name', 'wpRengineLocationsMode')->first();
            $wpRengineCurrentMode           = Option::where('option_name', 'wpRengineCurrentMode')->first();
            $wpRengineBaseCoordinates       = Option::where('option_name', 'base-coordinates')->first();
            $wpRengineBaseRadius            = Option::where('option_name', 'base-radius')->first();
            $wpRengineCouponMode            = Option::where('option_name', 'wpRengineCouponMode')->first();
            $wpRengineGroupMode             = Option::where('option_name', 'wpRengineGroupMode')->first();

            if (isset($wpRengineCalendarMode)) {
                $calendar_mode = $wpRengineCalendarMode->option_value;
            } else {
                $calendar_mode = "live";
            }
            if ($calendar_mode == "debug") {
                $debug = 'true';
            } else {
                $debug = 'false'; 
            } 

            if (isset($wpRengineCouponMode)) {
                $coupon_mode = $wpRengineCouponMode->option_value;
            } else {
                $coupon_mode = "hide";
            }

            if (isset($wpRengineGroupMode)) {
                $group_mode = $wpRengineGroupMode->option_value;
                $groups =  json_decode(json_encode($this->getAllGroups()), true); 
            } else {
                $group_mode = "hide";
            }

            if (isset($wpRengineCalendarView)) {
                $calendar_view = $wpRengineCalendarView->option_value;
            } else {
                $calendar_view = "popup";
            }
            if (isset($wpRengineCalendarStep)) {
                $calendar_step = $wpRengineCalendarStep->option_value;
            } else {
                $calendar_step = 5;
            }
            if (isset($wpRengineCalendarHoursDisabled)) {
                $calendar_hours = $wpRengineCalendarHoursDisabled->option_value;
            } else {
                $calendar_hours = '';
            }
            if (isset($wpRengineCalendarLocation)) {
                $placement = $wpRengineCalendarLocation->option_value;
            } else {
                $placement  = 'default';
            }
            if ($calendar_view == 'inline') {
                $inline = 'true';
            } else {
                $inline = 'false';
            }

            if (isset($wpRengineCurrentMode)) {
                $current_mode = $wpRengineCurrentMode->option_value;
            } else {
                $current_mode = "false";
            }

            if (isset($wpRengineSlideMode)) {
                $slideMode = $wpRengineSlideMode->option_value;
            } else {
                $slideMode = 'false';
            }

            if (isset($wpRengineBaseCoordinates)) {
                $base_coordinates = $wpRengineBaseCoordinates->option_value;
            } else {
                $base_coordinates = '';
            }

            if (isset($wpRengineBaseRadius)) {
                $base_radius = $wpRengineBaseRadius->option_value;
            } else {
                $base_radius = 100000;
            }
            $pluginsURL                     = plugin_dir_url(__DIR__) . '../';
            if (empty($group)) {
                $group = "";
            }
         
            if ($type == '') {
                if (empty($proffesion)) {
                    $type= "rentals";
                } elseif (($proffesion == 'rentals') || ($proffesion == 'carental2')) {
                    $type = 'rentals';
                 } elseif (($proffesion == 'hotels') || ($proffesion == 'hotels2')) {
                    $type = 'hotels';
                 } elseif ($proffesion == 'restaurants') {
                    $type = 'appointments';
                } elseif ($proffesion == 'transfers') {
                    $type = 'transfers';
                } else {
                    $type = 'appointments';
                }
            }

            if (isset($wpRengineLocationsMode)) {
                $location_mode = $wpRengineLocationsMode->option_value;
            } else {
              if ($type == 'rentals') {
                    $location_mode = "predefined";
                } else {
                     $location_mode = "auto";
                } 
            }
          
            $wpRengineResultsPage           = Option::where('option_name', 'wpRengineResultsPage')->first();
            if (!isset($wpRengineResultsPage)) {
                $wpRengineResultsPage = "workadu-search-results";
            } else {
                $wpRengineResultsPage = $wpRengineResultsPage->option_value;
            }
            $page = get_page_by_path($wpRengineResultsPage);
            if (function_exists('pll_get_post') && (pll_get_post($page->ID))) {
                $page = pll_get_post($page->ID);
            }

            $timezone = get_option('timezone_string');
                
            if ($timezone == "") {
                $timezone = "Europe/London";
            }
            $_SESSION['timezone'] = $timezone;

            
           if ($this->version == 'v2') {
                $view = '@WPRengine/front/reservation-form-v2.twig';
            } else {
                $view = '@WPRengine/front/reservation-form.twig';
            }

            if ($this->company->data[0]->desks[0]->default_start_time) {
                $start_date_time = Carbon::now(new \DateTimeZone($timezone))->toDateString() . ' '. $this->company->data[0]->desks[0]->default_start_time;
            } else {
                $start_date_time = Carbon::now(new \DateTimeZone($timezone))->toDateTimeString();
            }

            if ($this->company->data[0]->desks[0]->default_end_time) {
                if ($this->company->data[0]->desks[0]->default_end_time == $this->company->data[0]->desks[0]->default_start_time )
                    $end_date_time = Carbon::now(new \DateTimeZone($timezone))->addDay()->toDateString() . ' '. $this->company->data[0]->desks[0]->default_end_time;
                else {
                     $end_date_time = Carbon::now(new \DateTimeZone($timezone))->toDateString() . ' '. $this->company->data[0]->desks[0]->default_end_time;
                }
            } else {
                $end_date_time = Carbon::now(new \DateTimeZone($timezone))->addDay()->toDateTimeString();
            }
            
            return view($view, [
                'parkings'          => $parkings,
                'pluginsURL'        => $pluginsURL,
                'pickup_date_time'  =>  $start_date_time,
                'return_date_time'  =>  $end_date_time,
                'searchResultsURL'  => get_permalink($page),
                'tr_pickup_parking_name' => __( 'pickup_parking_name', 'wordpress-rengine' ),
                'tr_return_parking_name' => __( 'return_parking_name', 'wordpress-rengine' ),
                'tr_pickup_date'    => __( 'pickup_date', 'wordpress-rengine' ),
                'tr_return_date'    => __( 'return_date', 'wordpress-rengine' ),
                'tr_same_parking'   => __( 'same_parking', 'wordpress-rengine' ),
                'tr_search_button'  => __( 'search_button', 'wordpress-rengine' ),
                'tr_search_button_appointment'  => __( 'search_button_appointment', 'wordpress-rengine' ),
                'tr_adults'         => __( 'adults', 'wordpress-rengine' ),
                'tr_children'       => __( 'children', 'wordpress-rengine' ),
                'tr_infants'        => __( 'infants', 'wordpress-rengine' ),
                'tr_coupon_code'    => __( 'coupon_code', 'wordpress-rengine' ),
                'tr_group_mode'     => __( 'group_mode', 'wordpress-rengine' ),
                'tr_loading'        => __( 'loading', 'wordpress-rengine' ),
                'tr_when'           => __( 'when', 'wordpress-rengine' ),
                'tr_when_end'       => __( 'when_end', 'wordpress-rengine' ),
                'tr_what'           => __( 'what', 'wordpress-rengine' ),
                'tr_what_end'       => __( 'what_end', 'wordpress-rengine' ),
                'tr_where_from'     => __( 'where_from', 'wordpress-rengine' ),
                'tr_where_to'       => __( 'where_to', 'wordpress-rengine' ),
                'calendar_mode'     => $debug,
                'calendar_view'     => $inline,
                'calendar_step'     => $calendar_step,
                'calendar_hours'    => $calendar_hours,
                'coupon_mode'       => $coupon_mode,
                'group_mode'        => $group_mode,
                'placement'         => $placement,
                'slideMode'         => $slideMode,
                'services_ids'      => $services,
                'group_id'          => $group,
                'type'              => $type,
                'groups'            => $groups['data'],
                'hideLocations'     => $locations,
                'location_mode'     => $location_mode,
                'current_mode'      => $current_mode,
                'base_coordinates'  => $base_coordinates,
                'base_radius'       => $base_radius,
                'googleKey'         => $this->googleKey,
            ]);
        } else {
            return 'Connection with Workadu platform failed! Please check your api key and try again!';
        }
    }

    public function getAllAttributes()
    {
        $response = $this->client->request('GET', 'attributes');
        return json_decode($response->getBody());
    }

    public function getAllBrands()
    {
        $response = $this->client->request('GET', 'brands');
        return json_decode($response->getBody());
    }

  public function getAllGroups()
    {
        $response = $this->client->request('GET', 'groups?sort=sort');
        return json_decode($response->getBody());
    }

    public function transformRequestData(Http $http, $version = 'v1')
    {
        if($version == 'v1') {
            if (!is_null($http->get('pickup_coordinates')))
            {
                $requestData['availability']['pickup_coordinates'] = urldecode($http->get('pickup_coordinates'));
            }
            if (!is_null($http->get('return_coordinates')))
            {
                $requestData['availability']['return_coordinates'] = urldecode($http->get('return_coordinates'));
            }
            if (!is_null($http->get('pickup_datetime')))
            {
                $pickupDateTime                             = explode(' ', urldecode($http->get('pickup_datetime')));
                $requestData['availability']['pickup_date'] = (new Carbon($pickupDateTime[0]))->toDateString();
                $requestData['availability']['pickup_time'] = $pickupDateTime[1];
            }
            if (!is_null($http->get('return_datetime')))
            {
                $returnDateTime                             = explode(' ', urldecode($http->get('return_datetime')));
                $requestData['availability']['return_date'] = (new Carbon($returnDateTime[0]))->toDateString();
                $requestData['availability']['return_time'] = $returnDateTime[1];
            } else {
                if($http->get('type') == "activity") {
                    $returnDateTime                             = explode(' ', urldecode($http->get('pickup_datetime')));
                    $requestData['availability']['return_date'] = (new Carbon($returnDateTime[0]))->addDay()->toDateString();
                    $requestData['availability']['return_time'] = $returnDateTime[1];
                }
            }
            if (!is_null($http->get('coupon')))
            {
                $requestData['availability']['coupon'] = filter_var ($http->get('coupon'), FILTER_SANITIZE_STRING);;
            }

            if($http->get('type') == "activity") {
                if (!is_null($http->get('adults')))
                {
                    $requestData['availability']['adults'] = urldecode($http->get('adults'));
                } else {
                    $requestData['availability']['adults'] = 0;
                }
                if (!is_null($http->get('child')))
                {
                    $requestData['availability']['child'] = urldecode($http->get('child'));
                } else {
                    $requestData['availability']['child'] = 0;
                }
                if (!is_null($http->get('infants')))
                {
                    $requestData['availability']['infants'] = urldecode($http->get('infants'));
                } else {
                    $requestData['availability']['infants'] = 0;
                }
                if (!is_null($http->get('pets')))
                {
                    $requestData['availability']['pets'] = urldecode($http->get('pets'));
                } else {
                    $requestData['availability']['pets'] = 0;
                }
            } else {
                $requestData['availability']['adults'] = 1;
                $requestData['availability']['child'] = 0;
                $requestData['availability']['infants'] = 0;
                $requestData['availability']['pets'] = 0;
            }
            if (!is_null($http->get('brand')))
            {
                $requestData['extra_filters']['brand'] = implode(',', array_keys($http->get('brand')));
            }
            if (!is_null($http->get('attribute')))
            {
                $requestData['extra_filters']['attribute_id'] = implode(',', array_keys($http->get('attribute')));
            }
            if (!is_null($http->get('company')))
            {
                $requestData['extra_filters']['companyid'] = implode(',', array_keys($http->get('company')));
            }
            if (!is_null($http->get('group')))
            {
                if (is_array($http->get('group'))) {
                    $requestData['extra_filters']['category_id'] = implode(',', array_keys($http->get('group')));
                } else {
                    $requestData['extra_filters']['category_id'] = $http->get('group');
                }
                
            }
        } else {
            if (!is_null($http->get('pickup_coordinates')))
            {
                $requestData['availability']['start_coordinates'] = urldecode($http->get('pickup_coordinates'));
            }
            if (!is_null($http->get('return_coordinates')))
            {
                $requestData['availability']['end_coordinates'] = urldecode($http->get('return_coordinates'));
            }
            if (!is_null($http->get('pickup_datetime')))
            {
                $pickupDateTime                             = explode(' ', urldecode($http->get('pickup_datetime')));
                $requestData['availability']['start_date'] = (new Carbon($pickupDateTime[0]))->toDateString();
                $requestData['availability']['start_time'] = $pickupDateTime[1];
            }
            if (!is_null($http->get('return_datetime')))
            {
                $returnDateTime                             = explode(' ', urldecode($http->get('return_datetime')));
                $requestData['availability']['end_date'] = (new Carbon($returnDateTime[0]))->toDateString();
                $requestData['availability']['end_time'] = $returnDateTime[1];
            } else {
                if($http->get('type') == "activity") {
                    $returnDateTime                             = explode(' ', urldecode($http->get('pickup_datetime')));
                    $requestData['availability']['end_date'] = (new Carbon($returnDateTime[0]))->addDay()->toDateString();
                    $requestData['availability']['end_time'] = $returnDateTime[1];
                }
            }
            if (!is_null($http->get('coupon')))
            {
                $requestData['availability']['coupon'] = filter_var ($http->get('coupon'), FILTER_SANITIZE_STRING);;
            }

            if ($http->get('selectedTime')<>"") {
                $requestData['availability']['start_date']      = $http->get('selectedDate');
                $requestData['availability']['end_date']        = $http->get('selectedDate');
                $requestData['availability']['start_time']      = $http->get('selectedTime');
                $requestData['availability']['end_time']        = $http->get('selectedEndTime');
            }
            if (($http->get('type') == "appointments") || ($http->get('type') == "hotels") || ($http->get('type') == "transfers")) {
                if (!is_null($http->get('adults')))
                {
                    $requestData['availability']['adults'] = urldecode($http->get('adults'));
                } else {
                    $requestData['availability']['adults'] = 0;
                }
                if (!is_null($http->get('children')))
                {
                    $requestData['availability']['children'] = urldecode($http->get('children'));
                } else {
                    $requestData['availability']['children'] = 0;
                }
                if (!is_null($http->get('infants')))
                {
                    $requestData['availability']['infants'] = urldecode($http->get('infants'));
                } else {
                    $requestData['availability']['infants'] = 0;
                }
                if (!is_null($http->get('pets')))
                {
                    $requestData['availability']['pets'] = urldecode($http->get('pets'));
                } else {
                    $requestData['availability']['pets'] = 0;
                }
            } else {
                $requestData['availability']['adults'] = 1;
                $requestData['availability']['child'] = 0;
                $requestData['availability']['infants'] = 0;
                $requestData['availability']['pets'] = 0;
            }
            if (!is_null($http->get('brand')))
            {
                $requestData['extra_filters']['brand'] = implode(',', array_keys($http->get('brand')));
            }
           
            if (!is_null($http->get('attribute')))
            {
                $requestData['extra_filters']['attribute_id'] = implode(',', array_keys($http->get('attribute')));
            }
            if (!is_null($http->get('company')))
            {
                $requestData['extra_filters']['companyid'] = implode(',', array_keys($http->get('company')));
            }
            if (!is_null($http->get('group')))
            {
                if (is_array($http->get('group'))) {
                    $requestData['extra_filters']['category_id'] = implode(',', array_keys($http->get('group')));
                } else {
                    $requestData['extra_filters']['category_id'] = $http->get('group');
                }
                
            }


        }
        
        return $requestData;
    }

    public function calculatePricing($rates, $totalPrice)
    {
        foreach ($rates as $rate)
        {
            if (isset($rate['offer']))
            {
                foreach ($rate['offer'] as $offer)
                {
                    if ($offer)
                    {
                        $offersContainer[$offer['offer_id']][] = $offer;
                    }
                }
            }
        }
        $offerCost = [];
        if (!empty($offersContainer))
        {
            foreach ($offersContainer as $offerPackage)

            {
                foreach ($offerPackage as $offer)
                {

                    $offerCost[$offer['offer_id']]['name'] = $offer['type'];
                    $offerCost[$offer['offer_id']]['type'] = $offer['offer_type'];
                    if ($offer['offer_type'] == 'extra')
                    {
                        if ($offer['offer_apply'] == 'before') {
                            $offerCost[$offer['offer_id']]['cost'] = ($offer['new_value'] - $offer['old_value']) + $offerCost[$offer['offer_id']]['cost'];
                        } else {
                            $offerCost[$offer['offer_id']]['cost'] = ($offer['new_value'] - $offer['old_value']) ;
                        }
                    }
                    elseif ($offer['offer_type'] == 'discount')
                    {
                        if ($offer['offer_apply'] == 'before') {
                            $offerCost[$offer['offer_id']]['cost'] = ($offer['new_value'] - $offer['old_value']) + $offerCost[$offer['offer_id']]['cost'];
                        } else {
                            $offerCost[$offer['offer_id']]['cost'] = ($offer['new_value'] - $offer['old_value']) ;
                        }
                        
                    }
                }
            }
        }
        if (!empty($offerCost))
        {
            $calculatePricing = ['offers' => $offerCost, 'flatCost' => $totalPrice];
            foreach ($offerCost as $offer)
            {
                $calculatePricing['flatCost'] = $calculatePricing['flatCost'] - $offer['cost'];
            }
        }
        else
        {
            $calculatePricing = ['offer' => null, 'flatCost' => $totalPrice];
        }
        $calculatePricing['totalCost'] = $totalPrice;
        return $calculatePricing;
    }

    public function wpRengineSearchResultsShortcode(Http $http)

    {
        if ($this->company ) {
            $microStart = microtime();
            $company            = $this->company;
            $locale = get_locale();
            if ($locale == "el") {
                $locale = "el_GR";
            }

            $return_date = $http->get('return_datetime');
            $requestData            = $this->transformRequestData($http, $this->version);

            if ($http->get('pickup_range') != '') {
                $DateTimeArr = explode('to', $http->get('pickup_range'));
                $pickupDateTime = trim($DateTimeArr[0]);
                $returnDateTime = trim((isset($DateTimeArr[1]))? $DateTimeArr[1] : $DateTimeArr[0]);

                if (isset($pickupDateTime)) {
                    $pickupDateTimeArr                          = explode(' ', $pickupDateTime);
                    $requestData['availability']['start_date'] = (new Carbon($pickupDateTimeArr[0]))->toDateString();
                    $requestData['availability']['start_time'] = ($http->get('pickup_time') != '' )? $http->get('pickup_time') : ((isset($pickupDateTimeArr[1]))? $pickupDateTimeArr[1] : '12:00') ;
                    $pickupDateTime = $requestData['availability']['start_date'] . ' '.  $requestData['availability']['start_time'];
                }
                if (isset($returnDateTime)) {
                    $returnDateTimeArr                          = explode(' ', $returnDateTime);
                    $requestData['availability']['end_date'] = (new Carbon($returnDateTimeArr[0]))->toDateString();
                    $requestData['availability']['end_time'] = ($http->get('return_time') != '' )? $http->get('return_time') : ($http->get('pickup_time') != '' )?  $http->get('pickup_time'): ((isset($returnDateTimeArr[1]))? $returnDateTimeArr[1] : '12:00') ;
                    $returnDateTime = $requestData['availability']['end_date'] . ' '.  $requestData['availability']['end_time'];
                }
                // $requestData['availability']['start_date'] = Carbon::createFromFormat('Y-m-d H:i', $pickupDateTime)->format('Y-m-d');
                // $requestData['availability']['start_time']  = Carbon::createFromFormat('Y-m-d H:i', $pickupDateTime)->format('H:i');
                // $requestData['availability']['end_date']  = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime)->format('Y-m-d');
                // $requestData['availability']['end_time']  = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime)->format('H:i');

                // $requestData['availability']['start_date'] = 
            } else {
                if ($http->get('pickup_date') != '') {
                     $pickupDateTime = $http->get('pickup_date') .' '.$http->get('pickup_time');
                } else {
                    $pickupDateTime = $http->get('pickup_datetime');
                }

                if ($http->get('return_date') != '') {
                     $returnDateTime = $http->get('return_date') .' '.$http->get('return_time');
                } else {
                    $returnDateTime = $http->get('return_datetime');
                }

                if (isset($pickupDateTime)) {
                    $pickupDateTimeArr                          = explode(' ', $pickupDateTime);
                    $requestData['availability']['start_date'] = (new Carbon($pickupDateTimeArr[0]))->toDateString();
                    $requestData['availability']['start_time'] = ($http->get('pickup_time') != '' )? $http->get('pickup_time') : ((isset($pickupDateTimeArr[1]))? $pickupDateTimeArr[1] : '12:00') ;
                    $pickupDateTime = $requestData['availability']['start_date'] . ' '.  $requestData['availability']['start_time'];
                }
                if (isset($returnDateTime)) {
                    $returnDateTimeArr                          = explode(' ', $returnDateTime);
                    $requestData['availability']['end_date'] = (new Carbon($returnDateTimeArr[0]))->toDateString();

                    $requestData['availability']['end_time'] = ($http->get('return_time') != '' )? $http->get('return_time') : (($http->get('pickup_time') != '' )?  $http->get('pickup_time'): ((isset($returnDateTimeArr[1]))? $returnDateTimeArr[1] : '12:00')) ;

                    $returnDateTime = $requestData['availability']['end_date'] . ' '.  $requestData['availability']['end_time'];
                } else {
                    $requestData['availability']['end_date'] =  $requestData['availability']['start_date'];
                    $requestData['availability']['end_time'] = $requestData['availability']['start_time'];
                    $returnDateTime = $pickupDateTime;
                }

            }
            if (($pickupDateTime != null) &&  ($returnDateTime != null)) {
                setLocale(LC_TIME, $locale.'.utf-8');
                $pickupDateReadable = Carbon::createFromFormat('Y-m-d H:i',  $pickupDateTime)->formatLocalized('%d %B ');
                $returnDateReadable = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime)->formatLocalized('%d %B');
                $pickupTime         = Carbon::createFromFormat('Y-m-d H:i', $pickupDateTime)->format('H:i');
                $returnTime         = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime)->format('H:i');
                $pickup_date_time   = filter_var ($pickupDateTime, FILTER_SANITIZE_STRING);
                $return_date_time   = $returnDateTime;
                $parkings           = json_decode(Option::where('option_name', 'wpRengineParkings')->first()->option_value, true);
                $wpRengineButton    = "BOOK >";
                $group_id           = filter_var ($http->get('group'), FILTER_SANITIZE_STRING);
                $service_ids         = filter_var ($http->get('services_ids'), FILTER_SANITIZE_STRING);
                $type               = filter_var ($http->get('type'), FILTER_SANITIZE_STRING);
                $hideLocations      = filter_var ($http->get('hideLocations'), FILTER_SANITIZE_STRING);
                $pickupParking      = filter_var ($http->get('pickup_parking_name'), FILTER_SANITIZE_STRING);
                $returnParking      = filter_var ($http->get('return_parking_name'), FILTER_SANITIZE_STRING);
                $pickupCoordinates  = filter_var ($http->get('pickup_coordinates'), FILTER_SANITIZE_STRING);
                $returnCoordinates  = ($http->get('return_coordintates') != '') ? filter_var ($http->get('return_coordintates'), FILTER_SANITIZE_STRING) : $pickupCoordinates;
                $adults             = filter_var ($http->get('adults'), FILTER_VALIDATE_INT);
                $children           = filter_var ($http->get('children'), FILTER_VALIDATE_INT);
                $infants            = filter_var ($http->get('infants'), FILTER_VALIDATE_INT);
                $coupon             = filter_var ($http->get('coupon'), FILTER_SANITIZE_STRING);
            
                Carbon::setLocale(get_locale());
                // $timeDifferenceReadable = Carbon::createFromFormat('Y-m-d H:i', $http->get('pickup_datetime'))->diffForHumans(Carbon::createFromFormat('Y-m-d H:i', $return_date), true);
                $dtfrom = Carbon::createFromFormat('Y-m-d H:i', $pickupDateTime);
                $dtto   = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime);
                $littleHandRotations = $dtfrom->diffInHours($dtto);
                $timeDifferenceReadable = (int)($littleHandRotations/24);
                if ($littleHandRotations % 24 >= 1) {
                    $timeDifferenceReadable++;
                }
                $totalDays = $timeDifferenceReadable;

                if (($totalDays == 0)) {
                    $totalDays = 1; 
                }

                if ($totalDays == 1) {
                    $timeDifferenceReadable = (string)$totalDays .' '. __( 'day', 'wordpress-rengine' );
                } else {
                    $timeDifferenceReadable = (string)$totalDays .' '. __( 'days', 'wordpress-rengine' );
                }

                $tempPickup         = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime);
                $startOfWeek        = $tempPickup->startOfWeek();
                $startofWeekDate    = $startOfWeek->format('Y-m-d');
                $startOfWeekReadable = $startOfWeek->formatLocalized('%d %B ');
                $endOfPeriod        = $tempPickup->addDays(28);
                $endOfPeriodReadable = $endOfPeriod->formatLocalized('%d %B ');

                $requestData['extra_filters']['language'] =  get_locale();
                $filters                = ($requestData) ? ['filter' => $requestData] : '';
                $filters['sort']        = 'total_price_sort';
                $effectiveURL = '';
                $statusCode   = 0;
                if (($service_ids != '') && ($group_id == '')) {
                    $specificServices = '/'.$service_ids;
                } else {
                    $specificServices ='';
                }
                $wpRengineResultsCalendarMode   = Option::where('option_name', 'wpRengineResultsCalendarMode')->first();
                if (isset($wpRengineResultsCalendarMode)) {
                    $results_calendar_mode = $wpRengineResultsCalendarMode->option_value;
                } else {
                    $results_calendar_mode = "hide";
                    $filters['filter']['availability']['mode'] = 'strict';
                }
                $response  = $this->client->request('GET', 'services'.$specificServices, ['query' => $filters, 
                        'on_stats' => function (TransferStats $stats) use (&$effectiveURL, &$statusCode) {
                            $effectiveURL = (string) $stats->getEffectiveUri();
                            if ($stats->hasResponse()) {
                                $statusCode = $stats->getResponse()->getStatusCode();
                            }
                             },
                ]);

                // $microend = microtime();
                //  dd('time:' . $microend - $microStart);

                if ($adults) {
                    if ($adults == 1) {
                        $adultsLabel = '1 adult ';
                    } else {
                        $adultsLabel = $adults .' adults ';
                    }
                }
                if ($children) {
                    if ($children == 1) {
                        $childLabel = ', 1 child ';
                    } else {
                        $childLabel = ', '.$children .' children ';
                    }
                }
                if ($infants) {
                    if ($infants == 1) {
                        $infantLabel = ', 1 infant';
                    } else {
                        $infantLabel = ', '.$infants .' infants';
                    }
                }

                $paxReadable = $adultsLabel . $childLabel . $infantLabel;

                $parkings   = Option::where('option_name', 'wpRengineParkings')->first();

                $wpRengineCalendarMode          = Option::where('option_name', 'wpRengineCalendarMode')->first();
                
                $wpRengineResultsAvailableMode  = Option::where('option_name', 'wpRengineResultsAvailableMode')->first();
                $wpRengineCalendarView          = Option::where('option_name', 'wpRengineCalendarView')->first();
                $wpRengineCalendarStep          = Option::where('option_name', 'wpRengineCalendarStep')->first();
                $wpRengineCalendarLocation      = Option::where('option_name', 'wpRengineCalendarLocation')->first();
                $wpRengineCalendarHoursDisabled = Option::where('option_name', 'wpRengineCalendarHoursDisabled')->first();
                $wpRengineSlideMode             = Option::where('option_name', 'wpRengineSlideMode')->first();
                $wpRengineLocationsMode         = Option::where('option_name', 'wpRengineLocationsMode')->first();
                $wpRengineCurrentMode           = Option::where('option_name', 'wpRengineCurrentMode')->first();
                $wpRengineBaseCoordinates       = Option::where('option_name', 'base-coordinates')->first();
                $wpRengineBaseRadius            = Option::where('option_name', 'base-radius')->first();
                $wpRengineCouponMode            = Option::where('option_name', 'wpRengineCouponMode')->first();
                $wpRengineGroupMode             = Option::where('option_name', 'wpRengineGroupMode')->first();

                if (isset($wpRengineGroupMode)) {
                    $group_mode = $wpRengineGroupMode->option_value;
                    $groups =  json_decode(json_encode($this->getAllGroups()), true); 
                } else {
                    $group_mode = "hide";
                }

                if (isset($wpRengineCalendarMode)) {
                    $calendar_mode = $wpRengineCalendarMode->option_value;
                } else {
                    $calendar_mode = "live";
                }
                if ($calendar_mode == "debug") {
                    $debug = 'true';
                } else {
                    $debug = 'false'; 
                } 
                
                if (isset($wpRengineResultsAvailableMode)) {
                    $results_available_mode = $wpRengineResultsAvailableMode->option_value;
                } else {
                    $results_available_mode = "false";
                }
                if (isset($wpRengineCouponMode)) {
                    $coupon_mode = $wpRengineCouponMode->option_value;
                } else {
                    $coupon_mode = "hide";
                }
                if (isset($wpRengineCalendarView)) {
                    $calendar_view = $wpRengineCalendarView->option_value;
                } else {
                    $calendar_view = "popup";
                }
                if (isset($wpRengineCalendarStep)) {
                    $calendar_step = $wpRengineCalendarStep->option_value;
                } else {
                    $calendar_step = 5;
                }
                if (isset($wpRengineCalendarHoursDisabled)) {
                    $calendar_hours = $wpRengineCalendarHoursDisabled->option_value;
                } else {
                    $calendar_hours = '';
                }
                if (isset($wpRengineCalendarLocation)) {
                    $placement = $wpRengineCalendarLocation->option_value;
                } else {
                    $placement  = 'default';
                }

               if (isset($wpRengineLocationsMode)) {
                    $location_mode = $wpRengineLocationsMode->option_value;
                } else {
                  if ($type == 'rentals') {
                        $location_mode = "predefined";
                    } else {
                         $location_mode = "auto";
                    } 
                }

                if (isset($wpRengineCurrentMode)) {
                    $current_mode = $wpRengineCurrentMode->option_value;
                } else {
                    $current_mode = "false";
                }

                if ($calendar_view == 'inline') {
                    $inline = 'true';
                } else {
                    $inline = 'false';
                }
                if (isset($wpRengineSlideMode)) {
                    $slideMode = $wpRengineSlideMode->option_value;
                } else {
                    $slideMode = 'false';
                }

                if (isset($wpRengineBaseCoordinates)) {
                    $base_coordinates = $wpRengineBaseCoordinates->option_value;
                } else {
                    $base_coordinates = '';
                }

                if (isset($wpRengineBaseRadius)) {
                    $base_radius = $wpRengineBaseRadius->option_value;
                } else {
                    $base_radius = 100000;
                }
                $pluginsURL = plugin_dir_url(__DIR__) . '../';

                $wpRengineResultsPage           = Option::where('option_name', 'wpRengineResultsPage')->first();
                if (!isset($wpRengineResultsPage)) {
                    $wpRengineResultsPage = "workadu-search-results";
                } else {
                    $wpRengineResultsPage = $wpRengineResultsPage->option_value;
                }

                $wpRengineRevisionPage          = Option::where('option_name', 'wpRengineRevisionPage')->first();
                if (!isset($wpRengineRevisionPage)) {
                    $wpRengineRevisionPage = "workadu-contact-info";
                } else {
                    $wpRengineRevisionPage = $wpRengineRevisionPage->option_value;
                }

                $wpRengineCash          = Option::where('option_name', 'wpRengineCash')->first();
                if (!isset($wpRengineCash)) {
                    $wpRengineCash = "off";
                    $wpRengineCashMode = "no";
                } else {
                    $wpRengineCash = $wpRengineCash->option_value;
                    $wpRengineCashMode          = Option::where('option_name', 'wpRengineCashMode')->first()->option_value;
                }

                $page = get_page_by_path($wpRengineRevisionPage);
                //in case of polylang
                if (function_exists('pll_get_post') && (pll_get_post($page->ID))) {
                    $page = pll_get_post($page->ID);
                }

                $searchPage = get_page_by_path($wpRengineResultsPage);
                if (function_exists('pll_get_post') && (pll_get_post($searchPage->ID))) {
                    $searchPage = pll_get_post($searchPage->ID);
                }
                $parkings = json_decode($parkings->option_value, true);
                if ((!$parkings) || ((isset($parkings[0]) && ($parkings[0]['parking_coordinates'] == '')))) {
                    $desks = $company->data[0]->desks;
                    $parkings = [];
                    foreach ($desks as $desk) {
                        $parkings[] = ['parking_name' => $desk->name, 'parking_coordinates' => $desk->coordinates ];
                    }
                }
                if ($this->version == 'v2') {
                    $view = '@WPRengine/front/search-results-v2.twig';
                } else {
                    $view = '@WPRengine/front/search-results.twig';
                }
                $begin = new \DateTime($pickupDateTime );
                $end = new \DateTime( $return_date_time );
                $end = $end->modify( '+1 day' ); 
                $interval = new \DateInterval('P1D');
                $daterange = new \DatePeriod($begin, $interval ,$end);
                
                return view($view, [
                    'pickupDateReadable'     => $pickupDateReadable,
                    'returnDateReadable'     => $returnDateReadable,
                    'pickupCoordinates'      => $pickupCoordinates,
                    'returnCoordinates'      => $returnCoordinates,
                    'pickupTime'             => $pickupTime,
                    'returnTime'             => $returnTime,
                    'pickupParking'          => $pickupParking,
                    'returnParking'          => $returnParking,
                    'pickup_date_time'       => $pickup_date_time,
                    'return_date_time'       => $return_date_time,
                    'timeDifferenceReadable' => $timeDifferenceReadable,
                    'paxReadable'            => $paxReadable,
                    'adults'                 => $adults, 
                    'children'               => $children,
                    'infants'                => $infants,
                    'coupon'                 => $coupon,
                    'coupon_mode'            => $coupon_mode,
                    'cash'                   => $wpRengineCash,
                    'filters'                => json_encode($requestData),
                    'tr_cash'                => __( 'cash_search_label', 'wordpress-rengine' ),
                    'tr_adults'              => __( 'adults', 'wordpress-rengine' ),
                    'tr_children'            => __( 'children', 'wordpress-rengine' ),
                    'tr_infants'             => __( 'infants', 'wordpress-rengine' ),
                    'tr_notes'               => __( 'notes', 'wordpress-rengine' ),
                    'tr_includes'            => __( 'includes', 'wordpress-rengine' ),
                    'tr_excludes'            => __( 'excludes', 'wordpress-rengine' ),
                    'tr_distance'            => __( 'distance', 'wordpress-rengine' ),
                    'tr_duration'            => __( 'duration', 'wordpress-rengine' ),
                    'totalDays'              => $totalDays,
                    'services'               => json_decode($response->getBody(), true),
                    'detailsURL'             => get_permalink($page),
                    'queryString'            => $_SERVER['QUERY_STRING'],
                    'currency'               => $company->data[0]->currency,
                    'button'                 => $wpRengineButton,
                    'tr_change_dates'        => __( 'change_dates', 'wordpress-rengine' ),
                    'tr_available_cars'      => __( 'available_cars', 'wordpress-rengine' ),
                    'tr_available_activities'=> __( 'available_activities', 'wordpress-rengine' ),
                    'tr_what'           => __( 'what', 'wordpress-rengine' ),
                    'tr_from'                => __( 'from', 'wordpress-rengine' ),
                    'tr_to'                  => __( 'to', 'wordpress-rengine' ),
                    'tr_for'                 => __( 'for', 'wordpress-rengine' ),
                    'tr_brands'              => __( 'brands', 'wordpress-rengine' ),
                    'tr_attributes'          => __( 'attributes', 'wordpress-rengine' ),
                    'tr_return'              => __( 'return', 'wordpress-rengine' ),
                    'tr_pickup'              => __( 'pickup', 'wordpress-rengine' ),
                    'tr_no_results'          => __( 'no_results', 'wordpress-rengine' ),
                    'tr_button'              => __( 'button', 'wordpress-rengine' ),
                    'tr_sold_out'            => __( 'sold_out', 'wordpress-rengine' ),
                    'tr_last_booking'        => __( 'last_booking', 'wordpress-rengine' ),
                    'tr_coupon_code'         => __( 'coupon_code', 'wordpress-rengine' ),
                    'tr_loading'             => __( 'loading', 'wordpress-rengine' ),
                    'tr_when'                => __( 'when', 'wordpress-rengine' ),
                    'tr_where'               => __( 'where', 'wordpress-rengine' ),
                    'tr_where_from'          => __( 'where_from', 'wordpress-rengine' ),
                    'tr_where_to'            => __( 'where_to', 'wordpress-rengine' ),
                    'tr_day'                 => __( 'day', 'wordpress-rengine' ),
                    'tr_no_charge'           => __( 'no_charge', 'wordpress-rengine' ),
                    'parkings'               => $parkings,
                    'pluginsURL'             => $pluginsURL,
                    'searchResultsURL'       => get_permalink($searchPage),
                    'tr_pickup_parking_name' => __( 'pickup_parking_name', 'wordpress-rengine' ),
                    'tr_return_parking_name' => __( 'return_parking_name', 'wordpress-rengine' ),
                    'tr_pickup_date'         => __( 'pickup_date', 'wordpress-rengine' ),
                    'tr_return_date'         => __( 'return_date', 'wordpress-rengine' ),
                    'tr_same_parking'        => __( 'same_parking', 'wordpress-rengine' ),
                    'tr_search_button'       => __( 'search_button', 'wordpress-rengine' ),
                    'tr_paynow'              => __( 'pay_now', 'wordpress-rengine' ),
                    'group_id'               => $group_id,
                    'type'                   => $type,
                    'group_mode'             => $group_mode,
                    'groups'                 => $groups['data'],
                    'calendar_mode'          => $calendar_mode,
                    'results_available_mode' => $results_available_mode,
                    'results_calendar_mode'  => $results_calendar_mode,         
                    'calendar_view'          => $calendar_view,         
                    'calendar_step'          => $calendar_step,         
                    'placement'              => $placement,     
                    'calendar_hours'         => $calendar_hours,    
                    'slideMode'              => $slideMode, 
                    'location_mode'          => $location_mode,
                    'current_mode'           => $current_mode,
                    'base_coordinates'       => $base_coordinates,
                    'base_radius'            => $base_radius,
                    'hideLocations'          => $hideLocations,
                    'paymenetPercent'        => Option::where('option_name', 'wpRenginePaymentPercent')->first()->option_value,
                    'startOfWeek'            => $startOfWeekReadable,
                    'endOfPeriod'            => $endOfPeriodReadable,
                    'daterange'              => $daterange,
                    'startOfWeekDate'        => $startofWeekDate,
                    'timezone'               => $_SESSION['timezone'],
                    'tr_search_button_appointment'  => __( 'search_button_appointment', 'wordpress-rengine' ),
                    'googleKey'             => $this->googleKey,
                    'services_ids'          => $service_ids,

                ]);
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    public function wpRengineCarAndDriverDetailsShortcode(Http $http)
    {
        if ($this->company ) {
            $company            = $this->company;
            $requestData        = $this->transformRequestData($http, $this->version);   //$this->transformRequestData($http);
            $locale = get_locale();
            if ($locale == "el") {
                $locale = "el_GR";
            }

            $wpRengineLocationsMode        = Option::where('option_name', 'wpRengineLocationsMode')->first();
             if (isset($wpRengineLocationsMode)) {
                $location_mode = $wpRengineLocationsMode->option_value;
            } else {
                $location_mode = "predefined";
            }
            if (($http->get('pickup_range') != null) || (($http->get('pickup_date') != null) ||  ($http->get('return_date') != null))) {
                if ($http->get('pickup_range') != '') {

                    $DateTimeArr = explode('to', $http->get('pickup_range'));
                    $pickupDateTime = trim($DateTimeArr[0]);
                    $returnDateTime = trim((isset($DateTimeArr[1]))? $DateTimeArr[1] : $DateTimeArr[0]);

                    if (isset($pickupDateTime)) {
                        $pickupDateTimeArr                          = explode(' ', $pickupDateTime);

                        $requestData['availability']['start_date'] = (new Carbon($pickupDateTimeArr[0]))->toDateString();
                        $requestData['availability']['start_time'] = ($http->get('pickup_time') != '' )? $http->get('pickup_time') : ((isset($pickupDateTimeArr[1]))? $pickupDateTimeArr[1] : '12:00') ;
                        $pickupDateTime = $requestData['availability']['start_date'] . ' '.  $requestData['availability']['start_time'];
                    }
                    if (isset($returnDateTime)) {
                        $returnDateTimeArr                          = explode(' ', $returnDateTime);
                        $requestData['availability']['end_date'] = (new Carbon($returnDateTimeArr[0]))->toDateString();
                        $requestData['availability']['end_time'] = ($http->get('return_time') != '') ? $http->get('return_time') : ($http->get('pickup_time') != '' )?  $http->get('pickup_time'): ((isset($returnDateTimeArr[1]))? $returnDateTimeArr[1] : '12:00') ;
                        $returnDateTime = $requestData['availability']['end_date'] . ' '.  $requestData['availability']['end_time'];
                    }

                } else {
                   if ($http->get('pickup_date') != '') {
                         $pickupDateTime = $http->get('pickup_date') .' '.$http->get('pickup_time');
                    } else {
                        $pickupDateTime = $http->get('pickup_datetime');
                    }

                    if ($http->get('return_date') != '') {
                         $returnDateTime = $http->get('return_date') .' '.$http->get('return_time');
                    } else {
                        if ( $http->get('return_datetime') != '') {
                        $returnDateTime = $http->get('return_datetime');
                         } else {
                            $returnDateTime = $pickupDateTime;
                        }
                    }
                    $requestData['availability']['start_date'] = Carbon::createFromFormat('Y-m-d H:i', $pickupDateTime)->format('Y-m-d');
                    $requestData['availability']['start_time']  = Carbon::createFromFormat('Y-m-d H:i', $pickupDateTime)->format('H:i');
                    $requestData['availability']['end_date']  = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime)->format('Y-m-d');
                    $requestData['availability']['end_time']  = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime)->format('H:i');
                }

                if (($http->get('selectedTime') != '') && ($http->get('selectedTime') != 'undefined')) {
                    $requestData['availability']['start_date'] = $http->get('selectedDate');
                    $requestData['availability']['start_time']  = $http->get('selectedTime');
                    $requestData['availability']['end_date']  = $http->get('selectedDate');
                    $requestData['availability']['end_time']  = $http->get('selectedEndTime');
                    $pickupDateTime = $http->get('selectedDate').' '.$http->get('selectedTime');
                    $returnDateTime = $http->get('selectedDate').' '.$http->get('selectedEndTime');
                }
                setLocale(LC_TIME, $locale.'.utf-8');
               
                $pickupTime         = Carbon::createFromFormat('Y-m-d H:i',  $pickupDateTime  )->format("H:i");
                $returnTime         = Carbon::createFromFormat('Y-m-d H:i',  $returnDateTime  )->format("H:i");
                $parkings           = json_decode(Option::where('option_name', 'wpRengineParkings')->first()->option_value, true);
                $terms              = Option::where('option_name', 'wpRengineTerms')->first()->option_value;
                $adults             = filter_var ($http->get('adults'), FILTER_VALIDATE_INT);
                $children           = filter_var ($http->get('child'), FILTER_VALIDATE_INT);
                $infants            = filter_var ($http->get('infants'), FILTER_VALIDATE_INT);
                $type               = filter_var ($http->get('type'), FILTER_SANITIZE_STRING); 

                if ((!$parkings) || ((isset($parkings[0]) && ($parkings[0]['parking_coordinates'] == '')))) {
                    $desks = $company->data[0]->desks;
                    $parkings = [];
                    foreach ($desks as $desk) {
                        $parkings[] = ['parking_name' => $desk->name, 'parking_coordinates' => $desk->coordinates ];
                    }
                }

                if ($location_mode != "auto") {
                    foreach ($parkings as $parking)
                    {
                        $pickupParking = ($parking['parking_coordinates'] == $http->get('pickup_coordinates')) ? $parking['parking_name'] : $http->get('pickup_parking_name');
                        $returnParking = ($parking['parking_coordinates'] == $http->get('return_coordinates')) ? $parking['parking_name'] : $http->get('pickup_parking_name');
                    }
                } else {
                    $pickupParking = $http->get('pickup_parking_name');
                    $returnParking = $http->get('return_parking_name');
                }
                $filters                = ($requestData) ? ['filter' => $requestData] : '';
                $response               = $this->client->request('GET', 'services/' . $http->get('service_id'), ['query' => $filters]);
                $services               = json_decode($response->getBody(), true);
                $service                = $services['data'][0];

                if (($http->get('pickup_datetime')!='') && ($http->get('pickup_datetime') == $http->get('return_datetime')) && ($service['type'] =='daily')) {
                    $returnDateTime = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime);
                    $returnDateTime = $returnDateTime->addDay()->format('Y-m-d H:i');
                }

                if (($service['type'] == 'permile') ) {
                    $returnDateTime = Carbon::createFromFormat('Y-m-d H:i', $requestData['availability']['start_date'].' '. $requestData['availability']['start_time']);
                    $returnDateTime = $returnDateTime->addSeconds($service['distance'][$http->get('selectedRategroup')]['duration']['value']);
                    $returnTime = $returnDateTime->format('H:i');
                    $returnDateTime = $returnDateTime->format('Y-m-d H:i');
                }

                $pickupDateReadable = Carbon::createFromFormat('Y-m-d H:i',  $pickupDateTime  )->formatLocalized('%d %B');
                $returnDateReadable = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime)->formatLocalized('%d %B');
                Carbon::setLocale(get_locale());
                $totalDays = $http->get('totalDays');

                if ($adults) {
                    if ($adults == 1) {
                        $adultsLabel = '1 adult ';
                    } else {
                        $adultsLabel = $adults .' adults ';
                    }
                }
                if ($children) {
                    if ($children == 1) {
                        $childLabel = ', 1 child ';
                    } else {
                        $childLabel = ', '.$children .' children ';
                    }
                }
                if ($infants) {
                    if ($infants == 1) {
                        $infantLabel = ', 1 infant';
                    } else {
                        $infantLabel = ', '.$infants .' infants';
                    }
                }

                $paxReadable = $adultsLabel . $childLabel . $infantLabel;

                if (($service->type == 'daily') && ($totalDays == 0)) {
                    $totalDays = 1; 

                }
                if ($totalDays == 1) {
                    $timeDifferenceReadable = (string)$totalDays .' day';
                } else {
                    $timeDifferenceReadable = (string)$totalDays .' days';
                }
               
                $pricingData            = $this->calculatePricing($service['availabilities_and_rates'], ($http->get('totalPrice')=='') || ($http->get('totalPrice')=='undefined')? $service['total_price']: [$http->get('selectedRategroup') => $http->get('totalPrice')]);
                $registrationURL        = wp_registration_url();
                $loginURL               = wp_login_url(site_url() . $_SERVER['REQUEST_URI']);
                $isLoggedIn             = is_user_logged_in();

                $wpRengineGatewayPage           = Option::where('option_name', 'wpRengineGatewayPage')->first();
                if (!isset($wpRengineGatewayPage)) {
                    $wpRengineGatewayPage = "workadu-order-revision";
                } else {
                    $wpRengineGatewayPage = $wpRengineGatewayPage->option_value;
                }

                $wpRengineCash          = Option::where('option_name', 'wpRengineCash')->first();
                if (!isset($wpRengineCash)) {
                    $wpRengineCash = "on";
                    $wpRengineCashMode = "yes";
                } else {
                    $wpRengineCash = $wpRengineCash->option_value;
                    $wpRengineCashMode          = Option::where('option_name', 'wpRengineCashMode')->first()->option_value;
                }
                $wpRengineTermsPage         = Option::where('option_name', 'wpRengineTermsPage')->first();

                $wpRenginePaymentPercent    = Option::where('option_name', 'wpRenginePaymentPercent')->first()->option_value;

                $page = get_page_by_path($wpRengineGatewayPage);
                //in case of polylang
                if (function_exists('pll_get_post') && (pll_get_post($page->ID))) {
                    $page = pll_get_post($page->ID);
                }
                $payment_gateway    = Option::where('option_name', 'wpRengineGateway')->first()->option_value;
                 $payment_paypal    = Option::where('option_name', 'wpRenginePaypal')->first()->option_value;
                 $payment_stripe    = Option::where('option_name', 'wpRengineStripe')->first()->option_value;
                $payment_alpha    = Option::where('option_name', 'wpRengineAlpha')->first()->option_value;
                 $payment_viva    = Option::where('option_name', 'wpRengineViva')->first()->option_value;
                if ((!$payment_gateway) && (!$payment_viva) && (!$payment_paypal) && (!$payment_alpha) && (!$payment_stripe))  {
                    $payment_gateway = 'false';
                } else {
                    $payment_gateway = 'true';
                }
                $wpRengineRecaptcha = Option::where('option_name', 'wpRengineRecaptcha')->first()->option_value;
                if ($this->version == 'v2') {
                    $view = '@WPRengine/front/car-and-driver-details-v2.twig';
                } else {
                    $view = '@WPRengine/front/car-and-driver-details.twig';
                }
               $inputs = ($http->all());
               $inputs['return_datetime'] = $returnDateTime;
              
               $selectedSlots = explode(",", $http->get('selectedSlots'));
               if ($selectedSlots) {
                $slotsNo = count($selectedSlots);
               } else {
                $slotsNo = false;
               }
                return view($view, [
                    'pickupDateReadable'     => $pickupDateReadable,
                    'returnDateReadable'     => $returnDateReadable,
                    'pickupTime'             => $pickupTime,
                    'returnTime'             => $returnTime,
                    'pickupParking'          => $pickupParking,
                    'returnParking'          => $returnParking,
                    'service'                => $service,
                    'slotsNo'                => $slotsNo,  
                    'selectedSlots'          => $http->get('selectedSlots'),
                    'selectedDate'           => $http->get('selectedDate'),
                    'selectedTime'           => $http->get('selectedTime'),
                    'selectedRategroup'       => $http->get('selectedRategroup'),
                    'paxReadable'            => $paxReadable,
                    'adults'                 => $adults, 
                    'children'               => $children,
                    'infants'                => $infants,
                    'cash'                   => $wpRengineCash,
                    'cash_mode'              => $wpRengineCashMode,
                    'cash_icon'              => Helper::assetUrl('/img/email-mono.png'),
                    'card_icon'              => Helper::assetUrl('/img/card-mono.png'),
                    'tr_notes'               => __( 'notes', 'wordpress-rengine' ),
                    'tr_includes'            => __( 'includes', 'wordpress-rengine' ),
                    'tr_excludes'            => __( 'excludes', 'wordpress-rengine' ),
                    'tr_selected_services'   => __( 'selected_services', 'wordpress-rengine' ),
                    'tr_comment_placeholder' => __( 'comment_placeholder', 'wordpress-rengine' ),
                    'orderRevisionURL'       => get_permalink($page),
                    'pricingData'            => $pricingData,
                    'timeDifferenceReadable' => $timeDifferenceReadable,
                    'totalDays'              => $totalDays,
                    'registrationURL'        => $registrationURL,
                    'loginURL'               => $loginURL,
                    'type'                   => $type,
                    'isLoggedIn'             => $isLoggedIn,
                    'inputs'                 => $inputs,
                    'currentUser'            => wp_get_current_user(),
                    'queryString'            => $_SERVER['QUERY_STRING'],
                    'currency'               => $company->data[0]->currency,
                    'terms'                  => $terms,
                    'termsPage'              => $wpRengineTermsPage->option_value,
                    'tr_loading'             => __( 'loading', 'wordpress-rengine' ),
                    'tr_change_dates'        => __( 'change_dates', 'wordpress-rengine' ),
                    'tr_from'                => __( 'from', 'wordpress-rengine' ),
                    'tr_to'                  => __( 'to', 'wordpress-rengine' ),
                    'tr_for'                 => __( 'for', 'wordpress-rengine' ),
                    'tr_return'              => __( 'return', 'wordpress-rengine' ),
                    'tr_pickup'              => __( 'pickup', 'wordpress-rengine' ),
                    'tr_your_choise'         => __( 'your_choise', 'wordpress-rengine' ),
                    'tr_pricing'             => __( 'pricing', 'wordpress-rengine' ),
                    'tr_extras'              => __( 'extras', 'wordpress-rengine' ),
                    'tr_driver_details'      => __('driver_details', 'wordpress-rengine' ),
                    'tr_login_label'         => __('login_label', 'wordpress-rengine' ),
                    'tr_login_button'        => __('login_button', 'wordpress-rengine' ),
                    'tr_register_button'     => __('register_button', 'wordpress-rengine' ),
                    'tr_full_name'           => __('full_name', 'wordpress-rengine' ),
                    'tr_phone'               => __('phone', 'wordpress-rengine' ),  
                    'tr_flight_number'       => __('flight_number', 'wordpress-rengine' ),
                    'tr_terms_label'         => __('terms_label', 'wordpress-rengine' ),
                    'tr_total_cost'          => __('total_cost', 'wordpress-rengine' ), 
                    'tr_continue_button'     => __('continue_button', 'wordpress-rengine' ),
                    'tr_flat_cost'           => __('flat_cost', 'wordpress-rengine' ),
                    'tr_cash_button'         => __('cash_button', 'wordpress-rengine' ),
                    'tr_cash_button_dscr'   => __('cash_button_dscr', 'wordpress-rengine' ),
                    'tr_no'                 => __('No', 'wordpress-rengine' ),
                    'tr_yes'                => __('Yes', 'wordpress-rengine' ),
                    'tr_default'            => __('default', 'wordpress-rengine' ),
                    'tr_laststep'           => __('last_step', 'wordpress-rengine' ),
                    'tr_paynow'             => __('pay_now', 'wordpress-rengine' ),
                    'tr_paynow_dscr'        => __('pay_now_dscr', 'wordpress-rengine' ),
                    'tr_billAddress'        => __('bill_address', 'wordpress-rengine' ),
                    'tr_billCity'           => __('bill_city', 'wordpress-rengine' ),
                    'tr_billZip'            => __('bill_zip', 'wordpress-rengine' ),
                    'tr_billState'          => __('bill_state', 'wordpress-rengine' ),
                    'tr_billCountry'         => __('bill_country', 'wordpress-rengine' ),
                    'tr_payment_methods'     => __('payment_methods', 'wordpress-rengine' ),
                    'paymenetPercent'       => $wpRenginePaymentPercent,
                    'payment_gateway'       => $payment_gateway,
                    'recapcha'              => $wpRengineRecaptcha,
                    'payment_viva'          => $payment_viva,
                    'payment_alpha'         => $payment_alpha, 
                    'payment_paypal'        => $payment_paypal,
                    'payment_stripe'        => $payment_stripe,
                    'paypal'                => Helper::assetUrl('/img/paypal.png'),
                    'stripe'                => Helper::assetUrl('/img/stripe.png'),
                    'payzen'                => Helper::assetUrl('/img/payzen.png'),
                    'viva'                  => Helper::assetUrl('/img/vivapayments.png'),
                    'alpha'                 => Helper::assetUrl('/img/alpha-logo.png'),
                    'cash_img'                  => Helper::assetUrl('/img/cashondelivery.png'),
                ]);
            }else {
                return [];
            }
        } else {
             return [];
        }
    }

    public function wpRengineOrderRevisionShortcode(Http $http)
    {
        $requestData = $this->transformRequestData($http, $this->version);

         if ($http->get('pickup_range') != '') {
            if ( $http->get('selectedTime') == ''){
                $DateTimeArr = explode('to', $http->get('pickup_range'));
                $pickupDateTime = trim($DateTimeArr[0]);
                $returnDateTime = trim((isset($DateTimeArr[1]))? $DateTimeArr[1] : $DateTimeArr[0]);

                 if (isset($pickupDateTime)) {
                        $pickupDateTimeArr                          = explode(' ', $pickupDateTime);

                        $requestData['availability']['start_date'] = (new Carbon($pickupDateTimeArr[0]))->toDateString();
                        $requestData['availability']['start_time'] = ($http->get('pickup_time') != '' )? $http->get('pickup_time') : ((isset($pickupDateTimeArr[1]))? $pickupDateTimeArr[1] : '12:00') ;
                        $pickupDateTime = $requestData['availability']['start_date'] . ' '.  $requestData['availability']['start_time'];
                    }
                    if (isset($returnDateTime)) {
                        $returnDateTimeArr                          = explode(' ', $returnDateTime);
                        $requestData['availability']['end_date'] = (new Carbon($returnDateTimeArr[0]))->toDateString();
                        $requestData['availability']['end_time'] = ($http->get('return_time') != '') ? $http->get('return_time') : ($http->get('pickup_time') != '' )?  $http->get('pickup_time'): ((isset($returnDateTimeArr[1]))? $returnDateTimeArr[1] : '12:00') ;
                        $returnDateTime = $requestData['availability']['end_date'] . ' '.  $requestData['availability']['end_time'];
                    }
            } else {
                $requestData['availability']['start_date'] = $http->get('selectedDate');
                $requestData['availability']['start_time']  = $http->get('selectedTime');
                $requestData['availability']['end_date']  = $http->get('selectedDate');
                $requestData['availability']['end_time']  = $http->get('selectedEndTime');
                $pickupDateTime = $http->get('selectedDate').' '.$http->get('selectedTime');
                $returnDateTime = $http->get('selectedDate').' '.$http->get('selectedEndTime');
                $requestData['availability']['start'] = $http->get('selectedDate');
                $requestData['availability']['end'] = $http->get('selectedDate');
            }

            // $requestData['availability']['start_date'] = 
        } else {
            if ($http->get('pickup_date') != '') {
                 $pickupDateTime = $http->get('pickup_date') .' '.$http->get('pickup_time');
            } else {
                if ($http->get('pickup_datetime') != '') {
                    $pickupDateTime = $http->get('pickup_datetime');
                }
            }

            if ($http->get('return_date') != '') {
                 $returnDateTime = $http->get('return_date') .' '.$http->get('return_time');
            } else {
                if ($http->get('return_datetime') != '') {
                    $returnDateTime = $http->get('return_datetime');
                }
            }
            if (($pickupDateTime != null) &&  ($returnDateTime != null)) {
                $requestData['availability']['start_date'] = Carbon::createFromFormat('Y-m-d H:i', $pickupDateTime)->format('Y-m-d');
                $requestData['availability']['start_time']  = Carbon::createFromFormat('Y-m-d H:i', $pickupDateTime)->format('H:i');
                $requestData['availability']['end_date']  = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime)->format('Y-m-d');
                $requestData['availability']['end_time']  = Carbon::createFromFormat('Y-m-d H:i', $returnDateTime)->format('H:i');
            }
        }
        if (($pickupDateTime != null) &&  ($returnDateTime != null)) {
            $requestData = $requestData['availability'];
            // The developer sends the coordinates as he is obliged to.
            // He shouldn't be obliged to also send a parking name.
            // The parking name should be optional and therefore the rengine server
            // should resolve the coordinates into a street address if a name is not provided
            $wpRenginePaymentPage           = Option::where('option_name', 'wpRenginePaymentPage')->first();
            if (!isset($wpRenginePaymentPage)) {
                $wpRenginePaymentPage = "workadu-payment";
            } else {
                $wpRenginePaymentPage = $wpRenginePaymentPage->option_value;
            }
            $wpRengineCash          = Option::where('option_name', 'wpRengineCash')->first();
            if (!isset($wpRengineCash)) {
                $wpRengineCash = "off";
                $wpRengineCashMode = "no";
            } else {
                $wpRengineCash              = $wpRengineCash->option_value;
                $wpRengineCashMode          = Option::where('option_name', 'wpRengineCashMode')->first()->option_value;
            }

            $page = get_page_by_path($wpRenginePaymentPage);
            //in case of polylang
            if (function_exists('pll_get_post') && (pll_get_post($page->ID))) {
                $page = pll_get_post($page->ID);
            }

            
            $email = filter_var($http->get('email'), FILTER_SANITIZE_EMAIL);
            $requestData['start_location_name'] = $http->get('pickup_parking_name');
            $requestData['end_location_name'] = $http->get('return_parking_name');
            $requestData['extras']              = $http->get('extras');
            $requestData['fullname']            = filter_var ($http->get('fullname'), FILTER_SANITIZE_STRING);
            $requestData['mobile-email']        = filter_var ($http->get('tel') . ' ' . $email, FILTER_SANITIZE_STRING); 
            $requestData['service_id']          = (int)$http->get('service_id');
            $requestData['send-notifications']  = '1';
            if ($http->get('cashProcess') == 1) {
                $requestData['send-confirmation']   = $wpRengineCashMode;
            } else {
                $requestData['send-confirmation']   = 0;
            }
            
            $requestData['cash']                = $http->get('cashProcess');
            $requestData['comment']             = $http->get('flight-number');
            $requestData['selected_slots']      = $http->get('selectedSlots');
            $requestData['slot_start_time']     = $http->get('selectedTime');
            $requestData['slot_end_time']       = $http->get('selectedEndTime');
            $requestData['rategroup_id']        = (int)$http->get('selectedRategroup');
            $requestData['selected_slots']      = json_encode(explode(',', $http->get('selectedSlots')));
            $requestData['rate_type']           = $http->get('rate_type');
            $requestData['marketplace']         = false;
            // dd($requestData);
            try {
                $response    = $this->client->request('POST', 'orders', ['form_params' => $requestData]);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                if ($e->hasResponse()) {
                    $respone = $e->getResponse();
                    $body = $e->getResponseBodySummary($respone);
                    
                }
            }
            $order                              = json_decode($response->getBody(), true);
            $order                              = $order['data'];
            $returnUrl                          = get_permalink($page). '?order_id=' . $order['id'];
            $cancelUrl                          = $returnUrl . '&cancel=1';
            $currencyISO                        = $this->company->data[0]->currency_iso;
            $gateway                            = filter_var ($http->get('payMethod')) ;
            $wpRenginePaymentPercent            = Option::where('option_name', 'wpRenginePaymentPercent')->first()->option_value;
            if ($http->get('cashProcess') == 0) {
                if ($gateway == 'paypal') {
                    // ------------------------- PAYMENT GATEWAY MODE --------------------------------
                    // Use the PaypalController like this
                    $paypal      = new PaypalController();

                    if ($this->PaypalApiCurrencySupport($currencyISO)) {
                    $currency           = $currencyISO;
                        if (($wpRenginePaymentPercent >0) && ($wpRenginePaymentPercent < 100)) {
                            $totalCost          = number_format((float)($order['total_cost']*$wpRenginePaymentPercent/100), 2);
                        } else {
                            $totalCost          = $order['total_cost'];
                        }

                        $exchangeMessage    = '';
                    } else {
                        //Paypal REST API doesnot support all currencies see https://developer.paypal.com/webapps/developer/docs/integration/direct/rest-api-payment-country-currency-support/
                        // $XML=simplexml_load_file("https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22EUR".$currencyISO."%22)&env=store://datatables.org/alltableswithkeys");

                        try {
                            $XML=simplexml_load_file("https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22EUR".$currencyISO."%22)&env=store://datatables.org/alltableswithkeys");
                        } catch (Exception $e) {
                            $caught = true;
                        }

                        $rate               = (float)$XML->results->rate->Rate;
                        // dd( $XML);
                        $currency           = 'EUR';
                        $totalCost          = $order['total_cost']/$rate;
                        $exchangeMessage    = __('exchange_message', 'wordpress-rengine' );
                    }

                    $paymentData = $paypal->makePaymentUsingPayPal(
                        $totalCost,
                        $currency,
                        'A payment for booking #' . $order['reference'],
                        [
                            [
                                'name'     => 'A payment for booking #' . $order['reference'],
                                'quantity' => 1,
                                'price'    => $totalCost,
                            ],
                        ],
                        $returnUrl,
                        $cancelUrl
                    );
                } elseif ($gateway == 'viva') {
                    // ------------------------- VIVA PAYMENTS  GATEWAY MODE --------------------------------
                    $mode           = Option::where('option_name', 'wpRengineVivaMode')->first()->option_value;
                    $merchanndid    = Option::where('option_name', 'wpRengineVivaClientId')->first()->option_value;
                    $apikey         = Option::where('option_name', 'wpRengineVivaAPI')->first()->option_value;
                    $fullname       = $requestData['fullname'];
                    $phone          = filter_var ($http->get('tel'), FILTER_SANITIZE_STRING);
                    $viva      = new VivaPaymentsController();
                    if ($this->PaypalApiCurrencySupport($currencyISO)) {
                        $currency           = $currencyISO;
                        if (($wpRenginePaymentPercent >0) && ($wpRenginePaymentPercent < 100)) {
                            $totalCost          = number_format((float)($order['total_cost']*$wpRenginePaymentPercent/100), 2);
                        } else {
                            $totalCost          = $order['total_cost'];
                        }

                        $exchangeMessage    = '';
                    } else {
                        //Paypal REST API doesnot support all currencies see https://developer.paypal.com/webapps/developer/docs/integration/direct/rest-api-payment-country-currency-support/
                        $XML=simplexml_load_file("https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22EUR".$currencyISO."%22)&env=store://datatables.org/alltableswithkeys");
                        $rate               = (float)$XML->results->rate->Rate;
                        $currency           = 'EUR';
                        $totalCost          = $totalCost/$rate;
                        $exchangeMessage    = __('exchange_message', 'wordpress-rengine' );
                    }
                    
                    $paymentData = $viva->createPayment($mode, $merchanndid, $apikey, $totalCost, $fullname, $email, $phone, $order);
                } elseif ($gateway == 'alpha') {
                    // ------------------------- VIVA PAYMENTS  GATEWAY MODE --------------------------------
                    $mode           = Option::where('option_name', 'wpRengineAlphaMode')->first()->option_value;
                    $merchanndid    = Option::where('option_name', 'wpRengineAlphaClientId')->first()->option_value;
                    $apikey         = Option::where('option_name', 'wpRengineAlphaSecret')->first()->option_value;
                    $fullname       = $requestData['fullname'];
                    $phone          = filter_var ($http->get('tel'), FILTER_SANITIZE_STRING);
                    $currencyISO    = $order['currency_iso'];
                    if (($wpRenginePaymentPercent >0) && ($wpRenginePaymentPercent < 100)) {
                        $totalCost          = number_format((float)($order['total_cost']*$wpRenginePaymentPercent/100), 2);
                    } else {
                        $totalCost          = $order['total_cost'];
                    }
                    $form_data_array[1]  = $merchanndid;                    //Req
                    $form_data_array[4]  = $order['id'];               //Req
                    if ($mode == 'live') {
                        $form_data_array[6]  = $totalCost;              //Req
                    } else {
                        $form_data_array[6]  = 0.10;
                    }
                    $form_data_array[7]  = $currencyISO;            //Req
                    $form_data_array[8]  = $email; 
                    $form_data_array[11]  = filter_var ($http->get('billCountry'));
                    // $form_data_array[12]  = filter_var ($http->get('billState'));
                    $form_data_array[13]  = filter_var ($http->get('billZip'));
                    $form_data_array[14]  = filter_var ($http->get('billCity'));
                    $form_data_array[15]  = filter_var ($http->get('billAddress')) ;    //Req
                         //Req
                    $form_data_array2   = $form_data_array;
                    $form_data_array[33] = $returnUrl.'&gateway=alpha';           //Req
                    $form_data_array[34] = $cancelUrl.'&gateway=alpha';    
                    $form_data_array[40] =  $apikey ;                //Req
                    $form_data = implode("", $form_data_array);
                    $digest = base64_encode(sha1($form_data,true));
                    $form_data_array2[25] = 'auto:MasterPass';  
                    $form_data_array2[33] = $returnUrl.'&gateway=alpha';           //Req
                    $form_data_array2[34] = $cancelUrl.'&gateway=alpha';      
                    $form_data_array2[40] =  $apikey ;                
                    $form_data2 = implode("", $form_data_array2);
                    $digest2 = base64_encode(sha1($form_data2,true));
                    $paymentData = ['digest' =>$digest, 'data' => $form_data_array, 'order' => $order, 'digest2' => $digest2];

                } elseif ($gateway == 'stripe') {
                    $response               = $this->client->request('GET', 'companies/'.$order['company_id']);
                    $company                = json_decode($response->getBody(), true);
                    $company                = $company['data'];
                    $stripeKey              = Option::where('option_name', 'wpRengineStripeClientId')->first()->option_value;
                    if (($wpRenginePaymentPercent >0) && ($wpRenginePaymentPercent < 100)) {
                        $totalCost          = number_format((float)($order['total_cost']*$wpRenginePaymentPercent/100), 2);
                    } else {
                        $totalCost          = $order['total_cost'];
                    }

                    $stripeCost             = (int)($totalCost*100);

                } elseif ($gateway == 'payzen') {

                    $payzenClient   = new \LyraNetwork\Client(); 
                    $_username      = Option::where('option_name', 'wpRenginePayzenUsername')->first()->option_value;
                    $_password      = Option::where('option_name', 'wpRenginePayzenSecret')->first()->option_value;
                    $_publicKey     = $_username.':'.Option::where('option_name', 'wpRenginePayzenClientId')->first()->option_value;
                    $_endpoint      = "https://secure.payzen.eu";
                    $payzenClient->setUsername($_username);           /* username defined in configuration file */
                    $payzenClient->setPassword($_password);           /* password defined in configuration file */
                    $payzenClient->setPublicKey($_publicKey);         /* key defined in configuration file */
                    $payzenClient->setEndpoint($_endpoint);           /* REST API endpoint defined in configuration file */

                    /**
                     * I create a formToken
                     */
                    if (($wpRenginePaymentPercent >0) && ($wpRenginePaymentPercent < 100)) {
                        $totalCost          = number_format((float)($order['total_cost']*$wpRenginePaymentPercent/100), 2);
                    } else {
                        $totalCost          = $order['total_cost'];
                    }
                    $store = array("amount" => (int)($totalCost*100), "currency" => $currencyISO, "orderId" => "Booking #".$order['reference'], "customer" => ["email" => $email, "reference" => $requestData['fullname']]);
                    $response = $payzenClient->post("Charge/CreatePayment", $store);
                    /* I check if there is some errors */
                    if ($response['status'] != 'SUCCESS') {
                        /* an error occurs, I throw an exception */
                        $error = $response['answer'];
                        throw new Exception("error " . $error['errorCode'] . ": " . $error['errorMessage'] );
                    }
                    /* everything is fine, I extract the formToken */
                    $formToken = $response["answer"]["formToken"];
                    $response               = $this->client->request('GET', 'companies/'.$order['company_id']);
                    $company                = json_decode($response->getBody(), true);
                    $company                = $company['data'];
                    $payzenKey              = $_publicKey;
                    $payzenCost             = (int)($order['total_cost']*100);
                    $payzenUrl              = $payzenClient->getEndPoint().'/static/js/krypton-client/V3/stable/kr.min.js?formToken='.$formToken;
                } else {

                    $response               = $this->client->request('GET', 'companies/'.$order['company_id']);
                    $company                = json_decode($response->getBody(), true);
                    $company                = $company['data'];
                    // $stripeKey               = Option::where('option_name', 'wpRengineStripeClientId')->first()->option_value;
                    // $stripeCost              = (int)($order['total_cost']*100);
                }
            } else {
                // CASH ON DELIVERY 
                $response               = $this->client->request('GET', 'companies/'.$order['company_id']);
                $company                = json_decode($response->getBody(), true);
                $company                = $company['data'];
                $gateway = 'cash';
            }


            return view('@WPRengine/front/order-revision.twig', [
                'order'             => $order,
                'paymentData'       => $paymentData,
                'tr_action_required'=> __('action_required', 'wordpress-rengine' ),
                'tr_reference_message'=> __('reference_message', 'wordpress-rengine' ),
                'tr_paypal_message'=> __('paypal_message', 'wordpress-rengine' ),
                'tr_button_message'=> __('button_message', 'wordpress-rengine' ),
                'tr_payment_button'=> __('payment_button', 'wordpress-rengine' ),
                'paypal'           => Helper::assetUrl('/img/paypal.png'),
                'stripe'           => Helper::assetUrl('/img/stripe.png'),
                'payzen'           => Helper::assetUrl('/img/payzen.png'),
                'viva'             => Helper::assetUrl('/img/vivapayments.png'),
                'alpha'            => Helper::assetUrl('/img/alpha-logo.png'),
                'cash'             => Helper::assetUrl('/img/cashondelivery.png'),
                'networks'         => Helper::assetUrl('/img/networks.png'),
                 'masterpass'      => Helper::assetUrl('/img/masterpass.jpg'),
                'gateway'          => $gateway,
                'stripe_key'       => $stripeKey,
                'payzen_key'       => $payzenKey,
                'payzen_url'       => $payzenUrl,
                'company'          => $company,
                'exchangeMessage'  => $exchangeMessage,
                'currencyISO'      => $currencyISO,
                'stripeCost'       => $stripeCost,
                'payzenCost'       => $payzenCost,
                'returnUrl'        => $returnUrl,
                'email'            => $email,
                'timezone'         => '',
                'mode'             => $mode,
            ]);
        } else {
            return [];
        }
    }

    public function wpRenginePaymentShortcode(Http $http)
    {   
        $wpRenginePaymentPercent            = Option::where('option_name', 'wpRenginePaymentPercent')->first()->option_value;

        if (($http->get('order_id') != null) || ($http->get('order') != null)) {
            if ($http->get('gateway') == "stripe") {
                $stripe = array(
                  "secret_key"      => Option::where('option_name', 'wpRengineStripeSecret')->first()->option_value,
                  "publishable_key" => Option::where('option_name', 'wpRengineStripeClientId')->first()->option_value
                );

                 \Stripe\Stripe::setApiKey($stripe['secret_key']);
                $token  = $http->get('stripeToken');
                
                $response               = $this->client->request('GET', 'orders/'.$http->get('order_id'));
                $order                  = json_decode($response->getBody(), true);
                $order                  = $order['data'];

                if (! $order) {
                    return $this->cancelBooking($order);
                }

                $customer = \Stripe\Customer::create(array(
                  'email' => $order[0]['customer']['email'],
                  'source'  => $token
                ));

                if (($wpRenginePaymentPercent >0) && ($wpRenginePaymentPercent < 100)) {
                        $totalCost          = number_format((float)($order[0]['total_cost']*$wpRenginePaymentPercent/100), 2);
                } else {
                        $totalCost          = $order[0]['total_cost'];
                }
                $stripeCost             = (int)($totalCost*100);
               
                $currencyISO            = $this->company->data[0]->currency_iso;
                if (!$currencyISO) {
                    $currencyISO == 'EUR';
                    $XML=simplexml_load_file("https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22EUR".$currencyISO."%22)&env=store://datatables.org/alltableswithkeys");
                    $rate               = (float)$XML->results->rate->Rate;
                    $currency           = 'EUR';
                    $stripeCost         = (int)($stripeCost/$rate*100);
                }
                            
                if ($order[0]['status'] != 'CANCELLED') {
                    $charge = \Stripe\Charge::create(array(
                      'customer' => $customer->id,
                      'amount'   => $stripeCost,
                      'currency' => $currencyISO
                    ));

                    $paymentId      = $token;
                    $orderReference = $order[0]['reference'];
                    $renginePayment = $this->client->request('POST', 'payments', ['form_params' => ['order_id' => $http->get('order_id'), 'payment_gateway' => 'stripe', 'payment_reference' => $charge->id, 'deposit'=>    $wpRenginePaymentPercent.'%', 'send-notifications' => 1]]);
                    return view('@WPRengine/front/payment.twig', [
                        'resultPayPal'          => $paymentId,
                        'renginePayment'        => $renginePayment,
                        'reference'             => $orderReference,
                        'tr_reference_message'=> __('reference_message', 'wordpress-rengine' ),
                        'tr_payment_success'    => __('payment_success', 'wordpress-rengine' ),
                        'tr_success'            => __('success', 'wordpress-rengine' ),
                        'tr_success_message'    => __('success_message', 'wordpress-rengine' ),
                    ]);
                } else {
                    return $this->cancelBooking($order);
                }
            } elseif ($http->get('gateway') == "payzen") {
                $payzenClient   = new \LyraNetwork\Client(); 
                $_username      = Option::where('option_name', 'wpRenginePayzenUsername')->first()->option_value;
                $_password      = Option::where('option_name', 'wpRenginePayzenSecret')->first()->option_value;
                $_publicKey     = $_username.':'.Option::where('option_name', 'wpRenginePayzenClientId')->first()->option_value;
                $_endpoint      = "https://secure.payzen.eu";
                $_shaKey        = Option::where('option_name', 'wpRenginePayzenReturnKey')->first()->option_value;
                $payzenClient->setUsername($_username);           /* username defined in configuration file */
                $payzenClient->setPassword($_password);           /* password defined in configuration file */
                $payzenClient->setPublicKey($_publicKey);         /* key defined in configuration file */
                $payzenClient->setEndpoint($_endpoint);           /* REST API endpoint defined in configuration file */

                /* No POST data ? paid page in not called after a payment form */
                if (empty($_POST)) {
                    throw new Exception("no post data received!");
                }
                /* Check the SHA256 value to detect parameter changes */
                $sha256Key = $_shaKey; /* defined in keys.php file */
                $sha256String =  $_POST["kr_billingTransaction"] . ":" . $sha256Key;
                $validSha256 = hash("sha256", $sha256String);
                if ($validSha256 != $_POST['kr_sha256']) {
                    //something wrong, probably a fraud ....
                    throw new Exception("SHA256 error");
                }
                /** 
                 * To check if the payment has been paid in a secure way
                 * I retrieve the billingTransaction values from Charge/Get web-service
                 */
                $store = array("id" => $_POST["kr_billingTransaction"]);
                $response = $payzenClient->post('Charge/Get', $store);
                /* Check if there is no errors */
                if ($response['status'] != 'SUCCESS') {
                    $error = $response['answer'];
                    throw new Exception("error " . $error['errorCode'] . ": " . $error['errorMessage'] );
                }
                
                /* Now I have the billingTransaction object */
                $billingTransaction = $response['answer'];
                /* I check if it's really paid */
                if ($billingTransaction['status'] == 'PAID') {
                    $response               = $this->client->request('GET', 'orders/'.$http->get('order_id'));
                    $order                  = json_decode($response->getBody(), true);
                    $order                  = $order['data'];
                    $orderReference = $order[0]['reference'];
                    $renginePayment = $this->client->request('POST', 'payments', ['form_params' => ['order_id' => $http->get('order_id')]]);
                    return view('@WPRengine/front/payment.twig', [
                        'resultPayPal'          => $billingTransaction['id'],
                        'renginePayment'        => $renginePayment,
                        'reference'             => $orderReference,
                        'tr_reference_message'=> __('reference_message', 'wordpress-rengine' ),
                        'tr_payment_success'    => __('payment_success', 'wordpress-rengine' ),
                        'tr_success'            => __('success', 'wordpress-rengine' ),
                        'tr_success_message'    => __('success_message', 'wordpress-rengine' ),
                    ]);

                } else {
                   return $this->cancelBooking($order);
                }
            } elseif ($http->get('gateway') == "viva") {
                $merchanndid    = Option::where('option_name', 'wpRengineVivaClientId')->first()->option_value;
                $apikey         = Option::where('option_name', 'wpRengineVivaAPI')->first()->option_value;
                $mode           = Option::where('option_name', 'wpRengineVivaMode')->first()->option_value;
                if (!is_null($http->get('cancel')) && $http->get('cancel') == '1')
                {
                     $viva       = new VivaPaymentsController();
                    $return     = $viva->getOrder ($mode, $http->get('order'), $merchanndid, $apikey);
                    // $renginePayment = $this->client->request('POST', 'payments', ['form_params' => ['order_id' => $http->get('order_id')]]);
                    try {
                         $this->client->delete('orders/'.$return->MerchantTrns);
                    } catch (RequestException $e) {
                        echo Psr7\str($e->getRequest());
                        if ($e->hasResponse()) {
                            echo Psr7\str($e->getResponse());
                        }
                    }
                    return view('@WPRengine/front/payment.twig', [
                        'resultPayPal' => 'cancelled',
                        'tr_failure_success'    => __('payment_failure', 'wordpress-rengine' ),
                        'tr_failure'            => __('failure', 'wordpress-rengine' ),
                        'tr_failure_message'    => __('failure_message', 'wordpress-rengine' ),
                    ]);
                } else {
                    $viva       = new VivaPaymentsController();
                    $return     = $viva->getOrder ($mode, $http->get('order'), $merchanndid, $apikey);
                    if($return) {
                        $response       = $this->client->request('GET', 'orders/'.$return->MerchantTrns);
                        $order          = json_decode($response->getBody(), true);
                        $order          = $order['data'];
                        if ($order[0]['status'] != 'CANCELLED') {
                            $renginePayment = $this->client->request('POST', 'payments', ['form_params' => ['order_id' => $return->MerchantTrns, 'payment_gateway' => 'viva', 'payment_reference' => $return->OrderCode, 'deposit'=>    $wpRenginePaymentPercent.'%', 'send-notifications' => 1]]);
                            return view('@WPRengine/front/payment.twig', [
                                'resultPayPal'          => $paymentId,
                                'renginePayment'        => $renginePayment,
                                'reference'             => $order[0]['reference'],
                                'tr_reference_message'=> __('reference_message', 'wordpress-rengine' ),
                                'tr_payment_success'    => __('payment_success', 'wordpress-rengine' ),
                                'tr_success'            => __('success', 'wordpress-rengine' ),
                                'tr_success_message'    => __('success_message', 'wordpress-rengine' ),
                            ]);
                        } else {  // case that the time of 10 minutes has passed so the automatic scheduler has cancel the booking
                            return $this->cancelBooking($order);
                        }
                    }
                }
              } elseif ($http->get('gateway') == "alpha") {
                $merchanndid    = Option::where('option_name', 'wpRengineAlphaClientId')->first()->option_value;
                $apikey         = Option::where('option_name', 'wpRengineAlphaSecret')->first()->option_value;
                $mode           = Option::where('option_name', 'wpRengineAlphaMode')->first()->option_value;
                if (!is_null($http->get('cancel')) && $http->get('cancel') == '1')
                {
                    
                    $return = $request->all();
                    // $renginePayment = $this->client->request('POST', 'payments', ['form_params' => ['order_id' => $http->get('order_id')]]);
                    try {
                         $this->client->delete('orders/'.$return['orderid']);
                    } catch (RequestException $e) {
                        echo Psr7\str($e->getRequest());
                        if ($e->hasResponse()) {
                            echo Psr7\str($e->getResponse());
                        }
                    }
                    return view('@WPRengine/front/payment.twig', [
                        'resultPayPal' => 'cancelled',
                        'tr_failure_success'    => __('payment_failure', 'wordpress-rengine' ),
                        'tr_failure'            => __('failure', 'wordpress-rengine' ),
                        'tr_failure_message'    => __('failure_message', 'wordpress-rengine' ),
                    ]);
                } else {                    
                    $return = $http->all();
                    if (isset($return['digest'])) {
                        $post_data[0] = $return['mid'];
                        $post_data[1] = $return['orderid'];
                        $post_data[2] = $return['status'];
                        $post_data[3] = $return['orderAmount'];
                        $post_data[4] = $return['currency'];
                        $post_data[5] = $return['paymentTotal'];
                        // $post_data[6] = $return['message'];
                        $post_data[7] = $return['riskScore'];
                        $post_data[8] = $return['payMethod'];
                        $post_data[9] = $return['txId'];
                        $post_data[10] = $return['paymentRef'];
                        $post_data[11] = $apikey;
                        $post_data = implode("", $post_data);
                        $digest = base64_encode(sha1($post_data, true));
                    } else {
                        return $this->cancelBooking($return['orderid']);
                    }
                    if ($return['digest'] == $digest) {
                        $response       = $this->client->request('GET', 'orders/'.$return['orderid']);
                        $order          = json_decode($response->getBody(), true);
                        $order          = $order['data'];

                        if ($order[0]['status'] != 'CANCELLED') {

                             try {
                                  $renginePayment = $this->client->request('POST', 'payments', ['form_params' => ['order_id' => $return['orderid'], 'payment_gateway' => 'alpha', 'payment_reference' => $return['paymentRef'], 'deposit'=>    $return['paymentTotal'], 'send-notifications' => 1]]);
                            } catch (RequestException $e) {
                                echo Psr7\str($e->getRequest());
                                if ($e->hasResponse()) {
                                    echo Psr7\str($e->getResponse());
                                }
                            }
                            if (!$renginePayment) {
                                return $this->cancelBooking($order);
                            }
                            return view('@WPRengine/front/payment.twig', [
                                'resultPayPal'          => $paymentId,
                                'renginePayment'        => $renginePayment,
                                'reference'             => $order[0]['reference'],
                                'tr_reference_message'=> __('reference_message', 'wordpress-rengine' ),
                                'tr_payment_success'    => __('payment_success', 'wordpress-rengine' ),
                                'tr_success'            => __('success', 'wordpress-rengine' ),
                                'tr_success_message'    => __('success_message', 'wordpress-rengine' ),
                            ]);
                        } else {  // case that the time of 10 minutes has passed so the automatic scheduler has cancel the booking
                            return $this->cancelBooking($order);
                        }
                    }
                }


            } elseif ($http->get('gateway') == "cash") {
                $paymentId              = '';
                $renginePayment         = '';
                $response               = $this->client->request('GET', 'orders/'.$http->get('order_id'));
                $order                  = json_decode($response->getBody(), true);
                $order                  = $order['data'];
                $orderReference = $order[0]['reference'];
                return view('@WPRengine/front/payment.twig', [
                    'resultPayPal'          => $paymentId,
                    'renginePayment'        => $renginePayment,
                    'reference'             => $orderReference,
                    'tr_reference_message'=> __('reference_message', 'wordpress-rengine' ),
                    'tr_payment_success'    => __('payment_success', 'wordpress-rengine' ),
                    'tr_success'            => __('success', 'wordpress-rengine' ),
                    'tr_success_message'    => __('success_message', 'wordpress-rengine' ),
                ]);
            } else  {
                if (!is_null($http->get('cancel')) && $http->get('cancel') == '1')
                {
                    // $renginePayment = $this->client->request('POST', 'payments', ['form_params' => ['order_id' => $http->get('order_id')]]);
                    try {
                         $this->client->delete('orders/'.$http->get('order_id'));
                    } catch (RequestException $e) {
                        echo Psr7\str($e->getRequest());
                        if ($e->hasResponse()) {
                            echo Psr7\str($e->getResponse());
                        }
                    }
                    return view('@WPRengine/front/payment.twig', [
                        'resultPayPal' => 'cancelled',
                        'tr_failure_success'    => __('payment_failure', 'wordpress-rengine' ),
                        'tr_failure'            => __('failure', 'wordpress-rengine' ),
                        'tr_failure_message'    => __('failure_message', 'wordpress-rengine' ),
                    ]);
                } else {
                    $paymentId      = $http->get('paymentId');
                    $payerId        = $http->get('PayerID');
                    $paypal         = new PaypalController();
                    $response       = $this->client->request('GET', 'orders/'.$http->get('order_id'));
                    $order          = json_decode($response->getBody(), true);
                    $order          = $order['data'];
                    if ($order[0]['status'] != 'CANCELLED') {
                        $resultPayPal   = $paypal->executePayment($paymentId, $payerId);
                        $itemDscr       = $resultPayPal->transactions[0]->description;
                        $orderReference = trim(explode('#', $itemDscr)[1]);
                        $renginePayment = $this->client->request('POST', 'payments', ['form_params' => ['order_id' => $http->get('order_id'), 'payment_gateway' => 'paypal', 'payment_reference' => $paymentId, 'deposit'=>     $wpRenginePaymentPercent. '%', 'send-notifications' => 1]]);

                        return view('@WPRengine/front/payment.twig', [
                            'resultPayPal'          => $resultPayPal,
                            'renginePayment'        => $renginePayment,
                            'reference'             => $orderReference,
                            'tr_reference_message'=> __('reference_message', 'wordpress-rengine' ),
                            'tr_payment_success'    => __('payment_success', 'wordpress-rengine' ),
                            'tr_success'            => __('success', 'wordpress-rengine' ),
                            'tr_success_message'    => __('success_message', 'wordpress-rengine' ),
                        ]);
                    } else {  // case that the time of 10 minutes has passed so the automatic scheduler has cancel the booking
                        return $this->cancelBooking($order);
                    }
                    
                }
            }
        } else {
            return [];
        }
    }

    public function cancelBooking($orderArr) {
        try {
            $response = $this->client->delete('orders/'.$orderArr[0]['id']);
        } catch (RequestException $e) {
            echo Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                echo Psr7\str($e->getResponse());
            }
        }
        
        return view('@WPRengine/front/payment.twig', [
                'resultPayPal' => 'cancelled',
                'tr_failure_success'    => __('payment_failure', 'wordpress-rengine' ),
                'tr_failure'            => __('failure', 'wordpress-rengine' ),
                'tr_failure_message'    => __('failure_message', 'wordpress-rengine' ),
        ]);
    }

    public function currencySymbolToIso($symbol) {
        $currency['€']          = 'EUR';
        $currency['$']          = 'USD';
        $currency['£']          = 'GBP';
        $currency['₺']          = 'TRY';
        $currency['S/.']        = 'PEN';
        $currency['A$']         = 'AUD';
        $currency['лв']         = 'BGL';
        $currency['₦']          = 'NGN';
        $currency['₹']          = 'INR';
        $currency['د.إ']        = 'AED';
        $currency['Mex$']       = 'MXN';
        $currency['kn']         = 'HRK';

        
        if (isset($currency[$symbol])) {
            return $currency[$symbol];
        } else {
            return false;
        }
    }

    public function PaypalApiCurrencySupport($currency) {
        $supportedCurrencies = explode(",","AUD,BRL,CAD,CZK,DKK,EUR,HKD,HUF,ILS,JPY,MYR,MXN,TWD,NZD,NOK,PHP,PLN,GBP,RUB,SGD,SEK,CHF,THB,USD");
        if (in_array($currency, $supportedCurrencies)) {
            return true;
        } else {
            return false;
        }
    }
}