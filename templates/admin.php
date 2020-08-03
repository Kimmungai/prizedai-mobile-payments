<div class="wrap">
  <h1>Dashboard</h1>
  <?php settings_errors() ?>
  <ul class="prizedai-nav prizedai-nav-tabs">
    <li class="active"> <a href="#tab-1">Manage settings</a> </li>
    <li> <a href="#tab-2">Api Credentials</a> </li>
    <li> <a href="#tab-3">About</a> </li>
  </ul>
  <div class="prizedai-tab-content">

    <div id="tab-1" class="prizedai-tab-pane active">
      <form class="" action="options.php" method="post">
        <?php
          settings_fields('prizedai_mobile_payments_plugin_settings');
          do_settings_sections( 'prizedai_mobile_payments_plugin' );
          submit_button();
         ?>
      </form>
    </div>

    <div id="tab-2" class="prizedai-tab-pane">
      <?php $option = get_option( 'prizedai_mobile_payments_plugin' );?>
      <?php $mpesa = isset($option['mpesa']) ? $option['mpesa'] : false; ?>
      <?php $sms = isset($option['sms']) ? $option['sms'] : false; ?>

      <?php  if( $mpesa ):  ?>
      <h3>Lipa na Mpesa</h3>
      <form class="" action="options.php" method="post">
        <?php
          settings_fields('prizedai_mobile_payments_mpesa_api_settings');
          do_settings_sections( 'prizedai_mobile_payments_mpesa' );
          submit_button();
         ?>
      </form>
      <hr>
      <?php endif; ?>

      <?php  if( $sms ):  ?>
      <h3>Africas talking SMS</h3>
      <form class="" action="options.php" method="post">
        <?php
          settings_fields('prizedai_mobile_payments_sms_api_settings');
          do_settings_sections( 'prizedai_mobile_payments_sms' );
          submit_button();
         ?>
      </form>
      <hr>
      <?php endif; ?>

    </div>

    <div id="tab-3" class="prizedai-tab-pane">
      <h3>About</h3>
    </div>

  </div>

</div>
