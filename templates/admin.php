<div class="wrap">
  <h1>Dashboard</h1>
  <?php settings_errors() ?>
  <ul class="prizedai-nav prizedai-nav-tabs">
    <li class="active"> <a href="#tab-1">Manage settings</a> </li>
    <li> <a href="#tab-2">Updates</a> </li>
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
      <h3>Update</h3>
    </div>

    <div id="tab-3" class="prizedai-tab-pane">
      <h3>About</h3>
    </div>

  </div>

</div>
