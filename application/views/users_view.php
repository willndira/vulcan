<div class="row">
    <div class="col s12 m8">
        <div class="row">
            <div class="col s12">
                <h5 class="card-title grey-text">
                    <i class="mdi-navigation-chevron-right"></i>
                    <?= $page ?>
                </h5>
                <p class="medium green-text">All registered users</p>
            </div>
            <div class="col s12">
                <ul class="collection with-header">
                    <li class="collection-item">
                        <table class="responsive-table dt display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Access</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($this->crud_model->get_records('users') as $user) {
                                if ($user->user_id != 1) { ?>
                                    <tr class="link" id="profile/user/<?= urlencode(base64_encode($user->user_id)) ?>">
                                        <td><?= $user->user_id ?></td>
                                        <td><?= $user->user_name ?></td>
                                        <td><?= $this->users_model->get_category($user->user_category_id)->user_category_name ?></td>
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
            </div>
            <div class="col s12">
                <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                    <li>
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="red-text">Deleted Users</span>
                        </div>
                        <div class="collapsible-body" style=" padding: 20px 10px;">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Access</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->db->get_where('tbl_users', array("deleted" => true))->result() as $user) {
                                    if ($user->user_id != 1) { ?>
                                        <tr class="link" id="profile/user/<?= urlencode(base64_encode($user->user_id)) ?>">
                                            <td><?= $user->user_id ?></td>
                                            <td><?= $user->user_name ?></td>
                                            <td><?= $this->users_model->get_category($user->user_category_id)->user_category_name ?></td>
                                            <td><?= $user->user_email ?></td>
                                            <td><?= $user->user_phone ?></td>
                                            <td><?= $user->user_access ? '<span class="green-text">granted</span>' : '<span class="orange-text">blocked</span>' ?></td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col s12 m4">
        <div class="row">
            <div class="col s12" style="margin-top: 20px;">
                <a class="btn green right modal-trigger" href="#new-user">
                    <i class="mdi-content-add"></i> New user
                </a>
            </div>
            <div class="col s12">
                <ul id="task-card" class="collection with-header">
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s12 center">
                                <h5 class="green-text">
                                    <strong>
                                        <?= count($this->crud_model->get_records('users')) ?>
                                    </strong>
                                </h5>
                                <p class="medium">All Users</p>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s6 green-text center">
                                <h5 class="cyan-text">
                                    <strong>
                                        <?= count($this->crud_model->get_records('users', 'user_access', 1)) ?>
                                    </strong>
                                </h5>
                                <p class="medium">Can access</p>
                            </div>
                            <div class="col s6 center blue-text">
                                <h5 class="orange-text">
                                    <strong>
                                        <?= count($this->crud_model->get_records('users', 'user_access', 0)) ?>
                                    </strong>
                                </h5>
                                <p class="medium">Blocked</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col s12">
                <ul class="collection">
                    <li class="collection-item">
                        <h5 class="medium black-text">Latest Activities</h5>

                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>


<div id="new-user" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Register new user</h5>
            </li>
            <li class="collection-item">
                <form class="col s12" method="post" action="<?= site_url('users/register/true') ?>">
                    <div class="row">
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
                            <input id="email5" type="email" name="email" required>
                            <label for="email">Email</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select name="category">
                                <option value="" disabled selected>Choose user level</option>
                                <?php
                                foreach ($this->crud_model->get_records('user_category', 'deleted', false) as $cat) {
                                    if ($cat->user_category_id != 1)
                                        echo '<option value="' . $cat->user_category_id . '">' . $cat->user_category_name . '</option>';
                                }
                                ?>
                            </select>
                            <label>User Level</label>
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
                                <button class="btn cyan darken-4 waves-effect waves-light right" type="submit">
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