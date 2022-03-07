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
               <div>View User</div>
            </div>
            <div class="row">
               <div class="col-md-8">
                  <!-- START card-->
                  <div class="card card-default">
                     <div class="card-header">
                     </div>
                     <div class="card-body">
                        <form method="post" action="<?= $url_base ?>users/view/<?= htmlspecialchars($user_id) ?>">
                           <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                           <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                           <div class="form-group">
                              <label>Username</label>
                              <input class="form-control" type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($user_username) ?>" readonly />
                           </div>
                           <div class="form-group">
                              <label>Password</label>
                              <input class="form-control" type="text" name="password" placeholder="Password" value="<?= htmlspecialchars($user_password) ?>" readonly />
                           </div>
                           <div class="form-group">
                              <label>Timezone</label>
                              <select class="form-control" id="user-timezone-select" name="timezone" required="" readonly>
                                 <?php 
                                 $timezone_list = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                                 foreach ($timezone_list as $tz_key => $tz_value) { ?>
                                    <option value="<?= htmlspecialchars($tz_value) ?>" <?= ($user_timezone == $tz_value ? "selected" : "") ?>><?= htmlspecialchars($tz_value) ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="form-group">
                              <label>Notes</label>
                              <textarea class="form-control" name="notes" readonly><?= htmlspecialchars($user_notes) ?></textarea>
                           </div>
                        </form>
                     </div>
                  </div>
                  <!-- END card-->
               </div>
            </div>
         </div>
      </section>

<?php include $this->templatePath . "/components/page_footer.php" ?>  