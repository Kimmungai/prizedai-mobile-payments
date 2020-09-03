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
      Pages\Dashboard::class,
      Base\Enqueue::class,
      Base\SettingsLink::class,
      Base\CustomPostTypeController::class,
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
