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
use PrizedAI\Base\PrizedWoocommerce;

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

function prizedAiMobilePaymentsWoocommerceInit()
{
     PrizedWoocommerce::init();
}

add_filter( 'woocommerce_payment_gateways', 'add_to_woo_noob_payment_gateway');

function add_to_woo_noob_payment_gateway( $gateways ) {
    $gateways[] = 'PrizedAIWoocommerceMpesaGateway';
    return $gateways;
}

//Payments start

function prizedai_mpesa_request_payment(){
     $data = array();
		 $total = ceil(WC()->cart->total);
     //$total = 1;
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
  $postData = file_get_contents('php://input');
  $encapsulate = '{"callback_results":[' . $postData . ']}';
  $json_data = json_decode($encapsulate, true);
  $key = 0;

  $merchant_id = $json_data["callback_results"][$key]["Body"]["stkCallback"]["MerchantRequestID"];


  $rescode = $json_data["callback_results"][$key]["Body"]["stkCallback"]["ResultCode"];


  if( !$merchant_id  )
    exit();

  if( $rescode != "0" )
    exit();

  global $wpdb;
  $name = $wpdb->prefix .'prizedai_mobile_payments';
  $sql = 'UPDATE '.$name.' SET status = 1 WHERE  merchant_request_id = "'.$merchant_id.'"';
  echo $wpdb->query($sql);

  $option = get_option( 'prizedai_mobile_payments_sms' );

  if( !isset($option['enabled']) )
    exit();

  $sql2 = 'SELECT phone_number FROM '.$name.' WHERE merchant_request_id = "'.$merchant_id.'"';

  $recipient = $wpdb->get_var($sql2);

  $content =  isset($option['order_content']) && !empty($option['order_content']) ? $option['order_content'] : 'Dear customer, your order has been received. Thank you.';

  SMS::sendSMS($recipient,$content);
  exit();
}
