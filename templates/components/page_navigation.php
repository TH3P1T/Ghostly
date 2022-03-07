<?php

if (!defined("GHOSTLY_INTERNAL")) {
    exit(0);
}
?>
      <!-- sidebar-->
      <aside class="aside-container">
         <!-- START Sidebar (left)-->
         <div class="aside-inner">
            <nav class="sidebar" data-sidebar-anyclick-close="">
               <!-- START sidebar nav-->
               <ul class="sidebar-nav">
                  <!-- Iterates over all sidebar items-->
                  <li class="nav-heading ">
                     <span>Licensing</span>
                  </li>
                  <li class=" ">
                     <a href="<?= $url_base ?>dashboard" title="Dashboard">
                        <em class="icon-info"></em>
                        <span>Dashboard</span>
                     </a>
                  </li>
				      <li class=" ">
                     <a href="<?= $url_base ?>products" title="Products">
                        <em class="icon-layers"></em>
                        <span>Products</span>
                     </a>
                  </li>
				      <li class=" ">
                     <a href="<?= $url_base ?>licenses" title="Licenses">
                        <em class="icon-shield"></em>
                        <span>Licenses</span>
                     </a>
                  </li>
                  <li class=" ">
                     <a href="<?= $url_base ?>authorizations" title="Authorizations">
                        <em class="icon-check"></em>
                        <span>Authorizations</span>
                     </a>
                  </li>
                  <li class="nav-heading ">
                     <span>Administration</span>
                  </li>
                  <li class=" ">
                     <a href="<?= $url_base ?>users" title="Users">
                        <em class="icon-people"></em>
                        <span>Users</span>
                     </a>
                  </li>
                  <li class=" ">
                     <a href="<?= $url_base ?>endpoints" title="Api Endpoints">
                        <em class="icon-screen-desktop"></em>
                        <span>Api Endpoints</span>
                     </a>
                  </li>
               </ul>
               <!-- END sidebar nav-->
            </nav>
         </div>
         <!-- END Sidebar (left)-->
      </aside>