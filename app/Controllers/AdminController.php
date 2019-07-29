<?php
namespace WPRengine\Controllers;
use WPRengine\Helper;
use Herbert\Framework\Http;
use Herbert\Framework\Models\Option;
use Herbert\Framework\Models\Post;
use Herbert\Framework\Notifier;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;

class AdminController {

	
	private $timeout = 180;

	private $auth;

	private $client;

	public function __construct()
	{
		$version  = Option::where('option_name', 'wpRengineApiVersion')->first();

		if ($version->option_value == 'v1') {
			$base_uri = 'https://secure.reservationengine.net/rest/api/';
		} else {
			// $base_uri = 'http://192.168.1.6/reservationengine-fast/public/api/';
			$base_uri = 'https://app.workadu.com/api/';
		}
		$this->setAuth();
		$this->client = new Client([
			'base_uri' => $base_uri,
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


	public function getstarted(Http $request) {
		if ($request->method() == 'POST') {
			$result = $this->saveConfiguration($request);
			if ($result === true) {
				Notifier::success('WP Rengine login data Saved!', true);
			} else {
				Notifier::error('The system encountered an error. ' . implode('<br>', $error_messages), true);
			}
		}
		$posts0	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[WorkaduForm]%')->first();
		$posts1	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-search-results]%')->first();
		$posts2	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-contact-info]%')->first();
		$posts3	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-order-revision]%')->first();
		$posts4	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-payment]%')->first();
	
		$pages[0] = ($posts0)? $posts0 : '';
		$pages[1] = ($posts1)? $posts1 : '';
		$pages[2] = ($posts2)? $posts2 : '';
		$pages[3] = ($posts3)? $posts3 : '';
		$pages[4] = ($posts4)? $posts4 : '';
		
		try {
			$wpRengineUser        			= Option::where('option_name', 'wpRengineUser')->first();
			$wpRenginePassword    			= Option::where('option_name', 'wpRenginePassword')->first();
			$wpRengineApiVersion			= Option::where('option_name', 'wpRengineApiVersion')->first();


		} catch (Exception $e) {
			Notifier::error($e->getMessage(), true);
		}
		return view('@WPRengine/admin/getstarted.twig', [
			'options' => [
				'wpRengineUser'        			=> ($wpRengineUser) ? $wpRengineUser->option_value : 'democar@workadu.com',
				'wpRenginePassword'    			=> ($wpRenginePassword) ? $wpRenginePassword->option_value : '123123123',
				'wpRengineApiVersion'			=> ($wpRengineApiVersion) ? $wpRengineApiVersion->option_value : '',
				'pages'							=> $pages,
			],
		]);
	}

		public function shortcodes(Http $request) {
		if ($request->method() == 'POST') {
			$result = $this->saveConfiguration($request);
			if ($result === true) {
				Notifier::success('WP Rengine login data Saved!', true);
			} else {
				Notifier::error('The system encountered an error. ' . implode('<br>', $error_messages), true);
			}
		}
		$posts0	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[WorkaduForm]%')->first();
		$posts1	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-search-results]%')->first();
		$posts2	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-contact-info]%')->first();
		$posts3	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-order-revision]%')->first();
		$posts4	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-payment]%')->first();
	
		$pages[0] = ($posts0)? $posts0 : '';
		$pages[1] = ($posts1)? $posts1 : '';
		$pages[2] = ($posts2)? $posts2 : '';
		$pages[3] = ($posts3)? $posts3 : '';
		$pages[4] = ($posts4)? $posts4 : '';
		
		try {
			$wpRengineUser        			= Option::where('option_name', 'wpRengineUser')->first();
			$wpRenginePassword    			= Option::where('option_name', 'wpRenginePassword')->first();
			$wpRengineApiVersion			= Option::where('option_name', 'wpRengineApiVersion')->first();

		} catch (Exception $e) {
			Notifier::error($e->getMessage(), true);
		}
		return view('@WPRengine/admin/shortcodes.twig', [
			'options' => [
				'wpRengineUser'        			=> ($wpRengineUser) ? $wpRengineUser->option_value : 'democar@workadu.com',
				'wpRenginePassword'    			=> ($wpRenginePassword) ? $wpRenginePassword->option_value : '123123123',
				'wpRengineApiVersion'			=> ($wpRengineApiVersion) ? $wpRengineApiVersion->option_value : '',
				'pages'							=> $pages,
			],
		]);
	}


		public function apikey(Http $request) {
		if ($request->method() == 'POST') {
			$result = $this->saveConfiguration($request);
			if ($result === true) {
				Notifier::success('WP Rengine login data Saved!', true);
			} else {
				Notifier::error('The system encountered an error. ' . implode('<br>', $error_messages), true);
			}
		}
		$posts0	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[WorkaduForm]%')->first();
		$posts1	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-search-results]%')->first();
		$posts2	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-contact-info]%')->first();
		$posts3	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-order-revision]%')->first();
		$posts4	= Post::where('post_status', 'publish')->where('post_type', 'page')->where('post_content', 'like', '%[Workadu-payment]%')->first();
	
		$pages[0] = ($posts0)? $posts0 : '';
		$pages[1] = ($posts1)? $posts1 : '';
		$pages[2] = ($posts2)? $posts2 : '';
		$pages[3] = ($posts3)? $posts3 : '';
		$pages[4] = ($posts4)? $posts4 : '';
		
		try {
			$wpRengineUser        			= Option::where('option_name', 'wpRengineUser')->first();
			$wpRenginePassword    			= Option::where('option_name', 'wpRenginePassword')->first();
			$wpRengineApiVersion			= Option::where('option_name', 'wpRengineApiVersion')->first();


		} catch (Exception $e) {
			Notifier::error($e->getMessage(), true);
		}

		if (($this->auth) && (!$this->company)) {
            try {
            	
                 $response   = $this->client->request('GET', 'companies?'.strtotime());
                 $this->company            = json_decode($response->getBody());
            } catch (\GuzzleHttp\Exception\RequestException $e) {

                $this->company = false; 
            }
        }
		return view('@WPRengine/admin/apikey.twig', [
			'options' => [
				'wpRengineUser'        			=> ($wpRengineUser) ? $wpRengineUser->option_value : 'democar@workadu.com',
				'wpRenginePassword'    			=> ($wpRenginePassword) ? $wpRenginePassword->option_value : '123123123',
				'wpRengineApiVersion'			=> ($wpRengineApiVersion) ? $wpRengineApiVersion->option_value : '',
				'pages'							=> $pages,
				'company'						=>  $this->company 
			],
		]);
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
			
			$wpRengineStripeClientId		= Option::where('option_name', 'wpRengineStripeClientId')->first();
			$wpRengineStripeSecret			= Option::where('option_name', 'wpRengineStripeSecret')->first();
			$wpRengineStripeMode			= Option::where('option_name', 'wpRengineStripeMode')->first();
			
			$wpRenginePayzenClientId		= Option::where('option_name', 'wpRenginePayzenClientId')->first();
			$wpRenginePayzenSecret			= Option::where('option_name', 'wpRenginePayzenSecret')->first();
			$wpRenginePayzenMode			= Option::where('option_name', 'wpRenginePayzenMode')->first();
			$wpRenginePayzenUsername		= Option::where('option_name', 'wpRenginePayzenUsername')->first();
			$wpRenginePayzenReturnKey		= Option::where('option_name', 'wpRenginePayzenReturnKey')->first();

			$wpRengineVivaClientId			= Option::where('option_name', 'wpRengineVivaClientId')->first();
			$wpRengineVivaAPI				= Option::where('option_name', 'wpRengineVivaAPI')->first();
			$wpRengineVivaMode				= Option::where('option_name', 'wpRengineVivaMode')->first();

			$wpRengineButton				= Option::where('option_name', 'wpRengineButton')->first();
			$wpRengineCalendarMode			= Option::where('option_name', 'wpRengineCalendarMode')->first();
			$wpRengineResultsCalendarMode	= Option::where('option_name', 'wpRengineResultsCalendarMode')->first();
			$wpRengineResultsAvailableMode	= Option::where('option_name', 'wpRengineResultsAvailableMode')->first();
			
			$wpRengineCouponMode			= Option::where('option_name', 'wpRengineCouponMode')->first();
			$wpRengineGroupMode				= Option::where('option_name', 'wpRengineGroupMode')->first();

			$wpRengineCalendarView			= Option::where('option_name', 'wpRengineCalendarView')->first();
			$wpRengineCalendarLocation		= Option::where('option_name', 'wpRengineCalendarLocation')->first();
			$wpRengineSlideMode				= Option::where('option_name', 'wpRengineSlideMode')->first();
			$wpRengineCalendarStep			= Option::where('option_name', 'wpRengineCalendarStep')->first();
			$wpRengineCalendarHoursDisabled	= Option::where('option_name', 'wpRengineCalendarHoursDisabled')->first();
			$wpRengineGateway				= Option::where('option_name', 'wpRengineGateway')->first();
			$wpRengineCash					= Option::where('option_name', 'wpRengineCash')->first();
			$wpRengineCashMode				= Option::where('option_name', 'wpRengineCashMode')->first();
			$wpRengineTerms					= Option::where('option_name', 'wpRengineTerms')->first();
			$wpRengineResultsPage			= Option::where('option_name', 'wpRengineResultsPage')->first();
			$wpRengineRevisionPage			= Option::where('option_name', 'wpRengineRevisionPage')->first();
			$wpRengineGatewayPage			= Option::where('option_name', 'wpRengineGatewayPage')->first();
			$wpRenginePaymentPage			= Option::where('option_name', 'wpRenginePaymentPage')->first();
			$wpRenginePaymentPercent		= Option::where('option_name', 'wpRenginePaymentPercent')->first();
			$wpRengineTermsPage				= Option::where('option_name', 'wpRengineTermsPage')->first();
			$posts	 						= Post::where('post_status', 'publish')->get();
			$wpRengineRecaptcha				= Option::where('option_name', 'wpRengineRecaptcha')->first();
			$wpRengineApiVersion			= Option::where('option_name', 'wpRengineApiVersion')->first();

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
				'wpRenginePaypalMode'			=> ($wpRenginePaypalMode) ? $wpRenginePaypalMode->option_value : '',

				'wpRengineAlphaClientId'		=> ($wpRengineAlphaClientId) ? $wpRengineAlphaClientId->option_value : '',
				'wpRengineAlphaSecret'			=> ($wpRengineAlphaSecret) ? $wpRengineAlphaSecret->option_value : '',
				'wpRengineAlphaMode'			=> ($wpRengineAlphaMode) ? $wpRengineAlphaMode->option_value : '',
				
				'wpRengineStripeClientId'		=> ($wpRengineStripeClientId) ? $wpRengineStripeClientId->option_value : '',
				'wpRengineStripeSecret'			=> ($wpRengineStripeSecret) ? $wpRengineStripeSecret->option_value : '',
				'wpRengineStripeMode'			=> ($wpRengineStripeMode) ? $wpRengineStripeMode->option_value : '',
				
				'wpRenginePayzenClientId'		=> ($wpRenginePayzenClientId) ? $wpRenginePayzenClientId->option_value : '',
				'wpRenginePayzenSecret'			=> ($wpRenginePayzenSecret) ? $wpRenginePayzenSecret->option_value : '',
				'wpRenginePayzenMode'			=> ($wpRenginePayzenMode) ? $wpRenginePayzenMode->option_value : '',
				'wpRenginePayzenUsername'		=> ($wpRenginePayzenUsername) ? $wpRenginePayzenUsername->option_value : '',
				
				'wpRengineVivaClientId'			=> ($wpRengineVivaClientId) ? $wpRengineVivaClientId->option_value : '',
				'wpRengineVivaAPI'				=> ($wpRengineVivaAPI) ? $wpRengineVivaAPI->option_value : '',
				'wpRengineVivaMode'				=> ($wpRengineVivaMode) ? $wpRengineVivaMode->option_value : '',
				
				'wpRengineGateway'				=> ($wpRengineGateway) ? $wpRengineGateway->option_value : 'none',
				'wpRengineCash'					=> ($wpRengineCash) ? $wpRengineCash->option_value : 'on',
				'wpRengineCashMode'				=> ($wpRengineCashMode) ? $wpRengineCashMode->option_value : 'yes',
				'wpRengineCalendarMode'			=> ($wpRengineCalendarMode) ? $wpRengineCalendarMode->option_value : '',
				'wpRengineResultsCalendarMode'	=> ($wpRengineResultsCalendarMode) ? $wpRengineResultsCalendarMode->option_value : 'display',
				'wpRengineResultsAvailableMode'	=> ($wpRengineResultsAvailableMode) ? $wpRengineResultsAvailableMode->option_value : 'false',
				'wpRengineCouponMode'			=> ($wpRengineCouponMode) ? $wpRengineCouponMode->option_value : '',
				'wpRengineGroupMode'			=> ($wpRengineGroupMode) ? $wpRengineGroupMode->option_value : '',

				'wpRengineCalendarView'			=> ($wpRengineCalendarView) ? $wpRengineCalendarView->option_value : '',
				'wpRengineCalendarStep'			=> ($wpRengineCalendarStep) ? $wpRengineCalendarStep->option_value : '',
				'wpRengineCalendarLocation'		=> ($wpRengineCalendarLocation) ? $wpRengineCalendarLocation->option_value : '',
				'wpRengineSlideMode'			=> ($wpRengineSlideMode) ? $wpRengineSlideMode->option_value : '',
				'paypal'						=> Helper::assetUrl('/img/paypal.png'),
				'stripe'						=> Helper::assetUrl('/img/stripe.png'),
				'payzen'						=> Helper::assetUrl('/img/payzen.png'),
				'cash'							=> Helper::assetUrl('/img/cashondelivery.png'),
				'viva'							=> Helper::assetUrl('/img/vivapayments.png'),
				'wpRengineCalendarHoursDisabled'=> ($wpRengineCalendarHoursDisabled) ? $wpRengineCalendarHoursDisabled->option_value : '',
				'wpRenginebutton'				=> ($wpRengineButton) ? $wpRengineButton->option_value : '',
				'wpRengineTerms'				=> ($wpRengineTerms) ? $wpRengineTerms : '',
				'wpRengineTermsPage'			=> ($wpRengineTermsPage) ? $wpRengineTermsPage->option_value : '',
				'wpRengineResultsPage'			=> ($wpRengineResultsPage) ? $wpRengineResultsPage->option_value : 'workadu-search-results',
				'wpRengineRevisionPage'			=> ($wpRengineRevisionPage) ? $wpRengineRevisionPage->option_value : 'workadu-contact-info',
				'wpRengineGatewayPage'			=> ($wpRengineGatewayPage) ? $wpRengineGatewayPage->option_value : 'workadu-order-revision',
				'wpRenginePaymentPage'			=> ($wpRenginePaymentPage) ? $wpRenginePaymentPage->option_value : 'workadu-payment',
				'wpRenginePaymentPercent'			=> ($wpRenginePaymentPercent) ? $wpRenginePaymentPercent->option_value : '',
				'pages'							=> ($posts) ? $posts : '',
				'wpRengineRecaptcha'			=> ($wpRengineRecaptcha) ? $wpRengineRecaptcha->option_value : '',
				'wpRengineApiVersion'			=> ($wpRengineApiVersion) ? $wpRengineApiVersion->option_value : '',
			],
		]);
	}

		public function payments(Http $request) {
		if ($request->method() == 'POST') {
			$result = $this->saveConfiguration($request);
			if ($result === true) {
				Notifier::success('WP Rengine Payment Gateways Saved!', true);
			} else {
				Notifier::error('The system encountered an error. ' . implode('<br>', $error_messages), true);
			}
		}
		
	
		try {
		
			$wpRenginePaypalClientId		= Option::where('option_name', 'wpRenginePaypalClientId')->first();
			$wpRenginePaypalSecret			= Option::where('option_name', 'wpRenginePaypalSecret')->first();
			$wpRenginePaypalMode			= Option::where('option_name', 'wpRenginePaypalMode')->first();
			
			$wpRengineStripeClientId		= Option::where('option_name', 'wpRengineStripeClientId')->first();
			$wpRengineStripeSecret			= Option::where('option_name', 'wpRengineStripeSecret')->first();
			$wpRengineStripeMode			= Option::where('option_name', 'wpRengineStripeMode')->first();
			
			$wpRenginePayzenClientId		= Option::where('option_name', 'wpRenginePayzenClientId')->first();
			$wpRenginePayzenSecret			= Option::where('option_name', 'wpRenginePayzenSecret')->first();
			$wpRenginePayzenMode			= Option::where('option_name', 'wpRenginePayzenMode')->first();
			$wpRenginePayzenUsername		= Option::where('option_name', 'wpRenginePayzenUsername')->first();
			$wpRenginePayzenReturnKey		= Option::where('option_name', 'wpRenginePayzenReturnKey')->first();

			$wpRengineVivaClientId			= Option::where('option_name', 'wpRengineVivaClientId')->first();
			$wpRengineVivaAPI				= Option::where('option_name', 'wpRengineVivaAPI')->first();
			$wpRengineVivaMode				= Option::where('option_name', 'wpRengineVivaMode')->first();

			$wpRengineAlphaClientId			= Option::where('option_name', 'wpRengineAlphaClientId')->first();
			$wpRengineAlphaSecret			= Option::where('option_name', 'wpRengineAlphaSecret')->first();
			$wpRengineAlphaMode				= Option::where('option_name', 'wpRengineAlphaMode')->first();

			$wpRengineGateway				= Option::where('option_name', 'wpRengineGateway')->first();
			$wpRengineCash					= Option::where('option_name', 'wpRengineCash')->first();
			$wpRengineCashMode				= Option::where('option_name', 'wpRengineCashMode')->first();
			$wpRengineGatewayPage			= Option::where('option_name', 'wpRengineGatewayPage')->first();
			$wpRenginePaymentPage			= Option::where('option_name', 'wpRenginePaymentPage')->first();
			$wpRenginePaymentPercent		= Option::where('option_name', 'wpRenginePaymentPercent')->first();
			$posts	 						= Post::where('post_status', 'publish')->get();
			

		
		} catch (Exception $e) {
			Notifier::error($e->getMessage(), true);
		}
		return view('@WPRengine/admin/payments.twig', [
			'options' => [
			
				'wpRenginePaypalClientId'		=> ($wpRenginePaypalClientId) ? $wpRenginePaypalClientId->option_value : false,
				'wpRenginePaypalSecret'			=> ($wpRenginePaypalSecret) ? $wpRenginePaypalSecret->option_value : false,
				'wpRenginePaypalMode'			=> ($wpRenginePaypalMode) ? $wpRenginePaypalMode->option_value : false,

				'wpRengineAlphaClientId'		=> ($wpRengineAlphaClientId) ? $wpRengineAlphaClientId->option_value : false,
				'wpRengineAlphaSecret'			=> ($wpRengineAlphaSecret) ? $wpRengineAlphaSecret->option_value : false,
				'wpRengineAlphaMode'			=> ($wpRengineAlphaMode) ? $wpRengineAlphaMode->option_value : false,
				
				'wpRengineStripeClientId'		=> ($wpRengineStripeClientId) ? $wpRengineStripeClientId->option_value : false,
				'wpRengineStripeSecret'			=> ($wpRengineStripeSecret) ? $wpRengineStripeSecret->option_value : false,
				'wpRengineStripeMode'			=> ($wpRengineStripeMode) ? $wpRengineStripeMode->option_value : false,
				
				'wpRenginePayzenClientId'		=> ($wpRenginePayzenClientId) ? $wpRenginePayzenClientId->option_value : false,
				'wpRenginePayzenSecret'			=> ($wpRenginePayzenSecret) ? $wpRenginePayzenSecret->option_value : false,
				'wpRenginePayzenMode'			=> ($wpRenginePayzenMode) ? $wpRenginePayzenMode->option_value : false,
				'wpRenginePayzenUsername'		=> ($wpRenginePayzenUsername) ? $wpRenginePayzenUsername->option_value : false,
				
				'wpRengineVivaClientId'			=> ($wpRengineVivaClientId) ? $wpRengineVivaClientId->option_value : false,
				'wpRengineVivaAPI'				=> ($wpRengineVivaAPI) ? $wpRengineVivaAPI->option_value : false,
				'wpRengineVivaMode'				=> ($wpRengineVivaMode) ? $wpRengineVivaMode->option_value : false,
				
				'wpRengineGateway'				=> ($wpRengineGateway) ? $wpRengineGateway->option_value : 'none',
				'wpRengineCash'					=> ($wpRengineCash) ? $wpRengineCash->option_value : 'on',
				'wpRengineCashMode'				=> ($wpRengineCashMode) ? $wpRengineCashMode->option_value : 'yes',
			
				'paypal'						=> Helper::assetUrl('/img/paypal.png'),
				'stripe'						=> Helper::assetUrl('/img/stripe.png'),
				'payzen'						=> Helper::assetUrl('/img/payzen.png'),
				'cash'							=> Helper::assetUrl('/img/cashondelivery.png'),
				'viva'							=> Helper::assetUrl('/img/vivapayments.png'),
				'alpha'							=> Helper::assetUrl('/img/alpha-logo.png'),
				
				'wpRengineGatewayPage'			=> ($wpRengineGatewayPage) ? $wpRengineGatewayPage->option_value : 'workadu-order-revision',
				'wpRenginePaymentPage'			=> ($wpRenginePaymentPage) ? $wpRenginePaymentPage->option_value : 'workadu-payment',
				'wpRenginePaymentPercent'			=> ($wpRenginePaymentPercent) ? $wpRenginePaymentPercent->option_value : '',
				'pages'							=> ($posts) ? $posts : '',
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
			$wpRengineMapsApiKey			= Option::where('option_name', 'wpRengineMapsApiKey')->first();
		} catch (Exception $e) {
			$error_messages[] = $e->getMessage();
		}

		return view('@WPRengine/admin/places.twig', [
			'parkings' 						=> $parkings_list,
			'wpRengineLocationsMode'		=> ($wpRengineLocationsMode) ? $wpRengineLocationsMode->option_value : 'auto',
			'wpRengineCurrentMode'			=> ($wpRengineCurrentMode) ? $wpRengineCurrentMode->option_value : '',
			'baseCoordinates'				=> ($baseCoordinates) ? $baseCoordinates->option_value : '',
			'baseRadius'					=> ($baseRadius) ? $baseRadius->option_value : '',
			'wpRengineMapsApiKey' 			=>  ($wpRengineMapsApiKey) ? $wpRengineMapsApiKey->option_value : '' 
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

			$wpRengineAlphaClientId			= Option::where('option_name', 'wpRengineAlphaClientId')->first();
			$wpRengineAlphaSecret			= Option::where('option_name', 'wpRengineAlphaSecret')->first();
			$wpRengineAlphaMode				= Option::where('option_name', 'wpRengineAlphaMode')->first();

			$wpRengineStripeClientId		= Option::where('option_name', 'wpRengineStripeClientId')->first();
			$wpRengineStripeSecret			= Option::where('option_name', 'wpRengineStripeSecret')->first();
			$wpRengineStripeMode			= Option::where('option_name', 'wpRengineStripeMode')->first();
			
			$wpRenginePayzenClientId		= Option::where('option_name', 'wpRenginePayzenClientId')->first();
			$wpRenginePayzenSecret			= Option::where('option_name', 'wpRenginePayzenSecret')->first();
			$wpRenginePayzenMode			= Option::where('option_name', 'wpRenginePayzenMode')->first();
			$wpRenginePayzenUsername		= Option::where('option_name', 'wpRenginePayzenUsername')->first();
			$wpRenginePayzenReturnKey		= Option::where('option_name', 'wpRenginePayzenReturnKey')->first();

			$wpRengineVivaClientId			= Option::where('option_name', 'wpRengineVivaClientId')->first();
			$wpRengineVivaAPI				= Option::where('option_name', 'wpRengineVivaAPI')->first();
			$wpRengineVivaMode				= Option::where('option_name', 'wpRengineVivaMode')->first();
			
			$wpRengineGateway				= Option::where('option_name', 'wpRengineGateway')->first();
			$wpRengineCash					= Option::where('option_name', 'wpRengineCash')->first();
			$wpRengineCashMode				= Option::where('option_name', 'wpRengineCashMode')->first();
			$wpRengineCalendarMode			= Option::where('option_name', 'wpRengineCalendarMode')->first();
			$wpRengineResultsCalendarMode	= Option::where('option_name', 'wpRengineResultsCalendarMode')->first();
			$wpRengineResultsAvailableMode	= Option::where('option_name', 'wpRengineResultsAvailableMode')->first();
			$wpRengineCouponMode			= Option::where('option_name', 'wpRengineCouponMode')->first();
			$wpRengineGroupMode				= Option::where('option_name', 'wpRengineGroupMode')->first();

			$wpRengineCalendarView			= Option::where('option_name', 'wpRengineCalendarView')->first();
			$wpRengineCalendarStep			= Option::where('option_name', 'wpRengineCalendarStep')->first();
			$wpRengineSlideMode				= Option::where('option_name', 'wpRengineSlideMode')->first();
			$wpRengineCalendarLocation		= Option::where('option_name', 'wpRengineCalendarLocation')->first();
			$wpRengineCalendarHoursDisabled	= Option::where('option_name', 'wpRengineCalendarHoursDisabled')->first();
			$wpRengineButton				= Option::where('option_name', 'wpRengineButton')->first();
			$wpRengineTerms					= Option::where('option_name', 'wpRengineTerms')->first();
			$wpRengineTermsPage				= Option::where('option_name', 'wpRengineTermsPage')->first();
			$wpRengineResultsPage			= Option::where('option_name', 'wpRengineResultsPage')->first();
			$wpRengineRevisionPage			= Option::where('option_name', 'wpRengineRevisionPage')->first();
			$wpRengineGatewayPage			= Option::where('option_name', 'wpRengineGatewayPage')->first();
			$wpRenginePaymentPage			= Option::where('option_name', 'wpRenginePaymentPage')->first();
			$wpRenginePaymentPercent		= Option::where('option_name', 'wpRenginePaymentPercent')->first();
			$wpRengineRecaptcha				= Option::where('option_name', 'wpRengineRecaptcha')->first();
			$wpRengineApiVersion			= Option::where('option_name', 'wpRengineApiVersion')->first();

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

		// ALPHA

		if ($request->has('wpRengineAlphaClientId')) {
			if ($wpRengineAlphaClientId) {
				$wpRengineAlphaClientId->option_value = $request->input('wpRengineAlphaClientId');
			} else {
				$wpRengineAlphaClientId               = new Option();
				$wpRengineAlphaClientId->option_name  = 'wpRengineAlphaClientId';
				$wpRengineAlphaClientId->option_value = filter_var($request->input('wpRengineAlphaClientId'), FILTER_SANITIZE_STRING);
			}
			$wpRengineAlphaClientId->save();
		}



		if ($request->has('wpRengineAlphaSecret')) {
			if ($wpRengineAlphaSecret) {
				$wpRengineAlphaSecret->option_value = $request->input('wpRengineAlphaSecret');
			} else {
				$wpRengineAlphaSecret               = new Option();
				$wpRengineAlphaSecret->option_name  = 'wpRengineAlphaSecret';
				$wpRengineAlphaSecret->option_value = filter_var($request->input('wpRengineAlphaSecret'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineAlphaSecret->save();
		}

		if ($request->has('wpRengineAlphaMode')) {
			if ($wpRengineAlphaMode) {
				$wpRengineAlphaMode->option_value = $request->input('wpRengineAlphaMode');
			} else {
				$wpRengineAlphaMode               = new Option();
				$wpRengineAlphaMode->option_name  = 'wpRengineAlphaMode';
				$wpRengineAlphaMode->option_value = filter_var($request->input('wpRengineAlphaMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineAlphaMode->save();
		}

		// STRIPE 
		if ($request->has('wpRengineStripeClientId')) {
			if ($wpRengineStripeClientId) {
				$wpRengineStripeClientId->option_value = $request->input('wpRengineStripeClientId');
			} else {
				$wpRengineStripeClientId               = new Option();
				$wpRengineStripeClientId->option_name  = 'wpRengineStripeClientId';
				$wpRengineStripeClientId->option_value = filter_var($request->input('wpRengineStripeClientId'), FILTER_SANITIZE_STRING);
			}
			$wpRengineStripeClientId->save();
		}

		if ($request->has('wpRengineStripeSecret')) {
			if ($wpRengineStripeSecret) {
				$wpRengineStripeSecret->option_value = $request->input('wpRengineStripeSecret');
			} else {
				$wpRengineStripeSecret               = new Option();
				$wpRengineStripeSecret->option_name  = 'wpRengineStripeSecret';
				$wpRengineStripeSecret->option_value = filter_var($request->input('wpRengineStripeSecret'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineStripeSecret->save();
		}

		if ($request->has('wpRengineStripeMode')) {
			if ($wpRengineStripeMode) {
				$wpRengineStripeMode->option_value = $request->input('wpRengineStripeMode');
			} else {
				$wpRengineStripeMode               = new Option();
				$wpRengineStripeMode->option_name  = 'wpRengineStripeMode';
				$wpRengineStripeMode->option_value = filter_var($request->input('wpRengineStripeMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineStripeMode->save();
		}

		// PAYZEN 
		if ($request->has('wpRenginePayzenClientId')) {
			if ($wpRenginePayzenClientId) {
				$wpRenginePayzenClientId->option_value = $request->input('wpRenginePayzenClientId');
			} else {
				$wpRenginePayzenClientId               = new Option();
				$wpRenginePayzenClientId->option_name  = 'wpRenginePayzenClientId';
				$wpRenginePayzenClientId->option_value = filter_var($request->input('wpRenginePayzenClientId'), FILTER_SANITIZE_STRING);
			}
			$wpRenginePayzenClientId->save();
		}

		if ($request->has('wpRenginePayzenSecret')) {
			if ($wpRenginePayzenSecret) {
				$wpRenginePayzenSecret->option_value = $request->input('wpRenginePayzenSecret');
			} else {
				$wpRenginePayzenSecret               = new Option();
				$wpRenginePayzenSecret->option_name  = 'wpRenginePayzenSecret';
				$wpRenginePayzenSecret->option_value = filter_var($request->input('wpRenginePayzenSecret'), FILTER_SANITIZE_STRING); 
			}
			$wpRenginePayzenSecret->save();
		}

		if ($request->has('wpRenginePayzenMode')) {
			if ($wpRenginePayzenMode) {
				$wpRenginePayzenMode->option_value = $request->input('wpRenginePayzenMode');
			} else {
				$wpRenginePayzenMode               = new Option();
				$wpRenginePayzenMode->option_name  = 'wpRenginePayzenMode';
				$wpRenginePayzenMode->option_value = filter_var($request->input('wpRenginePayzenMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRenginePayzenMode->save();
		}

		if ($request->has('wpRenginePayzenUsername')) {
			if ($wpRenginePayzenUsername) {
				$wpRenginePayzenUsername->option_value = $request->input('wpRenginePayzenUsername');
			} else {
				$wpRenginePayzenUsername               = new Option();
				$wpRenginePayzenUsername->option_name  = 'wpRenginePayzenUsername';
				$wpRenginePayzenUsername->option_value = filter_var($request->input('wpRenginePayzenUsername'), FILTER_SANITIZE_STRING); 
			}
			$wpRenginePayzenUsername->save();
		}

		if ($request->has('wpRenginePayzenReturnKey')) {
			if ($wpRenginePayzenReturnKey) {
				$wpRenginePayzenReturnKey->option_value = $request->input('wpRenginePayzenReturnKey');
			} else {
				$wpRenginePayzenReturnKey               = new Option();
				$wpRenginePayzenReturnKey->option_name  = 'wpRenginePayzenReturnKey';
				$wpRenginePayzenReturnKey->option_value = filter_var($request->input('wpRenginePayzenReturnKey'), FILTER_SANITIZE_STRING); 
			}
			$wpRenginePayzenReturnKey->save();
		}

		//VIVA PAYMENTS 
		if ($request->has('wpRengineVivaClientId')) {
			if ($wpRengineVivaClientId) {
				$wpRengineVivaClientId->option_value = $request->input('wpRengineVivaClientId');
			} else {
				$wpRengineVivaClientId               = new Option();
				$wpRengineVivaClientId->option_name  = 'wpRengineVivaClientId';
				$wpRengineVivaClientId->option_value = filter_var($request->input('wpRengineVivaClientId'), FILTER_SANITIZE_STRING);
			}
			$wpRengineVivaClientId->save();
		}

		if ($request->has('wpRengineVivaAPI')) {
			if ($wpRengineVivaAPI) {
				$wpRengineVivaAPI->option_value = $request->input('wpRengineVivaAPI');
			} else {
				$wpRengineVivaAPI               = new Option();
				$wpRengineVivaAPI->option_name  = 'wpRengineVivaAPI';
				$wpRengineVivaAPI->option_value = filter_var($request->input('wpRengineVivaAPI'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineVivaAPI->save();
		}

		if ($request->has('wpRengineVivaMode')) {
			if ($wpRengineVivaMode) {
				$wpRengineVivaMode->option_value = $request->input('wpRengineVivaMode');
			} else {
				$wpRengineVivaMode               = new Option();
				$wpRengineVivaMode->option_name  = 'wpRengineVivaMode';
				$wpRengineVivaMode->option_value = filter_var($request->input('wpRengineVivaMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineVivaMode->save();
		}

		// CASH 
		if ($request->has('wpRengineCash')) {
			if ($wpRengineCash) {
				$wpRengineCash->option_value = $request->input('wpRengineCash');
			} else {
				$wpRengineCash               = new Option();
				$wpRengineCash->option_name  = 'wpRengineCash';
				$wpRengineCash->option_value = filter_var($request->input('wpRengineCash'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineCash->save();
		} else {
			if (!$wpRengineCash) {
				$wpRengineCash               = new Option();
				$wpRengineCash->option_name  = 'wpRengineCash';
			}
			$wpRengineCash->option_value = 'off';
			$wpRengineCash->save();
		}

		if ($request->has('wpRengineCashMode')) {
			if ($wpRengineCashMode) {
				$wpRengineCashMode->option_value = $request->input('wpRengineCashMode');

			} else {
				$wpRengineCashMode               = new Option();
				$wpRengineCashMode->option_name  = 'wpRengineCashMode';
				$wpRengineCashMode->option_value = filter_var($request->input('wpRengineCashMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineCashMode->save();
		} else {
			if (!$wpRengineCashMode) {
				$wpRengineCashMode               = new Option();
				$wpRengineCashMode->option_name  = 'wpRengineCashMode';
			}
			$wpRengineCashMode->option_value = 'no';
			$wpRengineCashMode->save();
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
			if (!$wpRengineCalendarMode) {
				$wpRengineCalendarMode  = new Option();
				$wpRengineCalendarMode->option_name  = 'wpRengineCalendarMode';
			}
			$wpRengineCalendarMode->option_value = 'normal';
			$wpRengineCalendarMode->save();
		}

		if ($request->has('wpRengineResultsCalendarMode')) {
			if ($wpRengineResultsCalendarMode) {
				$wpRengineResultsCalendarMode->option_value = $request->input('wpRengineResultsCalendarMode');
			} else {
				$wpRengineResultsCalendarMode              = new Option();
				$wpRengineResultsCalendarMode->option_name  = 'wpRengineResultsCalendarMode';
				$wpRengineResultsCalendarMode->option_value = filter_var($request->input('wpRengineResultsCalendarMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineResultsCalendarMode->save();
		} else {

			if (!$wpRengineResultsCalendarMode) {
				$wpRengineResultsCalendarMode  = new Option();
				$wpRengineResultsCalendarMode->option_name  = 'wpRengineResultsCalendarMode';
				$wpRengineResultsCalendarMode->option_value = 'display';
				$wpRengineResultsCalendarMode->save();
			}
		}

		if ($request->has('wpRengineResultsAvailableMode')) {
			if ($wpRengineResultsAvailableMode) {
				$wpRengineResultsAvailableMode->option_value = $request->input('wpRengineResultsAvailableMode');
			} else {
				$wpRengineResultsAvailableMode              = new Option();
				$wpRengineResultsAvailableMode->option_name  = 'wpRengineResultsAvailableMode';
				$wpRengineResultsAvailableMode->option_value = filter_var($request->input('wpRengineResultsAvailableMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineResultsAvailableMode->save();
		} else {
			if (!$wpRengineResultsAvailableMode) {
				$wpRengineResultsAvailableMode  = new Option();
				$wpRengineResultsAvailableMode->option_name  = 'wpRengineResultsAvailableMode';
			}
			$wpRengineResultsAvailableMode->option_value = 'false';
			$wpRengineResultsAvailableMode->save();
		}

		if ($request->has('wpRengineCouponMode')) {
			if ($wpRengineCouponMode) {
				$wpRengineCouponMode->option_value = $request->input('wpRengineCouponMode');
			} else {
				$wpRengineCouponMode               = new Option();
				$wpRengineCouponMode->option_name  = 'wpRengineCouponMode';
				$wpRengineCouponMode->option_value = filter_var($request->input('wpRengineCouponMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineCouponMode->save();
		} else {
			if (!$wpRengineCouponMode) {
				$wpRengineCouponMode  = new Option();
				$wpRengineCouponMode->option_name  = 'wpRengineCouponMode';
			}
			$wpRengineCouponMode->option_value = 'hide';
			$wpRengineCouponMode->save();
		}

		if ($request->has('wpRengineGroupMode') ) {
			if ($wpRengineGroupMode) {
				$wpRengineGroupMode->option_value = $request->input('wpRengineGroupMode');
			} else {
				$wpRengineGroupMode               = new Option();
				$wpRengineGroupMode->option_name  = 'wpRengineGroupMode';
				$wpRengineGroupMode->option_value = filter_var($request->input('wpRengineGroupMode'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineGroupMode->save();
		} else {
			if (!$wpRengineGroupMode) {
				$wpRengineGroupMode  = new Option();
				$wpRengineGroupMode->option_name  = 'wpRengineGroupMode';
			}
			$wpRengineGroupMode->option_value = 'hide';
			$wpRengineGroupMode->save();
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
			if (!$wpRengineSlideMode) {
				$wpRengineSlideMode  = new Option();
				$wpRengineSlideMode->option_name  = 'wpRengineSlideMode';
			}
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
			if (!$wpRengineCalendarView) {
				$wpRengineCalendarView  = new Option();
				$wpRengineCalendarView->option_name  = 'wpRengineCalendarView';
			}
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
			if (!$wpRengineCalendarLocation) {
				$wpRengineCalendarLocation  = new Option();
				$wpRengineCalendarLocation->option_name  = 'wpRengineCalendarLocation';
			}
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

		if ($request->has('wpRengineGateway')) {
			if ($wpRengineGateway) {
				$wpRengineGateway->option_value = $request->input('wpRengineGateway');
			} else {
				$wpRengineGateway               = new Option();
				$wpRengineGateway->option_name  = 'wpRengineGateway';
				$wpRengineGateway->option_value = filter_var($request->input('wpRengineGateway'), FILTER_SANITIZE_STRING);  
			}
			$wpRengineGateway->save();
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

		if ($request->has('wpRengineResultsPage')) {
			if ($wpRengineResultsPage) {
				$wpRengineResultsPage->option_value = $request->input('wpRengineResultsPage');
			} else {
				$wpRengineResultsPage               = new Option();
				$wpRengineResultsPage->option_name  = 'wpRengineResultsPage';
				$wpRengineResultsPage->option_value = filter_var($request->input('wpRengineResultsPage'), FILTER_SANITIZE_STRING);  
			}
			$wpRengineResultsPage->save();
		}

		if ($request->has('wpRengineRevisionPage')) {
			if ($wpRengineRevisionPage) {
				$wpRengineRevisionPage->option_value = $request->input('wpRengineRevisionPage');
			} else {
				$wpRengineRevisionPage               = new Option();
				$wpRengineRevisionPage->option_name  = 'wpRengineRevisionPage';
				$wpRengineRevisionPage->option_value = filter_var($request->input('wpRengineRevisionPage'), FILTER_SANITIZE_STRING);  
			}
			$wpRengineRevisionPage->save();
		}

		if ($request->has('wpRengineGatewayPage')) {
			if ($wpRengineGatewayPage) {
				$wpRengineGatewayPage->option_value = $request->input('wpRengineGatewayPage');
			} else {
				$wpRengineGatewayPage               = new Option();
				$wpRengineGatewayPage->option_name  = 'wpRengineGatewayPage';
				$wpRengineGatewayPage->option_value = filter_var($request->input('wpRengineGatewayPage'), FILTER_SANITIZE_STRING);  
			}
			$wpRengineGatewayPage->save();
		}

		if ($request->has('wpRenginePaymentPage')) {
			if ($wpRenginePaymentPage) {
				$wpRenginePaymentPage->option_value = $request->input('wpRenginePaymentPage');
			} else {
				$wpRenginePaymentPage               = new Option();
				$wpRenginePaymentPage->option_name  = 'wpRenginePaymentPage';
				$wpRenginePaymentPage->option_value = filter_var($request->input('wpRenginePaymentPage'), FILTER_SANITIZE_STRING);  
			}
			$wpRenginePaymentPage->save();
		}

		
		if ($request->has('wpRenginePaymentPercent')) {
			if ($wpRenginePaymentPercent) {
				$wpRenginePaymentPercent->option_value = filter_var($request->input('wpRenginePaymentPercent'), FILTER_SANITIZE_NUMBER_INT);  
			} else {
				$wpRenginePaymentPercent               = new Option();
				$wpRenginePaymentPercent->option_name  = 'wpRenginePaymentPercent';
				$wpRenginePaymentPercent->option_value = filter_var($request->input('wpRenginePaymentPercent'), FILTER_SANITIZE_NUMBER_INT);  
			}
			$wpRenginePaymentPercent->save();
		}

		if ($request->has('wpRengineTermsPage')) {
			if ($wpRengineTermsPage) {
				$wpRengineTermsPage->option_value = $request->input('wpRengineTermsPage');
			} else {
				$wpRengineTermsPage               = new Option();
				$wpRengineTermsPage->option_name  = 'wpRengineTermsPage';
				$wpRengineTermsPage->option_value = filter_var($request->input('wpRengineTermsPage'), FILTER_SANITIZE_URL);  
			}
			$wpRengineTermsPage->save();
		}

		if ($request->has('wpRengineRecaptcha')) {
			if ($wpRengineRecaptcha) {
				$wpRengineRecaptcha->option_value = filter_var($request->input('wpRengineRecaptcha'), FILTER_SANITIZE_STRING);
			} else {
				$wpRengineRecaptcha               = new Option();
				$wpRengineRecaptcha->option_name  = 'wpRengineRecaptcha';
				$wpRengineRecaptcha->option_value = filter_var($request->input('wpRengineRecaptcha'), FILTER_SANITIZE_STRING);
			}
			$wpRengineRecaptcha->save();
		} else {
			if (!$wpRengineRecaptcha) {
				$wpRengineRecaptcha               = new Option();
				$wpRengineRecaptcha->option_name  = 'wpRengineRecaptcha';
				$wpRengineRecaptcha->option_value = '';
				$wpRengineRecaptcha->save();
			}
		}

		if ($request->has('wpRengineApiVersion')) {
			if ($wpRengineApiVersion) {
				$wpRengineApiVersion->option_value = $request->input('wpRengineApiVersion');
			} else {
				$wpRengineApiVersion               = new Option();
				$wpRengineApiVersion->option_name  = 'wpRengineApiVersion';
				$wpRengineApiVersion->option_value = filter_var($request->input('wpRengineApiVersion'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineApiVersion->save();
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
		for ($i = 0; $i <= count($parkingsRequest['parking_name']) + 1; $i++) {
			if (!is_null($parkingsRequest['parking_name'][$i])) {
				$parkings[] = [
					'parking_name'        => $parkingsRequest['parking_name'][$i],
					'parking_coordinates' => $parkingsRequest['parking_coordinates'][$i],
				];
			}
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
			$wpRengineMapsApiKey			= Option::where('option_name', 'wpRengineMapsApiKey')->first();

		} catch (Exception $e) {
			$error_messages[] = $e->getMessage();
		}

		if ($request->has('base-coordinates') || $request->has('base-coordinates') == '') {
			if ($baseCoordinates) {
				$baseCoordinates->option_value = $request->input('base-coordinates');
			} else {
				$baseCoordinates               = new Option();
				$baseCoordinates->option_name  = 'base-coordinates';
				$baseCoordinates->option_value = filter_var($request->input('base-coordinates'), FILTER_SANITIZE_STRING); 
			}
			$baseCoordinates->save();
		}

		if ($request->has('base-radius') || $request->input('base-radius') == '' ) {
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


		if ($request->has('wpRengineMapsApiKey') || $request->input('wpRengineMapsApiKey') =='') {
			if ($wpRengineMapsApiKey) {
				$wpRengineMapsApiKey->option_value = filter_var($request->input('wpRengineMapsApiKey'), FILTER_SANITIZE_STRING); 
			} else {
				$wpRengineMapsApiKey               = new Option();
				$wpRengineMapsApiKey->option_name  = 'wpRengineMapsApiKey';
				$wpRengineMapsApiKey->option_value = filter_var($request->input('wpRengineMapsApiKey'), FILTER_SANITIZE_STRING); 
			}
			$wpRengineMapsApiKey->save();
		}

		if (empty($error_messages)) {
			return true;
		} else {
			return $error_messages;
		}

	}
}
?>