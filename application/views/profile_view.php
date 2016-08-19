<div id="profile-page" class="section">
    <!-- profile-page-header -->
    <div id="profile-page-header" class="card" style="background: transparent !important; border-radius: 5px; border:solid 1px  #e0e0e0">
        <div class="card-image waves-effect waves-block waves-light">
            <img class="activator" src="<?= base_url() ?>assets/images/user-bg.jpg" alt="user p pic">
        </div>
        <figure class="card-profile-image">
            <img src="<?= $this->users_model->user_pic($details->user_id) ?>" alt="profile image"
                 class="circle responsive-img activator">
        </figure>
        <div class="card-content">
            <div class="row">
                <div class="col s7 offset-s1" style=" padding-left: 20px !important;">
                    <h4 class="card-title" style="line-height: 48px !important;">
                        <?= $this->users_model->user($details->user_id)->user_name ?>
                    </h4>

                    <p class="medium-small"> <?= $this->users_model->get_category($details->user_category_id)->user_category_name ?></p>
                </div>
                <div class="col s4 right-align">
                    <?php
                    if ($this->users_model->requires_role(array('edit_user'))) {
                        ?>
                        <button class="btn-floating ajax orange"
                                value="users/trash/<?= urlencode(base64_encode($details->user_id)) . "/1/" . !$details->deleted ?>">
                            <?php if ($details->deleted) {
                                echo '<i class="mdi-action-restore"></i>';
                            } else {
                                echo '<i class="mdi-action-delete"></i>';
                            } ?>
                        </button>
                    <?php } ?>
                    <a class="btn-floating activator waves-effect waves-light darken-2 right">
                        <i class="mdi-action-perm-identity"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-reveal" style="background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 5px">
            <p>
                <span class="card-title grey-text text-darken-4">
                    <?= $details->user_name ?>
                    <i class="mdi-navigation-close right"></i>
                </span>
                <span>
                    <i class="mdi-action-perm-identity cyan-text text-darken-2"></i>
                    <?= $this->users_model->get_category($details->user_category_id)->user_category_name ?>
                </span>
            </p>

            <p><?= $details->user_about ?></p>

            <p>
                <i class="mdi-action-perm-phone-msg cyan-text text-darken-2"></i> <?= $this->users_model->user($details->user_id)->user_phone ?>
            </p>

            <p>
                <i class="mdi-communication-email cyan-text text-darken-2"></i> <?= $this->users_model->user($details->user_id)->user_email ?>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col s12 m8">
            <ul class="collection">
                <li class="collection-item">
                    <div class="row">
                        <div class="col s6">
                            <h5 class="card-title orange-text text-darken-4">
                                Profile
                            </h5>
                        </div>
                        <div class="col s6">
                            <ul class="tabs tab-profile">
                                <li class="tab col s3">
                                    <a class="waves-effect green-text waves-light" href="#details">
                                        <i class="mdi-action-info"></i> Details</a>
                                </li>
                                <li class="tab col s3">
                                    <a class="waves-effect green-text waves-light" href="#edit">
                                        <i class="mdi-editor-border-color"></i> Edit Details</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li class="collection-item" id="details">
                    <h4 class="header2">User Details</h4>
                    <hr/>
                    <div class="row padding-10">
                        <div class="col s2 grey-text darken-1">
                            Name
                        </div>
                        <div class="col s4 grey-text text-darken-4 left-align">
                            <?= $details->user_name ?>
                        </div>
                        <div class="col s2 grey-text darken-1">
                            Category
                        </div>
                        <div class="col s4 grey-text text-darken-4 left-align">
                            <?= $this->users_model->get_category($details->user_category_id)->user_category_name ?>
                        </div>
                    </div>
                    <div class="row padding-10">
                        <div class="col s2  grey-text darken-1">
                            Email
                        </div>
                        <div class="col s4 grey-text text-darken-4 left-align">
                            <?= $details->user_email ?>
                        </div>
                        <div class="col s2 grey-text darken-1">
                            Phone
                        </div>
                        <div class="col s4 grey-text text-darken-4 left-align">
                            <?= $details->user_phone ?>
                        </div>
                    </div>
                    <div class="row padding-10">
                        <div class="col s2 grey-text darken-1">
                            Registered By:
                        </div>
                        <div class="col s4 grey-text text-darken-4 left-align">
                            <?= $this->users_model->user($details->user_added_by)->user_name ?>
                        </div>
                        <div class="col s2 grey-text darken-1">
                            Registered Date
                        </div>
                        <div class="col s4 grey-text text-darken-4 left-align">
                            <?= $details->user_created_date ?>
                        </div>
                    </div>
                    <div class="row padding-10">
                        <div class="col s2 grey-text darken-1">
                            Account Status:
                        </div>
                        <div class="col s4 grey-text text-darken-4 left-align">
                            <?= $details->user_status ? 'Active' : 'Inactive' ?>
                        </div>
                        <div class="col s2 grey-text darken-1">
                            Account Access
                        </div>
                        <div class="col s4 grey-text text-darken-4 left-align">
                            <?= $details->user_access ? 'Allowed' : 'Blocked' ?>
                        </div>
                    </div>
                    <div class="row padding-10">
                        <div class="col s2 grey-text darken-1">
                            User About:
                        </div>
                        <div class="col s4 grey-text text-darken-4 left-align">
                            <?= $details->user_about ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item" id="edit">

                    <h4 class="header2">Change Details</h4>
                    <hr/>
                    <form method="post"
                          action="<?= base_url() ?>index.php/profile/update.html"
                          enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?= $details->user_id ?>" required/>

                        <div class="row">
                            <div class="input-field col s6">
                                <?php
                                $name = explode(" ", $details->user_name);
                                $f_name = $name[0];
                                unset($name[0]);
                                $l_name = implode(" ", $name);
                                ?>
                                <input id="first_name" name="first_name" type="text"
                                       value="<?= $f_name ?>" required>
                                <label class="active" for="first_name">First Name</label>
                            </div>

                            <div class="input-field col s6">
                                <input id="last_name" name="last_name"
                                       value="<?= $l_name ?>" type="text" required>
                                <label class="active" for="last_name">Last Name</label>
                            </div>
                        </div>
                        <?php if ($this->users_model->requires_role(array('man_users'))) { ?>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select id="user_level" data-placeholder="Select user level" class="browser-default chosen-select"
                                            name="category">
                                        <?php
                                        if ($details->user_category_id == 1) {
                                            echo '<option value="1" selected>System Manager </option>';
                                        } else {
                                            foreach ($this->crud_model->get_records('user_category', 'deleted', false) as $cat) { ?>
                                                <option <?= $cat->user_category_id == $details->user_category_id ? 'selected' : '' ?>
                                                    value="<?= $cat->user_category_id ?>"><?= $cat->user_category_name ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label for="user_level" class="active">User Level</label>
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="category"
                                   value="<?= $details->user_category_id ?>"/>
                        <?php } ?>
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="email5" type="email" value="<?= $details->user_email ?>"
                                       name="email" required>
                                <label class="active" for="email">Email</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="phone" type="text" value="<?= $details->user_phone ?>"
                                       name="phone" required>
                                <label class="active" for="phone">Phone</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <textarea id="about" name="about" class="materialize-textarea"><?= $details->user_about ?></textarea>
                                <label class="active" for="about">About the user</label>
                            </div>
                        </div>
                        <?php
                        if ($this->users_model->requires_role(array('change_account_access'))) {
                            ?>
                            <h4 class="header2">Account access Details</h4>
                            <hr/>
                            <br/>

                            <div class="row">
                                <div class="input-field col s6">
                                    <select name="status" id="status">
                                        <option value="1" <?= $details->user_status ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= $details->user_status ? '' : 'selected' ?>>Inactive</option>
                                    </select>
                                    <label for="status">Account Status</label>
                                </div>
                                <div class="input-field col s6">
                                    <select name="access" id="access">
                                        <option value="1" <?= $details->user_access ? 'selected' : '' ?>>Allowed</option>
                                        <option value="0" <?= $details->user_access ? '' : 'selected' ?>>Blocked</option>
                                    </select>
                                    <label for="access">Account Access</label>
                                </div>
                            </div>

                            <?php
                        }
                        if ($details->user_id == $this->users_model->user()->user_id) {
                            ?>
                            <h4 class="header2">Change Password</h4>
                            <hr/>
                            <br/>

                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="pass1" type="password" name="pass1">
                                    <label for="pass1">New Password</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="pass2" type="password" name="pass2">
                                    <label for="pass2">Confirm Password</label>
                                </div>
                            </div>

                            <h4 class="header2">Change My Face</h4>
                            <hr/>
                            <br/>
                            <div class="file-field input-field">
                                <div class="btn">
                                    <span>Picture</span>
                                    <input name="userfile" type="file">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" placeholder="Change profile pic"
                                           type="text">
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="row">
                            <div class="input-field col s12">
                                <?php
                                if ($this->users_model->requires_role(array('edit_user'))
                                    || $details->user_id == $this->users_model->user()->user_id
                                ) {
                                    ?>
                                    <button class="btn orange waves-effect waves-light right"
                                            type="submit">Update Details
                                        <i class="mdi-content-send right"></i>
                                    </button>
                                    <?php
                                } else {
                                    ?>
                                    <div id="card-alert" class="card red">
                                        <div class="card-content white-text">
                                            <p>DENIED : Sorry. No enough permissions to edit user
                                                profile.</p>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                </li>
            </ul>
            <ul class="collection">
                <li class="collection-item">
                    <div class="row">
                        <div class="col s6">
                            <h5 class="card-title orange-text text-darken-4">
                                System log
                            </h5>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <table id="data-table-simple" class="responsive-table display" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Time</th>
                            <th>IP Address</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $actions = read_file('./application/logs/' . $details->user_id . '-system_use');
                        if ($actions) {
                            $action = explode("%", $actions);
                            foreach ($action as $act) {
                                $log = explode("|", $act);
                                if (count($log) > 1) {
                                    ?>
                                    <tr>
                                        <td><?= $log[0] ?></td>
                                        <td><?= $log[1] ?></td>
                                        <td><?= $log[2] ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </li>
            </ul>
        </div>
        <div class="col s12 m4">
            <ul id="task-card" class="collection  with-header">
                <li class="collection-header">
                    <h5 class="task-card-title orange-text">Details</h5>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i> Name
                        </div>
                        <div class="col s7 grey-text text-darken-4 right-align">
                            <?= $details->user_name ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i> System
                            Level
                        </div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align">
                            <?= $this->users_model->get_category($details->user_category_id)->user_category_name ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-communication-email"></i> Email
                        </div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align"><?= $details->user_email ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-communication-call"></i> Phone
                        </div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align"><?= $details->user_phone ?></div>
                    </div>
                </li>
            </ul>
            <div class="card darken-2" style="background: transparent; border: 1px solid #e0e0e0; border-radius: 5px">
                <div class="card-content center">
                    <p>
                        <?= $details->user_about ?>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
