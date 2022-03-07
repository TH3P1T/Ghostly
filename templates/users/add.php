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
               <div>Add User</div>
            </div>
            <div class="row">
               <div class="col-md-8">
                  <!-- START card-->
                  <div class="card card-default">
                     <div class="card-header">
                     <?php if ($add_attempted) { ?>
                        <?php if ($validation_failed) { ?>
                           <div class="alert alert-danger"><strong>Oh snap!</strong> Validation failed try again.</div>
                        <?php } ?>
                        <?php if (!$added) { ?>
                           <div class="alert alert-danger"><strong>Oh snap!</strong> Unable to add the new user to the database.</div>
                        <?php } ?>
                        <?php if ($added) { ?>
                           <div class="alert alert-success"><strong>Success!</strong> User added</div>
                        <?php } ?>
                     <?php } ?>
                     </div>
                     <div class="card-body">
                        <form method="post" action="<?= $url_base ?>users/add">
                           <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                           <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                           <div class="form-group">
                              <label>Username</label>
                              <input class="form-control" type="text" name="username" placeholder="Username" required="" value="<?= htmlspecialchars($user_username) ?>" />
                           </div>
                           <div class="form-group">
                              <label>Password</label>
                              <input class="form-control" type="text" name="password" placeholder="Password" required="" value="<?= htmlspecialchars($user_password) ?>" />
                           </div>
                           <div class="form-group">
                              <label>Timezone</label>
                              <select class="form-control" id="user-timezone-select" name="timezone" required="">
                                 <?php 
                                 $timezone_list = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                                 foreach ($timezone_list as $tz_key => $tz_value) { ?>
                                    <option value="<?= htmlspecialchars($tz_value) ?>" <?= ($user_timezone == $tz_value ? "selected" : "") ?>><?= htmlspecialchars($tz_value) ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="form-group">
                              <label>Notes</label>
                              <textarea class="form-control" name="notes"><?= htmlspecialchars($user_notes) ?></textarea>
                           </div>
                           <button class="btn btn-primary" type="submit">Add User</button>
                        </form>
                     </div>
                  </div>
                  <!-- END card-->
               </div>
            </div>
         </div>
      </section>

<?php include $this->templatePath . "/components/page_footer.php" ?>  