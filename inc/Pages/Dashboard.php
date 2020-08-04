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
class Dashboard extends BaseController
{
  public $settings;
  public $pages;
  public $callbacks;
  public $callbacks_mngr;


  public function register()
  {
    $this->settings = new SettingsApi();
    $this->callbacks = new AdminCallbacks();
    $this->callbacks_mngr = new ManagerCallbacks();

    $this->setPages();
    $this->setSettings();
    $this->setSections();
    $this->setFields();

    $this->settings->addPages( $this->pages )->withSubPage( 'Dashboard' )->register();
  }

  public function setPages()
  {
    $this->pages = array(
          array(
            'page_title'      =>      'Prized Ai Mobile Payments',
            'menu_title'      =>      'PrizedAI Payments',
            'capability'      =>      'manage_options',
            'menu_slug'       =>      'prizedai_mobile_payments_plugin',
            'callback'        =>      array( $this->callbacks, 'adminDashboard' ),
            'icon_url'        =>      'dashicons-chart-pie',
            'position'        =>      110,
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
      ),
      array(
        'option_group' => 'prizedai_mobile_payments_mpesa_api_settings',
        'option_name' => 'prizedai_mobile_payments_mpesa',
        'callback' => array( $this->callbacks_mngr, 'mpesaInputSanitize' ),
      ),
      array(
        'option_group' => 'prizedai_mobile_payments_sms_api_settings',
        'option_name' => 'prizedai_mobile_payments_sms',
        'callback' => array( $this->callbacks_mngr, 'smsInputSanitize' ),
      ),
    );
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
      array(
        'id' => 'prizedai_mobile_payments_mpesa_index',
        'title' => '',
        'callback' => array( $this->callbacks_mngr, 'prizedaiMobilePaymentsMpesaSectionManager' ),
        'page' => 'prizedai_mobile_payments_mpesa'

      ),
      array(
        'id' => 'prizedai_mobile_payments_sms_index',
        'title' => '',
        'callback' => array( $this->callbacks_mngr, 'prizedaiMobilePaymentsSmsSectionManager' ),
        'page' => 'prizedai_mobile_payments_sms'

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

    foreach ( $this->mpesaFields as $key => $value ) {
			$args[] = array(
				'id' => $key,
				'title' => $value,
				'callback' => array( $this->callbacks_mngr, ( $key == 'shortcode' ? 'prizedaiMobilePaymentsInputField' : ( $key == 'live' ? 'prizedaiMobilePaymentsCheckboxField' : 'prizedaiMobilePaymentsPasswordField' )  ) ),
				'page' => 'prizedai_mobile_payments_mpesa',
				'section' => 'prizedai_mobile_payments_mpesa_index',
				'args' => array(
					'option_name' => 'prizedai_mobile_payments_mpesa',
					'label_for' => $key,
          'class' => 'form-control'
				)
			);
		}

    foreach ( $this->smsFields as $key => $value ) {
			$args[] = array(
				'id' => $key,
				'title' => $value,
				'callback' => array( $this->callbacks_mngr, ( $key == 'apikey' ? 'prizedaiMobilePaymentsPasswordField' : ( $key == 'enabled' ? 'prizedaiMobilePaymentsCheckboxField' : ( $key == 'order_content' ? 'prizedaiMobilePaymentsTextAreaField' : 'prizedaiMobilePaymentsInputField') )  ) ),
				'page' => 'prizedai_mobile_payments_sms',
				'section' => 'prizedai_mobile_payments_sms_index',
				'args' => array(
					'option_name' => 'prizedai_mobile_payments_sms',
					'label_for' => $key,
          'class' => 'form-control'
				)
			);
		}

		$this->settings->setFields( $args );

	}

}
