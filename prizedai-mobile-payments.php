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
use PrizedAI\Base\SMS;

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

    add_rewrite_rule( '^/callback/?([^/]*)/?', 'index.php?mpesa_callback_action=1', 'top' );
    add_rewrite_rule( '^/payment/?([^/]*)/?', 'index.php?payment_action=1', 'top' );
    add_rewrite_rule( '^/confirm/?([^/]*)/?', 'index.php?payment_status=1', 'top' );


} );



add_filter( 'query_vars', function( $query_vars ) {



    $query_vars []= 'mpesa_callback_action';
    $query_vars []= 'payment_action';
    $query_vars []= 'payment_status';
    return $query_vars;

} );



add_action( 'wp', function() {


    if ( get_query_var( 'payment_action' ) )
		    prizedai_mpesa_request_payment();

    if ( get_query_var( 'payment_status' ) )
		  prizedai_confirm_payment_status();

    if ( get_query_var( 'mpesa_callback_action' ) )
		  prizedai_mpesa_callback();


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
    $this->description  = trim( $this->description );

		// display the description with <p> tags etc.
		echo wpautop( wp_kses_post( $this->description ) );
	}

	// I will echo() the form, but you can close PHP tags and print it directly in HTML
	echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

	// Add this action hook if you want your custom payment gateway to support it
	do_action( 'woocommerce_credit_card_form_start', $this->id );

	// I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
	echo '<div id="peizedai-mpesa-checkout-form" class="form-row">
          <div id="peizedai-mpesa-loader" class="prizedai-ajax-loader prizedai-hidden">
            <img src="'.plugin_dir_url( __FILE__ ).'assets/img/loader.gif" height="31" width="31" />
          </div>
          <label>Mpesa phone number <span class="required">*</span></label>
		      <input id="prizedai-mpesa-number" type="text" autocomplete="off">
          <small class="prizedai-hidden" id="prizedai-mpesa-number-helper">Valid format: <strong>+254xxxxxxxxx</strong></small>
          <span id="prizedai-mpesa-field-controls" class="prizedai-hidden" >
            <button type="button" onclick="prizedai_complete_mpesa()">Complete</button>
            <button type="button" onclick="prizedai_hide_submit(false)">Retry</button>
          </span>
		    </div>
        <p id="prizedai-mpesa-status-info"></p>

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

  // we received the payment
  $order->payment_complete();
  $order->reduce_order_stock();

  // some notes to customer (replace true with false to make it private)
  //$order->add_order_note( 'Hey, your order is paid! Thank you!', true );

  // Empty cart
  $woocommerce->cart->empty_cart();

  //send sms

  return array(
    'result' => 'success',
    'redirect' => $this->get_return_url( $order )
  );
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

function prizedai_mpesa_request_payment(){
     $data = array();
		 $total = ceil(WC()->cart->total);
     $option = get_option( 'prizedai_mobile_payments_mpesa' );
     $phone = prizedai_clean_phone_number($_POST['mpesaPhoneNumber']);
     $consumer_key = isset($option['consumer_key']) ? $option['consumer_key'] : NULL;
     $consumer_secret = isset($option['consumer_secret']) ? $option['consumer_secret'] : NULL;
     $passkey = isset($option['passkey']) ? $option['passkey'] : NULL;
     //$live = isset($option['live']) ? $option['live'] : NULL;

     $baseUrl = isset($option['live']) ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';


     $url = $baseUrl.'/oauth/v1/generate?grant_type=client_credentials';


     if( !$consumer_key || !$consumer_secret || !$passkey )
     {
       echo 0;
       exit();
     }
     $credentials = base64_encode($consumer_key . ':' . $consumer_secret);



	//Request for access token



	$token_response = wp_remote_get( $url, array('headers' => array('Authorization' => 'Basic ' . $credentials)));



	$token_array = json_decode('{"token_results":[' . $token_response['body'] . ']}');





    if (array_key_exists("access_token", $token_array->token_results[0])) {

        $access_token = $token_array->token_results[0]->access_token;

    }

	else {

		//echo json_encode(array("rescode" => "1", "resmsg" => "Error, unable to send payment request"));
    echo 0;
		exit();

  }



    ///If the access token is available, start lipa na mpesa process

    if (array_key_exists("access_token", $token_array->token_results[0])) {



    ////Starting lipa na mpesa process

    $url = $baseUrl.'/mpesa/stkpush/v1/processrequest';



		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

		$domainName = $_SERVER['HTTP_HOST'].'/';

		$callback_url =  $protocol.$domainName;

    $callback_url = 'https://biznesskit.com/';//delete



		//Generate the password//


		$paybill = isset($option['shortcode']) ? $option['shortcode'] : 174379;

		$timestamp = date("YmdHis");

		$b64 = $paybill.$passkey.$timestamp;

		$pwd = base64_encode($b64);



		///End in pwd generation//



        $curl_post_data = array(

            //Fill in the request parameters with valid values

            'BusinessShortCode' => $paybill,

			'Password' => $pwd,

            'Timestamp' => $timestamp,

            'TransactionType' => 'CustomerPayBillOnline',

            'Amount' => $total,

            'PartyA' => $phone,

            'PartyB' => $paybill,

            'PhoneNumber' => $phone,

            'CallBackURL' => $callback_url.'/index.php?mpesa_callback_action=1',

            'AccountReference' => time(),

            'TransactionDesc' => 'Sending a lipa na mpesa request'

        );



        $data_string = json_encode($curl_post_data);



		$response = wp_remote_post( $url, array('headers' => array('Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $access_token),

		'body'    => $data_string));



		$response_array = json_decode('{"callback_results":[' . $response['body'] . ']}');


		if(array_key_exists("ResponseCode", $response_array->callback_results[0]) && $response_array->callback_results[0]->ResponseCode == 0){

			$response_array->callback_results[0]->MerchantRequestID;
			//echo json_encode(array("rescode" => "0", "resmsg" => "Request accepted for processing, check your phone to enter M-PESA pin"));

      $data['result_code'] = 0;
      $data['result_desc'] = $response_array->callback_results[0]->ResponseDescription;
      $data['merchant_request_id'] = $response_array->callback_results[0]->MerchantRequestID;
      $data['checkout_request_id'] = $response_array->callback_results[0]->CheckoutRequestID;


		}

		else{

			//echo json_encode(array("rescode" => "1", "resmsg" => "Payment request failed, please try again"));
      $data['result_code'] = 1;
      $data['result_desc'] = NULL;
      $data['merchant_request_id'] = NULL;
      $data['checkout_request_id'] = NULL;
      echo 0;
      exit();

		}

    $data['phone_number'] = $phone;

    echo prizedai_store_gateway_response( 'mpesa', $data );

        exit();



    }



}

function prizedai_clean_phone_number($phone)
{
    $phone = str_replace("-", "", $phone);
    $phone = str_replace( array(' ', '<', '>', '&', '{', '}', '*', "+", '!', '@', '#', "$", '%', '^', '&'), "", $phone );
	  $phone = "254".substr($phone, -9);
    return $phone;
}

function prizedai_store_gateway_response( $gateway, $data )
{
  global $wpdb;

  switch ($gateway) {
    case 'mpesa':

      $name = $wpdb->prefix .'prizedai_mobile_payments';

      $sql = ' INSERT INTO '.$name.' ( gateway, phone_number, merchant_request_id, checkout_request_id, result_code, result_desc,created_at ) VALUES ( "mpesa", "'.$data['phone_number'].'", "'.$data['merchant_request_id'].'", "'.$data['checkout_request_id'].'", "'.$data['result_code'].'", "'.$data['result_desc'].'", "'.time().'" );';

      $wpdb->query( $sql );

      return $wpdb->insert_id;

    break;

  }
}

function prizedai_confirm_payment_status()
{
  global $wpdb;
  $name = $wpdb->prefix .'prizedai_mobile_payments';
  $sql = 'SELECT status FROM '.$name.' WHERE id = '.$_POST['transactionID'].'';
  echo $wpdb->get_var($sql);
  exit();
}

function prizedai_mpesa_callback()
{
  if( !isset($_POST["Body"]) )
    exit();
  $body = $_POST["Body"];
  $callback = $body["stkCallback"];


  if( !isset($callback["ResultCode"]) )
    exit();
  if( $callback["ResultCode"] != "0" )
    exit();

  global $wpdb;
  $name = $wpdb->prefix .'prizedai_mobile_payments';
  $sql = 'UPDATE '.$name.' SET status = 1 WHERE  merchant_request_id = "'.$callback["MerchantRequestID"].'"';
  echo $wpdb->query($sql);

  $option = get_option( 'prizedai_mobile_payments_sms' );

  if( !isset($option['enabled']) )
    exit();

  $sql2 = 'SELECT phone_number FROM '.$name.' WHERE merchant_request_id = "'.$callback["MerchantRequestID"].'"';

  $recipient = $wpdb->get_var($sql2);

  $content =  isset($option['order_content']) && !empty($option['order_content']) ? $option['order_content'] : 'Dear customer, your order has been received. Thank you.';

  SMS::sendSMS($recipient,$content);
  exit();
}
