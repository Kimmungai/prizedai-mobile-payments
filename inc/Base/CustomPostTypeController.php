<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Base;
use \PrizedAI\Base\BaseController;
use PrizedAI\Api\SettingsApi;
use PrizedAI\Api\Callbacks\AdminCallbacks;

class CustomPostTypeController extends BaseController
{
  public $subpages;
  public $callbacks;
  public $settings;

  public function register()
  {
    $option = get_option( 'prizedai_mobile_payments_plugin' );
    $activated = isset($option['cpt_manager']) ? $option['cpt_manager'] : false;

    if( !$activated )
      return;

    $this->settings = new SettingsApi();
    $this->setSubpages();
    $this->settings->addSubPages( $this->subpages )->register();
    $this->callbacks = new AdminCallbacks();
    add_action( 'init', array($this,'activate'));
  }

  public function activate()
  {
    register_post_type( 'prizedai_payments',
      array(
        'labels' => array(
          'name' => 'Payments',
          'singular_name' => 'Payment',
        ),
        'public' => true,
        'has_archive' => true,
      ));
  }

  public function setSubpages()
  {
    $this->subpages = array();
  }
}
