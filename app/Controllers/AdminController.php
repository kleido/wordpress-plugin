<?php
namespace WPRengine\Controllers;
use Herbert\Framework\Http;
use Herbert\Framework\Models\Option;
use Herbert\Framework\Notifier;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;

class AdminController {

	private $base_uri = 'https://secure.reservationengine.net/rest/api/';

	// private $base_uri = 'https://xiteserver/secure.reservationengine.net/fast/public/api/';
	
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
		wp_enqueue_style('bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css');
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


	public function configuration(Http $request) {
		if ($request->method() == 'POST') {
			$result = $this->saveConfiguration($request);
			if ($result === true) {
				Notifier::success('WP Rengine Configuration Saved!', true);
			} else {
				Notifier::error('The system encountered an error. ' . implode('<br>', $error_messages), true);
			}
		}
		
		$term = "Copy & Paste here your terms & conditions.";
	
		try {
			$wpRengineUser        			= Option::where('option_name', 'wpRengineUser')->first();
			$wpRenginePassword    			= Option::where('option_name', 'wpRenginePassword')->first();
			$wpRenginePaypalClientId		= Option::where('option_name', 'wpRenginePaypalClientId')->first();
			$wpRenginePaypalSecret			= Option::where('option_name', 'wpRenginePaypalSecret')->first();
			$wpRenginePaypalMode			= Option::where('option_name', 'wpRenginePaypalMode')->first();
			$wpRengineButton				= Option::where('option_name', 'wpRengineButton')->first();
			$wpRengineCalendarMode			= Option::where('option_name', 'wpRengineCalendarMode')->first();
			$wpRengineCalendarView			= Option::where('option_name', 'wpRengineCalendarView')->first();
			$wpRengineCalendarLocation		= Option::where('option_name', 'wpRengineCalendarLocation')->first();
			$wpRengineSlideMode				= Option::where('option_name', 'wpRengineSlideMode')->first();
			$wpRengineCalendarStep			= Option::where('option_name', 'wpRengineCalendarStep')->first();
			$wpRengineCalendarHoursDisabled	= Option::where('option_name', 'wpRengineCalendarHoursDisabled')->first();

			$wpRengineTerms				= Option::where('option_name', 'wpRengineTerms')->first();
			if (is_null($wpRengineTerms)) {
				$wpRengineTerms 		= $term;
			} else {
				$wpRengineTerms 		= Option::where('option_name', 'wpRengineTerms')->first()->option_value;

			}

		} catch (Exception $e) {
			Notifier::error($e->getMessage(), true);
		}
		return view('@WPRengine/admin/configuration.twig', [
			'options' => [
				'wpRengineUser'        			=> ($wpRengineUser) ? $wpRengineUser->option_value : '',
				'wpRenginePassword'    			=> ($wpRenginePassword) ? $wpRenginePassword->option_value : '',
				'wpRenginePaypalClientId'		=> ($wpRenginePaypalClientId) ? $wpRenginePaypalClientId->option_value : '',
				'wpRenginePaypalSecret'			=> ($wpRenginePaypalSecret) ? $wpRenginePaypalSecret->option_value : '',
				'wpRengineCalendarMode'			=> ($wpRengineCalendarMode) ? $wpRengineCalendarMode->option_value : '',
				'wpRengineCalendarView'			=> ($wpRengineCalendarView) ? $wpRengineCalendarView->option_value : '',
				'wpRengineCalendarStep'			=> ($wpRengineCalendarStep) ? $wpRengineCalendarStep->option_value : '',
				'wpRengineCalendarLocation'		=> ($wpRengineCalendarLocation) ? $wpRengineCalendarLocation->option_value : '',
				'wpRengineSlideMode'			=> ($wpRengineSlideMode) ? $wpRengineSlideMode->option_value : '',
				'wpRenginePaypalMode'			=> ($wpRenginePaypalMode) ? $wpRenginePaypalMode->option_value : '',
				'wpRengineCalendarHoursDisabled'=> ($wpRengineCalendarHoursDisabled) ? $wpRengineCalendarHoursDisabled->option_value : '',
				'wpRenginebutton'				=> ($wpRengineButton) ? $wpRengineButton->option_value : '',
				'wpRengineTerms'				=> ($wpRengineTerms) ? $wpRengineTerms : '',
			],
		]);
	}

	public function places(Http $request) {
		if ($request->method() == 'POST') {
			$result = $this->savePlaces($request);
			if ($result === true) {
				Notifier::success('WP Rengine Places Saved!', true);
			} else {
				Notifier::error('The system encountered an error. ' . implode('<br>', $error_messages), true);
			}
		}
		$parkings = Option::where('option_name', 'wpRengineParkings')->first();

		if (isset($parkings->option_value)) {
			$parkings_list 	= json_decode($parkings->option_value, true);
		} else {
			$parkings_list = "Please add at least 1 parking";
		}

		try {
			$wpRengineLocationsMode			= Option::where('option_name', 'wpRengineLocationsMode')->first();
			$wpRengineCurrentMode			= Option::where('option_name', 'wpRengineCurrentMode')->first();
			$baseCoordinates				= Option::where('option_name', 'base-coordinates')->first();
			$baseRadius						= Option::where('option_name', 'base-radius')->first();
		} catch (Exception $e) {
			$error_messages[] = $e->getMessage();
		}

		return view('@WPRengine/admin/places.twig', [
			'parkings' 						=> $parkings_list,
			'wpRengineLocationsMode'		=> ($wpRengineLocationsMode) ? $wpRengineLocationsMode->option_value : '',
			'wpRengineCurrentMode'			=> ($wpRengineCurrentMode) ? $wpRengineCurrentMode->option_value : '',
			'baseCoordinates'				=> ($baseCoordinates) ? $baseCoordinates->option_value : '',
			'baseRadius'					=> ($baseRadius) ? $baseRadius->option_value : '',
		]);
	}

	public function saveConfiguration(Http $request) {
		$error_messages = [];
		try {
			$wpRengineUser        			= Option::where('option_name', 'wpRengineUser')->first();
			$wpRenginePassword    			= Option::where('option_name', 'wpRenginePassword')->first();
			$wpRenginePaypalClientId		= Option::where('option_name', 'wpRenginePaypalClientId')->first();
			$wpRenginePaypalSecret			= Option::where('option_name', 'wpRenginePaypalSecret')->first();
			$wpRenginePaypalMode			= Option::where('option_name', 'wpRenginePaypalMode')->first();
			$wpRengineCalendarMode			= Option::where('option_name', 'wpRengineCalendarMode')->first();
			$wpRengineCalendarView			= Option::where('option_name', 'wpRengineCalendarView')->first();
			$wpRengineCalendarStep			= Option::where('option_name', 'wpRengineCalendarStep')->first();
			$wpRengineSlideMode				= Option::where('option_name', 'wpRengineSlideMode')->first();
			$wpRengineCalendarLocation		= Option::where('option_name', 'wpRengineCalendarLocation')->first();
			$wpRengineCalendarHoursDisabled	= Option::where('option_name', 'wpRengineCalendarHoursDisabled')->first();
			$wpRengineButton				= Option::where('option_name', 'wpRengineButton')->first();
			$wpRengineTerms					= Option::where('option_name', 'wpRengineTerms')->first();

		} catch (Exception $e) {
			$error_messages[] = $e->getMessage();
		}

		if ($request->has('wpRengineUser')) {
			if ($wpRengineUser) {
				$wpRengineUser->option_value = filter_var ( $request->input('wpRengineUser'), FILTER_SANITIZE_STRING) ;
			} else {
				$wpRengineUser               = new Option();
				$wpRengineUser->option_name  = 'wpRengineUser';
				$wpRengineUser->option_value = filter_var ( $request->input('wpRengineUser'), FILTER_SANITIZE_STRING) ;
			}
			$wpRengineUser->save();
		}

		if ($request->has('wpRenginePassword')) {
			if ($wpRenginePassword) {
				$wpRenginePassword->option_value = $request->input('wpRenginePassword');
			} else {
				$wpRenginePassword               = new Option();
				$wpRenginePassword->option_name  = 'wpRenginePassword';
				$wpRenginePassword->option_value = filter_var ( $request->input('wpRenginePassword'), FILTER_SANITIZE_STRING) ;
			}
			$wpRenginePassword->save();
		}

		if ($request->has('wpRenginePaypalClientId')) {
			if ($wpRenginePaypalClientId) {
				$wpRenginePaypalClientId->option_value = $request->input('wpRenginePaypalClientId');
			} else {
				$wpRenginePaypalClientId               = new Option();
				$wpRenginePaypalClientId->option_name  = 'wpRenginePaypalClientId';
				$wpRenginePaypalClientId->option_value = filter_var($request->input('wpRenginePaypalClientId'), FILTER_SANITIZE_STRING);
			}
			$wpRenginePaypalClientId->save();
		}

		if ($request->has('wpRenginePaypalSecret')) {
			if ($wpRenginePaypalSecret) {
				$wpRenginePaypalSecret->option_value = $request->input('wpRenginePaypalSecret');
			} else {
				$wpRenginePaypalSecret               = new Option();
				$wpRenginePaypalSecret->option_name  = 'wpRenginePaypalSecret';
				$wpRenginePaypalSecret->option_value = filter_var($request->input('wpRenginePaypalSecret'), FILTER_SANITIZE_STRING); 
			}
			$wpRenginePaypalSecret->save();
		}

		if ($request->has('wpRenginePaypalMode')) {
			if ($wpRenginePaypalMode) {
				$wpRenginePaypalMode->option_value = $request->input('wpRenginePaypalMode');
			} else {
				$wpRenginePaypalMode               = new Option();
				$wpRenginePaypalMode->option_name  = 'wpRenginePaypalMode';
				$wpRenginePaypalMode->option_value = filter_var($request->input('wpRenginePaypalMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRenginePaypalMode->save();
		}

		if ($request->has('wpRengineCalendarMode')) {
			if ($wpRengineCalendarMode) {
				$wpRengineCalendarMode->option_value = $request->input('wpRengineCalendarMode');
			} else {
				$wpRengineCalendarMode               = new Option();
				$wpRengineCalendarMode->option_name  = 'wpRengineCalendarMode';
				$wpRengineCalendarMode->option_value = filter_var($request->input('wpRengineCalendarMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineCalendarMode->save();
		} else {
			$wpRengineCalendarMode->option_value = 'normal';
			$wpRengineCalendarMode->save();
		}

		if ($request->has('wpRengineSlideMode')) {
			if ($wpRengineSlideMode) {
				$wpRengineSlideMode->option_value = $request->input('wpRengineSlideMode');
			} else {
				$wpRengineSlideMode               = new Option();
				$wpRengineSlideMode->option_name  = 'wpRengineSlideMode';
				$wpRengineSlideMode->option_value = filter_var($request->input('wpRengineSlideMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineSlideMode->save();
		} else {
			$wpRengineSlideMode->option_value = 'false';
			$wpRengineSlideMode->save();
		}

		if ($request->has('wpRengineCalendarView')) {
			if ($wpRengineCalendarView) {
				$wpRengineCalendarView->option_value = $request->input('wpRengineCalendarView');
			} else {
				$wpRengineCalendarView               = new Option();
				$wpRengineCalendarView->option_name  = 'wpRengineCalendarView';
				$wpRengineCalendarView->option_value = filter_var($request->input('wpRengineCalendarView'), FILTER_SANITIZE_STRING);
			}
			$wpRengineCalendarView->save();
		} else {
			$wpRengineCalendarView->option_value = 'popup';
			$wpRengineCalendarView->save();
		}

		if ($request->has('wpRengineCalendarLocation')) {
			if ($wpRengineCalendarLocation) {
				$wpRengineCalendarLocation->option_value = $request->input('wpRengineCalendarLocation');
			} else {
				$wpRengineCalendarLocation               = new Option();
				$wpRengineCalendarLocation->option_name  = 'wpRengineCalendarLocation';
				$wpRengineCalendarLocation->option_value = filter_var($request->input('wpRengineCalendarLocation'), FILTER_SANITIZE_STRING);
			}
			$wpRengineCalendarLocation->save();
		} else {
			$wpRengineCalendarLocation->option_value = 'auto';
			$wpRengineCalendarLocation->save();
		}

		if ($request->has('wpRengineCalendarStep')) {
			if ($wpRengineCalendarStep) {
				$wpRengineCalendarStep->option_value = $request->input('wpRengineCalendarStep');
			} else {
				$wpRengineCalendarStep               = new Option();
				$wpRengineCalendarStep->option_name  = 'wpRengineCalendarStep';
				$wpRengineCalendarStep->option_value = filter_var($request->input('wpRengineCalendarStep'), FILTER_SANITIZE_NUMBER_INT); 
			}
			$wpRengineCalendarStep->save();
		} else {
			if ($wpRengineCalendarStep) {
				$wpRengineCalendarStep->option_value = '';
			} else {
				$wpRengineCalendarStep               = new Option();
				$wpRengineCalendarStep->option_name  = 'wpRengineCalendarStep';
				$wpRengineCalendarStep->option_value = ''; 
			}
			$wpRengineCalendarStep->save();
		}


		if ($request->has('wpRengineCalendarHoursDisabled')) {
			if ($wpRengineCalendarHoursDisabled) {
				$wpRengineCalendarHoursDisabled->option_value = $request->input('wpRengineCalendarHoursDisabled');
			} else {
				$wpRengineCalendarHoursDisabled               = new Option();
				$wpRengineCalendarHoursDisabled->option_name  = 'wpRengineCalendarHoursDisabled';
				$wpRengineCalendarHoursDisabled->option_value = filter_var($request->input('wpRengineCalendarHoursDisabled'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineCalendarHoursDisabled->save();
		} else {
			if ($wpRengineCalendarHoursDisabled) {
				$wpRengineCalendarHoursDisabled->option_value = '';
			} else {
				$wpRengineCalendarHoursDisabled               = new Option();
				$wpRengineCalendarHoursDisabled->option_name  = 'wpRengineCalendarHoursDisabled';
				$wpRengineCalendarHoursDisabled->option_value = '';
			}
			$wpRengineCalendarHoursDisabled->save();
		}

		if ($request->has('wpRengineButton')) {
			if ($wpRengineButton) {
				$wpRengineButton->option_value = $request->input('wpRengineButton');
			} else {
				$wpRengineButton               = new Option();
				$wpRengineButton->option_name  = 'wpRengineButton';
				$wpRengineButton->option_value = filter_var($request->input('wpRengineButton'), FILTER_SANITIZE_STRING);  
			}
			$wpRengineButton->save();
		}

		if ($request->has('wpRengineTerms')) {
			if ($wpRengineTerms) {
				$wpRengineTerms->option_value = $request->input('wpRengineTerms');
			} else {
				$wpRengineTerms               = new Option();
				$wpRengineTerms->option_name  = 'wpRengineTerms';
				$wpRengineTerms->option_value = filter_var($request->input('wpRengineTerms'), FILTER_SANITIZE_STRING);  
			}
			$wpRengineTerms->save();
		}

		if (empty($error_messages)) {
			return true;
		} else {
			return $error_messages;
		}

	}

	public function savePlaces(Http $request) {
		$error_messages = [];
		$parkingsRequest = $request->all();
		$parkings        = [];
		for ($i = 1; $i <= count($parkingsRequest['parking_name']); $i++) {
			$parkings[] = [
				'parking_name'        => $parkingsRequest['parking_name'][$i],
				'parking_coordinates' => $parkingsRequest['parking_coordinates'][$i],
			];
		}
		$wpRengineParkings = Option::where('option_name', 'wpRengineParkings')->first();
		if ($wpRengineParkings) {
			$wpRengineParkings->option_value = json_encode($parkings);
		} else {
			$wpRengineParkings               = new Option();
			$wpRengineParkings->option_name  = 'wpRengineParkings';
			$wpRengineParkings->option_value = json_encode($parkings);
		}
		$wpRengineParkings->save();

		try {
			$wpRengineLocationsMode			= Option::where('option_name', 'wpRengineLocationsMode')->first();
			$wpRengineCurrentMode			= Option::where('option_name', 'wpRengineCurrentMode')->first();
			$baseCoordinates				= Option::where('option_name', 'base-coordinates')->first();
			$baseRadius						= Option::where('option_name', 'base-radius')->first();
		} catch (Exception $e) {
			$error_messages[] = $e->getMessage();
		}

		if ($request->has('base-coordinates')) {
			if ($baseCoordinates) {
				$baseCoordinates->option_value = $request->input('base-coordinates');
			} else {
				$baseCoordinates               = new Option();
				$baseCoordinates->option_name  = 'base-coordinates';
				$baseCoordinates->option_value = filter_var($request->input('base-coordinates'), FILTER_SANITIZE_STRING); 
			}
			$baseCoordinates->save();
		}

		if ($request->has('base-radius')) {
			if ($baseRadius) {
				$baseRadius->option_value = $request->input('base-radius');
			} else {
				$baseRadius               = new Option();
				$baseRadius->option_name  = 'base-radius';
				$baseRadius->option_value = filter_var($request->input('base-radius'), FILTER_SANITIZE_STRING); 
			}
			$baseRadius->save();
		}

		if ($request->has('wpRengineLocationsMode')) {
			if ($wpRengineLocationsMode) {
				$wpRengineLocationsMode->option_value = filter_var($request->input('wpRengineLocationsMode'), FILTER_SANITIZE_STRING);
			} else {
				$wpRengineLocationsMode               = new Option();
				$wpRengineLocationsMode->option_name  = 'wpRengineLocationsMode';
				$wpRengineLocationsMode->option_value = filter_var($request->input('wpRengineLocationsMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineLocationsMode->save();
		}

		if ($request->has('wpRengineCurrentMode')) {
			if ($wpRengineCurrentMode) {
				$wpRengineCurrentMode->option_value = filter_var($request->input('wpRengineCurrentMode'), FILTER_SANITIZE_STRING); 
			} else {
				$wpRengineCurrentMode               = new Option();
				$wpRengineCurrentMode->option_name  = 'wpRengineCurrentMode';
				$wpRengineCurrentMode->option_value = filter_var($request->input('wpRengineCurrentMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineCurrentMode->save();
		}

		
		if (empty($error_messages)) {
			return true;
		} else {
			return $error_messages;
		}

	}
}
?>