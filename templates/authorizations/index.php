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
               <div>Authorizations</div>
            </div>
			   <div class="card">
               <div class="card-header">Authorization List</div>
               <div class="card-body">
                  <div class="table-responsive bootgrid">
                     <table class="table table-striped" id="authorizations-bootgrid">
                        <thead>
                           <tr>
                              <th data-column-id="id" data-type="numeric" data-order="desc">ID</th>
                              <th data-column-id="license_key">License</th>
                              <th data-column-id="hardware_id">HWID</th>
                              <th data-column-id="ip_address">IP Address</th>
                              <th data-column-id="commands" data-formatter="commands" data-sortable="false">
                                 <div class="text-right">Commands</div>
                              </th>
                           </tr>
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </section>

<?php include $this->templatePath . "/components/page_footer.php" ?>  