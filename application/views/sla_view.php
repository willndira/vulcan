<div class="row">
    <div class="col s12 m5">
        <h5 class="grey-text">
            <h5 class="card-title grey-text">
                <i class="mdi-navigation-chevron-right"></i>
                <?= $page ?>
            </h5>
        </h5>
    </div>
    <div class="col s12 m7">
        <button class="btn green right  modal-trigger" href="#new-sla">New SLA</button>
        <button class="btn green right hide-on-large-only  modal-trigger" href="#new-escalation">New escalation</button>
    </div>
    <div class="col m12 l8">
        <div class="row">
            <div class="col s12">
                <p class="medium">Service level Agreement configurations</p>
            </div>
            <div class="col s12">
                <h5 class="card-title  orange-text">
                    <i class="mdi-navigation-chevron-right"></i>
                    Registered <?= $page ?>
                </h5>
                <ul class="collection with-header">
                    <li class="collection-item">
                        <table class="responsive-table dt display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Maintenance after</th>
                                <th>Reg date</th>
                                <th>Reg Staff</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($this->sites_model->get_sla() as $sla) { ?>
                                <tr class="link" id="sla/profile/<?= urlencode(base64_encode($sla->sla_id)) ?>">
                                    <td><?= $sla->sla_id ?></td>
                                    <td><?= $sla->sla_name ?></td>
                                    <td><?= $sla->sla_description ?></td>
                                    <td><?= $sla->preventative_after ?> months</td>
                                    <td><?= $sla->reg_date ?></td>
                                    <td class="link" id="profile/user/<?= urlencode(base64_encode($sla->reg_staff)) ?>">
                                        <?= $this->users_model->user($sla->reg_staff)->user_name ?>
                                    </td>
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
                        <div class="collapsible-header active" style="padding: 10px;">
                            <span class="blue-text ">Escalation levels</span>
                        </div>
                        <div class="collapsible-body" style=" padding: 20px 10px;">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>SLA</th>
                                    <th>Delay</th>
                                    <th>Notified group</th>
                                    <th>Reg Staff</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($this->sites_model->escalation_levels() as $sla_level) { ?>
                                    <tr>
                                        <td><?= $sla_level->sla_notification_level_id ?></td>
                                        <td><?= $this->sites_model->sla($sla_level->sla_id)->sla_name ?></td>
                                        <td><?= $sla_level->ticket_delay_duration ?> hrs</td>
                                        <td class="link" id="users/level_profile/<?= urlencode(base64_encode($sla_level->user_category_id)) ?>">
                                            <?= $this->users_model->get_category($sla_level->user_category_id)->user_category_name ?>
                                        </td>
                                        <td class="link" id="profile/user/<?= urlencode(base64_encode($sla_level->reg_staff)) ?>">
                                            <?= $this->users_model->user($sla_level->reg_staff)->user_name ?>
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
                <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                    <li>
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="red-text ">Trashed <?= $page ?></span>
                        </div>
                        <div class="collapsible-body" style=" padding: 20px 10px;">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Maintenance after</th>
                                    <th>Reg date</th>
                                    <th>Reg Staff</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($this->sites_model->get_sla(true) as $sla) { ?>
                                    <tr class="link" id="sla/profile/<?= urlencode(base64_encode($sla->sla_id)) ?>">
                                        <td><?= $sla->sla_id ?></td>
                                        <td><?= $sla->sla_name ?></td>
                                        <td><?= $sla->sla_description ?></td>
                                        <td><?= $sla->preventative_after ?></td>
                                        <td><?= $sla->reg_date ?></td>
                                        <td class="link" id="profile/user/<?= urlencode(base64_encode($sla->reg_staff)) ?>">
                                            <?= $this->users_model->user($sla->reg_staff)->user_name ?>
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
        <h5 class="card-title  orange-text">
            <i class="mdi-content-add"></i>
            Create escalation level
        </h5>
        <ul class="collection with-header">
            <li class="collection-item">
                <form method="post" action="<?= site_url('sla/new_escalation') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <select class="chosen-select browser-default" data-placeholder="SLA category" name="sla_id" id="sla_id">
                                <option value="" selected disabled>--select SLA category--</option>
                                <?php
                                foreach ($this->crud_model->get_records("sla") as $sla) {
                                    printf('<option value="%d" %s>%s</option>', $sla->sla_id, (set_value('sla_id') == $sla->sla_id ? 'selected' : ''), $sla->sla_name);
                                }
                                ?>
                            </select>
                            <label for="sla_id" class="active">SLA categoty</label>
                        </div>
                        <div class="input-field col s12">
                            <select class="chosen-select browser-default" data-placeholder="Escalate to" name="user_category_id"
                                    id="user_category_id">
                                <option value="" selected disabled>--Escalate to--</option>
                                <?php
                                foreach ($this->crud_model->get_records("user_category") as $users) {
                                    printf('<option value="%d" %s>%s</option>', $users->user_category_id, (set_value('user_category_id') == $users->user_category_id ? 'selected' : ''), $users->user_category_name);
                                }
                                ?>
                            </select>
                            <label for="user_category_id" class="active">Escalate to</label>
                        </div>
                        <div class="input-field col s12">
                            <input type="number" name="ticket_delay_duration" id="duration" required>
                            <label for="duration">Escalate after (HRS)</label>
                        </div>
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


<div id="new-sla" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection with-header">
            <li class="collection-header">
                <h5 class="orange-text">Create <?= $page ?></h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('sla/add') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="first_name" name="sla_name" value="<?= set_value('sla_name') ?>" type="text" required>
                            <label for="first_name">SLA Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="first_name" name="preventative" min="1" max="12" value="<?= set_value('preventative') ?>" type="number"
                                   required>
                            <label for="first_name">Site Maintenance after every (months)</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="about" name="sla_description" class="materialize-textarea"><?= set_value('sla_description') ?></textarea>
                            <label class="active" for="about">SLA Description</label>
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
<div id="new-escalation" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection with-header">
            <li class="collection-header">
                <h5 class="orange-text">Create escalation level</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('sla/new_escalation') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <select class="chosen-select browser-default" data-placeholder="SLA category" name="sla_id" id="sla_id">
                                <option value="" disabled>--select SLA category--</option>
                                <?php
                                foreach ($this->crud_model->get_records("sla") as $sla) {
                                    printf('<option value="%d" %s>%s</option>', $sla->sla_id, (set_value('sla_id') == $sla->sla_id ? 'selected' : ''), $sla->sla_name);
                                }
                                ?>
                            </select>
                            <label for="sla_id" class="active">SLA categoty</label>
                        </div>
                    </div>
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