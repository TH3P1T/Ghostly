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
               <div>Delete Api Endpoint</div>
            </div>
            <div class="row">
               <div class="col-md-8">
                  <!-- START card-->
                  <div class="card card-default">
                     <div class="card-header">
                     <?php if ($delete_attempted) { ?>
                        <?php if (!$deleted) { ?>
                           <div class="alert alert-danger"><strong>Oh snap!</strong> Unable to delete the endpoint.</div>
                        <?php } ?>
                        <?php if ($deleted) { ?>
                           <div class="alert alert-success"><strong>Success!</strong> Endpoint deleted</div>
                        <?php } ?>
                     <?php } ?>
                     <?php if (!$delete_attempted) { ?>
                        <div class="alert alert-warning"><strong>Warning!</strong> Are you sure you want to delete this endpoint?</div>
                     <?php } ?>
                     </div>
                     <?php if (!$delete_attempted || !$deleted) { ?>
                     <div class="card-body">
                        <form method="post" action="<?= $url_base ?>endpoints/delete/<?= htmlspecialchars($endpoint_id) ?>">
                           <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                           <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                           <div class="form-group">
                              <label>Name</label>
                              <input class="form-control" type="text" name="name" placeholder="Name" required="" value="<?= htmlspecialchars($endpoint_name) ?>" disabled />
                           </div>
                           <div class="form-group">
                              <label>Key</label>
                              <input class="form-control" type="text" name="key" placeholder="Key" required="" value="<?= htmlspecialchars($endpoint_key) ?>" disabled />
                           </div>
                           <div class="form-group">
                              <label>Secret</label>
                              <input class="form-control" type="text" name="secret" placeholder="Secret" required="" value="<?= htmlspecialchars($endpoint_secret) ?>" disabled />
                           </div>
                           <button class="btn btn-primary" type="submit">Delete Endpoint</button>
                        </form>
                     </div>
                     <?php } ?>
                  </div>
                  <!-- END card-->
               </div>
            </div>
         </div>
      </section>

<?php include $this->templatePath . "/components/page_footer.php" ?>  