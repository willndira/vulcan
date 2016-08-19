<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="msapplication-tap-highlight" content="no">
        <meta name="description"
              content="<?= $this->lang->line('system_name') ?>">
        <meta name="keywords"
              content="<?= $this->lang->line('system_name') ?>">
        <title><?= $this->lang->line('system_name') ?> .::. <?= $page ?></title>

        <!-- Favicons-->

        <link rel="icon" href="<?= base_url() ?>assets/images/login-logo.png" sizes="32x32">
        <!-- Favicons-->
        <link rel="apple-touch-icon-precomposed" href="<?= base_url() ?>assets/images/login-logo.png">
        <meta name="msapplication-TileColor" content="#00bcd4">
        <meta name="msapplication-TileImage" content="<?= base_url() ?>assets/images/ogin-logo.png">
        <!-- For Windows Phone -->

        <!-- CORE CSS-->
        <link href="<?= base_url() ?>assets/css/materialize.min.css" type="text/css" rel="stylesheet"
              media="screen,projection">
        <link href="<?= base_url() ?>assets/css/style.min.css" type="text/css" rel="stylesheet" media="screen,projection">
        <!-- Custome CSS-->
        <link href="<?= base_url() ?>assets/css/custom/custom-style.css" type="text/css" rel="stylesheet"
              media="screen,projection">

        <!-- INCLUDED PLUGIN CSS ON THIS PAGE -->
        <link href="<?= base_url() ?>assets/js/plugins/prism/prism.css" type="text/css" rel="stylesheet"
              media="screen,projection">

        <link href="<?= base_url() ?>assets/js/plugins/chosen/chosen.min.css" type="text/css" rel="stylesheet"
              media="screen,projection">
        <link href="<?= base_url() ?>assets/js/plugins/perfect-scrollbar/perfect-scrollbar.css" type="text/css"
              rel="stylesheet" media="screen,projection">
        <link href="<?= base_url() ?>assets/js/plugins/chartist-js/chartist.min.css" type="text/css" rel="stylesheet"
              media="screen,projection">
        <link href="<?= base_url() ?>assets/js/plugins/data-tables/css/jquery.dataTables.min.css" type="text/css"
              rel="stylesheet"
              media="screen,projection">
        <link href="https://cdn.datatables.net/buttons/1.2.1/css/buttons.dataTables.min.css" type="text/css"
              rel="stylesheet"
              media="screen,projection">
              <?php
              if (isset($styles)) {
                  foreach ($styles as $style) {
                      ?>
                <link href="<?= base_url() ?>assets/<?= $style ?>.css" type="text/css"
                      rel="stylesheet" media="screen,projection">
                      <?php
                  }
              }
              ?>
    </head>

    <body>
        <div id="loader-wrapper">
            <div id="loader"></div>
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
        </div>
        <header id="header" class="page-topbar">
            <!-- start header nav-->
            <div class="navbar-fixed">
                <nav class="navbar-color green darken-4">
                    <div class="nav-wrapper">
                        <ul class="left">
                            <li>
                                <h1 class="logo-wrapper">
                                    <a href="<?= base_url() ?>" class="brand-logo darken-1">
                                        <?= $this->lang->line('system_name') ?>
                                    </a>
                                    <span class="logo-text"><?= $this->lang->line('system_name') ?></span>
                                </h1>
                            </li>
                        </ul>

                        <ul class="right hide-on-med-and-down">
                            <li>
                                <a href="#" data-activates="chat-out"
                                   class="waves-effect waves-block waves-light chat-collapse">
                                    <i class="mdi-social-notifications-on"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <!-- end header nav-->
        </header>
        <!-- END HEADER -->

        <!-- //////////////////////////////////////////////////////////////////////////// -->

        <!-- START MAIN -->
        <div id="main">
            <!-- START WRAPPER -->
            <div class="wrapper">

                <!-- START LEFT SIDEBAR NAV-->
                <aside id="left-sidebar-nav">
                    <ul id="slide-out" class="side-nav fixed leftside-navigation">
                        <li class="user-details  darken-2">
                            <div class="row">
                                <div class="col col s4 m4 l4">
                                    <img src="<?= $this->users_model->user_pic($this->users_model->user()->user_id) ?>" alt=""
                                         class="circle white-bg responsive-img valign profile-image">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col s11 m11 l11">
                                    <ul id="profile-dropdown" class="dropdown-content">
                                        <li><a href="<?= site_url('profile') ?>"><i
                                                    class="mdi-action-face-unlock"></i> Profile</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li><a href="<?= site_url('login/lock') ?>"><i
                                                    class="mdi-action-lock-outline"></i> Lock</a>
                                        </li>
                                        <li><a href="<?= site_url('logout') ?>"><i
                                                    class="mdi-hardware-keyboard-tab"></i> Logout</a>
                                        </li>
                                    </ul>
                                    <a class="btn-flat dropdown-button waves-effect waves-light white-text profile-btn"
                                       href="#" data-activates="profile-dropdown"><?= $this->users_model->user()->user_name ?>
                                        <i class="mdi-navigation-arrow-drop-down right"></i>
                                    </a>

                                    <p class="user-roal">
                                        <?= $this->users_model->get_category($this->users_model->user()->user_category_id)->user_category_name ?>
                                    </p>
                                </div>
                            </div>
                        </li>
                        <li class="bold"><a href="<?= site_url('dashboard') ?>" class="waves-effect waves-green">
                                <i class="mdi-action-home"></i> Home</a>
                        </li>



                        <?php if ($this->users_model->requires_role(array('view_sites', 'create_site'))) { ?>
                            <li class="no-padding">
                                <ul class="collapsible collapsible-accordion">
                                    <li class="bold">
                                        <a class="collapsible-header waves-effect waves-cyan">
                                            <i class="mdi-alert-warning"></i> Sites</a>

                                        <div class="collapsible-body">
                                            <ul>
                                                <?php if ($this->users_model->requires_role(array('view_sites', 'manage_sla', 'create_site'))) { ?>
                                                    <li>
                                                        <a href="<?= site_url('sites') ?>">
                                                            Sites Library
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($this->users_model->requires_role(array('view_sites', 'manage_sla', 'create_site'))) { ?>
                                                    <li>
                                                        <a href="<?= site_url('aps') ?>">
                                                            Aps Library
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($this->users_model->requires_role(array('manage_sla'))) { ?>
                                                    <li>
                                                        <a href="<?= site_url('sla') ?>">SLA settings</a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($this->users_model->requires_role(array('view_sites'))) { ?>
                                                    <li>
                                                        <a href="<?= site_url('sites/map') ?>">
                                                            Sites on Map
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        <?php } ?>


                        <?php if ($user_roles = $this->users_model->requires_role(array('man_users', 'man_roles'))) {
                            ?>
                            <li class="no-padding">
                                <ul class="collapsible collapsible-accordion">
                                    <li class="bold">
                                        <a class="collapsible-header waves-effect waves-cyan">
                                            <i class="mdi-social-group"></i> Users</a>

                                        <div class="collapsible-body">
                                            <ul>
                                                <?php if ($this->users_model->requires_role(array('man_users'))) { ?>
                                                    <li>
                                                        <a href="<?= site_url('users') ?>">Library </a>
                                                    </li>
                                                    <?php
                                                }
                                                if ($this->users_model->requires_role(array('man_roles'))) {
                                                    ?>
                                                    <li>
                                                        <a href="<?= site_url('users/level') ?>">Privileges</a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </li>


                            <?php
                        }
                        if ($this->users_model->requires_role(array('man_assets', 'define_assets', 'man_equips', 'define_equips'))) {
                            ?>


                        <?php } ?>


                    </ul>
                    <a href="#" data-activates="slide-out"
                       class="sidebar-collapse btn-floating btn-medium waves-effect waves-light hide-on-large-only cyan">
                        <i class="mdi-navigation-menu"></i>
                    </a>
                </aside>
                <!-- END LEFT SIDEBAR NAV-->
                <!-- //////////////////////////////////////////////////////////////////////////// -->

                <!-- START CONTENT -->
                <section id="content">
                    <!--start container-->
                    <div class="container">
                        <div class="row" style="margin-bottom: 5px !important;">
                            <!-- <div class="col s12 m9">
                                <h5 class="grey-text ">
                                    <i class="mdi-navigation-chevron-right"></i> <?= $page ?>
                                </h5>
                            </div> -->
                            <div class="col s12">
                                <?php
                                if (null != validation_errors() || null != $this->session->flashdata('error') || null != $this->session->flashdata('success')) {
                                    ?>
                                    <div class=" card <?= null == $this->session->flashdata('success') ? 'red' : 'green' ?>">
                                        <div class="card-content white-text">
                                            <?= validation_errors() ?>
                                            <?= $this->session->flashdata('error') ?>
                                            <?= $this->session->flashdata('success') ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>