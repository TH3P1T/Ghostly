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
               <div>Delete Product</div>
            </div>
            <div class="row">
               <div class="col-md-8">
                  <!-- START card-->
                  <div class="card card-default">
                     <div class="card-header">
                     <?php if ($delete_attempted) { ?>
                        <?php if (!$deleted) { ?>
                           <div class="alert alert-danger"><strong>Oh snap!</strong> Unable to delete the product.</div>
                        <?php } ?>
                        <?php if ($deleted) { ?>
                           <div class="alert alert-success"><strong>Success!</strong> Product deleted</div>
                        <?php } ?>
                     <?php } ?>
                     <?php if (!$delete_attempted) { ?>
                        <div class="alert alert-warning"><strong>Warning!</strong> Are you sure you want to delete this product?</div>
                     <?php } ?>
                     </div>
                     <?php if (!$delete_attempted || !$deleted) { ?>
                     <div class="card-body">
                        <form method="post" action="<?= $url_base ?>products/delete/<?= htmlspecialchars($product_id) ?>">
                           <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                           <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                           <div class="form-group row">
                              <div class="col-lg-6 mb-2">
                                 <label>Full Name</label>
                                 <input class="form-control" type="text" name="name" placeholder="Product Full Name" required="" value="<?= htmlspecialchars($product_name) ?>" disabled />
                              </div>
                              <div class="col-lg-6 mb-2">
                                 <label>Short Name</label>
                                 <input class="form-control" type="text" name="short_name" placeholder="Product Short Name" required="" value="<?= htmlspecialchars($product_short_name) ?>" disabled />
                              </div>
                           </div>
                           <div class="form-group">
                              <label>Description</label>
                              <input class="form-control" type="text" name="description" placeholder="Description" required="" value="<?= htmlspecialchars($product_description) ?>" disabled />
                           </div>
                           <div class="form-group">
                              <label>Private Key</label>
                              <textarea class="form-control" name="private_key" required="" disabled ><?= htmlspecialchars($product_private_key) ?></textarea>
                           </div>
                           <div class="form-group">
                              <label>Public Key</label>
                              <textarea class="form-control" name="public_key" required="" disabled ><?= htmlspecialchars($product_public_key) ?></textarea>
                           </div>
                           <button class="btn btn-primary" type="submit">Delete Product</button>
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