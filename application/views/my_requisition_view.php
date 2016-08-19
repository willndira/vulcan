<div class="row">
    <div class="col s12 m8 l8">

        <div class="card-panel">
            <div id="table-datatables">
                <h4 class="header">My requsitions</h4>

                <div class="row">
                    <div class="col s12 m12 l12">
                        <table id="data-table-simple" class="responsive-table display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Request time</th>
                                <th>Item Type</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Units</th>
                                <th>Purpose</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($this->crud_model->get_records('requisitions', 'requisition_officer', $this->users_model->user()->user_id) as $item) {
                                ?>
                                <tr id="procurements/profile/<?= urlencode(base64_encode($item->requisition_id)) ?>"
                                    class="link">
                                    <td><?= $item->requisition_id ?></td>
                                    <td><?= $item->requisition_time ?></td>
                                    <td><?= $this->items_model->get_model_details($item->item_model_id)->it_name ?></td>
                                    <td><?= $this->items_model->get_model_details($item->item_model_id)->make_name ?></td>
                                    <td><?= $this->items_model->get_model_details($item->item_model_id)->model_name ?></td>
                                    <td><?= $item->requisition_units ?></td>
                                    <td><?= $item->requisition_purpose ?></td>
                                    <td><?= $this->items_model->requisition_status($item->requisition_id) ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col s12 m4 l4">
        <div class="card-panel">
            <h4 class="header2"><?= $edit ? 'Edit Requisition' : 'Request an item' ?></h4>

            <div class="row">
                <form class="col s12" method="post"
                      action="<?= site_url('requisitions/request') ?>">
                    <div class="row">
                        <div class="input-field col s12 model">
                            <select id="model" name="model" required>
                                <option value="" selected disabled>Select Model</option>
                                <?php
                                foreach ($this->crud_model->get_records('item_models') as $type) {
                                    $model = $this->items_model->get_model_details($type->item_model_id);
                                    ?>
                                    <option value="<?= $type->item_model_id ?>">
                                        <?= $model->it_name . ' - ' . $model->make_name . ' ' . $type->model_name ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                            <label for="model">Item Model to request</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 model">
                            <input id="units" value="<?= $edit ? $requisition->requisition_units : '' ?>" name="units"
                                   type="text" required>
                            <label for="units">Number of units</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="about" name="reason"
                                      class="materialize-textarea"><?= $edit ? $requisition->requisition_purpose : '' ?></textarea>
                            <label for="about">Purpose</label>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <button class="btn cyan waves-effect waves-light right" type="submit"
                                        name="action"><?= $edit ? 'Update' : 'Request' ?>
                                    <i class="mdi-content-send right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>