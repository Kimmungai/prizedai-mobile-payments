<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Api\Callbacks;
use PrizedAI\Base\BaseController;

/**
 *
 */
class AdminCallbacks extends BaseController
{
  public function adminDashboard()
  {
    return require_once("$this->plugin_path/templates/admin.php");
  }
  public function mpesa()
  {
    return require_once("$this->plugin_path/templates/mpesa.php");
  }
  public function prizedaiMobilePaymentsOptionsGroup( $input )
  {
    return $input;
  }
  public function prizedaiMobilePaymentsAdminSection( )
  {
    echo "Daraja API Credentials";
  }
  public function prizedaiMobilePaymentsConsumerKey( )
  {
    $value = get_option('consumer_key');
    echo "<input type='text' class='regular-text' name='consumer_key' value='".$value."' placeholder='Your consumer key' required/>";
  }
  public function prizedaiMobilePaymentsPasskey( )
  {
    $value = get_option('passkey');
    echo "<input type='text' class='regular-text' name='passkey' value='".$value."' placeholder='Your passkey' required/>";
  }
  public function prizedaiMobilePaymentsShortCode( )
  {
    $value = get_option('shortcode');
    echo "<input type='number' class='regular-text' name='shortcode' value='".$value."' placeholder='Your paybill number' required/>";
  }
  public function prizedaiMobilePaymentsUrl( )
  {
    $value = get_option('url');
    echo "<input type='url' class='regular-text' name='url' value='".$value."' placeholder='Enter url' required/>";
  }
}
