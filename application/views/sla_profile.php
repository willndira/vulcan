<div class="section">
    <div class="row">
        <div class="col s5">
            <h5 class="grey-text">
                <i class="mdi-navigation-chevron-right"></i>
                <?= $details->sla_name ?>
            </h5>
        </div>
        <div class="col s7">
            <button class="btn ajax red right"
                    value="sla/trash/<?= urlencode(base64_encode($details->sla_id)) . "/" . !$details->deleted ?>">
                <?php echo $details->deleted ? 'Restore' : 'Trash'; ?>
            </button>
            <?php
            if ($this->users_model->requires_role(array('add_sla_level'))
            ) {
                ?>
                <a href="#new-escalation" class="btn modal-trigger green right">
                    New Escalation Level
                </a>
            <?php }
            if ($this->users_model->requires_role(array('edit_sla'))
            ) {
                ?> <a href="#edit" class="btn modal-trigger blue right">
                    Edit
                </a>
            <?php } ?>
        </div>
        <div class="col s12 l8">
            <div class="row">
                <div class="col s12">

                    <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                        <li>
                            <p class="medium-small">
                                <?= $details->sla_description ?>
                            </p>
                        </li>
                        <li>
                            <div class="collapsible-header active" style="padding: 10px;">
                                <span class="blue-text ">Escalation levels</span>
                            </div>
                            <div class="collapsible-body" style=" padding: 20px 10px;">
                                <table class="dt display" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Escalate after</th>
                                        <th>Notified group</th>
                                        <th>Reg Staff</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($escalations as $sla_level) { ?>
                                        <tr>
                                            <td><?= $sla_level->sla_notification_level_id ?></td>
                                            <td><?= $sla_level->ticket_delay_duration ?> hrs</td>
                                            <td class="link" id="users/level_profile/<?= urlencode(base64_encode($sla_level->user_category_id)) ?>">
                                                <?= $this->users_model->get_category($sla_level->user_category_id)->user_category_name ?>
                                            </td>
                                            <td class="link" id="profile/user/<?= urlencode(base64_encode($sla_level->reg_staff)) ?>">
                                                <?= $this->users_model->user($sla_level->reg_staff)->user_name ?>
                                            </td>
                                            <td>
                                                <a href="<?= site_url('sla/remove_level/' . urlencode(base64_encode($sla_level->sla_notification_level_id)) . '/1') ?>"
                                                   class="btn btn-xs red trash">
                                                    remove
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </li>
                    </ul>
                </div>
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
                                    <th>Escalated</th>
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
                                <span class="red-text ">Trashed escalation levels</span>
                            </div>
                            <div class="collapsible-body" style=" padding: 20px 10px;">
                                <table class="dt display" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Escalate after</th>
                                        <th>Notified group</th>
                                        <th>Reg Staff</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($del_escalations as $sla_level) { ?>
                                        <tr>
                                            <td><?= $sla_level->sla_notification_level_id ?></td>
                                            <td><?= $sla_level->ticket_delay_duration ?> hrs</td>
                                            <td class="link" id="users/level_profile/<?= urlencode(base64_encode($sla_level->user_category_id)) ?>">
                                                <?= $this->users_model->get_category($sla_level->user_category_id)->user_category_name ?>
                                            </td>
                                            <td class="link" id="profile/user/<?= urlencode(base64_encode($sla_level->reg_staff)) ?>">
                                                <?= $this->users_model->user($sla_level->reg_staff)->user_name ?>
                                            </td>
                                            <td>
                                                <a href="<?= site_url('sla/remove_level/' . urlencode(base64_encode($sla_level->sla_notification_level_id))) ?>"
                                                   class="btn btn-xs green trash">
                                                    recover
                                                </a>
                                            </td>
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
        <div class="col l4 hide-on-med-and-down">
            <div class="row">
                <ul id="task-card" class="col s12 collection with-header">
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s6 center">
                                <h5 class="red-text">
                                    <strong>
                                        <?= count($escalations) ?>
                                    </strong>
                                </h5>
                                <p class="medium">Escalation Levels</p>
                            </div>
                            <div class="col s6 center">
                                <h5 class="green-text">
                                    <strong>
                                        <?= count($tickets) ?>
                                    </strong>
                                </h5>
                                <p class="medium">Ticket</p>
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
                                Name
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $details->sla_name ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i>
                                Site maintenace after
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $details->preventative_after ?> months
                            </div>
                        </div>
                    </li>

                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i> Registered Date
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $details->reg_date ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1"><i class="mdi-editor-mode-edit"></i> Registered by
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align link"
                                 id="profile/user/<?= urlencode(base64_encode($details->reg_staff)) ?>">
                                <?= $this->users_model->user($details->reg_staff)->user_name ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="new-escalation" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection with-header">
            <li class="collection-header">
                <h5 class="orange-text">Create escalation level</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('sla/new_escalation') ?>">
                    <input type="hidden" value="<?= $details->sla_id ?>" name="sla_id"/>
                    <div class="row">
                        <div class="input-field col s12">
                            <select class="chosen-select browser-default" data-placeholder="Escalate to" name="user_category_id"
                                    id="user_category_id">
                                <option value="" disabled>--Escalate to--</option>
                                <?php
                                foreach ($this->crud_model->get_records("user_category") as $users) {
                                    printf('<option value="%d" %s>%s</option>', $users->user_category_id, (set_value('user_category_id') == $users->user_category_id ? 'selected' : ''), $users->user_category_name);
                                }
                                ?>
                            </select>
                            <label for="user_category_id" class="active">Escalate to</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="number" name="ticket_delay_duration" id="duration" required>
                            <label for="duration">Escalate after (HRS)</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="about" name="escalation_message"
                                      class="materialize-textarea"><?= set_value('escalation_message') ?></textarea>
                            <label class="active" for="about">Escalation message</label>
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

<div id="edit" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection with-header">
            <li class="collection-header">
                <h5 class="orange-text">Edit <?= $page ?> details</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('sla/update') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="hidden" name="sla_id" value="<?= $details->sla_id ?>"/>
                            <input id="first_name" name="sla_name" value="<?= $details->sla_name ?>" type="text" required>
                            <label for="first_name" class="active">SLA Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="first_name" name="preventative" min="1" max="12" value="<?= $details->preventative_after ?>" type="number"
                                   required>
                            <label for="first_name" class="active">Site Maintenance after every (months)</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="about" name="sla_description" class="materialize-textarea"><?= $details->sla_description ?></textarea>
                            <label class="active" for="about">SLA Description</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn orange waves-effect waves-light right" type="submit">Update
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>