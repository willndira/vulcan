<?php
$pm = $details->project_manager;
$equipment_available = true;
$is_pm = $this->users_model->user()->user_id == $pm;
$installed = true;
?>
<div class="section">
    <div class="row">
        <!-- profile-page-sidebar-->
        <div class="col s12 l8">
            <div class="row">
                <div class="col s12">
                    <h5 class="task-card-title grey-text">
                        <i class="mdi-navigation-chevron-right"></i>
                        <?= ucfirst($details->project_name) ?>
                    </h5>
                    <h6 class="medium green-text">
                        Project details
                    </h6>
                </div>
                <div class="col s12">
                    <h5 class="card-title text-darken-4 orange-text">
                        <i class="mdi-navigation-chevron-right"></i>
                        Equipment
                    </h5>
                    <ul class="collection with-header">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s12 m6 offset-m6">
                                    <ul class="tabs tab-profile">
                                        <li class="tab col s6">
                                            <a class=" waves-effect waves-light  green-text active" href="#assigned">
                                                <i class="mdi-image-timelapse"></i>Assigned</a>
                                        </li>
                                        <li class="tab col s6">
                                            <a class=" waves-effect waves-light green-text" href="#required">
                                                <i class="mdi-image-timelapse"></i> Required</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item tab-content" id="required">
                            <ul>
                                <li>
                                    <?php if ($this->users_model->requires_role(array('update_proj')) && $details->project_stage <= 2 && $details->project_proposer == $this->users_model->user()->user_id) { ?>
                                    <div class="row">
                                        <div class="col s12 m6">
                                        </div>
                                        <div class="col s12 m6">
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
                                        </div>
                                    </div>
                                </li>
                                <li class="collection-item" id="list">
                                    <?php } ?>
                                    <table class="dt display" cellspacing="0">
                                        <thead>
                                        <tr>
                                            <th>Added on</th>
                                            <th>Equipment</th>
                                            <th>No. of units</th>
                                            <th>Description</th>
                                            <th>Added by</th>
                                            <?php if ($details->project_stage > 2) { ?>
                                                <th></th>
                                            <?php } ?>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $required = 0;
                                        foreach ($this->crud_model->get_records('project_components', 'project_id', $details->project_id) as $component) {
                                            $request = $this->asset_model->requested($component->component_id, 2, 3, $details->project_id);
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

                                                <?php if ($details->project_stage > 2) { ?>
                                                    <td>
                                                        <?php
                                                        if (count($request) > 0 && $request->request_qty == $component->component_qty) {
                                                            $assignment = $this->asset_model->model_available(2, $component->component_id, $details->project_id, 3);
                                                            if (count($assignment) == $request->request_qty) {
                                                                echo "Fully Assigned";
                                                            } else {
                                                                $equipment_available = false;
                                                                echo "<strong>Requested</strong> (" . count($assignment) . " assigned)";
                                                            }
                                                        } else {
                                                            $equipment_available = false;
                                                            if ($is_pm) { ?>
                                                                <button class="ajax orange btn"
                                                                        value="assets/request/<?= urlencode(base64_encode(json_encode(
                                                                            array(
                                                                                "model_id" => $component->component_id,
                                                                                "request_asset_type" => 2,
                                                                                "request_category_id" => $details->project_id,
                                                                                "request_qty" => $component->component_qty,
                                                                                "purpose" => 'assembly requirement for <b>project : ' . $details->project_name . '</b>',
                                                                                "request_level" => 2,
                                                                                "request_category" => 3)
                                                                        ))) ?>"> Request

                                                                </button>
                                                            <?php } else {
                                                                echo "Not requested";
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                            $required += $component->component_qty;
                                        } ?>
                                        </tbody>
                                    </table>
                                    <?php if ($this->users_model->requires_role(array('update_proj')) && $details->project_stage <= 2 && $details->project_proposer == $this->users_model->user()->user_id) { ?>
                                </li>
                                <li id="add-comps" class="collection-item">
                                    <h4 class="header2">Add equipment</h4>
                                    <form action="<?= site_url('projects/add_machine') ?>" method="post">
                                        <input type="hidden" name="project_id"
                                               value="<?= $details->project_id ?>" required/>

                                        <div class="row">
                                            <div class="col s12 input-field">
                                                <select name="component_id" id="component" data-placeholder="Select component"
                                                        class="browser-default chosen-select">
                                                    <option value="" disabled>--Select equipment--</option>
                                                    <?php
                                                    foreach ($this->crud_model->get_records('components') as $component) {
                                                        ?>
                                                        <option
                                                            value="<?= $component->component_id ?>"><?= $component->component_name ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <label for="component" class="active">Component</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col s12 input-field">
                                                <input id="model_qty" type="number" name="component_qty" required/>
                                                <label for="model_qty">No. of equipment</label>
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
                                                    Add equipment
                                                    <i class="mdi-content-send right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </li>
                                <?php } ?>
                            </ul>
                        <li class="collection-item tab-content" id="assigned">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Assigned date</th>
                                    <th>Model</th>
                                    <th>Equipment No</th>
                                    <th>Assigning Staff</th>
                                    <th>Status</th>
                                    <?php if ($is_pm && $details->project_stage > 2) { ?>
                                        <th></th>
                                    <?php } ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($assigned = $this->asset_model->all_assigned($details->project_id, 3, 2) as $eq) {
                                    $equipment = $this->equipment_model->details($eq->asset_id);
                                    ?>
                                    <tr>
                                        <td><?= $eq->date ?></td>
                                        <td class="link"
                                            id="components/profile/<?= urlencode(base64_encode($equipment->component_id)) ?>">
                                            <?= $this->crud_model->get_record('components', 'component_id', $equipment->component_id)->component_name ?>
                                        </td>
                                        <td class="link"
                                            id="equipment/profile/<?= urlencode(base64_encode($equipment->equipment_id)) ?>">
                                            <?= $equipment->equipment_no ?>
                                        </td>
                                        <td><?= $this->users_model->user($eq->handling_staff)->user_name ?></td>
                                        <td>
                                            <?php
                                            $installed = ($equipment->equipment_stage == 8);
                                            echo $this->equipment_model->stage($equipment->equipment_stage)
                                            ?>
                                        </td>

                                        <?php if ($is_pm && $details->project_stage > 2) { ?>
                                            <td>
                                                <?php
                                                $install = true;
                                                if ($eq->confirmation) {
//                                                    if (is_bool($installation = $this->equipment_model->installation_ready($equipment->equipment_id))) {
//                                                        if ($equipment->equipment_stage >= 4)
//                                                            $assembly = $this->crud_model->get_record("equipment_assembly", "equipment_id", $equipment->equipment_id);
//                                                        ?>
                                                    <!--                                                        ready to install-->
                                                    <!--                                                    --><?php //} else {
                                                    $install = false;
                                                    echo "<span class='green-text'>collected</span>";
                                                    //  }
                                                } elseif ($equipment->equipment_stage < 4) {
                                                    $equipment_available = false;
                                                    echo "<span class='orange-text'>Under assembly</span>";
                                                } else {
                                                    $equipment_available = false;
                                                    ?>
                                                    <button
                                                        value='assets/collect/<?= urlencode(base64_encode($eq->asset_location_id)) ?>'
                                                        class="btn ajax green lighten-2">Collect
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
                    <h5 class="card-title text-darken-4 orange-text">
                        <i class="mdi-navigation-chevron-right"></i>
                        Project details
                    </h5>
                    <ul class="collection">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s12 m6 offset-m6">
                                    <ul class="tabs tab-profile">
                                        <li class="tab">
                                            <a class=" waves-effect waves-light green-text" href="#tl">
                                                <i class="mdi-image-timelapse"></i>Logs</a>
                                        </li>
                                        <li class="tab ">
                                            <a class="waves-effect waves-light green-text" href="#details">
                                                <i class="mdi-editor-border-color"></i> Details</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item" id="tl">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Staff</th>
                                    <th>Activity</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($this->crud_model->get_records('project_timeline', 'project_id', $details->project_id) as $log) {
                                    ?>
                                    <tr>
                                        <td><?= $log->pt_time ?></td>
                                        <td><?= $this->users_model->user($log->user_id)->user_name ?></td>
                                        <td><?= $log->pt_action ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                        <li id="details" class="collection-item">
                            <h4 class="header2">Change Details</h4>
                            <form class="col s12" method="post" action="<?= site_url('projects/update') ?>">
                                <input type="hidden" name="project_id" value="<?= $details->project_id ?>"
                                       required/>

                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="first_name" value="<?= $details->project_name ?>"
                                               name="project_name" type="text" required>
                                        <label for="first_name" class="active">Project Name</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="c_name" type="text" value="<?= $details->project_client ?>"
                                               name="client_name" required>
                                        <label for="phone" class="active">Client Name</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12 m6">
                                        <select id="pm" name="project_manager" data-placeholder="Select project manager"
                                                class="browser-default chosen-select">
                                            <option value="" disabled>Choose Project Manager</option>
                                            <?php foreach ($this->users_model->has_powers('manage_project') as $pm) { ?>
                                                <option <?= $details->project_manager == $pm->user_id ? 'selected' : '' ?>
                                                    value="<?= $pm->user_id ?>"><?= $pm->user_name ?></option>
                                            <?php } ?>
                                        </select>
                                        <label for="pm" class="active">Project Manager</label>
                                    </div>
                                    <div class="input-field col s12">
                                        <select id="ps" data-placeholder="Choose a site..." class="chosen-select browser-default"
                                                name="project_site_id">
                                            <option value="" disabled selected>--Choose Project Site--</option>
                                            <?php foreach ($this->crud_model->get_records('sites') as $site) { ?>
                                                <option <?= $details->site_id == $pm->site_id ? 'selected' : '' ?>
                                                    value="<?= $site->site_id ?>"><?= $site->site_name ?></option>
                                            <?php } ?>
                                        </select>
                                        <label for="ps" class="active">Project Site</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="s_date" type="date"
                                               value="<?= $details->project_start_date ?>" name="start_date"
                                               required>
                                        <label for="s_date" class="active">Project Start Date</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="end_date" type="date"
                                               value="<?= $details->project_due_date ?>" name="end_date"
                                               required>
                                        <label for="end_date" class="active">Project Due Date</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                                <textarea id="description" name="description"
                                                          class="materialize-textarea"><?= $details->project_description ?></textarea>
                                        <label for="description" class="active">
                                            A brief Description of the proposal
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <?php if ($this->users_model->requires_role(array('update_proj')) && $details->project_proposer == $this->users_model->user()->user_id) { ?>
                                            <button class="btn orange waves-effect waves-light right" type="submit">
                                                Update
                                                <i class="mdi-content-send right"></i>
                                            </button>
                                        <?php } else { ?>
                                            <div id="card-alert" class="card orange">
                                                <div class="card-content  white-text">
                                                    Not permitted to edit project at this stage
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col s12 l4">
            <div class="row">
                <div class="col s12">
                    <button class="btn ajax orange right"
                            value="projects/trash/<?= urlencode(base64_encode($details->project_id)) . "/" . !$details->deleted ?>">
                        <?php if ($details->deleted) {
                            echo '<i class="mdi-action-restore"></i> Restore';
                        } else {
                            echo '<i class="mdi-action-delete"></i> Trash';
                        } ?>
                    </button>
                    <?php
                    if ($this->users_model->requires_role(array('revProj')) && $details->project_stage == 1) { ?>
                        <a class="btn green right"
                           href="<?= site_url('projects/review/' . urlencode(base64_encode($details->project_id))) ?>">
                            <i class="mdi-image-remove-red-eye"></i> Review
                        </a>
                    <?php } ?>
                    <?php
                    if (($details->project_proposer == $this->users_model->user()->user_id) && $details->project_stage == 0) { ?>
                        <button class="btn ajax blue white-text right"
                                value="projects/request_approval/<?= $details->project_id ?>">
                            <i class="mdi-image-remove-red-eye"></i> Request approval
                        </button>
                    <?php } ?>
                    <?php if ($equipment_available && $details->project_stage == 3 && $is_pm) { ?>
                        <button <?= $details->project_stage > 3 ? "disabled" : "" ?>
                            class="ajax btn white-text orange lighten-2" value="projects/assembly_complete/<?= $details->project_id ?>/yes">
                            <i class="mdi-navigation-check"></i> Finish assembly
                        </button>
                    <?php } ?>
                    <?php if ($equipment_available && $details->project_stage == 4 && $is_pm && $installed) { ?>
                        <button <?= $details->project_stage > 4 ? "disabled" : "" ?>
                            class="ajax btn white-text cyan lighten-2" value="projects/assembly_complete/<?= $details->project_id ?>/yes">
                            <i class="mdi-navigation-check"></i> Finish installation
                        </button>
                    <?php } ?>
                    <?php if ($equipment_available && $details->project_stage == 5 && ($is_pm || $this->users_model->user()->user_id == $details->project_proposer)) { ?>
                        <a href="<?= site_url('projects/handover/' . urlencode(base64_encode($details->project_id))) ?>" <?= $details->project_stage > 5 ? "disabled" : "" ?>
                           class="btn white-text cyan lighten-2 right">
                            <i class="mdi-navigation-check"></i> Handover
                        </a>
                    <?php } ?>
                </div>
                <div class="col s12">
                    <ul class="collection">
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
                                    <h6>Equipment</h6>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s6 center">
                                    <p class="medium">From</p>
                                    <h5 class="green-text">
                                        <strong>
                                            <?= $details->project_start_date ?>
                                        </strong>
                                    </h5>
                                </div>
                                <div class="col s6 center">
                                    <p class="medium">To</p>
                                    <h5 class="green-text">
                                        <strong>
                                            <?= $details->project_due_date ?>
                                        </strong>
                                    </h5>
                                </div>
                                <div class="col s12 center">
                                    <h6>Expected duration</h6>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s12 center">
                                    <h5 class="orange-text">
                                        <strong>
                                            <?= $this->projects_model->stage($details->project_stage) ?>
                                        </strong>
                                    </h5>
                                    <p class="medium">Current stage</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col s12">
                    <ul id="task-card" class="collection  with-header">
                        <li class="collection-header">
                            <h5 class="task-card-title orange-text">Details</h5>
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
                                <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i>
                                    Proposed installtion site:
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $this->sites_model->site($details->site_id)->site_name ?>
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
                                <div class="col s6 grey-text darken-1"><i class="mdi-action-verified-user"></i>
                                    Project Manager:
                                </div>
                                <div class="col s6 grey-text text-darken-4 right-align">
                                    <?= $details->project_manager ? $this->users_model->user($details->project_manager)->user_name : "<span class='orange-text'>Not specified :-( </span>" ?>
                                    <?php if ($this->users_model->requires_role(array('update_proj'))) { ?>
                                        <a class="cyan darken-4 white-text modal-trigger" href="#edit-manager">change</a>
                                    <?php } ?>
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
                </div>
                <div class="col s12">
                    <div class="card" style="background: transparent !important; border-radius: 5px; border: 1px solid #e0e0e0">
                        <div class="card-content center">
                            <span class="card-title">About </span>
                            <p>
                                <?= $details->project_description ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col s12">
                    <ul id="task-card" class="collection with-header z-depth-1">
                        <li class="collection-header">
                            <h5 class="task-card-title orange-text">Reviews</h5>
                        </li>
                        <?php foreach ($this->crud_model->get_records("project_review", "project_id", $details->project_id) as $review) { ?>
                            <li class="collection-item">
                                <?= $review->pr_verdict ? "<span class='green-text right-align'>Approved :-)</span>" : "<span class='orange-text right-align'>Reject :-(</span>" ?>
                                <br/>
                                <?= $review->pr_comment ?> <br/>

                                <div class="grey-text">
                                    <?= $this->users_model->user($review->user_id)->user_name ?> @ <?= $review->pr_time ?>

                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="edit-manager" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Change project manager</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('projects/change_manager/' . urlencode(base64_encode($details->project_id))) ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="pm" name="project_manager" data-placeholder="Select project manager" class="browser-default chosen-select">
                                <option value="" disabled>--Choose New Project Manager--</option>
                                <?php foreach ($this->users_model->has_powers('man_project') as $pm) { ?>
                                    <option value="<?= $pm->user_id ?>"><?= $pm->user_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="pm" class="active">Project Manager</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn orange darken-4 waves-effect waves-light right" type="submit">
                                Change manager
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>


