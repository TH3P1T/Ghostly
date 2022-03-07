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
               <div>Delete User</div>
            </div>
            <div class="row">
               <div class="col-md-8">
                  <!-- START card-->
                  <div class="card card-default">
                     <div class="card-header">
                     <?php if ($delete_attempted) { ?>
                        <?php if (!$deleted) { ?>
                           <div class="alert alert-danger"><strong>Oh snap!</strong> Unable to delete the user.</div>
                        <?php } ?>
                        <?php if ($deleted) { ?>
                           <div class="alert alert-success"><strong>Success!</strong> User deleted</div>
                        <?php } ?>
                     <?php } ?>
                     <?php if (!$delete_attempted) { ?>
                        <div class="alert alert-warning"><strong>Warning!</strong> Are you sure you want to delete this user?</div>
                     <?php } ?>
                     </div>
                     <?php if (!$delete_attempted || !$deleted) { ?>
                     <div class="card-body">
                        <form method="post" action="<?= $url_base ?>users/delete/<?= htmlspecialchars($user_id) ?>">
                           <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                           <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                           <div class="form-group">
                              <label>Username</label>
                              <input class="form-control" type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($user_username) ?>" disabled />
                           </div>
                           <div class="form-group">
                              <label>Timezone</label>
                              <select class="form-control" id="user-timezone-select" name="timezone" required="" disabled>
                                 <?php 
                                 $timezone_list = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                                 foreach ($timezone_list as $tz_key => $tz_value) { ?>
                                    <option value="<?= htmlspecialchars($tz_value) ?>" <?= ($user_timezone == $tz_value ? "selected" : "") ?>><?= htmlspecialchars($tz_value) ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="form-group">
                              <label>Notes</label>
                              <textarea class="form-control" name="notes" disabled><?= htmlspecialchars($user_notes) ?></textarea>
                           </div>
                           <button class="btn btn-primary" type="submit">Delete User</button>
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