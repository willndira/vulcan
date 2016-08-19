<div class="row">
    <div class="col s12 m8">
        <div class="col s12">
            <h5 class="card-title grey-text text-darken-4">
                <i class="mdi-navigation-chevron-right"></i>
                <?= $page ?>
            </h5>
            <p class="medium green-text">All registered equipment categories</p>
        </div>
        <div class="col s12">
        <ul id="task-card" class="collection with-header">
            <li class="collection-item">
                <table class="display dt responsive-table">
                    <thead>
                    <tr>
                        <th> #</th>
                        <th>Name</th>
                        <th>Reg date</th>
                        <th>Registered by</th>
                        <th>No. of items</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($this->crud_model->get_records('components') as $component) {
                        ?>
                        <tr class="link" id="components/profile/<?= urlencode(base64_encode($component->component_id)) ?>">
                            <td><?= $component->component_id ?></td>
                            <td><?= $component->component_name ?></td>
                            <td><?= $component->component_add_date ?></td>
                            <td><?= $this->users_model->user($component->component_added_by)->user_name ?></td>
                            <td><?= $this->components_model->total_items($component->component_id) ?></td>
                        </tr>
                        <?php
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
                        <span class="red-text">Trashed categories</span>
                    </div>
                    <div class="collapsible-body" style=" padding: 20px 10px;">

                        <table class="display dt responsive-table">
                            <thead>
                            <tr>
                                <th> #</th>
                                <th>Name</th>
                                <th>Reg date</th>
                                <th>Registered by</th>
                                <th>No. of items</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($this->db->get_where('tbl_components', array("deleted" => true))->result() as $component) {
                                ?>
                                <tr class="link" id="components/profile/<?= urlencode(base64_encode($component->component_id)) ?>">
                                    <td><?= $component->component_id ?></td>
                                    <td><?= $component->component_name ?></td>
                                    <td><?= $component->component_add_date ?></td>
                                    <td><?= $this->users_model->user($component->component_added_by)->user_name ?></td>
                                    <td><?= $this->components_model->total_items($component->component_id) ?></td>
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
    <div class="col s12 m4 l4">
        <ul id="task-card" class="collection with-header">
            <li class="collection-header ">
                <h5 class="task-card-title orange-text">Define a Equipment</h5>
            </li>
            <li class="collection-item">
                <form action="<?= site_url('components/define') ?>" method="post">
                    <div class="row">
                        <div class="input-field col s12 type">
                            <input id="component_name" type="text" name="component_name" required/>
                            <label for="type">Equipment Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 input-field">
                            <textarea id="about" name="desc" class="materialize-textarea"></textarea>
                            <label for="about">Equipment Description</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <?php
                            if ($this->users_model->requires_role(array('define_equips'))
                            ) {
                                ?>
                                <button class="btn orange waves-effect waves-light right" type="submit">Define
                                    <i class="mdi-content-send right"></i>
                                </button>
                                <?php
                            } else {
                                ?>
                                <div id="card-alert" class="card red">
                                    <div class="card-content white-text">
                                        <p>DENIED : Sorry. No enough permissions to define components.</p>
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
    </div>
</div>