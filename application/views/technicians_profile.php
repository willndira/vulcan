<div class="section">
    <div class="row">
        <div class="col s12 m5">
            <h5 class="grey-text">
                <i class="mdi-navigation-chevron-right"></i>
                <?= $page ?>
            </h5>
        </div>
        <div class="col s12 m7">
            <button class="btn ajax red right"
                    value="technicians/trash/<?= urlencode(base64_encode($details->technician_id)) . "/" . !$details->deleted ?>">
                <?= $details->deleted ? 'Restore' : 'Trash'; ?>
            </button>
            <a href="#edit-technician" class="btn modal-trigger blue right">
                Edit
            </a>
        </div>
        <div class="col s12 l8">
            <div class="row">
                <div class="col s12">
                    <h5 class="card-title  orange-text text-darken-4">
                        <i class="mdi-navigation-chevron-right"></i>
                        <?= $staff_details->user_name ?>
                    </h5>
                </div>
                <div class="col s12">
                    <ul class="collection">
                        <li class="collection-item">
                            <h6 class="card-title text-darken-4 orange-text">
                                <i class="mdi-action-bug-report class green-text"></i>
                                Assigned Tickets
                            </h6>
                        </li>
                        <li class="collection-item">
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
                                <?php foreach ($this->tickets_model->my_tickets($details->user_id) as $ticket) { ?>
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
                    <ul class="collection ">
                        <li class="collection-item">
                            <h6 class="card-title text-darken-4 orange-text">
                                <i class="mdi-action-receipt green-text"></i>
                                Log trails</h6>
                        </li>
                        <li class="collection-item" id="tl">

                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col l4 hide-on-med-and-down">
            <div class="row">
                <!-- Profile About Details  -->
                <ul class="collection col s12">
                    <li class="collection-item">
                        <h6 class="chart-title orange-text">Details</h6>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1">
                                <i class="mdi-action-wallet-travel"></i>
                                Name
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $staff_details->user_name ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1">
                                <i class="mdi-av-timer"></i>
                                Email
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $staff_details->user_email ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1">
                                <i class="mdi-image-timelapse"></i>
                                Phone
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $staff_details->user_phone ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s5 grey-text darken-1">
                                <i class="mdi-action-verified-user"></i>
                                Device IMEI
                            </div>
                            <div class="col s7 grey-text text-darken-4 right-align">
                                <?= $details->device_id ?>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s6 grey-text darken-1"><i class="mdi-action-verified-user"></i>
                                Last seen
                            </div>
                            <div class="col s6 grey-text text-darken-4 right-align">
                                <?= $details->last_update ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div id="edit-technician" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Edit <?= $staff_details->user_name ?> Details</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('technicians/update') ?>">
                    <input type="hidden" value="<?= $details->technician_id ?>" name="technician_id" />
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="priority" multiple name="specialisation[]" class="browser-default chosen-select"
                                    data-placeholder="Select specialisation">
                                <option value="" disabled>--Select issue type--</option>
                                <option <?= set_value('specialisation') == 1 ? 'selected' : '' ?> value="1">Power</option>
                                <option <?= set_value('specialisation') == 2 ? 'selected' : '' ?> value="2">Network</option>
                                <option <?= set_value('specialisation') == 3 ? 'selected' : '' ?> value="3">Hardware</option>
                                <option <?= set_value('specialisation') == 4 ? 'selected' : '' ?> value="4">Software</option>
                                <option <?= set_value('specialisation') == 5 ? 'selected' : '' ?> value="5">Other</option>
                            </select>
                            <label for="priority" class="active">Specialisation</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="device_id" value="<?= $details->device_id ?>" type="number" name="device_imei" required>
                            <label for="device_id">Device IMEI</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn orange waves-effect waves-light right" type="submit">
                                Update
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>