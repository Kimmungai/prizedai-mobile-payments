<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI;

final class Init
{
  /**
  *Store all classes inside an array
  *@return array full list of classes
  **/
  public static function get_services()
  {
    return [
      Pages\Admin::class,
      Base\Enqueue::class,
      Base\SettingsLink::class,
    ];
  }

  /**
  *Loop through classes and initialize them if the register method exits
  *@return null
  **/
  public static function register_services()
  {
    foreach ( self::get_services() as $class )
    {
      $service = self::instantiate( $class );
      if( method_exists( $service, 'register' ) )
        $service->register();
    }
  }

  /**
  *Initiale a class
  *@param $class
  *@return instance of $class
  **/
  private static function instantiate( $class )
  {
    return new $class;
  }

}


/*use PrizedAI\Base\Activate;
use PrizedAI\Base\Deactivate;
use PrizedAI\Base\AdminPages;

if( !class_exists( 'PrizedaiMobilePayments' ) ):

class PrizedaiMobilePayments
{
  public $name;
  public function __construct()
  {
    $this->name = plugin_basename(__FILE__);
    add_action( 'init', array( $this, 'custom_post_type' ) );
  }
  public function register_admin_scripts()
  {
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
    add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );

    add_filter( "plugin_action_links_$this->name", array($this,'settings_link') );

  }
  public function settings_link( $links )
  {
    $settings_link = '<a href="admin.php?page=prizedai_mobile_payments_plugin">Settings</a>';
    array_push( $links, $settings_link);
    return $links;
  }
  public function add_admin_pages()
  {
    add_menu_page( 'Mobile payments', 'Mobile payments', 'manage_options', 'prizedai_mobile_payments_plugin',array($this,'admin_index'),'dashicons-chart-pie',110 );
  }
  public function register_scripts()
  {
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
  }
  public function admin_index()
  {
    require_once( plugin_dir_path(__FILE__).'templates/admin.php' );

  }
  public function activate()
  {
    //generate a CPT
    $this->custom_post_type();
    Activate::activate();
  }
  public function deactivate()
  {
    Deactivate::deactivate();
  }

  public function custom_post_type()
  {
    $args = [
      'public' => true,
      'label' => 'Mobile payments',
      'menu_icon' => 'dashicons-chart-pie',
     ];
    register_post_type( 'mobile_payments', $args );
  }

  public function enqueue()
  {
    wp_enqueue_style( 'PrizedaiMobilePaymentsStyles', plugins_url('/assets/css/master.css',__FILE__) );
    wp_enqueue_script( 'PrizedaiMobilePaymentsScript', plugins_url('/assets/js/main.js',__FILE__) );
  }
}

endif;

if( class_exists( 'PrizedaiMobilePayments' ) ):
  $prizedaiMobilePayments = new PrizedaiMobilePayments();
  $prizedaiMobilePayments->register_admin_scripts();
  $prizedaiMobilePayments->register_scripts();
else:
  exit();
endif;*/
