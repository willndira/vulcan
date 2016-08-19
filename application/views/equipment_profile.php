<?php $component = $this->crud_model->get_record("components", "component_id", $details->component_id); ?>
<div class="section">
    <div class="row">
        <div class="col s12 m8">
            <div class="row">
                <div class="col s12">
                    <h5 class="card-title grey-text text-darken-4">
                        <i class="mdi-navigation-chevron-right"></i>
                        Equipment No: <?= $details->equipment_no ?>
                    </h5>
                    <p class="medium green-text"><?= $component->component_name ?></p>
                </div>
                <div class="col s12">
                    <ul class="collection with-header">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s6">
                                    <h5 class="task-card-title orange-text">
                                        Components
                                    </h5>
                                </div>
                                <div class="col s6">
                                    <ul class="tabs tab-profile">
                                        <li class="tab col s6">
                                            <a class=" waves-effect waves-light  green-text active" href="#i-request">
                                                <i class="mdi-image-timelapse"></i>Assigned</a>
                                        </li>
                                        <li class="tab col s6">
                                            <a class=" waves-effect waves-light green-text " href="#i-required">
                                                <i class="mdi-image-timelapse"></i> Required</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item tab-content" id="i-request">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Asset No</th>
                                    <th>Model</th>
                                    <th>Assigned By</th>
                                    <th>Collection</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $assigned = $this->asset_model->all_assigned($details->equipment_id, 2, 1);
                                foreach ($assigned as $item) {
                                    $item_details = $this->crud_model->get_record("items", "item_id", $item->asset_id)
                                    ?>
                                    <tr>
                                        <td><?= $item->date ?></td>
                                        <td class="link"
                                            id="items/profile/<?= urlencode(base64_encode($item_details->item_id)) ?>"><?= $item_details->item_code ?></td>
                                        <td> <?php
                                            $model = $this->items_model->get_model_details($item_details->model_id);
                                            echo $model->make_name . ' ' . $model->model_name;
                                            ?></td>
                                        <td class="link"
                                            id="profile/user/<?= urlencode(base64_encode($item->handling_staff)) ?>"><?= $this->users_model->user($item->handling_staff)->user_name ?></td>
                                        <td>
                                            <?php
                                            if (!$item->confirmation) { ?>
                                                <span class="orange-text">Pending collection</span>
                                            <?php } else { ?>
                                                <span class="green-text">Collected</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                        <li class="collection-item tab-content" id="i-required">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Qty</th>
                                    <th>Asset</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $required = 0;
                                foreach ($this->components_model->assets($details->component_id) as $component) {
                                    $requested = $this->asset_model->requested($component->model_id, 1, 2, $details->equipment_id);
                                    $this_allocated = $this->asset_model->model_available(1, $component->model_id, $details->equipment_id, 2);
                                    ?>
                                    <tr>
                                        <td><?= $component->model_qty ?></td>
                                        <td>
                                            <?php
                                            $required += $component->model_qty;
                                            $model = $this->items_model->get_model_details($component->model_id);
                                            echo $model->make_name . ' ' . $model->model_name;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($requested && $requested->request_qty == $component->model_qty) {
                                                echo "Allocated " . count($this_allocated);
                                            } else {
                                                echo "Not Requested";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
                <div class="col s12">
                    <ul class="collection with-header">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s6">
                                    <h5 class="task-card-title orange-text">
                                        Logs and details
                                    </h5>
                                </div>
                                <div class="col s6">
                                    <ul class="tabs tab-profile">
                                        <li class="tab col s3">
                                            <a class=" waves-effect waves-light green-text active" href="#stats">
                                                <i class="mdi-image-timelapse"></i> Timeline</a>
                                        </li>
                                        <li class="tab col s3">
                                            <a class="waves-effect waves-light  green-text" href="#edit">
                                                <i class="mdi-editor-border-color"></i> Edit</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item" id="stats">
                            <table id="data-table-simple" class="responsive-table display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Staff</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($this->crud_model->get_records('equipment_timeline', 'equipment_id', $details->equipment_id) as $activity) {
                                    ?>
                                    <tr>
                                        <td><?= $activity->time ?></td>
                                        <td>
                                            <?= null != $activity->user_id ? $this->users_model->user($activity->user_id)->user_name : 'SYSTEM' ?>
                                        </td>
                                        <td><?= $activity->activity ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                        <li class="collection-item" id="edit">

                            <h4 class="header2">Change Details</h4>
                            <hr/>
                            <form method="post" action="<?= site_url('equipment/update') ?>">
                                <input type="hidden" name="item_id" value="<?= $details->equipment_id ?>"/>

                                <div class="row">
                                    <div class="input-field col s12">
                                        <select name="component_id" id="component" data-placeholder="Select equipment category"
                                                class="browser-default chosen-select">
                                            <option value="" disabled>Select Machine</option>
                                            <?php
                                            foreach ($this->crud_model->get_records('components') as $component) {
                                                ?>
                                                <option <?= $details->component_id == $component->component_id ? "selected" : "" ?>
                                                    value="<?= $component->component_id ?>"><?= $component->component_name ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <label for="component" class="active">Equipment Type</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <select id="equipment_condition" name="equipment_condition" data-placeholder="Select equipment condition"
                                                class="browser-default chosen-select">
                                            <option value="" disabled>--Select equipment condition--</option>
                                            <option <?= $details->equipment_condition == 1 ? "selected" : "" ?>
                                                value="1">Operational
                                            </option>
                                            <option <?= $details->equipment_condition == 0 ? "selected" : "" ?>
                                                value="0">Faulty
                                            </option>
                                        </select>
                                        <label for="equipment_condition" class="active">Condition</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <select id="equipment_availability" name="equipment_availability"
                                                data-placeholder="Select equipment availability" class="browser-default chosen-select">
                                            <option value="" disabled>--Select equipment availability--
                                            </option>
                                            <option <?= $details->equipment_availability == 1 ? "selected" : "" ?>
                                                value="1">Available
                                            </option>
                                            <option <?= $details->equipment_availability == 0 ? "selected" : "" ?>
                                                value="0">In Use
                                            </option>
                                        </select>
                                        <label for="equipment_availability" class="active">Availability</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                    <textarea id="description" name="equipment_comment"
                                              class="materialize-textarea"><?= $details->equipment_comment ?></textarea>
                                        <label for="description">Comments about equipment</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <?php
                                        if ($this->users_model->requires_role(array('edit_machine'))) {
                                            ?>
                                            <button class="btn cyan waves-effect waves-light right"
                                                    type="submit"
                                                    name="action">Update Details
                                                <i class="mdi-content-send right"></i>
                                            </button>
                                            <?php
                                        } else {
                                            ?>
                                            <div id="card-alert" class="card red">
                                                <div class="card-content white-text">
                                                    <p>DENIED : Sorry. No enough permissions to equipment item
                                                        details.</p>
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
        </div>
        <div class="col s12 m4">
            <div class="row">
                <div class="col s12" style="margin-top: 20px;">
                    <button class="btn ajax orange right"
                            value="components/trash/<?= urlencode(base64_encode($details->equipment_id)) . "/1/" . !$details->deleted ?>">
                        <?php if ($details->deleted) {
                            echo '<i class="mdi-action-restore"></i> Restore';
                        } else {
                            echo '<i class="mdi-action-delete"></i> Trash';
                        } ?>
                    </button>
                    <?php if ($this->users_model->requires_role(array('start_assembly'))) {
                        $assembly = $this->crud_model->get_record("equipment_assembly", "equipment_id", $details->equipment_id);
                        ?>
                        <a class="btn green right modal-trigger"
                           href="<?= $details->equipment_stage == 0 ? '#new-start' : site_url('assembly/process/' . urlencode(base64_encode($assembly->equipment_assembly_id))) ?>">
                            <i class="mdi-image-remove-red-eye"></i> Assembly
                        </a>
                    <?php } ?>
                </div>
                <div class="col s12">
                    <ul id="task-card" class="collection with-header">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s6 center">
                                    <h5 class="green-text">
                                        <strong>
                                            <?= $required ?>
                                        </strong>
                                    </h5>
                                    <p class="medium">Required</p>
                                </div>
                                <div class="col s6 center">
                                    <h5 class="green-text">
                                        <strong>
                                            <?= count($assigned) ?>
                                        </strong>
                                    </h5>
                                    <p class="medium">Assigned</p>
                                </div>
                                <div class="col s12 center">
                                    <h6>Components</h6>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s12 center">
                                    <h5 class="orange-text">
                                        <strong>
                                            <?= $this->equipment_model->stage($details->equipment_stage) ?>
                                        </strong>
                                    </h5>
                                    <p class="medium">Current stage</p>
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
                                    <i class="mdi-action-wallet-travel"></i>
                                    Type
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $component->component_name ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1">
                                    <i class="mdi-action-verified-user"></i>
                                    Equipment No.
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->equipment_no ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1">
                                    <i class="mdi-communication-location-on"></i>
                                    Condition:
                                </div>
                                <div
                                    class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->equipment_condition ? "Operational" : "Faulty" ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1">
                                    <i class="mdi-action-dashboard"></i>
                                    Available
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->equipment_availability ? "Available" : "Unavailable" ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1">
                                    <i class="mdi-editor-mode-edit"></i>
                                    Registration Date
                                </div>
                                <div
                                    class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->reg_date ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1">
                                    <i class="mdi-editor-mode-edit"></i>
                                    Registered by:
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->registering_user ?>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col s12">
                    <div class="card" style="background: transparent !important; border-radius: 5px; border: 1px solid #e0e0e0">
                        <div class="card-content center">
                            <p><?= $details->equipment_comment ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="new-start" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Start assembly</h5>
            </li>
            <li class="collection-item">
                <form class="col s12" method="post" action="<?= site_url('assembly/start') ?>">
                    <input type="hidden" name="equipment_id" value="<?= $details->equipment_id ?>"/>
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="hidden" name="stage" value="1" required/>
                            <select name="assembly_manager" id="assembly_manager" data-placeholder="Assembly manager"
                                    class="browser-default chosen-select">
                                <option value="" disabled>--Select Assembly Manager--</option>
                                <?php foreach ($this->users_model->has_powers('man_assembly') as $pm) { ?>
                                    <option value="<?= $pm->user_id ?>"><?= $pm->user_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="assembly_manager" class="active">Assembly Manager</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select name="assembly_priority" id="assembly_priority">
                                <option value="" disabled selected>--Select assembly priority--</option>
                                <option value="1">Very low priority</option>
                                <option value="2">Low priority</option>
                                <option value="3">Normal priority</option>
                                <option value="4">High priority</option>
                                <option value="5">Very high priority</option>

                            </select>
                            <label for="assembly_priority">Priority</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                                    <textarea id="description" name="equipment_comment"
                                              class="materialize-textarea"><?= set_value("equipment_comment") ?></textarea>
                            <label for="description">Brief comments for assembly team</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn orange waves-effect waves-light right" type="submit">Start
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>