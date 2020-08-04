<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Base;
use AfricasTalking\SDK\AfricasTalking;

class SMS
{
  public static function sendSMS($recipient,$content){

    $option = get_option( 'prizedai_mobile_payments_sms' );


      $username = isset($option['username']) && !empty($option['username']) ? $option['username'] : NULL;
      $apiKey   = isset($option['apikey']) && !empty($option['apikey']) ? $option['apikey'] : NULL;
      $from   = isset($option['from']) && !empty($option['from']) ? $option['from'] : NULL;

      if( !$username || !$apiKey )
        return 0;

      $AT       = new AfricasTalking($username, $apiKey);
      // Get one of the services
      $sms      = $AT->sms();

      // Use the service
      return $result   = $sms->send([
        'to'      => $recipient,
        'message' => $content,
        'from' => $from
      ]);

    }

}
