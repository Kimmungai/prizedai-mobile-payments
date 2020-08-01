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


  public function __construct()
  {
    $this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
    $this->plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) );
    $this->plugin_basename = plugin_basename(dirname(__FILE__, 3));
    $this->plugin = "$this->plugin_basename/$this->plugin_basename.php";
  }
}
