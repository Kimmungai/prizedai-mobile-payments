<?php
/**
*@package prizedai-mobile-payments
**/

/*
Plugin Name: Prized Ai Mobile Payments
Plugin URI: https://github.com/kimmungai/prizedai-mobile-payments
Description: This plugin adds Mpesa to WooCommerce payment gateways. Simply add your Daraja API credentials to use the plugin.
version: 1.2.0
Author: Peter Kimani
Author URI: #
License: GPLv2 or later
Text Domain: prizedai-mobile-payments
*/

/*
Copyright (C) 2020-2030  Prized AI Ltd

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined('ABSPATH') or die("Forbidden!");

if( ! in_array( 'woocommerce/woocommerce.php',apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
  return;

if( file_exists( dirname(__FILE__).'/vendor/autoload.php' ) )
  require_once dirname(__FILE__).'/vendor/autoload.php';


use PrizedAI\Base\Activate;
use PrizedAI\Base\Deactivate;

/*
*Code that excutes on activation
*/
function activatePrizedAiMobilePaymentsPlugin()
{
  Activate::activate();
}

/*
*Code that excutes on deactivation
*/
function deactivatePrizedAiMobilePaymentsPlugin()
{
  Deactivate::deactivate();
}

register_activation_hook( __FILE__, 'activatePrizedAiMobilePaymentsPlugin' );
register_deactivation_hook( __FILE__, 'deactivatePrizedAiMobilePaymentsPlugin' );

if( class_exists('PrizedAI\\Init') )
  PrizedAI\Init::register_services();


add_action( 'plugins_loaded', 'prizedAiMobilePaymentsWoocommerceInit', 11 );


//Callback scanner function start

add_action( 'init', function() {

    add_rewrite_rule( '^/scanner/?([^/]*)/?', 'index.php?scanner_action=1', 'top' );
    add_rewrite_rule( '^/payment/?([^/]*)/?', 'index.php?payment_action=1', 'top' );


} );



add_filter( 'query_vars', function( $query_vars ) {



    $query_vars []= 'scanner_action';
    $query_vars []= 'payment_action';

    return $query_vars;

} );



add_action( 'wp', function() {



    if ( get_query_var( 'scanner_action' ) ) {

        // invoke scanner function

		woompesa_scan_transactions();

    }

    if ( get_query_var( 'payment_action' ) ) {

        // invoke payment function

		mpesa_request_payment();

    }

} );

function prizedAiMobilePaymentsWoocommerceInit() {
    if( class_exists( 'WC_Payment_Gateway' ) ) {
        class PrizedAIWoocommerceMpesaGateway extends WC_Payment_Gateway {
          public function __construct() {

$this->id = 'prizedai'; // payment gateway plugin ID
$this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
$this->has_fields = true; // in case you need a custom credit card form
$this->method_title = 'PrizedAI payments';
$this->method_description = 'Accept mpesa payments easily. Your customers will receive an STK push and automatically redirected.'; // will be displayed on the options page

// gateways can support subscriptions, refunds, saved payment methods,
// but in this tutorial we begin with simple payments
$this->supports = array(
  'products'
);

// Method with all the options fields
$this->init_form_fields();

// Load the settings.
$this->init_settings();
$this->title = $this->get_option( 'title' );
$this->description = $this->get_option( 'description' );
$this->enabled = $this->get_option( 'enabled' );
$this->testmode = 'yes' === $this->get_option( 'testmode' );
$this->private_key = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
$this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );

// This action hook saves the settings
add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

// You can also register a webhook here
// add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
}

public function init_form_fields(){

$this->form_fields = array(
'enabled' => array(
'title'       => 'Enable/Disable',
'label'       => 'Enable PrizedAI mobile payments',
'type'        => 'checkbox',
'description' => '',
'default'     => 'no'
),
'title' => array(
'title'       => 'Title',
'type'        => 'text',
'description' => 'This controls the title which the user sees during checkout.',
'default'     => 'Mpesa',
'desc_tip'    => true,
),
'description' => array(
'title'       => 'Description',
'type'        => 'textarea',
'description' => 'This controls the description which the user sees during checkout.',
'default'     => 'Pay with your credit card via our super-cool payment gateway.',
),
);
}


public function payment_fields() {

	// ok, let's display some description before the payment form
	if ( $this->description ) {
		// you can instructions for test mode, I mean test card numbers etc.
		if ( $this->testmode ) {
			$this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#" target="_blank" rel="noopener noreferrer">documentation</a>.';
			$this->description  = trim( $this->description );
		}
		// display the description with <p> tags etc.
		echo wpautop( wp_kses_post( $this->description ) );
	}

	// I will echo() the form, but you can close PHP tags and print it directly in HTML
	echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

	// Add this action hook if you want your custom payment gateway to support it
	do_action( 'woocommerce_credit_card_form_start', $this->id );

	// I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
	echo '<div class="form-row">
          <label>Mpesa phone number <span class="required">*</span></label>
		      <input id="prizedai-mpesa-number" type="text" autocomplete="off">
          <small class="hidden" id="prizedai-mpesa-number-helper">Valid format: <strong>+254xxxxxxxxx</strong></small>
		    </div>
		<div class="clear"></div>';

	do_action( 'woocommerce_credit_card_form_end', $this->id );

	echo '<div class="clear"></div></fieldset>';

}

/*public function validate_fields(){

	if( empty( $_POST[ 'billing_first_name' ]) ) {
		wc_add_notice(  'First name is required!', 'error' );
		return false;
	}
	return true;

}*/

public function process_payment( $order_id ) {

	global $woocommerce;

	// we need it to get any order detailes
	$order = wc_get_order( $order_id );

  return array(
    'result' => 'success',
    'redirect' => $this->get_return_url( $order )
  );


	/*
 	 * Array with parameters for API interaction
	 */
	$args = array(



	);

	/*
	 * Your API interaction could be built with wp_remote_post()
 	 */
	 $response = wp_remote_post( '{payment processor endpoint}', $args );


	// if( !is_wp_error( $response ) ) {
  if( 0 ){

    return $this->mpesa_request_payment();

		 $body = json_decode( $response['body'], true );

		 // it could be different depending on your payment processor
		 if ( $body['response']['responseCode'] == 'APPROVED' ) {

			// we received the payment
			$order->payment_complete();
			$order->reduce_order_stock();

			// some notes to customer (replace true with false to make it private)
			$order->add_order_note( 'Hey, your order is paid! Thank you!', true );

			// Empty cart
			$woocommerce->cart->empty_cart();

			// Redirect to the thank you page
			return array(
				'result' => 'success',
				'redirect' => $this->get_return_url( $order )
			);

		 } else {
			wc_add_notice(  'Please try again.', 'error' );
			return;
		}

	} else {
		wc_add_notice(  'Connection error.', 'error' );

		return;
	}

}

public function webhook() {

	$order = wc_get_order( $_GET['id'] );
	$order->payment_complete();
	$order->reduce_order_stock();

	update_option('webhook_debug', $_GET);
}





}
}

}

add_filter( 'woocommerce_payment_gateways', 'add_to_woo_noob_payment_gateway');

function add_to_woo_noob_payment_gateway( $gateways ) {
    $gateways[] = 'PrizedAIWoocommerceMpesaGateway';
    return $gateways;
}

/////Scanner start

function woompesa_scan_transactions(){
//The code below is invoked after customer clicks on the Confirm Order button
echo json_encode(array("rescode" => "76", "resmsg" => "Callback processing has been disabled, please download the Pro Version of the plugin."));

exit();
}

//Payments start

function mpesa_request_payment(){



		echo $total = ceil(WC()->cart->total);

		$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';



    $YOUR_APP_CONSUMER_KEY = 'l1XpJsuPoThwNfHARKX4I9GwQgvgiSu5';

    $YOUR_APP_CONSUMER_SECRET = 'RxAKb7qr4AbzrYM1';

    $credentials = base64_encode($YOUR_APP_CONSUMER_KEY . ':' . $YOUR_APP_CONSUMER_SECRET);



	//Request for access token



	$token_response = wp_remote_get( $url, array('headers' => array('Authorization' => 'Basic ' . $credentials)));



	$token_array = json_decode('{"token_results":[' . $token_response['body'] . ']}');





    if (array_key_exists("access_token", $token_array->token_results[0])) {

        $access_token = $token_array->token_results[0]->access_token;

    }

	else {

		echo json_encode(array("rescode" => "1", "resmsg" => "Error, unable to send payment request"));

		exit();

  }



    ///If the access token is available, start lipa na mpesa process

    if (array_key_exists("access_token", $token_array->token_results[0])) {



        ////Starting lipa na mpesa process

        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';



		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

		$domainName = $_SERVER['HTTP_HOST'].'/';

		$callback_url =  $protocol.$domainName;

    $callback_url = 'https://biznesskit.com/';//delete



		//Generate the password//

		$shortcd = 174379;

		$timestamp = date("YmdHis");

		$b64 = $shortcd.'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'.$timestamp;

		$pwd = base64_encode($b64);



		///End in pwd generation//



        $curl_post_data = array(

            //Fill in the request parameters with valid values

            'BusinessShortCode' => $shortcd,

			'Password' => $pwd,

            'Timestamp' => $timestamp,

            'TransactionType' => 'CustomerPayBillOnline',

            'Amount' => $total,

            'PartyA' => $_POST['mpesaPhoneNumber'],

            'PartyB' => $shortcd,

            'PhoneNumber' => $_POST['mpesaPhoneNumber'],

            'CallBackURL' => $callback_url.'/index.php?scanner_action=1',

            'AccountReference' => time(),

            'TransactionDesc' => 'Sending a lipa na mpesa request'

        );



        $data_string = json_encode($curl_post_data);



		$response = wp_remote_post( $url, array('headers' => array('Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $access_token),

		'body'    => $data_string));



		$response_array = json_decode('{"callback_results":[' . $response['body'] . ']}');

		if(array_key_exists("ResponseCode", $response_array->callback_results[0]) && $response_array->callback_results[0]->ResponseCode == 0){

			$response_array->callback_results[0]->MerchantRequestID;
			echo json_encode(array("rescode" => "0", "resmsg" => "Request accepted for processing, check your phone to enter M-PESA pin"));



		}

		else{

			echo json_encode(array("rescode" => "1", "resmsg" => "Payment request failed, please try again"));



		}

        exit();



    }



}
