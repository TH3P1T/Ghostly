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
               <div>Api Endpoints</div>
            </div>
            <div class="row">
               <div class="col-xl-12">
                  <a class="btn btn-labeled btn-info mb-2" href="<?= $url_base ?>endpoints/add">
					      <span class="btn-label"><i class="fa fa-plus-square"></i></span>Add Endpoint</a>
               </div>
            </div>
			<div class="card">
               <div class="card-header">Endpoint List</div>
               <div class="card-body">
                  <div class="table-responsive bootgrid">
                     <table class="table table-striped" id="endpoints-bootgrid">
                        <thead>
                           <tr>
                              <th data-column-id="id" data-type="numeric" data-order="desc">ID</th>
                              <th data-column-id="name">Name</th>
                              <th data-column-id="key">Key</th>
                              <th data-column-id="secret">Secret</th>
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