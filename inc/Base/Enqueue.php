<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Base;
use \PrizedAI\Base\BaseController;
class Enqueue extends BaseController
{
  public function register()
  {
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
  }

  public function enqueue()
  {
    wp_enqueue_style( 'PrizedaiMobilePaymentsStyles', $this->plugin_url.'assets/css/master.css' );
    wp_enqueue_script( 'PrizedaiMobilePaymentsScript', $this->plugin_url.'assets/js/main.js' );
  }
}
