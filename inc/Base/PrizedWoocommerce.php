<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Base;

class PrizedWoocommerce
{
  public static function init()
  {
    if( class_exists( 'WC_Payment_Gateway' ) ) {
        class PrizedAIWoocommerceMpesaGateway extends WC_Payment_Gateway {
          public function __construct() {

$this->id = 'prizedai'; // payment gateway plugin ID
$this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
$this->has_fields = true; // in case you need a custom credit card form
$this->method_title = 'PrizedAI payments';
$this->method_description = 'Accept mpesa payments easily. Your customers will receive an STK push and automatically redirected.'; // will be displayed on the options page


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
'default'     => '1. Enter the Mpesa phone number in the box below 2.  Click on the place order button 3.  Enter the mpesa pin on your phone 4. Click on complete.',
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



public function process_payment( $order_id ) {

	global $woocommerce;

	// we need it to get any order detailes
  $customer_order = new WC_Order( $order_id );

   // paid order marked
	$customer_order->payment_complete();

	// this is important part for empty cart
	$woocommerce->cart->empty_cart();



  return array(
    'result' => 'success',
    'redirect' => $this->get_return_url( $customer_order )
  );
}


}
}

  }


}
