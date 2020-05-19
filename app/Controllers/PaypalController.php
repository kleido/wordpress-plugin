<?php
namespace WPRengine\Controllers;

// Wrapper methods for all PayPal integration
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use Herbert\Framework\Models\Option;

// for documentation and samples visit http://paypal.github.io/PayPal-PHP-SDK/sample/
class PaypalController
{

	private $apiContext;

	public function __construct()
	{
		$this->apiContext = $this->getApiContext();
	}

	/**
	 * This function is used in the contructor to build the necessary context for the payment functions to work
	 * @return object the mandatory class context
	 */
	public function getApiContext()
	{

		$PayPalClientID     = Option::where('option_name', 'wpRenginePaypalClientId')->first()->option_value;
		$PayPalClientSecret = Option::where('option_name', 'wpRenginePaypalSecret')->first()->option_value;
		$PayPalMode 		= Option::where('option_name', 'wpRenginePaypalMode')->first()->option_value;

		$siteurl = get_site_url();
		if (strpos($siteurl, 'localhost') === false)
		{
			$logLevel = 'INFO';
		}
		else
		{
			$logLevel = 'DEBUG';
		}

		$apiContext = new \PayPal\Rest\ApiContext(
			new \PayPal\Auth\OAuthTokenCredential(
				// WHEN THE SITE IS LIVE CHANGE THE KEYS BELOW WITH THE LIVE ONES!
				$PayPalClientID,    // ClientID
				$PayPalClientSecret // ClientSecret
			)
		);

		$apiContext->setConfig(
			[
				'mode'           => $PayPalMode ,
				'log.LogEnabled' => true,
				'log.FileName'   => 'wp-content/plugins/reservation-engine/PayPal.log',
				'log.LogLevel'   => $logLevel, // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
				'cache.enabled'  => false,
				// 'http.CURLOPT_CONNECTTIMEOUT' => 30
				// 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
				//'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
			]
		);

		return $apiContext;
	}

/**
 * Create a payment using the buyer's paypal
 * account as the funding instrument. Your app
 * will have to redirect the buyer to the paypal
 * website, obtain their consent to the payment
 * and subsequently execute the payment using
 * the execute API call.
 *
 * @param string $total	payment amount in DDD.DD format
 * @param string $currency	3 letter ISO currency code such as 'EUR'
 * @param string $paymentDesc	A description about the payment
 * @param string $returnUrl	The url to which the buyer must be redirected
 * 				to on successful completion of payment
 * @param string $cancelUrl	The url to which the buyer must be redirected
 * 				to if the payment is cancelled
 * @return \PayPal\Api\Payment
 */

	public function makePaymentUsingPayPal($total, $currency, $paymentDesc, $items, $returnUrl, $cancelUrl)
	{

		$payer = new Payer();
		$payer->setPaymentMethod('paypal');

		// Specify the payment amount.
		$amount = new Amount();
		$amount->setCurrency($currency);
		$amount->setTotal($total);
		$itemsArray = [];
		foreach ($items as $item)
		{
			$itemPP = new Item();
			$itemPP->setName($item['name'])
				->setCurrency($currency)
				->setQuantity($item['quantity'])
				->setPrice($item['price']);
			$itemsArray[] = $itemPP;
		}
		$itemList = new ItemList();
		$itemList->setItems($itemsArray);

		// ###Transaction
		// A transaction defines the contract of a
		// payment - what is the payment for and who
		// is fulfilling it. Transaction is created with
		// a `Payee` and `Amount` types
		$transaction = new Transaction();
		$transaction
			->setAmount($amount)
			->setDescription($paymentDesc)
			->setItemList($itemList)
			->setInvoiceNumber(uniqid());

		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl($returnUrl);
		$redirectUrls->setCancelUrl($cancelUrl);

		$payment = new Payment();
		$payment->setRedirectUrls($redirectUrls);
		$payment->setIntent('sale');
		$payment->setPayer($payer);
		$payment->setTransactions([$transaction]);

		try {
			$payment->create($this->getApiContext());
		}
		catch (PayPal\Exception\PayPalConnectionException $ex)
		{
			error_log(print_r($ex->getCode, true));
			error_log(print_r($ex->getData, true));
			return $ex;
		}
		catch (Exception $ex)
		{
			return $ex;
		}
		$approvalUrl = $payment->getApprovalLink();
		return ['approvalUrl' => $approvalUrl, 'paymentID' => $payment->getId()];
	}

/**
 * Retrieves the payment information based on PaymentID from Paypal APIs
 * Use it if you want to show payment related data to the user
 *
 * @param $paymentId
 *
 * @return Payment
 */
	public function getPaymentDetails($paymentId)
	{
		try {
			$payment = Payment::get($paymentId, $this->getApiContext());
		}
		catch (Exception $e)
		{
			return $e;
		}
		return $payment;
	}
/**
 * Completes the payment once buyer approval has been
 * obtained. Used only when the payment method is 'paypal'
 *
 * @param string $paymentId id of a previously created
 * 		payment that has its payment method set to 'paypal'
 * 		and has been approved by the buyer.
 *
 * @param string $payerId PayerId as returned by PayPal post
 * 		buyer approval.
 */
	public function executePayment($paymentId, $payerId)
	{
		$payment = $this->getPaymentDetails($paymentId);
		if (is_a($payment, 'PayPal\Api\Payment'))
		{
			if ($payment->getState() == 'approved')
			{
				return $payment;
			}
			$paymentExecution = new PaymentExecution();
			$paymentExecution->setPayerId($payerId);
			try {
				// Execute the payment
				$result = $payment->execute($paymentExecution, $this->getApiContext());
				try {
					$result = Payment::get($paymentId, $this->getApiContext());
				}
				catch (PayPal\Exception\PayPalConnectionException $ex)
				{
					error_log(print_r($ex->getCode, true));
					error_log(print_r($ex->getData, true));
					return $ex;
				}
				catch (Exception $ex)
				{
					return $ex;
				}
			}
			catch (PayPal\Exception\PayPalConnectionException $ex)
			{
				error_log(print_r($ex->getCode, true));
				error_log(print_r($ex->getData, true));
				return $ex;
			}
			catch (Exception $ex)
			{
				return $ex;
			}
			return $result;
		}
		else
		{
			return false;
		}
	}


}