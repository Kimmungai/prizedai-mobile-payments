<div class="wrap">
  <h1>Dashboard</h1>
  <?php settings_errors() ?>
  <form class="" action="options.php" method="post">
    <?php
      settings_fields('prizedai_mobile_payments_options_group');
      do_settings_sections( 'prizedai_mobile_payments_plugin' );
      submit_button();
     ?>
  </form>
</div>
