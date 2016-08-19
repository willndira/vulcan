<div class="row">
    <div class="col s12 m8">
        <div class="row">
            <div class="col s12">
                <h5 class="task-card-title grey-text">
                    <i class="mdi-navigation-chevron-right"></i>
                    <?= $page ?>
                </h5>
                <h6 class="medium green-text">
                    All raised tickets
                </h6>
            </div>
            <div class="col s12">
                <ul id="task-card" class="collection with-header">
                    <li class="collection-item">
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="green-text">New Tickets</span>
                        </div>
                        <table class="responsive-table dt display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Time</th>
                                <th>Title</th>
                                <th>Site</th>
                                <th>Priority</th>
                                <th>Comp't</th>
                                <th>Problem type</th>
                                <th>Status</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($tickets = $this->crud_model->get_records('tickets', "ticket_status < ", 3) as $ticket) { ?>
                                <tr class="link" id="tickets/profile/<?= urlencode(base64_encode($ticket->ticket_id)) ?>">
                                    <td><?= $ticket->ticket_time ?></td>
                                    <td><?= $ticket->ticket_title ?></td>
                                    <td><?= $this->sites_model->site($ticket->site_id)->site_name ?></td>
                                    <td><?= $this->tickets_model->priority($ticket->ticket_priority) ?></td>
                                    <td><?= $this->tickets_model->component($ticket->ticket_affected_component) ?></td>
                                    <td><?= $this->tickets_model->type($ticket->problem_type) ?></td>
                                    <td><?= $this->tickets_model->state($ticket->ticket_status) ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </li>
                </ul>
            </div>
            <div class="col s12">
                <ul id="task-card" class="collection with-header">
                    <li class="collection-item">
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="green-text">Resolved Tickets</span>
                        </div>
                        <table class="responsive-table dt display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Time</th>
                                <th>Title</th>
                                <th>Site</th>
                                <th>Priority</th>
                                <th>Comp't</th>
                                <th>Problem type</th>
                                <th>Status</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($this->crud_model->get_records('tickets', "ticket_status", 3) as $ticket) { ?>
                                <tr class="link" id="tickets/profile/<?= urlencode(base64_encode($ticket->ticket_id)) ?>">
                                    <td><?= $ticket->ticket_time ?></td>
                                    <td><?= $ticket->ticket_title ?></td>
                                    <td><?= $this->sites_model->site($ticket->site_id)->site_name ?></td>
                                    <td><?= $this->tickets_model->priority($ticket->ticket_priority) ?></td>
                                    <td><?= $this->tickets_model->component($ticket->ticket_affected_component) ?></td>
                                    <td><?= $this->tickets_model->type($ticket->problem_type) . ($ticket->is_remote == 1 ? " - REMOTE" : "") ?></td>
                                    <td><?= $this->tickets_model->state($ticket->ticket_status) ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </li>
                </ul>
            </div>
            <div class="col s12">
                <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                    <li>
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="green-text">My Tickets</span>
                        </div>
                        <div class="collapsible-body" style=" padding: 20px 10px;">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Title</th>
                                    <th>Site</th>
                                    <th>Priority</th>
                                    <th>Due Time</th>
                                    <th>Status</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($my_tickets = $this->tickets_model->my_tickets() as $ticket) { ?>
                                    <tr class="link" id="tickets/profile/<?= urlencode(base64_encode($ticket->ticket_id)) ?>">
                                        <td><?= $ticket->ticket_time ?></td>
                                        <td><?= $ticket->ticket_title ?></td>
                                        <td><?= $this->sites_model->site($ticket->site_id)->site_name ?></td>
                                        <td><?= $ticket->ticket_priority ?></td>
                                        <td><?= $ticket->ticket_due_date ?></td>
                                        <td><?= $this->tickets_model->state($ticket->ticket_status) ?></td>
                                    </tr>
                                <?php } ?>
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
                            <span class="red-text">Trashed tickets</span>
                        </div>
                        <div class="collapsible-body" style=" padding: 20px 10px;">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Title</th>
                                    <th>Site</th>
                                    <th>Priority</th>
                                    <th>Due Time</th>
                                    <th>Status</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($trashed = $this->crud_model->get_trash('tickets') as $ticket) { ?>
                                    <tr class="link" id="tickets/profile/<?= urlencode(base64_encode($ticket->ticket_id)) ?>">
                                        <td><?= $ticket->ticket_time ?></td>
                                        <td><?= $ticket->ticket_title ?></td>
                                        <td><?= $this->sites_model->site($ticket->site_id)->site_name ?></td>
                                        <td><?= $ticket->ticket_priority ?></td>
                                        <td><?= $ticket->ticket_due_date ?></td>
                                        <td><?= $this->tickets_model->state($ticket->ticket_status) ?></td>
                                    </tr>
                                <?php } ?>
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
                <?php
                if ($this->users_model->requires_role(array('create_ticket'))
                ) {
                    ?>
                    <a class="btn green right modal-trigger" href="#new-ticket">
                        New ticket
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
                                        <?= count($tickets) ?>
                                    </strong>
                                </h5>
                                <p class="medium">All tickets</p>
                            </div>
                            <div class="col s6 center">
                                <h5 class="green-text">
                                    <strong>
                                        <?= count($my_tickets) ?>
                                    </strong>
                                </h5>
                                <p class="medium">My tickets</p>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s4 center">
                                <h5 class="orange-text">
                                    <strong>
                                        <?= $approved = count($this->crud_model->get_records('tickets', "ticket_status", 3)) ?>
                                    </strong>
                                </h5>
                                <p class="medium">Resolved</p>
                            </div>
                            <div class="col s4 center">
                                <h5 class="orange-text">
                                    <strong>
                                        <?= count($this->crud_model->get_records('tickets', "ticket_status", 2)) ?>
                                    </strong>
                                </h5>
                                <p class="medium">In progress</p>
                            </div>
                            <div class="col s4 center">
                                <h5 class="red-text">
                                    <strong>
                                        <?= count($this->crud_model->get_trash('tickets')) ?>
                                    </strong>
                                </h5>
                                <p class="medium">Trashed</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col s12">
                <ul class="collection">
                    <li class="collection-item">
                        <h5 class="medium black-text">Latest Activities</h5>
                        <?php
                        $this->db->limit(5);
                        $this->db->order_by("ticket_log_id", "DESC");
                        foreach ($this->db->get("tbl_ticket_log")->result() as $activity) { ?>
                            <p class="medium-small">
                                <strong class="cyan-text link" id="profile/user/<?= urlencode(base64_encode($activity->user_id)) ?>">
                                    <i class="mdi-action-verified-user"></i> <?= $this->users_model->user($activity->user_id)->user_name ?>
                                </strong>
                                <span class="right orange-text">
                                        <i class="mdi-av-timer"></i> <?= $activity->tl_time ?>
                                </span>
                                <br/>

                                <span style="padding-left: 10px">
                                    <?= $activity->tl_action ?>
                                    <span class="link blue-text" id="tickets/profile/<?= urlencode(base64_encode($activity->ticket_id)) ?>">
                                        Project: <?= $this->tickets_model->details($activity->ticket_id)->ticket_title ?>
                                    </span>
                                </span>
                            </p>
                            <br/>
                        <?php }
                        ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="new-ticket" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Raise a ticket</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('tickets/raise') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="ticket_title" value="<?= set_value('ticket_title') ?>" name="ticket_title" type="text" required>
                            <label for="ticket_title">Ticket Title</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <select id="site" name="site_id">
                                <option value="" disabled selected>--Select ticket site--</option>
                                <?php
                                foreach ($this->crud_model->get_records('sites') as $site)
                                    echo '<option value="' . $site->site_id . '" ' . (set_value('site_id') == $site->site_id ? "selected" : "") . '> ' . $site->site_name . ' </option>';
                                ?>
                            </select>
                            <label for="site">Site</label>
                        </div>
                        <div class="input-field col s6">
                            <select id="site" name="affected_component">
                                <option value="" disabled selected>--Select component--</option>
                                <option value="1">Entry</option>
                                <option value="2">Exit</option>
                                <option value="3">APS</option>
                                <option value="4">Others</option>
                            </select>
                            <label for="site">Component affected</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <select id="priority" name="ticket_priority">
                                <option value="" disabled selected>--Ticket priority--</option>
                                <option <?= set_value('ticket_priority') == 3 ? 'selected' : '' ?> value="3">Normal</option>
                                <option <?= set_value('ticket_priority') == 2 ? 'selected' : '' ?> value="2">Urgent</option>
                                <option <?= set_value('ticket_priority') == 1 ? 'selected' : '' ?> value="1">Critical</option>
                            </select>
                            <label for="priority">Priority</label>
                        </div>
                        <div class="input-field col s6">
                            <select id="priority" name="problem_type">
                                <option value="" disabled selected>--Select issue type--</option>
                                <option <?= set_value('problem_type') == 1 ? 'selected' : '' ?> value="1">Power</option>
                                <option <?= set_value('problem_type') == 2 ? 'selected' : '' ?> value="2">Network</option>
                                <option <?= set_value('problem_type') == 3 ? 'selected' : '' ?> value="3">Hardware</option>
                                <option <?= set_value('problem_type') == 4 ? 'selected' : '' ?> value="4">Software</option>
                                <option <?= set_value('problem_type') == 5 ? 'selected' : '' ?> value="5">Other</option>
                            </select>
                            <label for="priority">Issue type</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="due_date" value="<?= set_value('date') != "" ? set_value('date') : date('Y-m-d H:m:s') ?>" type="date"
                                   name="ticket_due_date"
                                   required>
                            <label for="due_date" class="active">Due Date</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="expected_duration" type="number" value="<?= set_value('expected_duration') ?>" name="expected_duration"
                                   required>
                            <label for="expected_duration">Expected duration(hrs)</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="about" name="about" class="materialize-textarea" required><?= set_value('about') ?></textarea>
                            <label for="about">Pre diagnosis</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="checkbox" name="is_remote" value="1" id="is_remote">
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
                                    Create
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