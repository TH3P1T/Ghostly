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
               <div>Edit License</div>
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
                           <div class="alert alert-danger"><strong>Oh snap!</strong> Unable to update the license to the database.</div>
                        <?php } ?>
                        <?php if ($edited) { ?>
                           <div class="alert alert-success"><strong>Success!</strong> License updated</div>
                        <?php } ?>
                     <?php } ?>
                     </div>
                     <div class="card-body">
                        <form method="post" action="<?= $url_base ?>licenses/edit/<?= htmlspecialchars($license_id) ?>">
                           <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                           <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                           <div class="form-group">
                              <label>Product</label>
                              <select class="form-control" id="license-product-select" name="product_id" required="" value="<?= htmlspecialchars($product_id) ?>">
                                 <?php foreach ($product_list as $product) { ?>
                                    <option value="<?= htmlspecialchars($product['id']) ?>" <?= ($product_id == $product['id'] ? "selected" : "") ?>><?= htmlspecialchars($product['name']) ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="form-group">
                              <label>Serial / Key</label>
                              <input class="form-control" type="text" name="key" data-masked="" data-inputmask="'mask': '******-******-******-******'" placeholder="AABBCC-112233-44FF11-891908" required="" value="<?= htmlspecialchars($license_key) ?>" />
                           </div>
                           <div class="form-group row">
                              <div class="col-lg-6 mb-2">
                                 <label>Customer Name</label>
                                 <input class="form-control" type="text" name="customer_name" placeholder="Customer Name" value="<?= htmlspecialchars($license_customer_name) ?>" />
                              </div>
                              <div class="col-lg-6 mb-2">
                                 <label>Customer Email</label>
                                 <input class="form-control" type="text" name="customer_email" placeholder="Customer Email" value="<?= htmlspecialchars($license_customer_email) ?>" />
                              </div>
                           </div>
                           <div class="form-group">
                              <label>Purchase Date</label>
                              <div class="input-group date date-time-picker" id="purchase_date">
                                 <input class="form-control" type="text" name="purchase_date" required="" value="<?= htmlspecialchars($license_purchase_date) ?>"/>
                                 <span class="input-group-append input-group-addon">
                                    <span class="input-group-text fas fa-calendar-alt"></span>
                                 </span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label>Order Reference</label>
                              <input class="form-control" type="text" name="order_reference" placeholder="Order Reference ####" value="<?= htmlspecialchars($license_order_reference) ?>" />
                           </div>
                           <div class="form-group">
                              <label>Comments</label>
                              <textarea class="form-control" name="comments"><?= htmlspecialchars($license_comments) ?></textarea>
                           </div>
                           <div class="form-group">
                              <label>Hardware ID</label>
                              <input class="form-control" type="text" name="hardware_id" placeholder="Hardware ID" value="<?= htmlspecialchars($license_hardware_id) ?>" />
                           </div>
                           <div class="form-group">
                              <label>Expiration Date</label>
                              <div class="input-group date date-time-picker" id="expiration_date">
                                 <input class="form-control" type="text" name="expiration_date" value="<?= htmlspecialchars($license_expiration_date) ?>" />
                                 <span class="input-group-append input-group-addon">
                                    <span class="input-group-text fas fa-calendar-alt"></span>
                                 </span>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="checkbox c-checkbox">
                                 <label>
                                    <input type="checkbox" name="disabled" value="1" <?= ($license_disabled == 1 ? "checked" : "") ?>>
                                    <span class="fa fa-check"></span> Disabled</label>
                              </div>
                           </div>
                           <button class="btn btn-primary" type="submit">Save License</button>
                        </form>
                     </div>
                  </div>
                  <!-- END card-->
               </div>
            </div>
         </div>
      </section>

<?php include $this->templatePath . "/components/page_footer.php" ?>  