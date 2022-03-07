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
   <link rel="icon" type="image/x-icon" href="<?= $url_base ?>favicon.ico" />
   <title>Ghostly - Install</title>
   <!-- =============== VENDOR STYLES ===============-->
   <!-- FONT AWESOME-->
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/brands.css" />
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/regular.css" />
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/solid.css" />
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/fontawesome.css" />
   <!-- SIMPLE LINE ICONS-->
   <link rel="stylesheet" href="<?= $url_base ?>vendor/simple-line-icons/css/simple-line-icons.css" />
   <!-- =============== BOOTSTRAP STYLES ===============-->
   <link rel="stylesheet" href="<?= $url_base ?>css/bootstrap.css" id="bscss" />
   <!-- =============== APP STYLES ===============-->
   <link rel="stylesheet" href="<?= $url_base ?>css/app.css" id="maincss" />
   <link rel="stylesheet" href="<?= $url_base ?>css/theme-f.css" id="themecss" />
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
               <ul class="list-group mb-0">
                  <li class="list-group-item" href="#">
                     <?php if ($php_version_ok) { ?>
                        <em class="fa-fw fas fa-check-circle mr-2"></em>
                     <?php } else { ?>
                        <em class="fa-fw fas fa-circle mr-2"></em>
                     <?php } ?>
                     PHP Version Greater Than 5
                  </li>
                  <li class="list-group-item" href="#">
                     <?php if ($initialize_db) { ?>
                        <em class="fa-fw fas fa-check-circle mr-2"></em>
                     <?php } else { ?>
                        <em class="fa-fw fas fa-circle mr-2"></em>
                     <?php } ?>
                     Database Initialized
                  </li>
                  <li class="list-group-item" href="#">
                     <?php if ($setup_root_user) { ?>
                        <em class="fa-fw fas fa-check-circle mr-2"></em>
                     <?php } else { ?>
                        <em class="fa-fw fas fa-circle mr-2"></em>
                     <?php } ?>
                     Root Account Exists
                  </li>
                  <li class="list-group-item" href="#">
                     <?php if ($extension_gmp || $extension_bcmath) { ?>
                        <em class="fa-fw fas fa-check-circle mr-2"></em>
                     <?php } else { ?>
                        <em class="fa-fw fas fa-circle mr-2"></em>
                     <?php } ?>
                     GMP or BCMath Extension
                  </li>
                  <li class="list-group-item" href="#">
                     <?php if ($extension_openssl) { ?>
                        <em class="fa-fw fas fa-check-circle mr-2"></em>
                     <?php } else { ?>
                        <em class="fa-fw fas fa-circle mr-2"></em>
                     <?php } ?>
                     OpenSSL Extension
                  </li>
               </ul>
               <form class="mb-3" method="post" action="<?= $url_base ?>install" novalidate>
                  <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                  <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                  
                  <button class="btn btn-block btn-primary mt-3" type="submit">Install</button>
               </form>
            </div>
         </div>
         <!-- END card-->
      </div>
   </div>
   <!-- =============== VENDOR SCRIPTS ===============-->
   <!-- MODERNIZR-->
   <script src="<?= $url_base ?>vendor/modernizr/modernizr.custom.js"></script>
   <!-- STORAGE API-->
   <script src="<?= $url_base ?>vendor/js-storage/js.storage.js"></script>
   <!-- i18next-->
   <script src="<?= $url_base ?>vendor/i18next/i18next.js"></script>
   <script src="<?= $url_base ?>vendor/i18next-xhr-backend/i18nextXHRBackend.js"></script>
   <!-- JQUERY-->
   <script src="<?= $url_base ?>vendor/jquery/dist/jquery.js"></script>
   <!-- BOOTSTRAP-->
   <script src="<?= $url_base ?>vendor/popper.js/dist/umd/popper.js"></script>
   <script src="<?= $url_base ?>vendor/bootstrap/dist/js/bootstrap.js"></script>
   <!-- PARSLEY-->
   <script src="<?= $url_base ?>vendor/parsleyjs/dist/parsley.js"></script>
   <!-- =============== APP SCRIPTS ===============-->
   <script src="<?= $url_base ?>js/app.js"></script>
</body>

</html>