<div class="wrap">
  <h1>Dashboard</h1>
  <?php settings_errors() ?>
  <ul class="prizedai-nav prizedai-nav-tabs">
    <li id="prizedai-tab-btn-1" class="active"> <a href="#prizedai-tab-1">Manage settings</a> </li>
    <li id="prizedai-tab-btn-2"> <a href="#prizedai-tab-2">Api Credentials</a> </li>
  </ul>
  <div class="prizedai-tab-content">

    <div id="prizedai-tab-1" class="prizedai-tab-pane active">
      <form class="" action="options.php#prizedai-tab-1" method="post">
        <?php
          settings_fields('prizedai_mobile_payments_plugin_settings');
          do_settings_sections( 'prizedai_mobile_payments_plugin' );
          submit_button();
         ?>
      </form>
    </div>

    <div id="prizedai-tab-2" class="prizedai-tab-pane">
      <?php $option = get_option( 'prizedai_mobile_payments_plugin' );?>
      <?php $mpesa = isset($option['mpesa']) ? $option['mpesa'] : false; ?>
      <?php $sms = isset($option['sms']) ? $option['sms'] : false; ?>

      <?php  if( $mpesa ):  ?>
      <h3>Lipa na Mpesa</h3>
      <form class="" action="options.php#prizedai-tab-2" method="post">
        <?php
          settings_fields('prizedai_mobile_payments_mpesa_api_settings');
          do_settings_sections( 'prizedai_mobile_payments_mpesa' );
          submit_button();
         ?>
      </form>
      <?php endif; ?>

    </div>

  </div>

</div>
