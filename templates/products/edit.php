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
               <div>Edit Product</div>
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
                           <div class="alert alert-danger"><strong>Oh snap!</strong> Unable to update the product to the database.</div>
                        <?php } ?>
                        <?php if ($edited) { ?>
                           <div class="alert alert-success"><strong>Success!</strong> Product updated</div>
                        <?php } ?>
                     <?php } ?>
                     </div>
                     <div class="card-body">
                        <form method="post" action="<?= $url_base ?>products/edit/<?= htmlspecialchars($product_id) ?>">
                           <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                           <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                           <div class="form-group row">
                              <div class="col-lg-6 mb-2">
                                 <label>Full Name</label>
                                 <input class="form-control" type="text" name="name" placeholder="Product Full Name" required="" value="<?= htmlspecialchars($product_name) ?>" />
                              </div>
                              <div class="col-lg-6 mb-2">
                                 <label>Short Name</label>
                                 <input class="form-control" type="text" name="short_name" placeholder="Product Short Name" required="" value="<?= htmlspecialchars($product_short_name) ?>" />
                              </div>
                           </div>
                           <div class="form-group">
                              <label>Description</label>
                              <input class="form-control" type="text" name="description" placeholder="Description" required="" value="<?= htmlspecialchars($product_description) ?>" />
                           </div>
                           <div class="form-group">
                              <label>Private Key</label>
                              <textarea class="form-control" name="private_key" required=""><?= htmlspecialchars($product_private_key) ?></textarea>
                           </div>
                           <div class="form-group">
                              <label>Public Key</label>
                              <textarea class="form-control" name="public_key" required=""><?= htmlspecialchars($product_public_key) ?></textarea>
                           </div>
                           <button class="btn btn-primary" type="submit">Save Product</button>
                        </form>
                     </div>
                  </div>
                  <!-- END card-->
               </div>
            </div>
         </div>
      </section>

<?php include $this->templatePath . "/components/page_footer.php" ?>  