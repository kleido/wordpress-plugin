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

	private $base_uri = 'https://secure.reservationengine.net/rest/api/';

	// private $base_uri = 'http://xiteserver/secure.reservationengine.net/fast/public/api/';
	 // private $base_uri = 'http://localhost/reservationengine-fast/public/api/';
	private $timeout = 180;

	private $auth;

	private $client;

	public function __construct()
	{
		$this->setAuth();
		$this->client = new Client([
			'base_uri' => $this->base_uri,
			'timeout'  => $this->timeout,
			'auth'     => $this->auth,
		]);
		add_action('wp_enqueue_scripts', [ & $this, 'enqueueAssets']);
	}

	public function enqueueAssets()
	{
		wp_enqueue_script("jquery");
		wp_enqueue_style('bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
		if (!wp_script_is( 'bootstrap-traveler') && !wp_script_is( 'bootstrap.js')) {
			wp_enqueue_script('bootstrap-rengine', '//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
		}
		wp_enqueue_style('wp-rengine-front-css', Helper::assetUrl('/css/wp-rengine-front.css'), [], false, 'all');
		wp_enqueue_script('wp-rengine-front-js', Helper::assetUrl('/js/custom-front.js'), [], false, true);
	}

	private function setAuth()
	{
		$wpRengineUser     = Option::where('option_name', 'wpRengineUser')->first();
		$wpRenginePassword = Option::where('option_name', 'wpRenginePassword')->first();
		if (($wpRengineUser) && ($wpRenginePassword)) {
			$this->auth        = [$wpRengineUser->option_value, $wpRenginePassword->option_value];
		}
	}

	public function showReservationForm($group = '', $locations = '', $type = '')	{

		$parkings   	= Option::where('option_name', 'wpRengineParkings')->first();
		// dd('unable to load form');
		if (isset($parkings->option_value)) {
			$parkings_list 	= json_decode($parkings->option_value, true);
		} else {
			$parkings_list = "Please add at least 1 parking";
		}
		$wpRengineCalendarMode    		= Option::where('option_name', 'wpRengineCalendarMode')->first();
		$wpRengineCalendarView			= Option::where('option_name', 'wpRengineCalendarView')->first();
		$wpRengineCalendarStep			= Option::where('option_name', 'wpRengineCalendarStep')->first();
		$wpRengineCalendarLocation		= Option::where('option_name', 'wpRengineCalendarLocation')->first();
		$wpRengineCalendarHoursDisabled	= Option::where('option_name', 'wpRengineCalendarHoursDisabled')->first();
		$wpRengineSlideMode				= Option::where('option_name', 'wpRengineSlideMode')->first();
		$wpRengineLocationsMode			= Option::where('option_name', 'wpRengineLocationsMode')->first();
		$wpRengineCurrentMode			= Option::where('option_name', 'wpRengineCurrentMode')->first();
		$wpRengineBaseCoordinates		= Option::where('option_name', 'base-coordinates')->first();
		$wpRengineBaseRadius			= Option::where('option_name', 'base-radius')->first();

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

		if (isset($wpRengineLocationsMode)) {
			$location_mode = $wpRengineLocationsMode->option_value;
		} else {
			$location_mode = "predefined";
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
		$pluginsURL 					= plugin_dir_url(__DIR__) . '../';
		if (empty($group)) {
			$group = "";
		}
		if (empty($type)) {
			$type= "";
		}
		return view('@WPRengine/front/reservation-form.twig', [
			'parkings'         	=> $parkings_list,
			'pluginsURL'       	=> $pluginsURL,
			'pickup_date_time' 	=> Carbon::now(),
			'return_date_time' 	=> Carbon::now()->addDay(),
			'searchResultsURL' 	=> Post::where('post_name', 'wp-rengine-search-results')->first()->guid,
			'tr_pickup_parking_name' => __( 'pickup_parking_name', 'wordpress-rengine' ),
			'tr_return_parking_name' => __( 'return_parking_name', 'wordpress-rengine' ),
			'tr_pickup_date' 	=> __( 'pickup_date', 'wordpress-rengine' ),
			'tr_return_date' 	=> __( 'return_date', 'wordpress-rengine' ),
			'tr_same_parking' 	=> __( 'same_parking', 'wordpress-rengine' ),
			'tr_search_button' 	=> __( 'search_button', 'wordpress-rengine' ),
			'tr_adults' 		=> __( 'adults', 'wordpress-rengine' ),
			'tr_children' 		=> __( 'children', 'wordpress-rengine' ),
			'tr_infants'	 	=> __( 'infants', 'wordpress-rengine' ),
			'calendar_mode'		=> $debug,
			'calendar_view'		=> $inline,
			'calendar_step'		=> $calendar_step,
			'calendar_hours'	=> $calendar_hours,
			'placement'			=> $placement,
			'slideMode'			=> $slideMode,
			'group_id'			=> $group,
			'type'				=> $type,
			'hideLocations'		=> $locations,
			'location_mode'		=> $location_mode,
			'current_mode'		=> $current_mode,
			'base_coordinates'	=> $base_coordinates,
			'base_radius'		=> $base_radius,
		]);
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

	public function transformRequestData(Http $http)
	{
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
						$offerCost[$offer['offer_id']]['cost'] = ($offer['new_value'] - $offer['old_value']) + $offerCost[$offer['offer_id']]['cost'];
					}
					else
					{
						$offerCost[$offer['offer_id']]['cost'] = ($offer['new_value'] - $offer['old_value']) + $offerCost[$offer['offer_id']]['cost'];
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
		$response           = $this->client->request('GET', 'companies');
		$company 			= json_decode($response->getBody());
		$locale = get_locale();
		if ($locale == "el") {
			$locale = "el_GR";
		}

		$return_date = $http->get('return_datetime');
		if($http->get('type') == "activity") {
			$return_date = $http->get('pickup_datetime');
			$return_date = Carbon::createFromFormat('Y-m-d H:i', $return_date)->addDay()->toDateTimeString();
			$return_date = date('Y-m-d H:i', strtotime($return_date));
		}
		
		setLocale(LC_TIME, $locale.'.utf-8');
		$pickupDateReadable = Carbon::createFromFormat('Y-m-d H:i', $http->get('pickup_datetime'))->formatLocalized('%d %B ');
		$returnDateReadable = Carbon::createFromFormat('Y-m-d H:i', $return_date)->formatLocalized('%d %B');
		$pickupTime         = Carbon::createFromFormat('Y-m-d H:i', $http->get('pickup_datetime'))->format('H:i');
		$returnTime         = Carbon::createFromFormat('Y-m-d H:i', $return_date)->format('H:i');
		$pickup_date_time   = filter_var ($http->get('pickup_datetime'), FILTER_SANITIZE_STRING);
		$return_date_time	= $return_date;
		$parkings           = json_decode(Option::where('option_name', 'wpRengineParkings')->first()->option_value, true);
		$wpRengineButton 	= "BOOK >";
		$group_id			= filter_var ($http->get('group'), FILTER_SANITIZE_STRING);
		$type				= filter_var ($http->get('type'), FILTER_SANITIZE_STRING);
		$hideLocations		= filter_var ($http->get('hideLocations'), FILTER_SANITIZE_STRING);
		$pickupParking 		= filter_var ($http->get('pickup_parking_name'), FILTER_SANITIZE_STRING);
		$returnParking 		= filter_var ($http->get('return_parking_name'), FILTER_SANITIZE_STRING);
		$adults				= filter_var ($http->get('adults'), FILTER_VALIDATE_INT);
		$children			= filter_var ($http->get('child'), FILTER_VALIDATE_INT);
		$infants			= filter_var ($http->get('infants'), FILTER_VALIDATE_INT);
		Carbon::setLocale(get_locale());
		// $timeDifferenceReadable = Carbon::createFromFormat('Y-m-d H:i', $http->get('pickup_datetime'))->diffForHumans(Carbon::createFromFormat('Y-m-d H:i', $return_date), true);
		$dtfrom = Carbon::createFromFormat('Y-m-d H:i', $http->get('pickup_datetime'));
		$dtto 	= Carbon::createFromFormat('Y-m-d H:i', $return_date);
		$littleHandRotations = $dtfrom->diffInHours($dtto);
		$timeDifferenceReadable = (int)$littleHandRotations/24;
		if ($littleHandRotations % 24 >= 1) {
			$timeDifferenceReadable++;
		}
		$totalDays = $timeDifferenceReadable;

		

		if ($timeDifferenceReadable == 1) {
			$timeDifferenceReadable = (string)$timeDifferenceReadable .' day';
		} else {
			$timeDifferenceReadable = (string)$timeDifferenceReadable .' days';
		}
		$requestData            = $this->transformRequestData($http);
		$filters                = ($requestData) ? ['filter' => $requestData] : '';
		$filters['sort'] 		= 'total_price';
		$effectiveURL = '';
		$statusCode   = 0;

 		$response  = $this->client->request('GET', 'services', ['query' => $filters, 
				'on_stats' => function (TransferStats $stats) use (&$effectiveURL, &$statusCode) {
			        $effectiveURL = (string) $stats->getEffectiveUri();
			        if ($stats->hasResponse()) {
			            $statusCode = $stats->getResponse()->getStatusCode();
			        }
					 },
		]);

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

		$wpRengineCalendarMode    		= Option::where('option_name', 'wpRengineCalendarMode')->first();
		$wpRengineCalendarView			= Option::where('option_name', 'wpRengineCalendarView')->first();
		$wpRengineCalendarStep			= Option::where('option_name', 'wpRengineCalendarStep')->first();
		$wpRengineCalendarLocation		= Option::where('option_name', 'wpRengineCalendarLocation')->first();
		$wpRengineCalendarHoursDisabled	= Option::where('option_name', 'wpRengineCalendarHoursDisabled')->first();
		$wpRengineSlideMode				= Option::where('option_name', 'wpRengineSlideMode')->first();
		$wpRengineLocationsMode			= Option::where('option_name', 'wpRengineLocationsMode')->first();
		$wpRengineCurrentMode			= Option::where('option_name', 'wpRengineCurrentMode')->first();
		$wpRengineBaseCoordinates		= Option::where('option_name', 'base-coordinates')->first();
		$wpRengineBaseRadius			= Option::where('option_name', 'base-radius')->first();

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
			$location_mode = "predefined";
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
		return view('@WPRengine/front/search-results.twig', [
			'pickupDateReadable'     => $pickupDateReadable,
			'returnDateReadable'     => $returnDateReadable,
			'pickupTime'             => $pickupTime,
			'returnTime'             => $returnTime,
			'pickupParking'          => $pickupParking,
			'returnParking'          => $returnParking,
			'pickup_date_time' 		 => $pickup_date_time,
			'return_date_time'		 => $return_date_time,
			'timeDifferenceReadable' => $timeDifferenceReadable,
			'paxReadable'			 => $paxReadable,
			'adults'				 => $adults, 
			'children'				 => $children,
			'infants'				 => $infants,
			'tr_adults' 			 => __( 'adults', 'wordpress-rengine' ),
			'tr_children' 			 => __( 'children', 'wordpress-rengine' ),
			'tr_infants'	 		 => __( 'infants', 'wordpress-rengine' ),
			'tr_notes'	 			 => __( 'notes', 'wordpress-rengine' ),
			'tr_includes'	 		 => __( 'includes', 'wordpress-rengine' ),
			'tr_excludes'	 		 => __( 'excludes', 'wordpress-rengine' ),
			'totalDays'				 => $totalDays,
			'services'               => json_decode($response->getBody(), true),
			'detailsURL'             => Post::where('post_name', 'wp-rengine-car-driver-details')->first()->guid,
			'queryString'            => $_SERVER['QUERY_STRING'],
			'currency'				 => $company->data[0]->currency,
			'button'				 => $wpRengineButton,
			'tr_change_dates' 		 => __( 'change_dates', 'wordpress-rengine' ),
			'tr_available_cars' 	 => __( 'available_cars', 'wordpress-rengine' ),
			'tr_available_activities'=> __( 'available_activities', 'wordpress-rengine' ),
			'tr_from' 				 => __( 'from', 'wordpress-rengine' ),
			'tr_to' 				 => __( 'to', 'wordpress-rengine' ),
			'tr_for' 				 => __( 'for', 'wordpress-rengine' ),
			'tr_brands' 			 => __( 'brands', 'wordpress-rengine' ),
			'tr_attributes' 		 => __( 'attributes', 'wordpress-rengine' ),
			'tr_return' 			 => __( 'return', 'wordpress-rengine' ),
			'tr_pickup' 			 => __( 'pickup', 'wordpress-rengine' ),
			'tr_no_results' 		 => __( 'no_results', 'wordpress-rengine' ),
			'tr_button'		 		 => __( 'button', 'wordpress-rengine' ),
			'tr_sold_out'	 		 => __( 'sold_out', 'wordpress-rengine' ),
			'tr_last_booking'		 => __( 'last_booking', 'wordpress-rengine' ),
			'parkings'         		 => json_decode($parkings->option_value, true),
			'pluginsURL'       		 => $pluginsURL,
			'searchResultsURL' 		 => Post::where('post_name', 'wp-rengine-search-results')->first()->guid,
			'tr_pickup_parking_name' => __( 'pickup_parking_name', 'wordpress-rengine' ),
			'tr_return_parking_name' => __( 'return_parking_name', 'wordpress-rengine' ),
			'tr_pickup_date' 		 => __( 'pickup_date', 'wordpress-rengine' ),
			'tr_return_date' 		 => __( 'return_date', 'wordpress-rengine' ),
			'tr_same_parking' 		 => __( 'same_parking', 'wordpress-rengine' ),
			'tr_search_button' 		 => __( 'search_button', 'wordpress-rengine' ),
			'group_id'				 =>	$group_id,
			'type'				 	 =>	$type,
			'calendar_mode'			 =>	$calendar_mode,    		
			'calendar_view'			 =>	$calendar_view,			
			'calendar_step'			 =>	$calendar_step,			
			'placement'				 =>	$placement,		
			'calendar_hours'		 =>	$calendar_hours,	
			'slideMode'				 =>	$slideMode,	
			'location_mode'			 => $location_mode,
			'current_mode'			 => $current_mode,
			'base_coordinates'		 => $base_coordinates,
			'base_radius'			 => $base_radius,
			'hideLocations'			 => $hideLocations,
		]);
	}

	public function wpRengineCarAndDriverDetailsShortcode(Http $http)
	{
		$response           = $this->client->request('GET', 'companies');
		$company 			= json_decode($response->getBody());
		$requestData        = $this->transformRequestData($http);
		$locale = get_locale();
		if ($locale == "el") {
			$locale = "el_GR";
		}
		$return_date = $http->get('return_datetime');
		if($http->get('type') == "activity") {
			$return_date = $http->get('pickup_datetime');
		}
		$return_date = Carbon::createFromFormat('Y-m-d H:i', $return_date)->addDay()->toDateTimeString();
		$return_date = date('Y-m-d H:i', strtotime($return_date));

		setLocale(LC_TIME, $locale.'.utf-8');
		$pickupDateReadable = Carbon::createFromFormat('Y-m-d H:i', $http->get('pickup_datetime'))->formatLocalized('%d %B');
		$returnDateReadable = Carbon::createFromFormat('Y-m-d H:i', $return_date)->formatLocalized('%d %B');
		$pickupTime         = Carbon::createFromFormat('Y-m-d H:i', $http->get('pickup_datetime'))->format('H:i');
		$returnTime         = Carbon::createFromFormat('Y-m-d H:i', $return_date)->format('H:i');
		$parkings           = json_decode(Option::where('option_name', 'wpRengineParkings')->first()->option_value, true);
		$terms 				= Option::where('option_name', 'wpRengineTerms')->first()->option_value;
		$adults				= filter_var ($http->get('adults'), FILTER_VALIDATE_INT);
		$children			= filter_var ($http->get('child'), FILTER_VALIDATE_INT);
		$infants			= filter_var ($http->get('infants'), FILTER_VALIDATE_INT);
		$type				= filter_var ($http->get('type'), FILTER_SANITIZE_STRING); 
		foreach ($parkings as $parking)
		{
			$pickupParking = ($parking['parking_coordinates'] == $http->get('pickup_coordinates')) ? $parking['parking_name'] : '';
			$returnParking = ($parking['parking_coordinates'] == $http->get('return_coordinates')) ? $parking['parking_name'] : '';
		}
		$filters                = ($requestData) ? ['filter' => $requestData] : '';
		$response               = $this->client->request('GET', 'services/' . $http->get('service_id'), ['query' => $filters]);
		$services               = json_decode($response->getBody(), true);
		$service                = $services['data'][0];
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

		if ($totalDays == 1) {
			$timeDifferenceReadable = (string)$totalDays .' day';
		} else {
			$timeDifferenceReadable = (string)$totalDays .' days';
		}
		$pricingData            = $this->calculatePricing($service['availabilities_and_rates'], $service['total_price']);
		$registrationURL        = wp_registration_url();
		$loginURL               = wp_login_url(site_url() . $_SERVER['REQUEST_URI']);
		$isLoggedIn             = is_user_logged_in();
		return view('@WPRengine/front/car-and-driver-details.twig', [
			'pickupDateReadable'     => $pickupDateReadable,
			'returnDateReadable'	 => $returnDateReadable,
			'pickupTime'             => $pickupTime,
			'returnTime'             => $returnTime,
			'pickupParking'          => $pickupParking,
			'returnParking'          => $returnParking,
			'service'                => $service,
			'paxReadable'			 => $paxReadable,
			'adults'				 => $adults, 
			'children'				 => $children,
			'infants'				 => $infants,
			'tr_notes'	 			 => __( 'notes', 'wordpress-rengine' ),
			'tr_includes'	 		 => __( 'includes', 'wordpress-rengine' ),
			'tr_excludes'	 		 => __( 'excludes', 'wordpress-rengine' ),
			'tr_selected_services'	 => __( 'selected_services', 'wordpress-rengine' ),
			'tr_comment_placeholder' => __( 'comment_placeholder', 'wordpress-rengine' ),
			'orderRevisionURL'       => Post::where('post_name', 'wp-rengine-order-revision')->first()->guid,
			'pricingData'            => $pricingData,
			'timeDifferenceReadable' => $timeDifferenceReadable,
			'totalDays'				 => $totalDays,
			'registrationURL'        => $registrationURL,
			'loginURL'               => $loginURL,
			'type'					 => $type,
			'isLoggedIn'             => $isLoggedIn,
			'inputs'                 => $http->all(),
			'currentUser'            => wp_get_current_user(),
			'queryString'            => $_SERVER['QUERY_STRING'],
			'currency'				 => $company->data[0]->currency,
			'terms'					 => $terms,
			'tr_change_dates' 		 => __( 'change_dates', 'wordpress-rengine' ),
			'tr_from' 				 => __( 'from', 'wordpress-rengine' ),
			'tr_to' 				 => __( 'to', 'wordpress-rengine' ),
			'tr_for' 				 => __( 'for', 'wordpress-rengine' ),
			'tr_return' 			 => __( 'return', 'wordpress-rengine' ),
			'tr_pickup' 			 => __( 'pickup', 'wordpress-rengine' ),
			'tr_your_choise'		 => __( 'your_choise', 'wordpress-rengine' ),
			'tr_pricing'			 => __( 'pricing', 'wordpress-rengine' ),
			'tr_extras'				 => __( 'extras', 'wordpress-rengine' ),
			'tr_driver_details'		 => __('driver_details', 'wordpress-rengine' ),
			'tr_login_label'		 => __('login_label', 'wordpress-rengine' ),
			'tr_login_button'		 => __('login_button', 'wordpress-rengine' ),
			'tr_register_button'	 => __('register_button', 'wordpress-rengine' ),
			'tr_full_name'	 		 => __('full_name', 'wordpress-rengine' ),
			'tr_phone'	 			 => __('phone', 'wordpress-rengine' ),	
			'tr_flight_number'		 => __('flight_number', 'wordpress-rengine' ),
			'tr_terms_label'		 => __('terms_label', 'wordpress-rengine' ),
			'tr_total_cost'			 => __('total_cost', 'wordpress-rengine' ),	
			'tr_continue_button'	 => __('continue_button', 'wordpress-rengine' ),
			'tr_flat_cost'	 		 => __('flat_cost', 'wordpress-rengine' ),

		]);
	}

	public function wpRengineOrderRevisionShortcode(Http $http)
	{

		$requestData = $this->transformRequestData($http);
		$requestData = $requestData['availability'];
		// The developer sends the coordinates as he is obliged to.
		// He shouldn't be obliged to also send a parking name.
		// The parking name should be optional and therefore the rengine server
		// should resolve the coordinates into a street address if a name is not provided
		$requestData['pickup_parking_name'] = $http->get('pickup_parking_name');
		$requestData['return_parking_name'] = $http->get('return_parking_name');
		$requestData['extras']              = $http->get('extras');
		$requestData['fullname']            = $http->get('fullname');
		$requestData['mobile-email']        = $http->get('tel') . ' ' . $http->get('email');
		$requestData['service_id']          = $http->get('service_id');
		$requestData['send-notifications']  = '1';
		$requestData['comment']  			= $http->get('flight-number');
		$response                           = $this->client->request('POST', 'orders', ['form_params' => $requestData]);
		$order                              = json_decode($response->getBody(), true);
		$order                              = $order['data'];
		$returnUrl                          = Post::where('post_name', 'wp-rengine-payment')->first()->guid . '?order_id=' . $order['id'];
		$cancelUrl                          = $returnUrl . '&cancel=1';
		$currencyISO 						= $this->currencySymbolToIso($http->get('currency'));
		// Use the PaypalController like this
		$paypal      = new PaypalController();

		if ($this->PaypalApiCurrencySupport($currencyISO)) {
			$currency 			= $currencyISO;
			$totalCost 			= $order['total_cost'];
			$exchangeMessage 	= '';
		} else {
			//Paypal REST API doesnot support all currencies see https://developer.paypal.com/webapps/developer/docs/integration/direct/rest-api-payment-country-currency-support/
			$XML=simplexml_load_file("http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22USD".$currencyISO."%22)&env=store://datatables.org/alltableswithkeys");
			$rate 				= (float)$XML->results->rate->Rate;
			$currency 			= 'USD';
			$totalCost 			= $order['total_cost']/$rate;
			$exchangeMessage 	= __('exchange_message', 'wordpress-rengine' );
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
		return view('@WPRengine/front/order-revision.twig', [
			'order'       		=> $order,
			'paymentData' 		=> $paymentData,
			'tr_action_required'=> __('action_required', 'wordpress-rengine' ),
			'tr_reference_message'=> __('reference_message', 'wordpress-rengine' ),
			'tr_paypal_message'=> __('paypal_message', 'wordpress-rengine' ),
			'tr_button_message'=> __('button_message', 'wordpress-rengine' ),
			'tr_payment_button'=> __('payment_button', 'wordpress-rengine' ),
			'exchangeMessage'  => $exchangeMessage,
		]);
	}

	public function wpRenginePaymentShortcode(Http $http)
	{
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
				'tr_failure_success'	=> __('payment_failure', 'wordpress-rengine' ),
				'tr_failure'			=> __('failure', 'wordpress-rengine' ),
				'tr_failure_message'	=> __('failure_message', 'wordpress-rengine' ),
			]);
		} else {
			$paymentId      = $http->get('paymentId');
			$payerId        = $http->get('PayerID');
			$paypal         = new PaypalController();
			$resultPayPal   = $paypal->executePayment($paymentId, $payerId);
			$itemDscr 		= $resultPayPal->transactions[0]->description;
			$orderReference	= trim(explode('#', $itemDscr)[1]);
			$renginePayment = $this->client->request('POST', 'payments', ['form_params' => ['order_id' => $http->get('order_id')]]);
			return view('@WPRengine/front/payment.twig', [
				'resultPayPal'   		=> $resultPayPal,
				'renginePayment' 		=> $renginePayment,
				'reference'				=> $orderReference,
				'tr_reference_message'=> __('reference_message', 'wordpress-rengine' ),
				'tr_payment_success'	=> __('payment_success', 'wordpress-rengine' ),
				'tr_success'			=> __('success', 'wordpress-rengine' ),
				'tr_success_message'	=> __('success_message', 'wordpress-rengine' ),
			]);
		}
	}

	public function currencySymbolToIso($symbol) {
		$currency['€']  = 'EUR';
		$currency['$']       = 'USD';
		$currency['£'] = 'GBP';
		$currency['₺'] = 'TRY';
		$currency['S/.']     = 'PEN';
		$currency['A$']      = 'AUD';
		$currency['лв']      = 'BGL';
		$currency['₦']      = 'NGN';
		$currency['₹']      = 'INR';
		$currency['د.إ']     = 'AED';
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