<?php

if (!defined("GHOSTLY_INTERNAL")) {
    exit(0);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
   <meta name="description" content="Bootstrap Admin App" />
   <meta name="keywords" content="app, responsive, jquery, bootstrap, dashboard, admin" />
   <link rel="icon" type="image/x-icon" href="<?php print $url_base; ?>favicon.ico" />
   <title>Ghostly - Login</title>
   <!-- =============== VENDOR STYLES ===============-->
   <!-- FONT AWESOME-->
   <link rel="stylesheet" href="<?php print $url_base; ?>vendor/@fortawesome/fontawesome-free/css/brands.css" />
   <link rel="stylesheet" href="<?php print $url_base; ?>vendor/@fortawesome/fontawesome-free/css/regular.css" />
   <link rel="stylesheet" href="<?php print $url_base; ?>vendor/@fortawesome/fontawesome-free/css/solid.css" />
   <link rel="stylesheet" href="<?php print $url_base; ?>vendor/@fortawesome/fontawesome-free/css/fontawesome.css" />
   <!-- SIMPLE LINE ICONS-->
   <link rel="stylesheet" href="<?php print $url_base; ?>vendor/simple-line-icons/css/simple-line-icons.css" />
   <!-- =============== BOOTSTRAP STYLES ===============-->
   <link rel="stylesheet" href="<?php print $url_base; ?>css/bootstrap.css" id="bscss" />
   <!-- =============== APP STYLES ===============-->
   <link rel="stylesheet" href="<?php print $url_base; ?>css/app.css" id="maincss" />
   <link rel="stylesheet" href="<?php print $url_base; ?>css/theme-f.css" id="themecss" />
</head>

<body>
   <div class="wrapper">
      <div class="block-center mt-4 wd-xl">
         <!-- START card-->
         <div class="card card-flat">
            <div class="card-header text-center bg-dark">
               <em class="fa-2x icon-ghost bg-gray-dark"></em>
            </div>
            <div class="card-body">
               <?php if ($login_failed) { ?>
                  <div class="alert alert-danger" role="alert"><strong>Oh snap!</strong> Login failed try again.</div>
               <?php } ?>
               <p class="text-center py-2">SIGN IN TO CONTINUE.</p>
               <form class="mb-3" id="loginForm" method="post" action="<?php print $url_base; ?>login" novalidate>
                  <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                  <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                  <div class="form-group">
                     <div class="input-group with-focus">
                        <input class="form-control border-right-0" name="username" type="text" placeholder="Enter username" autocomplete="off" required />
                        <div class="input-group-append">
                           <span class="input-group-text text-muted bg-transparent border-left-0">
                              <em class="fa fa-envelope"></em>
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="input-group with-focus">
                        <input class="form-control border-right-0" name="password" type="password" placeholder="Password" required />
                        <div class="input-group-append">
                           <span class="input-group-text text-muted bg-transparent border-left-0">
                              <em class="fa fa-lock"></em>
                           </span>
                        </div>
                     </div>
                  </div>
                  
                  <button class="btn btn-block btn-primary mt-3" type="submit">Login</button>
               </form>
            </div>
         </div>
         <!-- END card-->
      </div>
   </div>
   <!-- =============== VENDOR SCRIPTS ===============-->
   <!-- MODERNIZR-->
   <script src="<?php print $url_base; ?>vendor/modernizr/modernizr.custom.js"></script>
   <!-- STORAGE API-->
   <script src="<?php print $url_base; ?>vendor/js-storage/js.storage.js"></script>
   <!-- i18next-->
   <script src="<?php print $url_base; ?>vendor/i18next/i18next.js"></script>
   <script src="<?php print $url_base; ?>vendor/i18next-xhr-backend/i18nextXHRBackend.js"></script>
   <!-- JQUERY-->
   <script src="<?php print $url_base; ?>vendor/jquery/dist/jquery.js"></script>
   <!-- BOOTSTRAP-->
   <script src="<?php print $url_base; ?>vendor/popper.js/dist/umd/popper.js"></script>
   <script src="<?php print $url_base; ?>vendor/bootstrap/dist/js/bootstrap.js"></script>
   <!-- PARSLEY-->
   <script src="<?php print $url_base; ?>vendor/parsleyjs/dist/parsley.js"></script>
   <!-- =============== APP SCRIPTS ===============-->
   <script src="<?php print $url_base; ?>js/app.js"></script>
</body>

</html>