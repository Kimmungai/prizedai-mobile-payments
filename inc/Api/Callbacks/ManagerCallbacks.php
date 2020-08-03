<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Api\Callbacks;
use PrizedAI\Base\BaseController;

/**
 *
 */
class ManagerCallbacks extends BaseController
{

  public function checkboxSanitize( $input )
  {
    $output = array();
    foreach ($this->managers as $key => $value)
    {
      $output[$key] =isset($input[$key]) ? true : false;
    }
    return $output;
  }

  public function mpesaInputSanitize( $input )
  {
    $output = array();
    foreach ($this->mpesaFields as $key => $value)
    {
      $output[$key] = $input[$key];
    }
    return $output;
  }

  public function smsInputSanitize( $input )
  {
    $output = array();
    foreach ($this->smsFields as $key => $value)
    {
      $output[$key] = $input[$key];
    }
    return $output;
  }

  public function prizedaiMobilePaymentsAdminSectionManager( )
  {
    echo "Manage the sections and features of this plugin";
  }

  public function prizedaiMobilePaymentsMpesaSectionManager( )
  {
    echo "Enter your daraja API credentials";
  }

  public function prizedaiMobilePaymentsSmsSectionManager( )
  {
    echo "Enter your API credentials";
  }

  public function prizedaiMobilePaymentsCheckboxField( $args )
  {
    $name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checkbox = get_option( $option_name );
    $checked = isset($checkbox[$name]) ? ($checkbox[$name] ? true : false) : false;
    echo '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ( $checked ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
  }

  public function prizedaiMobilePaymentsPasswordField( $args )
  {
    $name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$input = get_option( $option_name );
    $value = isset($input[$name]) ? $input[$name] : '';
    echo '<div class="' . $classes . '"><input type="password" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="'.$value.'" class="" ' . $classes . '><label for="' . $name . '"><div></div></label></div>';
  }

  public function prizedaiMobilePaymentsInputField( $args )
  {
    $name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$input = get_option( $option_name );
    $value = isset($input[$name]) ? $input[$name] : '';
    echo '<div class="' . $classes . '"><input type="text" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="'.$value.'" class="" ' . $classes . '><label for="' . $name . '"><div></div></label></div>';
  }

}
