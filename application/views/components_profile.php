<div id="profile-page" class="section">
    <div class="row">
        <!-- profile-page-sidebar-->
        <div class="col m12 l8">
            <div class="row">
                <div class="col s12">
                    <h5 class="card-title grey-text text-darken-4">
                        <i class="mdi-navigation-chevron-right"></i>
                        <?= $details->component_name ?>
                    </h5>
                    <p class="medium green-text">Equipment category
                        <?php
                        if ($this->users_model->requires_role(array('edit_machine'))) {
                            ?>
                            <button class="btn ajax orange right hide-on-large-only"
                                    value="components/trash/<?= urlencode(base64_encode($details->component_id)) . "/0/" . !$details->deleted ?>">
                                <?php if ($details->deleted) {
                                    echo '<i class="mdi-action-restore"></i> Restore';
                                } else {
                                    echo '<i class="mdi-action-delete"></i> Trash';
                                } ?>
                            </button>
                            <?php
                        }
                        ?>
                    </p>
                </div>
                <div class="col s12">
                    <ul class="collection ">
                        <li class="collection-item">
                            <ul class="tabs tab-profile">
                                <li class="tab col s3">
                                    <a class=" waves-effect waves-light green-text active" href="#stats">
                                        <i class="mdi-image-timelapse"></i> Assets</a>
                                </li>
                                <li class="tab col s3">
                                    <a class="waves-effect waves-light green-text" href="#edit">
                                        <i class="mdi-editor-border-color"></i> Edit</a>
                                </li>
                            </ul>
                        </li>
                        <li class="collection-item" id="stats">
                            <?php if ($this->users_model->requires_role(array('edit_machine'))) { ?>
                            <ul class="tabs tab-profile">
                                <li class="tab col s3">
                                    <a class=" waves-effect waves-light active" href="#list">
                                        <i class="mdi-image-timelapse"></i> Item list</a>
                                </li>
                                <li class="tab col s3">
                                    <a class=" waves-effect waves-light" href="#add-comps">
                                        <i class="mdi-image-timelapse"></i>Add items</a>
                                </li>
                            </ul>
                            <div id="list" class="tab-content">
                                <?php } ?>
                                <table class="responsive-table dt display" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th>Item model</th>
                                        <th>No of units</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Added by</th>
                                        <th>Add date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($this->crud_model->get_records('component_items', 'component_id', $details->component_id) as $component) {
                                        $model = $this->items_model->get_model_details($component->model_id);
                                        ?>
                                        <tr>
                                            <td><?= $model->make_name . ' ' . $model->model_name ?></td>
                                            <td><?= $component->model_qty ?></td>
                                            <td><?= $component->component_description ?></td>
                                            <td><?= $component->component_type ? 'Assembly Asset' : 'Installation Asset' ?></td>
                                            <td><?= $this->users_model->user($component->ci_added_by)->user_name ?></td>
                                            <td><?= $component->ci_date ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <?php
                                if ($this->users_model->requires_role(array('edit_machine'))) {
                                ?>
                            </div>
                            <div id="add-comps" class="tab-content">
                                <div class="row">
                                    <div class="col s12 m12 l12">
                                        <h4 class="header2">Add required items</h4>

                                        <form action="<?= site_url('components/add_item') ?>" method="post">
                                            <input type="hidden" name="component_id"
                                                   value="<?= $details->component_id ?>" required/>

                                            <div class="row">
                                                <div class="col s12 input-field">
                                                    <select name="model_id" id="component" class="browser-default chosen-select"
                                                            data-placeholder="select item category">
                                                        <option value="" selected disabled>--Select item model--</option>
                                                        <?php foreach ($this->crud_model->get_records('item_models') as $type) {
                                                            $model = $this->items_model->get_model_details($type->item_model_id);
                                                            ?>
                                                            <option
                                                                value="<?= $type->item_model_id ?>"><?= $model->it_name . ' - ' . $model->make_name . ' ' . $type->model_name ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                    <label for="component" class="active">Item model</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col s12 m6 l6 input-field">
                                                    <input id="model_qty" type="number" name="model_qty" value="<?= set_value("model_qty") ?>"
                                                           required/>
                                                    <label for="model_qty">No. of items</label>
                                                </div>
                                                <div class="col s12 m6 l6 input-field">
                                                    <select id="type" name="comp_type">
                                                        <option value="" selected disabled>--Stage of usage--</option>
                                                        <option value="1">Assembly Asset</option>
                                                        <option value="0">Installation Asset</option>
                                                    </select>
                                                    <label for="type">Asset Type</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col s12 input-field">
                                                    <textarea id="about" name="description"
                                                              class="materialize-textarea"><?= set_value("description") ?></textarea>
                                                    <label for="about">Purpose</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="input-field col s12">
                                                    <button class="btn orange waves-effect waves-light right"
                                                            type="submit" name="action">
                                                        Add component
                                                        <i class="mdi-content-send right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        </li>
                        <li class="collection-item" id="edit">

                            <h4 class="header2">Change Details</h4>

                            <form method="post" action="<?= site_url('components/update') ?>">
                                <input type="hidden" name="component_id" value="<?= $details->component_id ?>"/>


                                <div class="row">
                                    <div class="input-field col s12">
                                        <input id="email5" type="text" value="<?= $details->component_name ?>"
                                               name="component_name"
                                               required>
                                        <label class="active" for="email">Name</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col s12 input-field">
                                                <textarea id="about" name="desc"
                                                          class="materialize-textarea"><?= $details->component_description ?></textarea>
                                        <label for="about">Guide Description</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <?php
                                        if ($this->users_model->requires_role(array('edit_machine'))) {
                                            ?>
                                            <button class="btn orange waves-effect waves-light right"
                                                    type="submit"
                                                    name="action">Update
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
                            <ul class="tabs tab-profile">
                                <li class="tab col s3">
                                    <a class=" waves-effect waves-light green-text active" href="#assembly">
                                        <i class="mdi-image-timelapse"></i> Assembly steps</a>
                                </li>
                                <li class="tab col s3">
                                    <a class="waves-effect waves-light green-text" href="#installation">
                                        <i class="mdi-editor-border-color"></i> Installation steps</a>
                                </li>
                            </ul>
                        </li>
                        <li class="collection-item tab-content" id="assembly">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Added by</th>
                                    <th>Required items</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($this->db->get_where('tbl_setup_guide',
                                    array("component_id" => $details->component_id,
                                        "step_category" => 1))->result() as $guide) { ?>
                                    <tr>
                                        <td><?= $guide->step_description ?></td>
                                        <td><?= $guide->is_test ? 'Testing' : 'Setting up' ?></td>
                                        <td><?= $this->users_model->user($guide->guide_added_by)->user_name ?></td>
                                        <td>
                                            <?php
                                            foreach ($this->crud_model->get_records('setup_items', 'step_id', $guide->ag_id) as $component) {
                                                $model = $this->items_model->get_model_details($component->model_id); ?>
                                                <?= $model->make_name . ' ' . $model->model_name ?>,
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                        <li class="collection-item tab-content" id="installation">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Added by</th>
                                    <th>Required items</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($this->db->get_where('tbl_setup_guide',
                                    array("component_id" => $details->component_id,
                                        "step_category" => 2))->result() as $guide) { ?>
                                    <tr>
                                        <td><?= $guide->step_description ?></td>
                                        <td><?= $guide->is_test ? 'Testing' : 'Setting up' ?></td>
                                        <td><?= $this->users_model->user($guide->guide_added_by)->user_name ?></td>
                                        <td>
                                            <?php
                                            foreach ($this->crud_model->get_records('setup_items', 'step_id', $guide->ag_id) as $component) {
                                                $model = $this->items_model->get_model_details($component->model_id); ?>
                                                <?= $model->make_name . ' ' . $model->model_name ?>,
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                    <ul class="collection with-header">
                        <li class="collection-header">
                            <h5 class="task-card-title orange-text"> Registered Equipment</h5>
                        </li>
                        <li class="collection-item">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>No</th>
                                    <th>Location</th>
                                    <th>Available</th>
                                    <th>Condition</th>
                                    <th>Stage</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($this->crud_model->get_records("equipment", "component_id", $details->component_id) as $equipment) { ?>
                                    <tr class="link"
                                        id="equipment/profile/<?= urlencode(base64_encode($equipment->equipment_id)) ?>">
                                        <td><?= $equipment->equipment_id ?></td>
                                        <td><?= $equipment->equipment_no ?></td>
                                        <td><?= $this->equipment_model->current_location($equipment->equipment_id, 2) ?></td>
                                        <td><?= $equipment->equipment_availability ? "YES" : "NO" ?></td>
                                        <td><?= $equipment->equipment_condition ? "Operational" : "Faulty" ?></td>
                                        <td><?= $this->equipment_model->stage($equipment->equipment_stage) ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col m12 l4">
            <div class="row">
                <div class="col s12 hide-on-med-and-down">
                    <?php
                    if ($this->users_model->requires_role(array('edit_machine'))) {
                        ?>
                        <button class="btn ajax orange right"
                                value="components/trash/<?= urlencode(base64_encode($details->component_id)) . "/0/" . !$details->deleted ?>">
                            <?php if ($details->deleted) {
                                echo '<i class="mdi-action-restore"></i> Restore';
                            } else {
                                echo '<i class="mdi-action-delete"></i> Trash';
                            } ?>
                        </button>
                        <?php
                    }
                    ?>
                </div>
                <div class="col s12">
                    <ul id="task-card" class="collection with-header">
                        <li class="collection-header">
                            <h5 class="task-card-title orange-text">Details</h5>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i> Name
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->component_name ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i>
                                    Defined By:
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $this->users_model->user($details->component_added_by)->user_name ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i>
                                    Defined On:
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->component_add_date ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-communication-location-on"></i> No of Items
                                </div>
                                <div
                                    class="col s7 grey-text text-darken-4 right-align">
                                    <?= $this->components_model->total_items($details->component_id) ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s12 grey-text text-darken-4 center">
                                    <?= $details->component_description ?>
                                </div>
                            </div>
                        </li>
                    </ul>

                </div>
                <div class="col s12">
                    <ul id="task-card" class="collection with-header">
                        <li class="collection-header">
                            <h5 class="task-card-title orange-text">Create Instruction</h5>
                        </li>
                        <li class="collection-item">
                            <form action="<?= site_url('components/create_instruction') ?>" method="post">
                                <input type="hidden" name="component_id"
                                       value="<?= $details->component_id ?>" required/>

                                <div class="row">
                                    <div class="col s12 input-field">
                                        <select name="step_category" id="step_category">
                                            <option value="" selected disabled>---Select Category---</option>
                                            <option value="1">Assembly</option>
                                            <option value="2">Installation</option>
                                        </select>
                                        <label for="step_type">Instruction Category</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12 input-field">
                                        <select name="is_test" id="is_test">
                                            <option value="" selected disabled>---Select type---</option>
                                            <option value="0">Setup</option>
                                            <option value="1">Testing</option>
                                        </select>
                                        <label for="is_test">Instruction Type</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12 input-field">
                                        <select id="assets" name="assets[]" multiple class="browser-default chosen-select">
                                            <option value="" disabled>---Select Asset---</option>
                                            <?php
                                            foreach ($this->crud_model->get_records('component_items', 'component_id', $details->component_id) as $component) {
                                                $model = $this->items_model->get_model_details($component->model_id);
                                                ?>
                                                <option
                                                    value="<?= $component->model_id ?>"><?= $model->make_name . ' ' . $model->model_name ?></option>
                                            <?php } ?>
                                        </select>
                                        <label for="assets" class="active">Required Asset</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12 input-field">
                                        <select name="result_type" id="result_type">
                                            <option value="" selected disabled>---Select result type---</option>
                                            <option value="1">Check box</option>
                                            <option value="2">Text</option>
                                            <option value="3">Number</option>
                                        </select>
                                        <label for="result_type">Expected result type</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12 input-field">
                                        <textarea id="about" name="description" class="materialize-textarea"></textarea>
                                        <label for="about">Instruction</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <button class="btn orange waves-effect waves-light right" type="submit">
                                            Create
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
    </div>
</div>