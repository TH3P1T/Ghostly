<?php

if (!defined("GHOSTLY_INTERNAL")) {
    exit(0);
}

?>
<?php include $this->templatePath . "/components/page_header.php" ?>
<?php include $this->templatePath . "/components/page_navigation.php" ?>

      <!-- Main section-->
      <section class="section-container">
         <!-- Page content-->
         <div class="content-wrapper">
            <div class="content-heading">
               <div>View Authorization</div>
            </div>
            <div class="row">
               <div class="col-md-8">
                  <!-- START card-->
                  <div class="card card-default">
                     <div class="card-header">
                     </div>
                     <div class="card-body">
                        <form method="post" action="<?= $url_base ?>authorizations/view/<?= htmlspecialchars($authorization_id) ?>">
                           <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                           <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                           <div class="form-group">
                              <label>License Key</label>
                              <input class="form-control" type="text" name="license_key" placeholder="License Key" value="<?= htmlspecialchars($authorization_license_key) ?>" readonly />
                           </div>
                           <div class="form-group">
                              <label>Hardware ID</label>
                              <input class="form-control" type="text" name="hardware_id" placeholder="Hardware ID" value="<?= htmlspecialchars($authorization_hardware_id) ?>" readonly />
                           </div>
                           <div class="form-group">
                              <label>IP Address</label>
                              <input class="form-control" type="text" name="ip_address" placeholder="IP Address" value="<?= htmlspecialchars($authorization_ip_address) ?>" readonly />
                           </div>
                           <div class="form-group">
                              <label>Response</label>
                              <textarea class="form-control" name="response" readonly><?= htmlspecialchars($authorization_response) ?></textarea>
                           </div>
                        </form>
                     </div>
                  </div>
                  <!-- END card-->
               </div>
            </div>
         </div>
      </section>

<?php include $this->templatePath . "/components/page_footer.php" ?>  