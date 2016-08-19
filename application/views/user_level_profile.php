<div class="section">
    <?php
    $perms = $this->crud_model->get_records('user_roles', 'category_id', $details->user_category_id);
    ?>
    <div class="row">
        <div class="col s12 m8">
            <div class="row">
                <div class="col s12">
                    <h5 class="card-title grey-text text-darken-4">
                        <i class="mdi-navigation-chevron-right"></i>
                        <?= $details->user_category_name ?>
                    </h5>
                    <p class="medium green-text">User category details</p>
                </div>
                <div class="col s12">
                    <ul class="collection">
                        <li class="collection-item">
                            <h5 class="task-card-title orange-text">
                                Users
                            </h5>

                        </li>
                        <li class="collection-item">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Access</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($users = $this->crud_model->get_records('users', "user_category_id", $details->user_category_id) as $user) {
                                    if ($user->user_id != 1) { ?>
                                        <tr class="link" id="profile/user/<?= urlencode(base64_encode($user->user_id)) ?>">
                                            <td><?= $user->user_id ?></td>
                                            <td><?= $user->user_name ?></td>
                                            <td><?= $user->user_email ?></td>
                                            <td><?= $user->user_phone ?></td>
                                            <td><?= $user->user_access ? '<span class="green-text">granted</span>' : '<span class="orange-text">blocked</span>' ?></td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                    <ul class="collection">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s6">
                                    <h5 class="task-card-title orange-text">
                                        Details
                                    </h5>
                                </div>
                                <div class="col s6">
                                    <ul class="tabs tab-profile">
                                        <li class="tab col s3">
                                            <a class=" waves-effect green-text waves-light active" href="#stats">
                                                <i class="mdi-image-timelapse"></i> Permissions</a>
                                        </li>
                                        <li class="tab col s3">
                                            <a class="waves-effect green-text waves-light" href="#edit">
                                                <i class="mdi-editor-border-color"></i> Details</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item" id="stats">
                            <table id="data-table-simple" class="responsive-table display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Role code</th>
                                    <th>Description</th>
                                    <th>Created by</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($perms as $perm) {
                                    $perm_details = $this->crud_model->get_record('roles', 'role_code', $perm->role_code);
                                    ?>
                                    <tr>
                                        <td><?= $perm->user_role_id ?></td>
                                        <td><?= $perm->ur_add_date ?></td>
                                        <td><?= $perm_details->role_code ?></td>
                                        <td><?= $perm_details->role_name ?></td>
                                        <td><?= $this->users_model->user($perm->ur_added_by)->user_name ?></td>
                                        <td>
                                            <i id="users/remove_perm/<?= urlencode(base64_encode($perm->user_role_id)) ?>"
                                               class="material-icons mdi-navigation-close link"></i>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                        <li class="collection-item" id="edit">
                            <h4 class="header2">Edit details</h4>
                            <hr/>
                            <form method="post"
                                  action="<?= site_url('users/update_level/' . urlencode(base64_encode($details->user_category_id))) ?>">
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input id="first_name" name="level_name"
                                               value="<?= $details->user_category_name ?>" type="text" required>
                                        <label for="first_name" class="active">Level Name</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <select id="perms" name="permissions[]" data-placeholder="Select privileges"
                                                class="browser-default chosen-select" multiple>
                                            <option value="" disabled>Choose Permissions</option>
                                            <?php foreach ($this->crud_model->get_records('roles') as $role) {
                                                if (!$this->users_model->role_selected($details->user_category_id, $role->role_code)) {
                                                    ?>
                                                    <option value="<?= $role->role_code ?>">
                                                        <?= $role->role_name ?>
                                                    </option>
                                                <?php }
                                            } ?>
                                        </select>
                                        <label for="perms" class="active">Level Permissions</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                            <textarea id="about" name="about"
                                                      class="materialize-textarea"><?= $details->user_category_description ?></textarea>
                                        <label class="active" for="about">Level Description</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <button class="btn orange waves-effect waves-light right" type="submit">
                                            Update
                                            <i class="mdi-content-send right"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="row">
                <div class="col s12">
                    <button class="btn ajax orange right"
                            value="users/trash/<?= urlencode(base64_encode($details->user_category_id)) . "/0/" . !$details->deleted ?>">
                        <?php if ($details->deleted) {
                            echo '<i class="mdi-action-restore"></i> Restore';
                        } else {
                            echo '<i class="mdi-action-delete"></i> Trash';
                        } ?>
                    </button>
                    <a class="btn green right modal-trigger" href="#new-user">
                        <i class="mdi-content-add"></i> New user
                    </a>
                </div>
                <div class="col s12">
                    <ul class="collection with-header">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s6 center">
                                    <h5 class="green-text">
                                        <strong>
                                            <?= count($perms) ?>
                                        </strong>
                                    </h5>
                                    <p class="medium">Total permissions</p>
                                </div>
                                <div class="col s6 center green-text">
                                    <h5 class="orange-text">
                                        <strong>
                                            <?= count($users) ?>
                                        </strong>
                                    </h5>
                                    <p class="medium">Users</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col s12">
                    <ul id="task-card" class="collection with-header">
                        <li class="collection-header">
                            <h5 class="task-card-title orange-text">Details</h5>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1">
                                    <i class="mdi-action-wallet-travel"></i> Name
                                </div>
                                <div class="col s7 grey-text text-darken-4">
                                    <?= $details->user_category_name ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i>
                                    Permissions
                                </div>
                                <div
                                    class="col s7 grey-text text-darken-4">
                                    <?= count($perms) ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item link"
                            id="profile/user/<?= urlencode(base64_encode($details->user_category_created_by)) ?>">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i>
                                    Created By
                                </div>
                                <div
                                    class="col s7 grey-text text-darken-4">
                                    <?= $this->users_model->user($details->user_category_created_by)->user_name ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-action-view-day"></i> Date
                                </div>
                                <div class="col s7 grey-text text-darken-4">
                                    <?= $details->user_category_date ?>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col s12">
                    <div class="card" style="background: transparent !important; border-radius: 5px; border: 1px solid #e0e0e0">
                        <div class="card-content center">
                            <p>
                                <?= $details->user_category_description ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="new-user" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Register a <?= $this->users_model->get_category($details->user_category_id)->user_category_name ?></h5>
            </li>
            <li class="collection-item">
                <form class="col s12" method="post" action="<?= site_url('users/register/true') ?>">
                    <div class="row">
                        <input type="hidden" name="category" value="<?= $details->user_category_id ?>"/>
                        <div class="input-field col s6">
                            <input id="first_name" name="first_name" type="text" required>
                            <label for="first_name">First Name</label>
                        </div>

                        <div class="input-field col s6">
                            <input id="last_name" name="last_name" type="text" required>
                            <label for="last_name">Last Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="email" type="email" name="email" required>
                            <label for="email">Email</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="phone" type="text" name="phone" required>
                            <label for="phone">Phone</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="about" name="about" class="materialize-textarea"></textarea>
                            <label for="about">About the user</label>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <button class="btn orange waves-effect waves-light right" type="submit">
                                    Register
                                    <i class="mdi-content-send right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>