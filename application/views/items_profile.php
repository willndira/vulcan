<div class="section">
    <!-- profile-page-header -->
    <?php
    $model = $this->items_model->get_model_details($details->model_id);
    ?>
    <div class="row">
        <!-- profile-page-sidebar-->
        <div class="col s12 m8">
            <div class="col s12">
                <h5 class="card-title grey-text text-darken-4"><?= $model->model_name . ": " . $details->item_code ?></h5>
                <p class="medium green-text"> <?= $model->it_name ?></p>
            </div>
            <ul class="collection">
                <li class="collection-item">
                    <h6 class=" orange-text">
                        <i class="mdi-image-timelapse"></i> Item logs
                    </h6>
                </li>
                <li class="collection-item" id="stats">
                    <table class="responsive-table dt" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Time</th>
                            <th>Staff</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($this->crud_model->get_records('item_timeline', 'item_id', $details->item_id) as $activity) {
                            ?>
                            <tr>
                                <td><?= $activity->time ?></td>
                                <td>
                                    <?= null != $activity->activity_by ? $this->users_model->user($activity->activity_by)->user_name : 'DEFAULT' ?>
                                </td>
                                <td><?= $activity->item_activity ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </li>
            </ul>
        </div>
        <!-- profile-page-sidebar-->
        <div class="col s12 m4">
            <div class="row">
                <div class="col s12">
                    <button class="btn ajax orange right"
                            value="items/trash/<?= urlencode(base64_encode($details->item_id)) . "/" . !$details->deleted ?>">
                        <?php if ($details->deleted) {
                            echo '<i class="mdi-action-restore"></i> Restore';
                        } else {
                            echo '<i class="mdi-action-delete"></i> Trash';
                        } ?>
                    </button>
                    <a href="#edit" class="btn blue right modal-trigger">
                        <i class="mdi-editor-border-color"></i> Edit
                    </a>
                </div>
            </div>
            <ul id="task-card" class="collection with-header">
                <li class="collection-item">
                    <div class="row">
                        <div class="col s12 center">
                            <h5 class="green-text">
                                <strong>
                                    0 %
                                </strong>
                            </h5>
                            <p class="medium">Failed Rate</p>
                        </div>
                    </div>
                </li>
            </ul>
            <ul id="task-card" class="collection with-header">
                <li class="collection-header">
                    <h5 class="task-card-title blue-text"><strong>Details</strong></h5>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i>
                            Make
                        </div>
                        <div class="col s7 grey-text text-darken-4 right-align">
                            <?= $model->make_name ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-communication-location-on"></i> Model</div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align"><?= $model->model_name ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-action-dashboard"></i> Code
                        </div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align"><?= $details->item_code ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-editor-mode-edit"></i> Serial No.
                        </div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align">
                            <?= $details->item_serial_no ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-editor-mode-edit"></i>Condition</div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align">
                            <?= $details->item_condition ? "Functional" : "Faulty" ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-editor-mode-edit"></i>Location</div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align">
                            <?= $this->equipment_model->current_location($details->item_id, 1) ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-editor-mode-edit"></i> Registration Date
                        </div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align">
                            <?= $details->item_added_time ?>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>


<div id="edit" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h6 class="orange-text center">Edit <?= $model->model_name . ": " . $details->item_code ?></h6>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('items/update') ?>">
                    <input type="hidden" name="item_id" value="<?= $details->item_id ?>"/>

                    <div class="row">
                        <div class="input-field col s12">

                            <select id="model" name="model" required>
                                <option>Select Model</option>
                                <?php
                                foreach ($this->crud_model->get_records('item_models') as $type) {
                                    ?>
                                    <option
                                        <?php
                                        $models = $this->items_model->get_model_details($type->item_model_id);
                                        if ($details->model_id == $type->item_model_id)
                                            echo 'selected';
                                        ?>
                                        value="<?= $type->item_model_id ?>"><?= $models->it_name . ' ' . $models->make_name . ' ' . $models->model_name ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <label for="model">Model e.g Xperia T2</label>
                        </div>

                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="email" type="text" value="<?= $details->item_code ?>"
                                   name="code" required>
                            <label class="active" for="email">Item Code</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="phone" type="text"
                                   value="<?= $details->item_serial_no ?>" name="serial_no"
                                   required>
                            <label class="active" for="phone">Serial No</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <?php
                            if ($this->users_model->requires_role(array('edit_item'))) {
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
                                        <p>DENIED : Sorry. No enough permissions to edit item
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

