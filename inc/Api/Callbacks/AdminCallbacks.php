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
  public function nogo()
  {
    return require_once("$this->plugin_path/templates/nogo.php");
  }

}
