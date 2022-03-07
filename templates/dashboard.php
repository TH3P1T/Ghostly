<?php

if (!defined("GHOSTLY_INTERNAL")) {
    exit(0);
}

?>
<?php include "components/page_header.php" ?>
<?php include "components/page_navigation.php" ?>

      <!-- Main section-->
      <section class="section-container">
         <!-- Page content-->
         <div class="content-wrapper">
            <div class="content-heading">
               <div>Dashboard</div>
            </div>
            <!-- START cards box-->
            <div class="row">
               <div class="col-xl-3 col-md-6">
                  <!-- START card-->
                  <div class="card flex-row align-items-center align-items-stretch border-0">
                     <div class="col-4 d-flex align-items-center bg-primary-dark justify-content-center rounded-left">
                        <em class="icon-layers fa-3x"></em>
                     </div>
                     <div class="col-8 py-3 bg-primary rounded-right">
                        <div class="h2 mt-0"><?= htmlspecialchars($products_total) ?></div>
                        <div class="text-uppercase">Products</div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-md-6">
                  <!-- START card-->
                  <div class="card flex-row align-items-center align-items-stretch border-0">
                     <div class="col-4 d-flex align-items-center bg-primary-dark justify-content-center rounded-left">
                        <em class="icon-shield fa-3x"></em>
                     </div>
                     <div class="col-8 py-3 bg-primary rounded-right">
                        <div class="h2 mt-0"><?= htmlspecialchars($licenses_total) ?></div>
                        <div class="text-uppercase">Licenses</div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-md-6">
                  <!-- START card-->
                  <div class="card flex-row align-items-center align-items-stretch border-0">
                     <div class="col-4 d-flex align-items-center bg-green-dark justify-content-center rounded-left">
                        <em class="icon-check fa-3x"></em>
                     </div>
                     <div class="col-8 py-3 bg-green rounded-right">
                        <div class="h2 mt-0"><?= htmlspecialchars($authorizations_valid_total) ?></div>
                        <div class="text-uppercase">Valid Authorizations</div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-md-6">
                  <!-- START card-->
                  <div class="card flex-row align-items-center align-items-stretch border-0">
                     <div class="col-4 d-flex align-items-center bg-danger-dark justify-content-center rounded-left">
                        <em class="icon-close fa-3x"></em>
                     </div>
                     <div class="col-8 py-3 bg-danger rounded-right">
                        <div class="h2 mt-0"><?= htmlspecialchars($authorizations_invalid_total) ?></div>
                        <div class="text-uppercase">Invalid Authorizations</div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-xl-3">
                  <!-- START messages and activity-->
                  <div class="card card-default">
                     <div class="card-header">
                        <div class="card-title">Latest Products</div>
                     </div>
                     <!-- START list group-->
                     <div class="table-responsive">
                        <table class="table table-hover table-striped">
                           <thead>
                              <tr>
                                 <th>Name</th>
                                 <th>Lic. #</th>
                              </tr>
                           </thead>
                           <tbody>
                           <?php foreach ($latest_products as $latest_product) { ?>
                              <tr>
                                 <td><a href="<?= $url_base ?>products/view/<?= htmlspecialchars($latest_product['id']) ?>"><?= htmlspecialchars($latest_product['name']) ?></a></td>
                                 <td><div class="badge badge-info"><?= htmlspecialchars($latest_product['license_count']) ?></div></td>
                              </tr>
                           <?php } ?>
                           </tbody>
                        </table>
                     </div>
                     <!-- END list group-->
                     <!-- START card footer-->
                     <div class="card-footer text-right">
                        <a class="btn btn-secondary btn-sm" type="button" href="<?= $url_base ?>products">View All Products</a>
                     </div>
                     <!-- END card-footer-->
                  </div>
                  <!-- END messages and activity-->
               </div>

               <div class="col-xl-3">
                  <!-- START messages and activity-->
                  <div class="card card-default">
                     <div class="card-header">
                        <div class="card-title">Latest Licenses</div>
                     </div>
                     <!-- START list group-->
                     <div class="table-responsive">
                        <table class="table table-hover table-striped">
                           <thead>
                              <tr>
                                 <th>Key</th>
                                 <th>Auth. #</th>
                              </tr>
                           </thead>
                           <tbody>
                           <?php foreach ($latest_licenses as $latest_license) { ?>
                              <tr>
                                 <td><a href="<?= $url_base ?>licenses/view/<?= htmlspecialchars($latest_license['id']) ?>"><?= htmlspecialchars($latest_license['key']) ?></a></td>
                                 <td><div class="badge badge-success"><?= htmlspecialchars($latest_license['auth_valid_count']) ?></div>&nbsp;<div class="badge badge-danger"><?= htmlspecialchars($latest_license['auth_invalid_count']) ?></div></td>
                              </tr>
                           <?php } ?>
                           </tbody>
                        </table>
                     </div>
                     <!-- END list group-->
                     <!-- START card footer-->
                     <div class="card-footer text-right">
                        <a class="btn btn-secondary btn-sm" type="button" href="<?= $url_base ?>licenses">View All Licenses</a>
                     </div>
                     <!-- END card-footer-->
                  </div>
                  <!-- END messages and activity-->
               </div>

               <div class="col-xl-3">
                  <!-- START messages and activity-->
                  <div class="card card-default">
                     <div class="card-header">
                        <div class="card-title">Latest Valid Authorizations</div>
                     </div>
                     <!-- START list group-->
                     <div class="table-responsive">
                        <table class="table table-hover table-striped">
                           <thead>
                              <tr>
                                 <th>When?</th>
                                 <th>Key</th>
                              </tr>
                           </thead>
                           <tbody>
                           <?php foreach ($latest_authorizations_valid as $latest_authorization) { ?>
                              <tr>
                                 <td><div class="badge badge-success"><?= htmlspecialchars($latest_authorization['when']) ?></div></td>
                                 <td>
                                    <a href="<?= $url_base ?>authorizations/view/<?= htmlspecialchars($latest_authorization['id']) ?>">
                                    <?php if (strlen($latest_authorization['license_key']) > 0) { ?>
                                       <?= htmlspecialchars($latest_authorization['license_key']) ?>
                                    <?php } else { ?>
                                       NO LICENSE KEY
                                    <?php } ?>
                                    </a>
                                 </td>
                              </tr>
                           <?php } ?>
                           </tbody>
                        </table>
                     </div>
                     <!-- END list group-->
                     <!-- START card footer-->
                     <div class="card-footer text-right">
                        <a class="btn btn-secondary btn-sm" type="button" href="<?= $url_base ?>authorizations">View All Authorizations</a>
                     </div>
                     <!-- END card-footer-->
                  </div>
                  <!-- END messages and activity-->
               </div>

               <div class="col-xl-3">
                  <!-- START messages and activity-->
                  <div class="card card-default">
                     <div class="card-header">
                        <div class="card-title">Latest Invalid Authorizations</div>
                     </div>
                     <!-- START list group-->
                     <div class="table-responsive">
                        <table class="table table-hover table-striped">
                           <thead>
                              <tr>
                                 <th>When?</th>
                                 <th>Key</th>
                              </tr>
                           </thead>
                           <tbody>
                           <?php foreach ($latest_authorizations_invalid as $latest_authorization) { ?>
                              <tr>
                                 <td><div class="badge badge-danger"><?= htmlspecialchars($latest_authorization['when']) ?></div></td>
                                 <td>
                                    <a href="<?= $url_base ?>authorizations/view/<?= htmlspecialchars($latest_authorization['id']) ?>">
                                    <?php if (strlen($latest_authorization['license_key']) > 0) { ?>
                                       <?= htmlspecialchars($latest_authorization['license_key']) ?>
                                    <?php } else { ?>
                                       NO LICENSE KEY
                                    <?php } ?>
                                    </a>
                                 </td>
                              </tr>
                           <?php } ?>
                           </tbody>
                        </table>
                     </div>
                     <!-- END list group-->
                     <!-- START card footer-->
                     <div class="card-footer text-right">
                        <a class="btn btn-secondary btn-sm" type="button" href="<?= $url_base ?>authorizations">View All Authorizations</a>
                     </div>
                     <!-- END card-footer-->
                  </div>
                  <!-- END messages and activity-->
               </div>
            </div>
            <!-- END cards box-->
         </div>
      </section>

<?php include "components/page_footer.php" ?>  