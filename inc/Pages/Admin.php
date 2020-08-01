<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Pages;
use PrizedAI\Base\BaseController;
use PrizedAI\Api\SettingsApi;
use PrizedAI\Api\Callbacks\AdminCallbacks;

/**
 *
 */
class Admin extends BaseController
{
  public $settings;
  public $pages;
  public $subpages;
  public $callbacks;

  public function register()
  {
    $this->settings = new SettingsApi();
    $this->callbacks = new AdminCallbacks();
    $this->setPages();
    $this->setSubpages();
    $this->setSettings();
    $this->setSections();
    $this->setFields();

    $this->settings->addPages( $this->pages )->withSubPage( 'Dashboard' )->addSubPages( $this->subpages )->register();
  }

  public function setPages()
  {
    $this->pages = array(
          array(
            'page_title'      =>      'Prized Ai Mobile Payments',
            'menu_title'      =>      'Mobile Payments',
            'capability'      =>      'manage_options',
            'menu_slug'       =>      'prizedai_mobile_payments_plugin',
            'callback'        =>      array( $this->callbacks, 'adminDashboard' ),
            'icon_url'        =>      'dashicons-chart-pie',
            'position'        =>      110,
          ),
        );
  }

  public function setSubpages()
  {
    $this->subpages = array(
      array(
          'parent_slug'      =>     'prizedai_mobile_payments_plugin',
          'page_title'      =>      'Mpesa',
          'menu_title'      =>      'Mpesa',
          'capability'      =>      'manage_options',
          'menu_slug'       =>      'prizedai_mobile_payments_mpesa',
          'callback'        =>      array( $this->callbacks, 'mpesa' ),
        ),

    );
  }

  public function setSettings()
  {
    $args = array(
      array(
        'option_group' => 'prizedai_mobile_payments_options_group',
        'option_name' => 'consumer_key',
        'callback' => array( $this->callbacks, 'prizedaiMobilePaymentsOptionsGroup' ),

      ),
      array(
        'option_group' => 'prizedai_mobile_payments_options_group',
        'option_name' => 'passkey',
        'callback' => array( $this->callbacks, 'prizedaiMobilePaymentsOptionsGroup' ),

      ),
      array(
        'option_group' => 'prizedai_mobile_payments_options_group',
        'option_name' => 'shortcode',
        'callback' => array( $this->callbacks, 'prizedaiMobilePaymentsOptionsGroup' ),

      ),
      array(
        'option_group' => 'prizedai_mobile_payments_options_group',
        'option_name' => 'url',
        'callback' => array( $this->callbacks, 'prizedaiMobilePaymentsOptionsGroup' ),

      ),
    );

    $this->settings->setSettings($args);
  }

  public function setSections()
  {
    $args = array(
      array(
        'id' => 'prizedai_mobile_payments_admin_index',
        'title' => 'Mpesa',
        'callback' => array( $this->callbacks, 'prizedaiMobilePaymentsAdminSection' ),
        'page' => 'prizedai_mobile_payments_plugin'

      ),
    );

    $this->settings->setSections($args);
  }

  public function setFields()
  {
    $args = array(
      array(
        'id' => 'consumer_key',
        'title' => 'Consumer key',
        'callback' => array( $this->callbacks, 'prizedaiMobilePaymentsConsumerKey' ),
        'page' => 'prizedai_mobile_payments_plugin',
        'section' => 'prizedai_mobile_payments_admin_index',
        'args' => array(
          'label_for' => 'consumer_key',
          'class' => 'example-class'
        )
      ),

        array(
          'id' => 'passkey',
          'title' => 'Passkey',
          'callback' => array( $this->callbacks, 'prizedaiMobilePaymentsPasskey' ),
          'page' => 'prizedai_mobile_payments_plugin',
          'section' => 'prizedai_mobile_payments_admin_index',
          'args' => array(
            'label_for' => 'passkey',
            'class' => 'example-class'
          ),

      ),
      array(
        'id' => 'shortcode',
        'title' => 'Short code',
        'callback' => array( $this->callbacks, 'prizedaiMobilePaymentsShortCode' ),
        'page' => 'prizedai_mobile_payments_plugin',
        'section' => 'prizedai_mobile_payments_admin_index',
        'args' => array(
          'label_for' => 'shortcode',
          'class' => 'example-class'
        ),

    ),
    array(
      'id' => 'url',
      'title' => 'Url',
      'callback' => array( $this->callbacks, 'prizedaiMobilePaymentsUrl' ),
      'page' => 'prizedai_mobile_payments_plugin',
      'section' => 'prizedai_mobile_payments_admin_index',
      'args' => array(
        'label_for' => 'url',
        'class' => 'example-class'
      ),

  ),
    );

    $this->settings->setFields($args);
  }

}
