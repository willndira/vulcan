<?php
$pm = $details->project_manager;
$is_pm = false;
$equipment_available = true;
if ($this->users_model->user()->user_id == $pm)
    $is_pm = true;
?>
    <div id="profile-page" class="section">
        <div class="card blue-grey white-text">
            <div class="card-content">
                <div class="row">
                    <div class="col s12">
                        <h4 class="card-title  text-darken-4">
                            <?= ucfirst($details->project_name) ?>
                            <span class="chip"><?= $this->projects_model->stage($details->project_stage) ?></span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m4">
                <ul id="task-card" class="collection  with-header z-depth-1">
                    <li class="collection-header cyan darken-2">
                        <h5 class="task-card-title">Details</h5>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i> Name
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $details->project_name ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i>
                                Project Stage:
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $this->projects_model->stage($details->project_stage) ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i>Client Name
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $details->project_client ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i>
                                Proposed By:
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $this->users_model->user($details->project_proposer)->user_name ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i>
                                Project Manager:
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $details->project_manager ? $this->users_model->user($details->project_manager)->user_name : "<span class='orange-text'>Not specified :-( </span>" ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i>
                                Defined On:
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $details->project_create_date ?>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="card cyan darken-2">
                    <div class="card-content center white-text">
                        <span class="card-title">About </span>

                        <p>
                            <?= $details->project_description ?>
                        </p>
                    </div>
                </div>
            </div>
            <!-- profile-page-sidebar-->
            <div class="col s12 m8">
                <div class="row">
                    <div class="col s12">
                        <ul id="task-card" class="collection with-header z-depth-1">
                            <li class="collection-header cyan darken-2  white-text">
                                <h6>Equipment</h6>
                            </li>
                            <li>
                                <ul class="tabs tab-profile cyan lighten-1">
                                    <li class="tab col s6">
                                        <a class=" waves-effect waves-light white-text active" href="#required">
                                            <i class="mdi-image-timelapse"></i> Required</a>
                                    </li>
                                    <li class="tab col s6">
                                        <a class=" waves-effect waves-light  white-text" href="#assigned">
                                            <i class="mdi-image-timelapse"></i>Assigned</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="collection-item tab-content" id="required">
                                <div class="row">
                                    <div class="col s12">
                                        <?php if ($this->users_model->requires_role(array('update_proj')) && $details->project_stage <= 2) { ?>
                                        <ul class="tabs tab-profile">
                                            <li class="tab col s3">
                                                <a class=" waves-effect waves-light active" href="#list">
                                                    <i class="mdi-image-timelapse"></i> Listed Machines</a>
                                            </li>
                                            <li class="tab col s3">
                                                <a class=" waves-effect waves-light" href="#add-comps">
                                                    <i class="mdi-image-timelapse"></i>Add Machines</a>
                                            </li>
                                        </ul>
                                        <div id="list" class="tab-content col s12 white-bg lighten-4">
                                            <?php } ?>
                                            <table class="responsive-table dt display" cellspacing="0">
                                                <thead>
                                                <tr>
                                                    <th>ADDED ON</th>
                                                    <th>MACHINE NAME</th>
                                                    <th>NO. OF UNITS</th>
                                                    <th>DESCRIPTION</th>
                                                    <th>ADDED BY</th>
                                                    <?php if ($is_pm && $details->project_stage > 2) { ?>
                                                        <th></th>
                                                    <?php } ?>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($this->crud_model->get_records('project_components', 'project_id', $details->project_id) as $component) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $component->pc_added_date ?></td>
                                                        <td class="link"
                                                            id="components/profile/<?= urlencode(base64_encode($component->component_id)) ?>">
                                                            <?= $this->crud_model->get_record('components', 'component_id', $component->component_id)->component_name ?>
                                                        </td>
                                                        <td><?= $component->component_qty ?></td>
                                                        <td><?= $component->pc_description ?></td>
                                                        <td><?= $this->users_model->user($component->pc_added_by)->user_name ?></td>

                                                        <?php if ($is_pm && $details->project_stage > 2) { ?>
                                                            <td>
                                                                <?php
                                                                $request = $this->equipment_model->requests($component->component_id, $details->project_id);
                                                                $assignment = $this->stores_model->equipment_request_assignment($request->equipment_request_id);
                                                                if (count($request) > 0 && $request->request_qty == $component->component_qty) {
                                                                    if (count($assignment) == $request->request_qty) {
                                                                        echo "Fully Assigned";
                                                                    } else {
                                                                        $equipment_available = false;
                                                                        echo "Assigned " . count($assignment);
                                                                    }
                                                                } else {
                                                                    ?>
                                                                    <a href='<?= site_url("equipment/request/" .
                                                                        urlencode(json_encode(array(
                                                                                "project_id" => $details->project_id,
                                                                                "component_id" => $component->component_id,
                                                                                "request_qty" => $component->component_qty,
                                                                                "request_level" => 2)
                                                                        ))) ?>'
                                                                       class="chip green lighten-2">Request</a>
                                                                <?php } ?>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                            <?php if ($this->users_model->requires_role(array('update_proj')) && $details->project_stage <= 2) { ?>
                                        </div>
                                        <div id="add-comps" class="tab-content col s12 white-bg lighten-4">
                                            <div class="card-content col s12 m12 l12">
                                                <div class="row">
                                                    <h4 class="header2">Add Machine Components</h4>

                                                    <form action="<?= site_url('projects/add_machine') ?>" method="post">
                                                        <input type="hidden" name="project_id"
                                                               value="<?= $details->project_id ?>" required/>

                                                        <div class="row">
                                                            <div class="col s12 input-field">
                                                                <select name="component_id" id="component">
                                                                    <option>Select Machine</option>
                                                                    <?php
                                                                    foreach ($this->crud_model->get_records('components') as $component) {
                                                                        ?>
                                                                        <option
                                                                            value="<?= $component->component_id ?>"><?= $component->component_name ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                                <label for="component">Component</label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col s12 input-field">
                                                                <input id="model_qty" type="number" name="component_qty"
                                                                       required/>
                                                                <label for="model_qty">Units of Machines</label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col s12 input-field">
                                                                <textarea id="about" name="description" class="materialize-textarea"></textarea>
                                                                <label for="about">Purpose</label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="input-field col s12">
                                                                <button
                                                                    class="btn orange darken-4 waves-effect waves-light right"
                                                                    type="submit">
                                                                    Add Machine
                                                                    <i class="mdi-content-send right"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    </div>
                                </div>
                            </li>
                            <li class="collection-item tab-content" id="assigned">
                                <table class="responsive-table dt display" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th>Assigned date</th>
                                        <th>Equipment Type</th>
                                        <th>Equipment No</th>
                                        <th>Assigning Staff</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($this->projects_model->equipment($details->project_id) as $eq) {
                                        ?>
                                        <tr>
                                            <td><?= $eq->assignment_date ?></td>
                                            <td class="link"
                                                id="components/profile/<?= urlencode(base64_encode($eq->component_id)) ?>">
                                                <?= $this->crud_model->get_record('components', 'component_id', $eq->component_id)->component_name ?>
                                            </td>
                                            <td class="link"
                                                id="equipment/profile/<?= urlencode(base64_encode($eq->equipment_id)) ?>">
                                                <?= $eq->equipment_no ?>
                                            </td>
                                            <td><?= $this->users_model->user($eq->assignment_staff)->user_name ?></td>
                                            <td><?= $this->equipment_model->stage($eq->equipment_stage) ?></td>

                                            <?php if ($is_pm && $details->project_stage > 2) { ?>
                                                <td>
                                                    <?php
                                                    if ($eq->assignment_confirmation) {
                                                        echo "Received";
                                                    } elseif ($eq->equipment_stage < 2) {
                                                        echo "Under assembly";
                                                    } else {
                                                        ?>
                                                        <button
                                                            value='equipment/receive/<?= urlencode(base64_encode($eq->request_assignment_id)) ?>'
                                                            class="chip ajax green lighten-2">Collect
                                                        </button>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </li>
                        </ul>
                    </div>
                    <div class="col s12">
                        <ul id="task-card" class="collection with-header z-depth-1">
                            <li class="collection-header cyan darken-2 white-text">
                                <h5 class="task-card-title"> Installation Process</h5>
                            </li>
                            <?php if ($is_assembled && $specs->equipment_stage > 1) { ?>
                                <li>
                                    <ul class="tabs tab-profile cyan lighten-1">
                                        <li class="tab col s6">
                                            <a class=" waves-effect waves-light white-text active" href="#assembly">
                                                <i class="mdi-image-timelapse"></i> Installation</a>
                                        </li>
                                        <li class="tab col s6">
                                            <a class=" waves-effect waves-light  white-text" href="#testing">
                                                <i class="mdi-image-timelapse"></i> Testing</a>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="collection-item" id="assembly">
                                <ul class="collection">
                                    <?php foreach ($assembly_steps as $guide) { ?>
                                        <li class="collection-item">
                                            <div class="row">
                                                <div class="col s11">
                                                    <?php
                                                    $requirements = $this->projects_model->step_requirements($details->equipment_id, $guide->ag_id);
                                                    $performed = $this->equipment_model->is_step_performed($guide->ag_id, $details->equipment_id);
                                                    echo $guide->step_description;
                                                    if (!$requirements) { ?>
                                                        <span class="right orange-text">Required assets not available</span>
                                                    <?php } elseif (!$is_pm && !($teamer)) { ?>
                                                        <span class="right orange-text">Not a team member</span>
                                                        <?php
                                                    }
                                                    if (count($performed) > 0 && !$performed->deleted) { ?>
                                                        <span
                                                            class="right grey-text">Performed by: <?= $this->users_model->user($performed->assembly_team_id)->user_name ?>
                                                            on: <?= $performed->setup_time ?></span>
                                                    <?php } ?>
                                                </div>
                                                <div class="col s1">
                                                    <input
                                                        <?= (!$requirements || (!$is_pm && !$teamer)) ? "disabled" : "" ?>
                                                        <?= count($performed) > 0 && !$performed->deleted ? "checked" : "" ?>
                                                        value="projects/perform_procedure/<?= urlencode(json_encode(array("step_id" => $guide->ag_id,
                                                            'is_pm' => $is_pm, 'setup_result' => true, "equipment_id" => $details->equipment_id))) ?>"
                                                        class="ajax right-align" id="assemble-step_<?= $guide->ag_id ?>"
                                                        type="checkbox">
                                                    <label for="assemble-step_<?= $guide->ag_id ?>"> </label>
                                                </div>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php if ($is_assembled && $specs->equipment_stage > 1) { ?>
                                <li class="collection-item" id="testing">
                                    <ul class="collection">
                                        <?php foreach ($test_steps as $guide) { ?>
                                            <li class="collection-item">
                                                <div class="row">
                                                    <div class="col s11">
                                                        <?php
                                                        $requirements = $this->projects_model->step_requirements($details->equipment_id, $guide->ag_id);
                                                        $performed = $this->equipment_model->is_step_performed($guide->ag_id, $details->equipment_id);
                                                        echo $guide->step_description;
                                                        if (!$requirements) { ?>
                                                            <span class="right orange-text">Required assets not available</span>
                                                        <?php } elseif (!$is_pm && !($teamer)) { ?>
                                                            <span class="right orange-text">Not a team member</span>
                                                            <?php
                                                        }
                                                        if (count($performed) > 0 && !$performed->deleted) { ?>
                                                            <span
                                                                class="right grey-text">Performed by: <?= $this->users_model->user($performed->assembly_team_id)->user_name ?>
                                                                on: <?= $performed->setup_time ?></span>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="col s1">
                                                        <input
                                                            <?= (!$requirements || (!$is_pm && !$teamer)) ? "disabled" : "" ?>
                                                            <?= count($performed) > 0 && !$performed->deleted ? "checked" : "" ?>
                                                            value="projects/perform_procedure/<?= urlencode(json_encode(array("step_id" =>
                                                                $guide->ag_id, 'is_pm' => $is_pm, 'setup_result' => true, "equipment_id" =>
                                                                $details->equipment_id))) ?>"
                                                            class="ajax right-align" id="assemble-step_<?= $guide->ag_id ?>"
                                                            type="checkbox">
                                                        <label for="assemble-step_<?= $guide->ag_id ?>"> </label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php if ($equipment_available && $is_pm && $details->project_stage < 4) { ?>
    <div class="fixed-action-btn" style="top: 70px !important;">
        <button <?= $details->project_stage > 3 ? "disabled" : "" ?>
            class="ajax btn white-text orange lighten-2 z-depth-2" value="projects/assembly_complete/<?= $details->project_id ?>/true">
            Complete Assembly
        </button>
    </div>
<?php } ?>