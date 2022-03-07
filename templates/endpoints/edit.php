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
               <div>Edit Api Endpoint</div>
            </div>
            <div class="row">
               <div class="col-md-8">
                  <!-- START card-->
                  <div class="card card-default">
                     <div class="card-header">
                     <?php if ($edit_attempted) { ?>
                        <?php if ($validation_failed) { ?>
                           <div class="alert alert-danger"><strong>Oh snap!</strong> Validation failed try again.</div>
                        <?php } ?>
                        <?php if (!$edited) { ?>
                           <div class="alert alert-danger"><strong>Oh snap!</strong> Unable to update the endpoint to the database.</div>
                        <?php } ?>
                        <?php if ($edited) { ?>
                           <div class="alert alert-success"><strong>Success!</strong> Endpoint updated</div>
                        <?php } ?>
                     <?php } ?>
                     </div>
                     <div class="card-body">
                        <form method="post" action="<?= $url_base ?>endpoints/edit/<?= htmlspecialchars($endpoint_id) ?>">
                           <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                           <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                           <div class="form-group">
                              <label>Name</label>
                              <input class="form-control" type="text" name="name" placeholder="Name" required="" value="<?= htmlspecialchars($endpoint_name) ?>" />
                           </div>
                           <div class="form-group">
                              <label>Key</label>
                              <input class="form-control" type="text" name="key" placeholder="Key" required="" value="<?= htmlspecialchars($endpoint_key) ?>" />
                           </div>
                           <div class="form-group">
                              <label>Secret</label>
                              <input class="form-control" type="text" name="secret" placeholder="Secret" required="" value="<?= htmlspecialchars($endpoint_secret) ?>" />
                           </div>
                           <button class="btn btn-primary" type="submit">Save Endpoint</button>
                        </form>
                     </div>
                  </div>
                  <!-- END card-->
               </div>
            </div>
         </div>
      </section>

<?php include $this->templatePath . "/components/page_footer.php" ?>  