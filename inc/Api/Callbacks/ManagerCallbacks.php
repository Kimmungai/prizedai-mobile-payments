<?php
/**
*@package prizedai-mobile-payments
**/
namespace PrizedAI\Api\Callbacks;
use PrizedAI\Base\BaseController;

/**
 *
 */
class ManagerCallbacks extends BaseController
{

  public function checkboxSanitize( $input )
  {
    $output = array();
    foreach ($this->managers as $key => $value)
    {
      $output[$key] =isset($input[$key]) ? true : false;
    }
    return $output;
  }

  public function prizedaiMobilePaymentsAdminSectionManager( )
  {
    echo "Manage the sections and features of this plugin";
  }

  public function prizedaiMobilePaymentsCheckboxField( $args )
  {
    $name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checkbox = get_option( $option_name );
    echo '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ($checkbox[$name] ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
  }

}
