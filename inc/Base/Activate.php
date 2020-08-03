<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Base;

class Activate
{
  public static function activate()
  {
    flush_rewrite_rules();

    if( get_option('prizedai_mobile_payments_plugin') )
      return;

    update_option('prizedai_mobile_payments_plugin',array());

    if( get_option('prizedai_mobile_payments_mpesa') )
      return;

    update_option('prizedai_mobile_payments_mpesa',array());

  }
}
