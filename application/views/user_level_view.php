<div class="row">
    <div class="col m12 l8">
        <div class="row">
            <div class="col s12">
                <h5 class="card-title grey-text">
                    <i class="mdi-navigation-chevron-right"></i>
                    <?= $page ?>
                </h5>
                <p class="medium">Available user categories
                    <button class="btn-flat btn orange hide-on-large-only right  modal-trigger" href="#new-category">New category</button>
                </p>
            </div>
            <div class="col s12">
                <ul class="collection with-header">
                    <li class="collection-item">
                        <table class="responsive-table dt display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th># of Staff</th>
                                <th>Permissions</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($this->crud_model->get_records('user_category') as $cat) {
                                if ($cat->user_category_id != 1) {
                                    ?>
                                    <tr class="link"
                                        id="users/level_profile/<?= urlencode(base64_encode($cat->user_category_id)) ?>">
                                        <td><?= $cat->user_category_id ?></td>
                                        <td><?= $cat->user_category_name ?></td>
                                        <td><?= count($this->crud_model->get_records('users', 'user_category_id', $cat->user_category_id)) ?></td>
                                        <td><?= count($this->crud_model->get_records('user_roles', 'category_id', $cat->user_category_id)) ?></td>
                                        <td><?= $cat->user_category_description ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </li>
                </ul>
            </div>
            <div class="col s12">
                <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                    <li>
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="red-text">Deleted categories</span>
                        </div>
                        <div class="collapsible-body" style=" padding: 20px 10px;">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th># of Staff</th>
                                    <th>Permissions</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($this->db->get_where('tbl_user_category', array("deleted" => true))->result() as $cat) {
                                    if ($cat->user_category_id != 1) {
                                        ?>
                                        <tr class="link"
                                            id="users/level_profile/<?= urlencode(base64_encode($cat->user_category_id)) ?>">
                                            <td><?= $cat->user_category_id ?></td>
                                            <td><?= $cat->user_category_name ?></td>
                                            <td><?= $cat->user_category_name ?></td>
                                            <td><?= count($this->crud_model->get_records('users', 'user_category_id', $cat->user_category_id)) ?></td>
                                            <td><?= count($this->crud_model->get_records('user_roles', 'category_id', $cat->user_category_id)) ?></td>
                                            <td><?= $cat->user_category_description ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col s12">
                <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                    <li>
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="orange-text">System Roles</span>
                        </div>
                        <div class="collapsible-body" style=" padding: 20px 10px;">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Role code</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($this->crud_model->get_records("roles") as $role) {
                                    ?>
                                    <tr>
                                        <td><?= $role->role_id ?></td>
                                        <td><?= $role->role_code ?></td>
                                        <td><?= $role->role_name ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col l4 hide-on-med-and-down">
        <ul class="collection with-header">
            <li class="collection-header">
                <h5 class="orange-text">Create <?= $page ?></h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('users/create_level') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="Privileges" name="permissions[]" multiple data-placeholder="Select privileges"
                                    class="browser-default chosen-select">
                                <option value="" disabled>Choose Privileges</option>
                                <?php
                                foreach ($this->crud_model->get_records('roles') as $role) { ?>
                                    <option value="<?= $role->role_code ?>"><?= $role->role_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="Privileges" class="active">Privileges</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="first_name" name="level_name" type="text" required>
                            <label for="first_name">Level Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="about" name="about" class="materialize-textarea" length="60"></textarea>
                            <label class="active" for="about">Level Description</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn orange waves-effect waves-light right" type="submit">Register
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>


<div id="new-category" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Create new staff category</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('users/create_level') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="Privileges" name="permissions[]" multiple data-placeholder="Select privileges"
                                    class="browser-default chosen-select">
                                <option value="" disabled>Choose Privileges</option>
                                <?php
                                foreach ($this->crud_model->get_records('roles') as $role) { ?>
                                    <option value="<?= $role->role_code ?>"><?= $role->role_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="Privileges" class="active">Privileges</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="first_name" name="level_name" type="text" required>
                            <label for="first_name">Level Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="about" name="about" class="materialize-textarea" length="60"></textarea>
                            <label class="active" for="about">Level Description</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn orange waves-effect waves-light right" type="submit">Register
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>