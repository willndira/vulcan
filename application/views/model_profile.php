<div class="section">
    <?php
    $model = $details;
    $items = $this->crud_model->get_records('items', 'model_id', $model->item_model_id);
    ?>
    <div class="row">
        <div class="col s12 m8">
            <div class="row">
                <div class="col s12">
                    <h5 class="card-title grey-text text-darken-4">
                        <i class="mdi-navigation-chevron-right"></i>
                        <?= $model->make_name . ' ' . $model->model_name ?>
                    </h5>
                    <p class="medium green-text"><?= $model->it_name ?></p>
                </div>
                <div class="col s12">
                    <ul class="collection">
                        <li class="collection-item">
                            <h6 class="chart-title orange-text">
                                Items
                            </h6>
                        </li>
                        <li class="collection-item">
                            <table class="responsive-table dt" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Serial No.</th>
                                    <th>Location.</th>
                                    <th>Reg on</th>
                                    <th>Status</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php
                                foreach ($items as $item) {
                                    ?>
                                    <tr class="link" id="items/profile/<?= urlencode(base64_encode($item->item_id)) ?>">
                                        <td>KAPS-<?= $item->item_id ?></td>
                                        <td class="capitalize"><?= $item->item_code ?></td>
                                        <td class="capitalize"><?= $item->item_serial_no ?></td>
                                        <td><?= $this->equipment_model->current_location($item->item_id, 1) ?></td>
                                        <td><?= $item->item_added_time ?></td>
                                        <td>
                                            <?= $item->item_condition ? '<span class="green-text"> Functional</span>' : '<span class="orange-text"> Faulty</span>' ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
                <div class="col s12">
                    <ul class="collection">
                        <li class="collection-item">
                            <h6 class="chart-title orange-text">
                                Edit details
                            </h6>
                        </li>
                        <li class="collection-item">
                            <form method="post" action="<?= site_url('items/cat_update') ?>">
                                <input type="hidden" name="model_id" value="<?= $model->item_model_id ?>">

                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="type" type="text" name="type"
                                               value="<?= $model->it_name ?>" required/>
                                        <label for="type" class="active">Type e.g Smart phone</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="make" type="text" name="make"
                                               value="<?= $model->make_name ?>" required/>
                                        <label for="make" class="active">Make e.g Sony</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="model" type="text" name="model"
                                               value="<?= $model->model_name ?>" required/>
                                        <label for="model" class="active">Model e.g Xperia T2</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="model" type="text" name="model_est_cost"
                                               value="<?= $model->model_est_cost ?>" required/>
                                        <label for="model" class="active">Estimated Cost</label>
                                    </div>

                                    <div class="input-field col s12">
                                        <textarea name="description" class="materialize-textarea"
                                                  required><?= $details->model_description ?></textarea>
                                        <label for="model" class="active">Item description and its uses</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <?php
                                        if ($this->users_model->requires_role(array('edit_item'))) {
                                            ?>
                                            <button class="btn orange waves-effect waves-light right"
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
        </div>
        <div class="col s12 m4">

            <?php
            if ($this->users_model->requires_role(array('edit_item'))) {
                ?>
                <div class="row">
                    <div class="col s12">
                        <button class="btn ajax orange right"
                                value="items/trash/<?= urlencode(base64_encode($details->item_model_id)) . "/" . !$details->deleted ?>">
                            <?php if ($details->deleted) {
                                echo '<i class="mdi-action-restore"></i> Restore';
                            } else {
                                echo '<i class="mdi-action-delete"></i> Trash';
                            } ?>
                        </button>
                    </div>
                </div>
            <?php } ?>
            <!-- Profile About  -->
            <div class="card" style="background: transparent !important; border-radius: 5px; border: 1px solid #e0e0e0">
                <div class="card-content center">
                    <p>
                        <?= $model->model_description ?>
                    </p>
                </div>
            </div>
            <div class="card center" style="background: transparent !important; border-radius: 5px; border: 2px solid #0f9d58; max-height: 300px">
                <img class="responsive-img" height="300" src='<?= base_url("assets/images/KAPS-logo.png") ?>'>
            </div>
            <!-- Profile About  -->
            <!-- Profile About  -->
            <ul id="task-card" class="collection with-header">
                <li class="collection-header">
                    <h5 class="task-card-title orange-text">Details</h5>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i> Category
                        </div>
                        <div class="col s7 grey-text text-darken-4 right-align">
                            <?= $model->it_name ?>
                        </div>
                    </div>
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
                        <div class="col s5 grey-text darken-1"><i class="mdi-action-dashboard"></i> Registered By
                        </div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align"><?= $this->users_model->user($model->model_added_by)->user_name ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s6 grey-text darken-1"><i class="mdi-editor-mode-edit"></i>
                            Registration Date
                        </div>
                        <div
                            class="col s6 grey-text text-darken-4 right-align">
                            <?= $model->model_adding_time ?>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>