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
          content="<?= $this->lang->line('system_name') ?>, maynedev.co.ke">
    <title><?= $this->lang->line('system_name') ?></title>

    <link rel="icon" href="<?= base_url() ?>assets/images/login-logo.png" sizes="32x32">
    <!-- Favicons-->
    <link rel="apple-touch-icon-precomposed" href="<?= base_url() ?>assets/images/login-logo.png">
    <!-- For iPhone -->
    <meta name="msapplication-TileColor" content="#00bcd4">
    <meta name="msapplication-TileImage" content="images/favicon/mstile-144x144.png">
    <!-- For Windows Phone -->


    <!-- CORE CSS-->

    <link href="<?= base_url() ?>assets/css/materialize.min.css" type="text/css" rel="stylesheet"
          media="screen,projection">
    <link href="<?= base_url() ?>assets/css/style.min.css" type="text/css" rel="stylesheet" media="screen,projection">
    <!-- Custome CSS-->
    <link href="<?= base_url() ?>assets/css/custom/custom-style.css" type="text/css" rel="stylesheet"
          media="screen,projection">
    <link href="<?= base_url() ?>assets/css/layouts/page-center.css" type="text/css" rel="stylesheet"
          media="screen,projection">

    <!-- INCLUDED PLUGIN CSS ON THIS PAGE -->
    <link href="<?= base_url() ?>assets/js/plugins/prism/prism.css" type="text/css" rel="stylesheet"
          media="screen,projection">
    <link href="<?= base_url() ?>assets/js/plugins/perfect-scrollbar/perfect-scrollbar.css" type="text/css"
          rel="stylesheet" media="screen,projection">

</head>

<body class="cyan ">
<!-- Start Page Loading
<div id="loader-wrapper">
    <div id="loader"></div>
    <div class="loader-section section-left"></div>
    <div class="loader-section section-right"></div>
</div>
<!-- End Page Loading -->


<div class="row">
    <div class="col s12 z-depth-4 card-panel">
        <form class="login-form"
              action="<?= base_url('index.php/' . (!$recover ? 'login' : 'recover') . '/validate') ?>"
              method="post">
            <div class="row">
                <div class="input-field col s12 center">
                    <img src="<?= base_url() ?>assets/images/login-logo.png" alt=""
                         class="responsive-img valign profile-image-login">

                    <h1 style="font-weight: 800 !important;"><?= $this->lang->line('system_name') ?></h1>
                    <h5> <?= $recover ? 'Reset Password' : 'Login'; ?></h5>
                </div>
                <div class="col s12 center">
                    <span class="text-success"><?= $this->session->flashdata('success') ?></span>
                    <span class="secondary-text-color"><?= validation_errors() ?></span>
                </div>
            </div>
            <div class="row margin">
                <div class="input-field col s12">
                    <i class="mdi-social-person-outline prefix"></i>
                    <input id="email" name="email" type="email" value="<?= set_value('email') ?>" required/>
                    <label for="email" class="center-align">Email address</label>
                </div>
            </div>

            <?php
            if (!$recover) {
                ?>
                <div class="row margin">
                    <div class="input-field col s12">
                        <i class="mdi-action-lock-outline prefix"></i>
                        <input id="password" name="password" type="password" value="<?= set_value('password') ?>"
                               required/>
                        <label for="password">Password</label>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="row">
                <div class="input-field col s12">
                    <button type="submit" class="btn waves-effect waves-light col s12">
                        <?= $recover ? 'Reset Password' : 'Login'; ?>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s6 m6 l6">
                    <?php if (!$recover) { ?>
                        <a class="pull-left" href="<?= base_url('index.php/recover') ?>">Forgot Password?</a>
                    <?php } else {
                        ?>
                        <a class="pull-left" href="<?= base_url('index.php/login') ?>">Go to login</a>
                        <?php
                    } ?>
                </div>
            </div>

        </form>
    </div>
</div>


<!-- ================================================
  Scripts
  ================================================ -->

<!-- jQuery Library -->
<script type="text/javascript" src="<?= base_url() ?>assets/js/plugins/jquery-1.11.2.min.js"></script>
<!--materialize js-->
<script type="text/javascript" src="<?= base_url() ?>assets/js/materialize.min.js"></script>
<!--prism-->
<script type="text/javascript" src="<?= base_url() ?>assets/js/plugins/prism/prism.js"></script>
<!--scrollbar-->
<script type="text/javascript"
        src="<?= base_url() ?>assets/js/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>

<!--plugins.js - Some Specific JS codes for Plugin Settings-->
<script type="text/javascript" src="<?= base_url() ?>assets/js/plugins.min.js"></script>
<!--custom-script.js - Add your own theme custom JS-->
<script type="text/javascript" src="<?= base_url() ?>assets/js/custom-script.js"></script>

</body>

</html>