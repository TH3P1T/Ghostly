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
               <div>Delete License</div>
            </div>
            <div class="row">
               <div class="col-md-8">
                  <!-- START card-->
                  <div class="card card-default">
                     <div class="card-header">
                     <?php if ($delete_attempted) { ?>
                        <?php if (!$deleted) { ?>
                           <div class="alert alert-danger"><strong>Oh snap!</strong> Unable to delete the license.</div>
                        <?php } ?>
                        <?php if ($deleted) { ?>
                           <div class="alert alert-success"><strong>Success!</strong> License deleted</div>
                        <?php } ?>
                     <?php } ?>
                     <?php if (!$delete_attempted) { ?>
                        <div class="alert alert-warning"><strong>Warning!</strong> Are you sure you want to delete this license?</div>
                     <?php } ?>
                     </div>
                     <?php if (!$delete_attempted || !$deleted) { ?>
                     <div class="card-body">
                        <form method="post" action="<?= $url_base ?>licenses/delete/<?= htmlspecialchars($license_id) ?>">
                           <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                           <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                           <div class="form-group">
                              <label>Product</label>
                              <select class="form-control" id="license-product-select" name="product_id" required="" value="<?= htmlspecialchars($product_id) ?>" disabled >
                                 <?php foreach ($product_list as $product) { ?>
                                    <option value="<?= htmlspecialchars($product['id']) ?>" <?= ($product_id == $product['id'] ? "selected" : "") ?>><?= htmlspecialchars($product['name']) ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="form-group">
                              <label>Serial / Key</label>
                              <input class="form-control" type="text" name="key" data-masked="" data-inputmask="'mask': '******-******-******-******'" placeholder="AABBCC-112233-44FF11-891908" required="" value="<?= htmlspecialchars($license_key) ?>" disabled />
                           </div>
                           <div class="form-group row">
                              <div class="col-lg-6 mb-2">
                                 <label>Customer Name</label>
                                 <input class="form-control" type="text" name="customer_name" placeholder="Customer Name" value="<?= htmlspecialchars($license_customer_name) ?>" disabled />
                              </div>
                              <div class="col-lg-6 mb-2">
                                 <label>Customer Email</label>
                                 <input class="form-control" type="text" name="customer_email" placeholder="Customer Email" value="<?= htmlspecialchars($license_customer_email) ?>" disabled />
                              </div>
                           </div>
                           <div class="form-group">
                              <label>Purchase Date</label>
                              <div class="input-group date date-time-picker" id="purchase_date">
                                 <input class="form-control" type="text" name="purchase_date" required="" value="<?= htmlspecialchars($license_purchase_date) ?>" disabled />
                                 <span class="input-group-append input-group-addon">
                                    <span class="input-group-text fas fa-calendar-alt"></span>
                                 </span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label>Order Reference</label>
                              <input class="form-control" type="text" name="order_reference" placeholder="Order Reference ####" value="<?= htmlspecialchars($license_order_reference) ?>" disabled />
                           </div>
                           <div class="form-group">
                              <label>Comments</label>
                              <textarea class="form-control" name="comments" disabled ><?= htmlspecialchars($license_comments) ?></textarea>
                           </div>
                           <div class="form-group">
                              <label>Hardware ID</label>
                              <input class="form-control" type="text" name="hardware_id" placeholder="Hardware ID" value="<?= htmlspecialchars($license_hardware_id) ?>" disabled />
                           </div>
                           <div class="form-group">
                              <label>Expiration Date</label>
                              <div class="input-group date date-time-picker" id="expiration_date">
                                 <input class="form-control" type="text" name="expiration_date" value="<?= htmlspecialchars($license_expiration_date) ?>" disabled />
                                 <span class="input-group-append input-group-addon">
                                    <span class="input-group-text fas fa-calendar-alt"></span>
                                 </span>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="checkbox c-checkbox">
                                 <label>
                                    <input type="checkbox" name="disabled" value="1" <?= ($license_disabled == 1 ? "checked" : "") ?> disabled >
                                    <span class="fa fa-check"></span> Disabled</label>
                              </div>
                           </div>
                           <button class="btn btn-primary" type="submit">Delete License</button>
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