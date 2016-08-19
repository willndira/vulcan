<div class="row">
    <div class="col s12 m8">

        <div class="card-panel">
            <div id="table-datatables">
                <h4 class="header"><?= $page ?></h4>

                <div class="row">
                    <div id="all_items" class="col s12 m12 l12">
                        <table class="responsive-table display dt" cellspacing="0">
                            <thead>
                            <tr>
                                <th>TIME</th>
                                <th>STORE</th>
                                <th>ITEM CODE</th>
                                <th>ITEM SERIAL NO</th>
                                <th>MODEL</th>
                                <th>RECEIVED BY</th>
                                <th>STATE</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($this->crud_model->get_records('store_items', 'si_available', true) as $item) {
                                ?>
                                <tr>
                                    <td><?= $item->time ?></td>
                                    <td><?= $this->stores_model->specific($item->store_id)->store_name ?></td>
                                    <td><?= $this->items_model->details($log->item_id)->item_code ?></td>
                                    <td><?= $this->items_model->details($log->item_id)->item_serial_no ?></td>
                                    <td>
                                        <?php
                                        $model = $this->items_model->get_model_details($this->items_model->details($log->item_id)->model_id);
                                        echo $model->make_name . ' ' . $model->model_name;
                                        ?>
                                    </td>
                                    <td><?= $this->users_model->user($item->received_by)->user_name ?></td>
                                    <td><?= $this->items_model->last_state($log->item_id)->item_state ? '<span class="green-text">FUNCTIONAL</span>' : '<span class="orange-text">DEFECTIVE</span>' ?></td>
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
    <div class="col s12 m4">
        <div class="card-panel">
            <h4 class="header">Register Item</h4>

            <div class="row">
                <form class="col s12" method="post" action="<?= site_url('items/register') ?>">
                    <div class="row">
                        <div class="input-field col s6">
                            <select id="item_type" name="type" required>
                                <option>Select Type</option>
                                <?php
                                foreach ($this->crud_model->get_records('item_types') as $type) {
                                    ?>
                                    <option value="<?= $type->it_id ?>"><?= $type->it_name ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <label for="item_type">Type e.g Smart phone</label>
                        </div>
                        <div class="input-field col s6">
                            <select id="myMake" name="make" required>
                                <option>Select Make</option>
                                <?php
                                foreach ($this->crud_model->get_records('item_make') as $type) {
                                    ?>
                                    <option value="<?= $type->make_id ?>"><?= $type->make_name ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <label for="myMake">Make e.g Sony</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 model">
                            <select id="model" name="model" required>
                                <option>Select Model</option>
                                <?php
                                foreach ($this->crud_model->get_records('item_models') as $type) {
                                    ?>
                                    <option value="<?= $type->item_model_id ?>"><?= $type->model_name ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <label for="model">Model e.g Xperia T2</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 type">
                            <input id="serial_no" type="text" name="serial_no" required/>
                            <label for="serial_no">Serial No.</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 make">
                            <input id="code" type="text" name="code" required/>
                            <label for="code">Unique Code</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 model">
                            <input type="hidden" name="location" value="IN STORE" required/>
                            <select id="store_id" name="store_id" required>
                                <option value="" selected disabled>Store Location</option>
                                <?php foreach ($this->crud_model->get_records('stores') as $store) { ?>
                                    <option value="<?= $store->store_id ?>"><?= $store->store_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="store_id"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <select id="item_location" name="state" required>
                                <option>Item State</option>
                                <option value="1">Functional</option>
                                <option value="0">Defective</option>
                            </select>
                            <label for="item_location">Current Item location</label>
                        </div>
                        <div class="input-field col s6">
                            <select id="available" name="available" required>
                                <option>Availability</option>
                                <option value="1">Available</option>
                                <option value="0">Not Available</option>
                            </select>
                            <label for="available">Item Availability</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn cyan waves-effect waves-light right" type="submit"
                                    name="action">Register Item
                                <i class="mdi-editor-mode-edit right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>