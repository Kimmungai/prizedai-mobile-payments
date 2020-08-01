<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Base;

class Deactivate
{
  public static function deactivate()
  {
    flush_rewrite_rules();
  }
}
