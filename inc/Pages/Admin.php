<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Pages;
use PrizedAI\Base\BaseController;
use PrizedAI\Api\SettingsApi;
use PrizedAI\Api\Callbacks\AdminCallbacks;
use PrizedAI\Api\Callbacks\ManagerCallbacks;


/**
 *
 */
class Admin extends BaseController
{
  public $settings;
  public $pages;
  public $subpages;
  public $callbacks;
  public $callbacks_mngr;


  public function register()
  {
    $this->settings = new SettingsApi();
    $this->callbacks = new AdminCallbacks();
    $this->callbacks_mngr = new ManagerCallbacks();

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
        'option_group' => 'prizedai_mobile_payments_plugin_settings',
        'option_name' => 'prizedai_mobile_payments_plugin',
        'callback' => array( $this->callbacks_mngr, 'checkboxSanitize' ),
      )
    );
    /*foreach ($this->managers as $key => $value)
    {
      $args[] = array(
        'option_group' => 'prizedai_mobile_payments_plugin_settings',
        'option_name' => $key,
        'callback' => array( $this->callbacks_mngr, 'checkboxSanitize' ),

      );
    }*/

    $this->settings->setSettings($args);
  }

  public function setSections()
  {
    $args = array(
      array(
        'id' => 'prizedai_mobile_payments_admin_index',
        'title' => 'Settings manager',
        'callback' => array( $this->callbacks_mngr, 'prizedaiMobilePaymentsAdminSectionManager' ),
        'page' => 'prizedai_mobile_payments_plugin'

      ),
    );

    $this->settings->setSections($args);
  }

  public function setFields()
	{
    $args = array();

    foreach ( $this->managers as $key => $value ) {
			$args[] = array(
				'id' => $key,
				'title' => $value,
				'callback' => array( $this->callbacks_mngr, 'prizedaiMobilePaymentsCheckboxField' ),
				'page' => 'prizedai_mobile_payments_plugin',
				'section' => 'prizedai_mobile_payments_admin_index',
				'args' => array(
					'option_name' => 'prizedai_mobile_payments_plugin',
					'label_for' => $key,
					'class' => 'ui-toggle'
				)
			);
		}
		$this->settings->setFields( $args );
	}

}
