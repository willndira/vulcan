<div class="section">
    <div class="row">
        <div class="col s12 m5">
            <h5 class="grey-text">
                <i class="mdi-navigation-chevron-right"></i>
                <?= $details->code_name ?>
            </h5>
        </div>
        <div class="col s12 m7">
            <button class="btn ajax red right"
                    value="aps/trash/<?= urlencode(base64_encode($details->aps_id)) . "/" . !$details->deleted ?>">
                        <?= $details->deleted ? 'Restore' : 'Trash'; ?>
            </button>

            <a href="#edit-aps" class="btn modal-trigger blue right">
                Edit
            </a>

        </div>
        <div class="col s12 l8">
            <div class="row">
                <div class="col s12">
                    <h5 class="card-title text-darken-4 orange-text">
                        <i class="mdi-navigation-chevron-right"></i>
                        Site Equipment
                    </h5>
                    <ul id="task-card" class="collection with-header">
                        <li class="collection-item">
                            <table class="responsive-table display" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>No</th>
                                        <th>Model</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($equipments as $equipment) { ?>
                                    <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                                        <li>
                                            <div class="collapsible-header" style="padding: 10px;">
                                                <span class="red-text">Trashed sites</span>
                                            </div>
                                            <div class="collapsible-body" style=" padding: 20px 10px;">
                                                <table class="responsive-table dt display" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Name</th>
                                                            <th>Zone</th>
                                                            <th>SLA</th>
                                                            <th>Email</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php foreach ($trashed as $site) { ?>
                                                            <tr class="link" id="sites/profile/<?= urlencode(base64_encode($site->site_id)) ?>">
                                                                <td><?= $site->site_id ?></td>
                                                                <td><?= $site->site_name ?></td>
                                                                <td><?= $site->site_zone ?></td>
                                                                <td><?= $this->sites_model->sla($site->site_category)->sla_name ?></td>
                                                                <td><?= $site->site_email ?></td>
                                                                <td><?= $this->sites_model->status($site->site_status) ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </li>
                                    </ul>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col s12">
                        <h5 class="card-title text-darken-4 orange-text">
                            <i class="mdi-navigation-chevron-right"></i>
                            Raised Tickets
                        </h5>
                        <ul id="task-card" class="collection with-header">
                            <li class="collection-item">
                                <table class="responsive-table display dt" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Time</th>
                                            <th>Title</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets as $ticket) { ?>
                                            <tr class="link" id="tickets/profile/<?= urlencode(base64_encode($ticket->ticket_id)) ?>">
                                                <td><?= $ticket->ticket_id ?></td>
                                                <td><?= $ticket->ticket_time ?></td>
                                                <td><?= $ticket->ticket_title ?></td>
                                                <td><?= $this->tickets_model->priority($ticket->ticket_priority) ?></td>
                                                <td><?= $this->tickets_model->state($ticket->ticket_status) ?></td>
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
                            Site Equipment
                        </h5>
                        <ul id="task-card" class="collection with-header">
                            <li class="collection-item">
                                <table class="responsive-table display" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>No</th>
                                            <th>Model</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($equipments as $equipment) { ?>
                                        <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                                            <li>
                                                <div class="collapsible-header" style="padding: 10px;">
                                                    <span class="red-text">Trashed sites</span>
                                                </div>
                                                <div class="collapsible-body" style=" padding: 20px 10px;">
                                                    <table class="responsive-table dt display" cellspacing="0">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Name</th>
                                                                <th>Zone</th>
                                                                <th>SLA</th>
                                                                <th>Email</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php foreach ($trashed as $site) { ?>
                                                                <tr class="link" id="sites/profile/<?= urlencode(base64_encode($site->site_id)) ?>">
                                                                    <td><?= $site->site_id ?></td>
                                                                    <td><?= $site->site_name ?></td>
                                                                    <td><?= $site->site_zone ?></td>
                                                                    <td><?= $this->sites_model->sla($site->site_category)->sla_name ?></td>
                                                                    <td><?= $site->site_email ?></td>
                                                                    <td><?= $this->sites_model->status($site->site_status) ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </li>
                                        </ul>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </li>
                        </ul>
                    </div>
                    <div class="col s12">
                        <h5 class="card-title text-darken-4 orange-text">
                            <i class="mdi-navigation-chevron-right"></i>
                            Supervisor Schedules
                        </h5>
                        <ul id="task-card" class="collection with-header">
                            <li class="collection-item">
                                <?php foreach ($supervisors as $supervisor) { ?>
                                    <div class="col s12">
                                        <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                                            <li>
                                                <div class="collapsible-header">
                                                    <?= $this->users_model->user($supervisor->staff_id)->user_name ?>
                                                </div>
                                                <div class="collapsible-body">

                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </li>
                        </ul>
                    </div>
                    <div class="col s12">
                        <h5 class="card-title text-darken-4 orange-text">
                            <i class="mdi-navigation-chevron-right"></i>
                            Site logs
                        </h5>
                        <ul class=" collection with-header">
                            <li id="requests" class="collection-item col s12">
                                <table class="responsive-table display dt" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Time</th>
                                            <th>Staff</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        foreach ($this->crud_model->get_records('site_timeline', 'site_id', $details->site_id) as $log) {
                                            ?>
                                            <tr>
                                                <td><?= $log->st_id ?></td>
                                                <td><?= $log->st_time ?></td>
                                                <td><?= $this->users_model->user($log->user_id)->user_name ?></td>
                                                <td><?= $log->st_action ?></td>
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
            <div class="col l4 hide-on-med-and-down">
                <div class="row">
                    <ul id="task-card" class="col s12 collection with-header">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s6 center">
                                    <h5 class="red-text">
                                        <strong>
                                            <?= count($tickets) ?>
                                        </strong>
                                    </h5>
                                    <p class="medium">Tickets</p>
                                </div>
                                <div class="col s6 center">
                                    <h5 class="green-text">
                                        <strong>
                                            0 %
                                        </strong>
                                    </h5>
                                    <p class="medium">Equipment</p>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item center">
                            <div class="row ">
                                <div class="col s12 ">
                                    <h5 class="orange-text text-darken-4 card-title center">
                                        Current Supervisor
                                    </h5>
                                </div>
                                <div class="col s12 ">
                                    <h6 class="card-title blue-text text-darken-4">
                                        <?php
                                        $m = $this->sites_model->manager($details->site_id);

                                        echo count($m) != 0 ? $this->users_model->user($m->staff_id)->user_name : "No one scheduled"
                                        ?>
                                    </h6>
                                </div>
                            </div>
                            <?php if (count($m) != 0) { ?>
                                <div class="col s6">
                                    From: <?= $m->check_in ?>
                                </div>
                                <div class="col s6">
                                    To: <?= $m->check_out ?>
                                </div>
                                <p class="medium"><?= is_null($m->checkin_time) != 0 ? "Checked In" : "Not reported" ?></p>
                            <?php } ?>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s12 ">
                                    <h5 class="orange-text text-darken-4 card-title center">
                                        Next Maintenance
                                    </h5>
                                </div>
                                <div class="col s12 center green-text">
                                    <?= $this->sites_model->preventative($details->site_id) ?>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <ul id="task-card" class="col s12 collection with-header">
                        <li class="collection-header">
                            <h5 class="task-card-title blue-text"><strong>Details</strong></h5>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i>
                                    Code Name
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->code_name ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-maps-my-location"></i>
                                    Site Name
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $this->sites_model->site($details->site_id)->site_name ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-maps-my-location"></i>
                                    IP Address
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->ip_address ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-maps-my-location"></i>
                                    Status
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->status == 1 ? 'Online' : 'Offline' ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-maps-my-location"></i>
                                    Comments
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->comments ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-maps-my-location"></i>
                                    OS
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->os ?>
                                </div>
                            </div>
                        </li>

                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-editor-mode-edit"></i> Registered BY
                                </div>
                                <div
                                    class="col s7 grey-text text-darken-4 right-align">
                                        <?= $this->users_model->user($details->site_added_by)->user_name ?>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div id="change-preventative" data-keyboard="false" class="modal bottom-sheet">
        <div class="modal-content" style="padding: 5px !important;">
            <ul class="collection">
                <li class="collection-item">
                    <h5 class="orange-text center">Schedule Maintenance</h5>
                </li>
                <li class="collection-item">
                    <form method="post" action="<?= site_url('sites/manager') ?>">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="s_email" type="text" value="<?= date("Y-m-d") ?>" name="check_in" required>
                                <label for="s_email">Maintenance date</label>
                            </div>
                        </div>
                        <input type="hidden" name="site_id" value="<?= $details->site_id ?>"/>
                        <div class="row">
                            <div class="input-field col s12">
                                <button class="btn cyan waves-effect waves-light right" type="submit"
                                        name="action">Schedule
                                    <i class="mdi-content-send right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <div id="change-manager" data-keyboard="false" class="modal bottom-sheet">
        <div class="modal-content" style="padding: 5px !important;">
            <ul class="collection">
                <li class="collection-item">
                    <h5 class="orange-text center">Schedule Site manager</h5>
                </li>
                <li class="collection-item">
                    <form method="post" action="<?= site_url('sites/manager') ?>">
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="manager" class="chosen-select browser-default" data-placeholder="Site manager" id="category">
                                    <option value="" disabled selected>--select manager--</option>
                                    <?php
                                    foreach ($this->users_model->has_powers("man_sites") as $manager) {
                                        printf('<option value="%d" %s>%s</option>', $manager->user_id, (set_value('manager') == $manager->user_id ? 'selected' : ''), $manager->user_name);
                                    }
                                    ?>
                                </select>
                                <label for="category" class="active">Site manager</label>
                                <input type="hidden" name="site_id" value="<?= $details->site_id ?>"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="s_email" type="text" value="<?= set_value("check_in") != "" ? set_value("check_in") : date("Y-m-d H:m") ?>"
                                       name="check_in" required>
                                <label for="s_email">Check in</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="s_email" type="text" value="<?= set_value("check_out") != "" ? set_value("check_out") : date("Y-m-d H:m") ?>"
                                       name="check_out" required>
                                <label for="s_email">Check out</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <textarea id="description" name="instructions" class="materialize-textarea"><?= set_value("instructions") ?></textarea>
                                <label for="description">A brief instructions</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <button class="btn cyan waves-effect waves-light right" type="submit"
                                        name="action">Assign
                                    <i class="mdi-content-send right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </li>
            </ul>
        </div>
    </div>






    <!------------------------------------------------APS------------------------------------------------------->
    <div id="edit-aps" data-keyboard="false" class="modal bottom-sheet">
        <div class="modal-content" style="padding: 5px !important;">
            <ul class="collection">
                <li class="collection-item">
                    <h5 class="orange-text center">Edit <?= $details->code_name ?> Details</h5>
                </li>
                <li class="collection-item">
                    <form class="col s12" method="post" action="<?= site_url('aps/update') ?>">

                        <div class="row">
                            <div class="input-field col s6">
                                <input type="hidden" name="aps_id" value="<?= $details->aps_id ?>" required/>
                                <input id="aps_no"  name="aps_no" value="<?= $details->aps_no ?>" type="text" required>
                                <label for="aps_no" class="active">APS No.</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="code_name"  name="code_name" value="<?= $details->code_name ?>" type="text" required>
                                <label for="code_name" class="active">Code Name</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s6">
                                <input id="ip_address"  name="ip_address" value="<?= $details->ip_address ?>" type="text" required>
                                <label for="ip_address" class="active">IP Address</label>
                            </div>

                            <div class="input-field col s6">
                                <input id="aps_os"  name="aps_os" value="<?= $details->os ?>" type="text" required>
                                <label for="aps_os" class="active">OS</label>
                            </div>

                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="aps_status" id="aps_status" class="chosen-select browser-default">
                                    <option disabled="true">Aps Status</option>
                                    <option <?= $details->status == 1 ? "selected='true'" : "" ?> value="1">Active</option>
                                    <option <?= $details->status == 0 ? "selected='true'" : "" ?> value="0">Offline</option>
                                </select>
                                <label for="aps_status" class="active">APS Status</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="site_id" id="aps_status" class="chosen-select browser-default">
                                    <option disabled="true">Select Site</option>
                                    <?php
                                    if (isset($sites) && count($sites)) {
                                        foreach ($sites as $row) {
                                            ?>
                                            <option <?= $details->site_id == $row->site_id ? "selected='true'" : "" ?> value="<?= $row->site_id ?>"><?= $row->site_name ?></option>
                                            <?php
                                        }
                                    }
                                    ?>

                                </select>
                                <label for="aps_status" class="active">Site Name</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <textarea name="aps_comments" id="aps_comments" value="<?= $details->comments ?>" class="materialize-textarea"></textarea>
                                <label for="aps_comments" class="active">Comments</label>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <button class="btn orange waves-effect waves-light right" name="action">Update
                                    <i class="mdi-content-send right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </li>
            </ul>
        </div>
    </div>