<?php
$site = $this->sites_model->site($details->site_id);
?>
<div class="section">
    <div class="row">
        <div class="col s12 m8">
            <div class="row">
                <div class="col s12">
                    <h5 class="card-title  grey-text">
                        <i class="mdi-navigation-chevron-right"></i>
                        <?= $details->ticket_title ?>
                    </h5>
                    <p class="medium green-text">
                        <?= $site->site_name ?>
                    </p>
                </div>
                <div class="col s12">
                    <hr/>
                    <h5 class="card-title text-darken-4 orange-text">
                        <i class="mdi-action-bug-report class green-text"></i>
                        Affected equipment
                    </h5>
                    <p class="medium text-darken-4">
                        <?= $this->tickets_model->component($details->ticket_affected_component) ?>
                    </p>
                    <h5 class="card-title text-darken-4 orange-text">
                        <i class="mdi-action-bug-report class green-text"></i>
                        Description
                    </h5>
                    <p class="medium text-darken-4">
                        <?= $details->ticket_issue ?>
                    </p>
                    <hr/>
                    <h5 class="card-title text-darken-4 orange-text">
                        <i class="mdi-action-bug-report class green-text"></i>
                        Affected items
                    </h5>
                    <ul class="collection">
                        <li class="collection-item">
                            <table class="responsive-table display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Model</th>
                                    <th>Verification mode</th>
                                    <th>Status</th>
                                    <th>Problem</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->crud_model->get_records('ticket_items', 'ticket_id', $details->ticket_id) as $item) {
                                    $item = $this->crud_model->get_record('items', 'item_id', $item->item_id);
                                    $model = $this->items_model->get_model_details($item->model_id);
                                    ?>
                                    <tr class="item_link" id="<?= $item->item_id ?>">
                                        <td><?= $item->ti_id ?></td>
                                        <td class="capitalize"><?= $item->item_code ?></td>
                                        <td><?= $model->make_name . ' ' . $model->model_name ?></td>
                                        <td><?= $item->verification_mode ? '<span class="green-text"> Scan code </span>' : '<span class="orange-text"> Typed</span>' ?></td>
                                        <td>
                                            <?= $item->item_condition ? '<span class="green-text"> Functional </span>' : '<span class="orange-text"> Faulty</span>' ?>
                                        </td>
                                        <td><?= $item->fail_cause ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
                <div class="col s12">
                    <h5 class="card-title text-darken-4 orange-text">
                        <i class="mdi-social-group class green-text"></i>
                        Assigned technicians
                    </h5>
                    <ul class="collection">
                        <li class="collection-item">
                            <table class="responsive-table display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Assigned by</th>
                                    <th>Reported</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php
                                foreach ($this->crud_model->get_records('ticket_staff', 'ticket_id', $details->ticket_id) as $assign) {
                                    $staff = $this->users_model->technician($assign->technician_id);
                                    ?>
                                    <tr>
                                        <td><?= $assign->ts_id ?></td>
                                        <td><?= $assign->ts_time ?></td>
                                        <td class="link" id="profile/user/<?= urlencode(base64_encode($staff->user_id)) ?>">
                                            <?= $staff->user_name ?>
                                        </td>
                                        <td><?= $assign->staff_role ?></td>
                                        <td><?= $this->users_model->user($assign->assigned_by)->user_name ?></td>
                                        <td><?= $assign->staff_reported ? '<span class="green-text">Reported</span>' : '<span class="green-text">Yet</span>' ?></td>
                                        <td>
                                            <button class="ajax btn btn-xs orange" <?= $assign->staff_reported ? "disabled" : "" ?>
                                                    value="tickets/cancel_assign/<?= urlencode(base64_encode($assign->ts_id)) . "/" . !$assign->deleted ?>">
                                                unassign
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
                <div class="col s12">
                    <h5 class="card-title text-darken-4 orange-text">
                        <i class="mdi-action-receipt green-text"></i>
                        Logs trails</h5>
                    <ul class="collection">
                        <li class="collection-item" id="tl">
                            <table class="responsive-table display dt" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Staff</th>
                                    <th>Location</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php
                                foreach ($this->crud_model->get_records('ticket_log', 'ticket_id', $details->ticket_id) as $log) {
                                    ?>
                                    <tr>
                                        <td><?= $log->ticket_log_id ?></td>
                                        <td><?= $log->tl_time ?></td>
                                        <td><?= $this->users_model->user($log->user_id)->user_name ?></td>
                                        <td><?= $log->location ?></td>
                                        <td><?= $log->tl_action ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="row">
                <div class="col s12">
                    <button class="btn  ajax orange right"
                            value="tickets/trash/<?= urlencode(base64_encode($details->ticket_id)) . "/" . !$details->deleted ?>">
                        <?php if ($details->deleted) {
                            echo 'Restore';
                        } else {
                            echo 'Trash';
                        } ?>
                    </button>
                    <?php
                    if ($details->ticket_status != 3) { ?>
                        <a class="btn  blue right modal-trigger" href="#edit">
                            Edit
                        </a>
                        <a class="btn  green right modal-trigger" href="#assign">
                            Assign
                        </a>
                    <?php } ?>
                </div>
                <ul class="col s12 collection">
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s6 center">
                                <h6 class="chart-title">
                                    <strong>
                                        <?= $this->tickets_model->priority($details->ticket_priority) ?>
                                    </strong>
                                </h6>
                                <p class="medium">Priority</p>
                            </div>
                            <div class="col s6 center">
                                <h6 class="chart-title green-text">
                                    <strong>
                                        <?= $details->ticket_etime ?> (Hrs)
                                    </strong>
                                </h6>
                                <p class="medium">Expected resolve time</p>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s6 center green-text">
                                <h6 class="chart-title">
                                    <strong>
                                        <?= $this->tickets_model->state($details->ticket_status) ?>
                                    </strong>
                                </h6>
                                <p class="medium">Status</p>
                            </div>
                            <div class="col s6 center green-text">
                                <h6 class="chart-title">
                                    <strong>
                                        <?= $this->tickets_model->component($details->ticket_affected_component) ?>
                                    </strong>
                                </h6>
                                <p class="medium">Affect component</p>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- Profile About Details  -->
                <ul class="collection col s12">
                    <li class="collection-item">
                        <h6 class="chart-title orange-text">Details</h6>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1">
                                <i class="mdi-action-wallet-travel"></i>
                                Site
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $site->site_name ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1">
                                <i class="mdi-av-timer"></i>
                                Raise Time
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $details->ticket_time ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1">
                                <i class="mdi-image-timelapse"></i>
                                Due time
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $details->ticket_due_date ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1">
                                <i class="mdi-action-verified-user"></i>
                                Raised by
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $this->users_model->user($details->raised_by)->user_name ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s6 grey-text darken-1"><i class="mdi-action-verified-user"></i>Priority
                            </div>
                            <div class="col s6 grey-text text-darken-4 right-align">
                                <?= $this->tickets_model->priority($details->ticket_priority) ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div id="edit" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Edit details</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('tickets/update') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="ticket_title" value="<?= $details->ticket_title ?>" name="ticket_title" type="text" required>
                            <label for="ticket_title">Ticket Title</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <select id="site" name="site_id" class="chosen-select browser-default" data-placeholder="Select ticket site">
                                <option value="" disabled>--Select ticket site--</option>
                                <?php
                                foreach ($this->crud_model->get_records('sites') as $site)
                                    echo '<option value="' . $site->site_id . '" ' . ($details->site_id == $site->site_id ? "selected" : "") . '> ' . $site->site_name . ' </option>';
                                ?>
                            </select>
                            <label for="site">Site</label>
                        </div>
                        <input type="hidden" name="ticket_id" value="<?= $details->ticket_id ?>"/>
                        <div class="input-field col s6">
                            <select id="site" name="affected_component" class="chosen-select browser-default"
                                    data-placeholder="Select affected equipment">
                                <option value="" disabled>--Select component--</option>
                                <option <?= $details->ticket_affected_component == 1 ? 'selected' : '' ?> value="1">Entry</option>
                                <option <?= $details->ticket_affected_component == 2 ? 'selected' : '' ?> value="2">Exit</option>
                                <option <?= $details->ticket_affected_component == 3 ? 'selected' : '' ?> value="3">APS</option>
                                <option <?= $details->ticket_affected_component == 4 ? 'selected' : '' ?> value="4">Others</option>
                            </select>
                            <label for="site" class="active">Component affected</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <select id="priority" name="ticket_priority" class="chosen-select browser-default"
                                    data-placeholder="Select issue priority">
                                <option value="" disabled>--Ticket priority--</option>
                                <option <?= $details->ticket_priority == 3 ? 'selected' : '' ?> value="3">Normal</option>
                                <option <?= $details->ticket_priority == 2 ? 'selected' : '' ?> value="2">Urgent</option>
                                <option <?= $details->ticket_priority == 1 ? 'selected' : '' ?> value="1">Critical</option>
                            </select>
                            <label for="priority" class="active">Priority</label>
                        </div>
                        <div class="input-field col s6">
                            <select id="priority" name="problem_type" class="chosen-select browser-default" data-placeholder="Select issue type">
                                <option value="" disabled>--Select issue type--</option>
                                <option <?= $details->problem_type == 1 ? 'selected' : '' ?> value="1">Power</option>
                                <option <?= $details->problem_type == 2 ? 'selected' : '' ?> value="2">Network</option>
                                <option <?= $details->problem_type == 3 ? 'selected' : '' ?> value="3">Hardware</option>
                                <option <?= $details->problem_type == 4 ? 'selected' : '' ?> value="4">Software</option>
                                <option <?= $details->problem_type == 5 ? 'selected' : '' ?> value="5">Other</option>
                            </select>
                            <label for="priority" class="active">Issue type</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="due_date" value="<?= $details->ticket_due_date ?>" type="date"
                                   name="ticket_due_date"
                                   required>
                            <label for="due_date" class="active">Due Date</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="expected_duration" type="number" value="<?= $details->ticket_etime ?>" name="expected_duration"
                                   required>
                            <label for="expected_duration">Expected duration(hrs)</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="about" name="about" class="materialize-textarea" required><?= $details->ticket_issue ?></textarea>
                            <label for="about">Pre diagnosis</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="checkbox" value="1" name="is_remote" id="is_remote">
                            <label for="is_remote">Remote?</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <?php
                            if ($this->users_model->requires_role(array('create_ticket'))
                            ) {
                                ?>
                                <button class="btn orange waves-effect waves-light right" type="submit">
                                    Update
                                    <i class="mdi-content-send right"></i>
                                </button>
                                <?php
                            } else {
                                ?>
                                <div id="card-alert" class="card red">
                                    <div class="card-content white-text">
                                        <p>DENIED : Sorry. No enough permissions update this ticket.</p>
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
<div id="assign" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Assign technician</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('tickets/assign') ?>">
                    <input type="hidden" name="ticket_id" value="<?= $details->ticket_id ?>"/>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="tech" name="technicians[]" class="chosen-select browser-default" data-placeholder="Staff to assign" multiple>
                                <option value="" disabled>--Assign Technicians--</option>
                                <?php
                                foreach ($this->users_model->technicians() as $technician) {
                                    $is_assigned = $this->users_model->is_assigned($technician->technician_id);
                                    if (!$this->tickets_model->isAssigned($technician->technician_id, $details->ticket_id))
                                        echo '<option value="' . $technician->technician_id . '">' . $technician->user_name . ' ' . ($is_assigned ? '- <b>Assigned another</b>' : '') . '</option>';
                                }
                                ?>
                            </select>
                            <label for="tech" class="active">Assign Technician</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="about" name="staff_role" class="materialize-textarea" required></textarea>
                            <label for="about">Staff role</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <?php
                            if ($this->users_model->requires_role(array('assign_ticket'))
                            ) {
                                ?>
                                <button class="btn orange waves-effect waves-light right" type="submit">
                                    Assign
                                    <i class="mdi-content-send right"></i>
                                </button>
                                <?php
                            } else {
                                ?>
                                <div id="card-alert" class="card red">
                                    <div class="card-content white-text">
                                        <p>DENIED : Sorry. No enough permissions create a ticket.</p>
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