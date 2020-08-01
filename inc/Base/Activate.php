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
  }
}
