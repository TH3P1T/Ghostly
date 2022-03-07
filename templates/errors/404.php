<?php

if (!defined("GHOSTLY_INTERNAL")) {
    exit(0);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <meta name="description" content="Bootstrap Admin App">
   <meta name="keywords" content="app, responsive, jquery, bootstrap, dashboard, admin">
   <link rel="icon" type="image/x-icon" href="<?= $url_base ?>favicon.ico">
   <title>Ghostly - File Not Found</title>
   <!-- =============== VENDOR STYLES ===============-->
   <!-- FONT AWESOME-->
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/brands.css">
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/regular.css">
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/solid.css">
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/fontawesome.css">
   <!-- SIMPLE LINE ICONS-->
   <link rel="stylesheet" href="<?= $url_base ?>vendor/simple-line-icons/css/simple-line-icons.css">
   <!-- =============== BOOTSTRAP STYLES ===============-->
   <link rel="stylesheet" href="<?= $url_base ?>css/bootstrap.css" id="bscss">
   <!-- =============== APP STYLES ===============-->
   <link rel="stylesheet" href="<?= $url_base ?>css/app.css" id="maincss">
</head>

<body>
   <div class="wrapper">
      <div class="abs-center wd-xl">
         <!-- START card-->
         <div class="text-center mb-4">
            <div class="text-lg mb-3">404</div>
            <p class="lead m-0">We couldn't find this page.</p>
            <p>The page you are looking for does not exists.</p>
         </div>
         
         <ul class="list-inline text-center text-sm mb-4">
            <li class="list-inline-item">
               <a class="text-muted" href="<?= $url_base ?>dashboard">Go to App</a>
            </li>
            <li class="text-muted list-inline-item">|</li>
            <li class="list-inline-item">
               <a class="text-muted" href="<?= $url_base ?>login">Login</a>
            </li>
         </ul>
         <div class="p-3 text-center">
            
         </div>
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