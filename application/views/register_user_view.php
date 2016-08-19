<div class="row">
    <div class="col s12 m8 l8">
        <div class="card-panel">
            <h4 class="header2">Register System User</h4>

            <div class="row">
                <form class="col s12" method="post" action="<?= base_url() ?>index.php/users/register/true">
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
                        <div class="input-field col s6">
                            <input id="email5" type="email" name="email" required>
                            <label for="email">Email</label>
                        </div>
                        <div class="input-field col s6">
                            <select name="category">
                                <option value="" disabled selected>Choose user level</option>
                                <?php
                                foreach ($this->crud_model->get_records('user_category', 'category_deleted', false) as $cat) {
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
                            <textarea id="message5" class="materialize-textarea" length="120"></textarea>
                            <label for="message">About the user</label>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <button class="btn cyan waves-effect waves-light right" type="submit"
                                        name="action">Register
                                    <i class="mdi-content-send right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col s12 m4 l4">
        <div class="card-panel">
            <h4 class="header2">User Levels</h4>

            <div class="row">
                <div class="col s12 m12 l12">
                    <table class="responsive-table display" cellspacing="0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Category Name</th>
                        </tr>
                        </thead>
                        <?php
                        foreach ($this->crud_model->get_records('user_category', 'category_deleted', false) as $cat) {
                            ?>
                            <td>
                                <?= $cat->user_category_id ?>
                            </td>
                            <td>
                                <?= $cat->user_category_name ?>
                            </td>
                            <?php
                        }
                        ?>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-header">
                        <span class="text-lighten-5">
                            Click on each category for more details and actions
                        </span>
            </div>
        </div>
    </div>
</div>