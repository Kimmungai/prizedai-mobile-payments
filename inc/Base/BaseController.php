<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Base;

class BaseController
{
  public $plugin_path;
  public $plugin_url;
  public $plugin;
  public $managers = array();
  public $mpesaFields = array();
  public $smsFields = array();



  public function __construct()
  {
    $this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
    $this->plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) );
    $this->plugin_basename = plugin_basename(dirname(__FILE__, 3));
    $this->plugin = "$this->plugin_basename/$this->plugin_basename.php";

    $this->managers = array(
      'cpt_manager' => 'Save payment records',
      'mpesa' => 'Mpesa Payments',
      'sms' => 'Send sms',
    );

    $this->mpesaFields = array(
      'consumer_key' => 'Consumer key',
      'consumer_secret' => 'Consumer secret',
      'passkey' => 'Passkey',
      'shortcode' => 'Short code',
    );

    $this->smsFields = array(
      'username' => 'Username',
      'apikey' => 'Api key',
      'from' => 'From',
    );

  }
}
