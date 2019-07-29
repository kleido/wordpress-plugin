<?php
namespace WPRengine\Controllers;

// Wrapper methods for all Viva integration
use Herbert\Framework\Models\Option;

// for documentation and samples visit http://paypal.github.io/PayPal-PHP-SDK/sample/
class VivaPaymentsController
{

	public function createPayment($mode, $merchandid, $apikey, $amount, $fullname, $email, $phone, $order) {

		// The POST URL and parameters

		if ($mode == 'sandbox') {
			$request =  'http://demo.vivapayments.com/api/orders';	// demo environment URL
		} else {
			$request =  'https://www.vivapayments.com/api/orders';	// production environment URL
		}
		
		// Your merchant ID and API Key can be found in the 'Security' settings on your profile.
		$MerchantId = $merchandid;
		$APIKey = $apikey; 	
		//Set the Payment Amount
		$Amount = $amount*100;	// Amount in cents
		//Set some optional parameters (Full list available here: https://github.com/VivaPayments/API/wiki/Optional-Parameters)
		$AllowRecurring = 'true'; // This flag will prompt the customer to accept recurring payments in tbe future.
		$RequestLang = 'en-US'; //This will display the payment page in English (default language is Greek)
		$Source = 'Default'; // This will assign the transaction to the Source with Code = "Default". If left empty, the default source will be used.
		$postargs = 'Amount='.urlencode($Amount).'&AllowRecurring='.$AllowRecurring.'&RequestLang='.$RequestLang.'&SourceCode='.$Source.'&FullName='.$fullname.'&Email='.$email.'&Phone='.$phone.'&MerchantTrns='.$order['id'].'&CustomerTrns='.'This is a payment for booking #'.$order['reference'];

		// Get the curl session object
		$session = curl_init($request);
		// Set the POST options.
		curl_setopt($session, CURLOPT_POST, true);
		curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_USERPWD, $MerchantId.':'.$APIKey);
		curl_setopt($session, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
		// Do the POST and then close the session
		$response = curl_exec($session);
		// Separate Header from Body
		$header_len = curl_getinfo($session, CURLINFO_HEADER_SIZE);
		$resHeader = substr($response, 0, $header_len);
		$resBody =  substr($response, $header_len);
		curl_close($session);
		// Parse the JSON response
		try {
			if(is_object(json_decode($resBody))){
			  	$resultObj=json_decode($resBody);
			}else{
				preg_match('#^HTTP/1.(?:0|1) [\d]{3} (.*)$#m', $resHeader, $match);
						throw new \Exception("API Call failed! The error was: ".trim($match[1]));
			}
		} catch( Exception $e ) {
			echo $e->getMessage();
		}
		if ($resultObj->ErrorCode==0){	//success when ErrorCode = 0
			$orderId = $resultObj->OrderCode;
			if ($mode == 'sandbox') {
				$PaymentUrl = "http://demo.vivapayments.com/web/newtransaction.aspx?ref=".$orderId; 
			} else {
				$PaymentUrl = "https://www.vivapayments.com/web/newtransaction.aspx?ref=".$orderId; 
			}
			
			return ['id'=>$orderId, 'approvalUrl' => $PaymentUrl];
			// echo 'Your Order Code is: <b>'. $orderId.'</b>';
			// echo '<br/><br/>';
			// echo 'To simulate a successfull payment, use the credit card 4111 1111 1111 1111, with a valid expiration date and 111 as CVV2.';
			// echo '</br/><a href="http://demo.vivapayments.com/web/newtransaction.aspx?ref='.$orderId.'" >Make Payment</a>';
		} else {
			return false;
		}
	}


	public function getOrder ($mode, $order, $merchandid, $apikey) {
		// The POST URL and parameters
		
		if ($mode == 'sandbox') {
			$request =  'http://demo.vivapayments.com/api/orders';	// demo environment URL
		} else {
			$request =  'https://www.vivapayments.com/api/orders';	// production environment URL
		}
		
		// Your merchant ID and API Key can be found in the 'Security' settings on your profile.
		$MerchantId = $merchandid;
		$APIKey = $apikey; 	
		// Set your order code here
		$OrderCode = $order;	
		$getargs = '/'.urlencode($OrderCode);
		// Get the curl session object
		$session = curl_init($request);
		// Set the GET options.
		curl_setopt($session, CURLOPT_HTTPGET, true);
		curl_setopt($session, CURLOPT_URL, $request . $getargs);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_USERPWD, $MerchantId.':'.$APIKey);
		curl_setopt($session, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
		// Do the GET and then close the session
		$response = curl_exec($session);
		curl_close($session);
		// Parse the JSON response
		try {
			// echo $response . '<br /><br />'; // you can see all properties with their values in a json string here.
				
			if(is_object(json_decode($response))){
			  	$resultObj=json_decode($response);
			}
		} catch( Exception $e ) {
			echo $e->getMessage();
		}
		// return $request . $getargs; 		
		if ($resultObj->ErrorCode==0){	//success when ErrorCode = 0
			return $resultObj;
		} else {
			return false;
		}
	}

}
