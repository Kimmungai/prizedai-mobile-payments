<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Base;

class Activate
{
  public static function activate()
  {
    self::create_db_table();
    flush_rewrite_rules();

    if( get_option('prizedai_mobile_payments_plugin') )
      return;

    update_option('prizedai_mobile_payments_plugin',array());

    if( get_option('prizedai_mobile_payments_mpesa') )
      return;

    update_option('prizedai_mobile_payments_mpesa',array());

  }

  private static function create_db_table()
  {
    global $wpdb;
	  $name = $wpdb->prefix .'prizedai_mobile_payments';
	  $charset = $wpdb->get_charset_collate();
	  $sql = self::table_sql($name,$charset);
    $wpdb->query( $sql );

  }

  private static function table_sql($name,$charset)
  {
    return "CREATE TABLE IF NOT EXISTS $name (

  		id BIGINT NOT NULL AUTO_INCREMENT,

  		order_id varchar(191)  NULL,

      gateway varchar(191) NOT NULL,

  		phone_number varchar(191)  NULL,

  		merchant_request_id varchar(191)  NULL,

  		checkout_request_id varchar(191)  NULL,

  		result_code varchar(191)  NULL,

  		result_desc varchar(191)  NULL,

  		status TINYINT DEFAULT 0 NOT NULL,

      created_at varchar(191)  NOT NULL,


  		PRIMARY KEY  (id)

  	) $charset;";
  }
}
