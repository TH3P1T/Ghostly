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
   <title>Ghostly</title>
   <!-- =============== VENDOR STYLES ===============-->
   <!-- FONT AWESOME-->
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/brands.css" />
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/regular.css" />
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/solid.css" />
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@fortawesome/fontawesome-free/css/fontawesome.css" />
   <!-- SIMPLE LINE ICONS-->
   <link rel="stylesheet" href="<?= $url_base ?>vendor/simple-line-icons/css/simple-line-icons.css" />
   <!-- ANIMATE.CSS-->
   <link rel="stylesheet" href="<?= $url_base ?>vendor/animate.css/animate.css" />
   <!-- WHIRL (spinners)-->
   <link rel="stylesheet" href="<?= $url_base ?>vendor/whirl/dist/whirl.css" />
   <!-- =============== PAGE VENDOR STYLES ===============-->
   <!-- Bootgrid-->
   <link rel="stylesheet" href="<?= $url_base ?>vendor/jquery-bootgrid/dist/jquery.bootgrid.css">
   <!-- DATETIMEPICKER-->
   <link rel="stylesheet" href="<?= $url_base ?>vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker.css">
   <!-- SELECT2-->
   <link rel="stylesheet" href="<?= $url_base ?>vendor/select2/dist/css/select2.css">
   <link rel="stylesheet" href="<?= $url_base ?>vendor/@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css">
   <!-- =============== BOOTSTRAP STYLES ===============-->
   <link rel="stylesheet" href="<?= $url_base ?>css/bootstrap.css" id="bscss" />
   <!-- =============== APP STYLES ===============-->
   <link rel="stylesheet" href="<?= $url_base ?>css/app.css" id="maincss" />
   <link rel="stylesheet" href="<?= $url_base ?>css/theme-f.css" id="themecss" />
</head>

<body>
   <div class="wrapper">
      <!-- top navbar-->
      <header class="topnavbar-wrapper">
         <!-- START Top Navbar-->
         <nav class="navbar topnavbar">
            <!-- START navbar header-->
            <div class="navbar-header">
               <a class="navbar-brand" href="<?= $url_base ?>">
                  <div class="brand-logo">
                     <em class="fa-2x icon-ghost"></em>
                  </div>
                  <div class="brand-logo-collapsed">
                     <em class="icon-ghost"></em>
                  </div>
               </a>
            </div>
            <!-- END navbar header-->
            <!-- START Left navbar-->
            <ul class="navbar-nav mr-auto flex-row">
               <li class="nav-item">
                  <!-- Button used to collapse the left sidebar. Only visible on tablet and desktops-->
                  <a class="nav-link d-none d-md-block d-lg-block d-xl-block" href="#" data-trigger-resize="" data-toggle-state="aside-collapsed">
                     <em class="fas fa-bars"></em>
                  </a>
                  <!-- Button to show/hide the sidebar on mobile. Visible on mobile only.-->
                  <a class="nav-link sidebar-toggle d-md-none" href="#" data-toggle-state="aside-toggled" data-no-persist="true">
                     <em class="fas fa-bars"></em>
                  </a>
               </li>
            </ul>
            <?php if ($install_enabled) { ?>
            <ul class="navbar-nav mr-auto flex-row">
               <li class="nav-item">
                  <span class="alert alert-danger" role="alert"><strong>Oh snap!</strong> Install page is still enabled, Please disable it in your config.php</span>
               </li>
            </ul>
            <?php } ?>
            <!-- END Left navbar-->
            <!-- START Right navbar -->
            <ul id="right-nav" class="navbar-nav flex-row">
               <li class="nav-item">
                  <form class="mb-3" method="post" action="<?= $url_base ?>logout" novalidate>
                     <input type="hidden" name="<?= $csrf_name_key ?>" value="<?= $csrf_name ?>" />
                     <input type="hidden" name="<?= $csrf_value_key ?>" value="<?= $csrf_value ?>" />
                     
                     <button class="btn btn-square btn-inverse mt-3" type="submit">Logout</button>
                  </form>
               </li>

            </ul>
            <!-- END Right navbar -->
         </nav>
         <!-- END Top Navbar-->
      </header>