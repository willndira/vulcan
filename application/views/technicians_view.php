<div class="row">
    <div class="col s12 m8">
        <div class="row">
            <div class="col s12">
                <h5 class="task-card-title grey-text">
                    <i class="mdi-navigation-chevron-right"></i>
                    <?= $page ?>
                </h5>
                <h6 class="medium green-text">
                    Help desk technicians
                </h6>
            </div>
            <div class="col s12">
                <ul class="collection ">
                    <li class="collection-item">
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="orange-text">Registered technicians</span>
                        </div>
                        <table class="responsive-table dt display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Device</th>
                                <th>Last update</th>
                                <th>Assigned</th>
                                <th>Registered on</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($this->users_model->technicians() as $technician) { ?>
                                <tr class="link" id="technicians/profile/<?= urlencode(base64_encode($technician->technician_id)) ?>">
                                    <td><?= $technician->technician_id ?></td>
                                    <td><?= $technician->user_name ?></td>
                                    <td><?= $technician->user_email ?></td>
                                    <td><?= $technician->device_id ?></td>
                                    <td><?= $technician->last_update ?></td>
                                    <td><?= $this->users_model->is_assigned($technician->technician_id) ? "Assigned" : "Available" ?></td>
                                    <td><?= $technician->reg_date ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col s12 m4">
        <div class="row">
            <div class="col s12" style="margin-top: 20px;">
                <a class="btn green right modal-trigger" href="#new-tech">
                    <i class="mdi-content-add"></i> New technician
                </a>
            </div>
            <div class="col s12">
                <ul class="collection">
                    <li class="collection-item">
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="orange-text">Available</span>
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

<div id="new-tech" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Register technician</h5>
            </li>
            <li class="collection-item">

                <form method="post" action="<?= site_url('technicians/register') ?>">

                    <div class="row">
                        <div class="input-field col s12">
                            <select id="device_user" name="user_id">
                                <option value="" selected disabled>--Staff--</option>
                                <?php
                                foreach ($this->users_model->has_powers('resolve_ticket') as $user) {
                                    if ($this->users_model->is_tech($user->user_id) == 0)
                                        echo '<option value="' . $user->user_id . '">' . $user->user_name . '</option>';
                                }
                                ?>
                            </select>
                            <label for="device_user">Staff</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="priority" multiple name="specialisation[]">
                                <option value="" disabled selected>--Select issue type--</option>
                                <option <?= set_value('specialisation') == 1 ? 'selected' : '' ?> value="1">Power</option>
                                <option <?= set_value('specialisation') == 2 ? 'selected' : '' ?> value="2">Network</option>
                                <option <?= set_value('specialisation') == 3 ? 'selected' : '' ?> value="3">Hardware</option>
                                <option <?= set_value('specialisation') == 4 ? 'selected' : '' ?> value="4">Software</option>
                                <option <?= set_value('specialisation') == 5 ? 'selected' : '' ?> value="5">Other</option>
                            </select>
                            <label for="priority">Specialisation</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="device_id" type="number" name="device_imei" required>
                            <label for="device_imei">Device IMEI</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <?php
                            if ($this->users_model->requires_role(array('man_technicians'))
                            ) {
                                ?>
                                <button class="btn orange waves-effect waves-light right" type="submit">
                                    Register technician
                                    <i class="mdi-content-send right"></i>
                                </button>
                                <?php
                            } else {
                                ?>
                                <div id="card-alert" class="card red">
                                    <div class="card-content white-text">
                                        <p>DENIED : Sorry. No enough permissions register a technician.</p>
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
<!---->