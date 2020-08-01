<?php
/**
*@package prizedai-mobile-payments
**/

/*
Plugin Name: Prized Ai Mobile Payments
Plugin URI: https://github.com/kimmungai/prizedai-mobile-payments
Description: This plugin adds Mpesa to WooCommerce payment gateways. Simply add your Daraja API credentials to use the plugin.
version: 1.2.0
Author: Peter Kimani
Author URI: #
License: GPLv2 or later
Text Domain: prizedai-mobile-payments
*/

/*
Copyright (C) 2020-2030  Prized AI Ltd

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined('ABSPATH') or die("Forbidden!");

if( file_exists( dirname(__FILE__).'/vendor/autoload.php' ) )
  require_once dirname(__FILE__).'/vendor/autoload.php';


use PrizedAI\Base\Activate;
use PrizedAI\Base\Deactivate;

/*
*Code that excutes on activation
*/
function activatePrizedAiMobilePaymentsPlugin()
{
  Activate::activate();
}

/*
*Code that excutes on deactivation
*/
function deactivatePrizedAiMobilePaymentsPlugin()
{
  Deactivate::deactivate();
}

register_activation_hook( __FILE__, 'activatePrizedAiMobilePaymentsPlugin' );
register_deactivation_hook( __FILE__, 'deactivatePrizedAiMobilePaymentsPlugin' );

if( class_exists('PrizedAI\\Init') )
  PrizedAI\Init::register_services();
