<?php $is_manager = (count($man) > 0 ? ($man->user_id == $this->users_model->user()->user_id) : false); ?>
<div class="section">
    <div class="row">
        <?php if (!$is_manager) { ?>
            <div class="col s12">
                <span class="red-text">Kindly note you are not the store manager. So you can't perform any store activity</span>
            </div>
        <?php } ?>
        <div class="col s12 m8">
            <div class="row">
                <div class="col s12">
                    <h5 class="card-title grey-text">
                        <i class="mdi-navigation-chevron-right"></i>
                        <?= $page ?>
                    </h5>
                </div>
                <div class="col s12">
                    <ul class="collection">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s6">
                                    <h5 class="task-card-title orange-text">Assets currently in stock</h5>
                                </div>
                                <div class="col s6">
                                    <ul class="tabs tab-profile">
                                        <li class="tab col s4">
                                            <a class=" waves-effect waves-light green-text active" href="#items">
                                                <i class="mdi-image-timelapse"></i> Available</a>
                                        </li>
                                        <li class="tab col s4">
                                            <a class=" waves-effect waves-light green-text" href="#supplied">
                                                <i class="mdi-image-timelapse"></i> New</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item tab-content" id="items">
                            <table class="dt display responsive-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Item code</th>
                                    <th>Model</th>
                                    <th>Serial No</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($items = $this->asset_model->assigned($details->store_type, 1, 1, 1) as $item) { ?>
                                    <tr>
                                        <td><?= $item->asset_location_id ?></td>
                                        <td><span class="green-text"><b>Item</b></span></td>
                                        <?php
                                        $item_details = $this->items_model->details($item->asset_id);
                                        $model = $this->items_model->get_model_details($item_details->model_id);
                                        ?>
                                        <td><?= $item_details->item_code ?></td>
                                        <td>
                                            <?= $model->make_name . ' ' . $model->model_name ?>
                                        </td>
                                        <td><?= $item_details->item_serial_no ?></td>
                                        <td><?php $item->asset_id ?></td>
                                    </tr>
                                <?php }
                                foreach ($equipment = $this->asset_model->assigned($details->store_type, 1, 2, 1) as $equipment) {
                                    $item_details = $this->equipment_model->details($equipment->asset_id);
                                    $model = $this->components_model->details($item_details->component_id); ?>
                                    <tr>
                                        <td><?= $equipment->asset_location_id ?></td>
                                        <td><span class="orange-text"><b>Equipment</b></span></td>
                                        <td><?= $item_details->equipment_no ?></td>
                                        <td>
                                            <?= $model->component_name ?>
                                        </td>
                                        <td><?= $this->equipment_model->stage($item_details->equipment_stage) ?></td>
                                        <td><?= $item_details->equipment_condition ? '<span class="green-text">Functional</span>' : '<span class="orange-text">Faulty</span>' ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                        <li class="collection-item tab-content" id="supplied">
                            <table class="dt display responsive-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Asset No</th>
                                    <th>Model</th>
                                    <th>Status</th>
                                    <th>Stage</th>
                                    <th width="5%"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($items = $this->asset_model->assigned($details->store_type, 1, 1, 0) as $item) { ?>
                                    <tr>
                                        <td><?= $item->asset_location_id ?></td>
                                        <?php
                                        $item_details = $this->items_model->details($item->asset_id);
                                        $model = $this->items_model->get_model_details($item_details->model_id);
                                        ?>
                                        <td><?= $item_details->item_code ?></td>
                                        <td>
                                            <?= $model->make_name . ' ' . $model->model_name ?>
                                        </td>
                                        <td><?= $item_details->item_serial_no ?></td>
                                        <td><?= $item_details->item_condition ? '<span class="green-text">Functional</span>' : '<span class="orange-text">Faulty</span>' ?></td>
                                        <td>
                                            <?php if ($is_manager) { ?>
                                                <button class="btn green lighten-1 ajax"
                                                        value="assets/collect/<?= urlencode(base64_encode($item->asset_location_id)) ?>">
                                                    collect
                                                </button>
                                            <?php } else {
                                                echo '<span class="orange-text">not confirmed</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php }
                                foreach ($equipment = $this->asset_model->assigned($details->store_type, 1, 2, 0) as $equipment) {
                                    $item_details = $this->equipment_model->details($equipment->asset_id);
                                    $model = $this->components_model->details($item_details->component_id); ?>
                                    <tr>
                                        <td><?= $equipment->asset_location_id ?></td>
                                        <td><?= $item_details->equipment_no ?>(Equipment)</td>
                                        <td>
                                            <?= $model->component_name ?>
                                        </td>
                                        <td><?= $this->equipment_model->stage($item_details->equipment_stage) ?></td>
                                        <td><?= $item_details->equipment_condition ? '<span class="green-text">Functional</span>' : '<span class="orange-text">Faulty</span>' ?></td>
                                        <td>
                                            <?php if ($is_manager) { ?>
                                                <button class="btn green lighten-1 ajax"
                                                        value="assets/collect/<?= urlencode(base64_encode($equipment->asset_location_id)) ?>">
                                                    collect
                                                </button>
                                            <?php } else {
                                                echo '<span class="orange-text">not confirmed</span>';
                                            }
                                            ?>
                                        </td>
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
                            <div class="row">
                                <div class="col s5">
                                    <h5 class="task-card-title orange-text">Requested assets</h5>
                                </div>
                                <div class="col s7">
                                    <ul class="tabs tab-profile">
                                        <li class="tab col s4">
                                            <a class=" waves-effect waves-light green-text active" href="#r-items">
                                                <i class="mdi-image-timelapse"></i> Items</a>
                                        </li>
                                        <li class="tab col s4">
                                            <a class=" waves-effect waves-light green-text" href="#requested">
                                                <i class="mdi-image-timelapse"></i> Pending supply</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item tab-content" id="r-items">
                            <table class="dt display responsive-table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Done by</th>
                                    <th>Asset Model</th>
                                    <th>Asset type</th>
                                    <th>Qty</th>
                                    <th>Purpose</th>
                                    <th>Assigned</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->asset_model->store_requested(1, $details->store_type) as $item) { ?>
                                    <tr <?php if ($is_manager) { ?> class="link" id="requisitions/asset/<?= urlencode(base64_encode($item->request_id)) ?>" <?php } ?>>
                                        <td><?= $item->request_date ?></td>
                                        <td><?= $this->users_model->user($item->requesting_user)->user_name ?></td>
                                        <td>
                                            <?php
                                            $model = $this->items_model->get_model_details($item->model_id);
                                            echo $model->make_name . ' ' . $model->model_name;
                                            ?>
                                        </td>
                                        <td><span class="green-text"><b>Item</b></span></td>
                                        <td><?= $item->request_qty ?></td>
                                        <td><?= $item->purpose ?></td>
                                        <td><?= count($this->asset_model->all_assigned($item->request_category_id, $item->request_category, $item->request_asset_type)) ?></td>
                                    </tr>
                                <?php }
                                foreach ($this->asset_model->store_requested(2, $details->store_type) as $item) { ?>
                                    <tr <?php if ($is_manager) { ?> class="link" id="requisitions/asset/<?= urlencode(base64_encode($item->request_id)) ?>" <?php } ?> >
                                        <td><?= $item->request_date ?></td>
                                        <td><?= $this->users_model->user($item->requesting_user)->user_name ?></td>
                                        <td>
                                            <?php
                                            $model = $this->components_model->details($item->model_id);
                                            echo $model->component_name;
                                            ?>
                                        </td>
                                        <td><span class="blue-text"><b>Equipment</b></span></td>
                                        <td><?= $item->request_qty ?></td>
                                        <td><?= $item->purpose ?></td>
                                        <td><?= count($this->asset_model->all_assigned($item->request_category_id, $item->request_category, $item->request_asset_type)) ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                        <li class="collection-item tab-content" id="requested">
                            <table class="dt display responsive-table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Supplier</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach (array() as $item) {
                                    if ($details->store_type == $item->forwarded || $item->forwarded == 0) {
                                        ?>
                                        <tr class="link"
                                            id="requisitions/asset/<?= urlencode(base64_encode($item->item_request_id)) ?>">
                                            <td><?= $item->request_date ?></td>
                                            <td>
                                                <?php
                                                $model = $this->items_model->get_model_details($item->model_id);
                                                echo $model->make_name . ' ' . $model->model_name;
                                                ?>
                                            </td>
                                            <td><?= $item->request_qty ?></td>
                                            <td><?= $this->stores_model->specific($item->store_id)->store_name ?></td>
                                            <td>

                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>

                <div class="col s12">
                    <ul class="collection">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5">
                                    <h5 class="task-card-title orange-text">Details</h5>
                                </div>
                                <div class="col s7">
                                    <ul class="tabs tab-profile">
                                        <li class="tab col s3">
                                            <a class=" waves-effect waves-light" href="#stats">
                                                <i class="mdi-action-receipt"></i> Store Logs</a>
                                        </li>
                                        <li class="tab col s3">
                                            <a class="waves-effect waves-light" href="#edit">
                                                <i class="mdi-editor-border-color"></i> Details</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li id="stats" class="collection-item">
                            <table class="responsive-table display dt" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Staff</th>
                                    <th>Activity</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($this->crud_model->get_records('store_logs', 'store_id', $details->store_id) as $log) {
                                    ?>
                                    <tr>
                                        <td><?= $log->time ?></td>
                                        <td><?= $this->users_model->user($log->user_id)->user_name ?></td>
                                        <td><?= $log->activity ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </li>
                        <li id="edit" class="collection-item" style="padding-bottom: 50px !important;">
                            <form method="post" action="<?= site_url('stores/update') ?>">
                                <input type="hidden" name="store_id" value="<?= $details->store_id ?>"
                                       required/>

                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="store_name" name="store_name" type="text"
                                               value="<?= $details->store_name ?>" required>
                                        <label for="store_name" class="active">Store Name</label>
                                    </div>
                                    <input type="hidden" name="store_type" value="<?= $details->store_type ?>">
                                    <div class="input-field col s6">
                                        <input id="store_address" type="text" name="store_address"
                                               value="<?= $details->store_address ?>"
                                               required>
                                        <label for="store_address" class="active">Address</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <select id="manager" name="manager">
                                            <option value="" disabled selected>Choose Manager</option>
                                            <?php
                                            $type = $details->store_type;
                                            foreach ($this->users_model->has_powers($type == 1 ? 'man_main_store' : ($type == 2 ? 'man_assembly_store' : ($type == 3 ? 'man_install_store' : 'man_maintenance_store'))) as $manager) { ?>
                                                <option <?= $man ? ($man->user_id == $manager->user_id ? 'selected' : '') : ''; ?>
                                                    value="<?= $manager->user_id ?>"> <?= $manager->user_name ?></option>';
                                            <?php } ?>
                                        </select>
                                        <label for="manager">Manager</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <?php
                                        if ($this->users_model->requires_role(array('edit_stores'))
                                        ) {
                                            ?>
                                            <button class="btn orange waves-effect waves-light right"
                                                    type="submit"
                                                    name="action">Update
                                                <i class="mdi-content-send right"></i>
                                            </button>
                                            <?php
                                        } else {
                                            ?>
                                            <div id="card-alert" class="card red">
                                                <div class="card-content white-text">
                                                    <p>DENIED : Sorry. No enough permissions to edit this store
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
            <?php if ($is_manager) { ?>
                <div class="row">
                    <div class="col s12">
                        <?php if ($details->store_type == 1) { ?>
                            <a class="btn green right modal-trigger" href="#new-item">
                                New item
                            </a>
                        <?php } ?>
                        <?php if ($details->store_type == 2) { ?>
                            <a class="btn green right modal-trigger" href="#new-equipment">
                                New equipment
                            </a>
                        <?php } ?>
                        <a class="btn blue right modal-trigger" href="#request">
                            Request
                        </a>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col s12">
                    <ul id="task-card" class="collection with-header">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s12 center">
                                    <h6 class="grey-text">
                                        In stock
                                    </h6>
                                </div>
                                <div class="col s6 center">
                                    <h5 class="green-text">
                                        <strong>
                                            <?= count($items) ?>
                                        </strong>
                                    </h5>
                                    <p class="medium">Items</p>
                                </div>
                                <div class="col s6 center">
                                    <h5 class="green-text">
                                        <strong>
                                            <?= count($equipment) ?>
                                        </strong>
                                    </h5>
                                    <p class="medium">Equipment</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div id="task-card" class="col s12">
                    <ul class="collection with-header">
                        <li class="collection-header">
                            <h5 class="task-card-title orange-text">
                                Details
                            </h5>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i> Name
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->store_name ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i>
                                    Manager
                                </div>
                                <div class="col s7 grey-text text-darken-4 right-align">
                                    <?php
                                    if ($man) {
                                        echo $this->users_model->user($man->user_id)->user_name;
                                    } else { ?>
                                        <span class="orange-text">Unassigned :-(</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-communication-location-on"></i>
                                    Address
                                </div>
                                <div
                                    class="col s7 grey-text text-darken-4 right-align"><?= $details->store_address ?></div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-editor-mode-edit"></i> Registered
                                    BY
                                </div>
                                <div
                                    class="col s7 grey-text text-darken-4 right-align">
                                    <?= $this->users_model->user($details->added_by)->user_name ?>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s5 grey-text darken-1"><i class="mdi-editor-mode-edit"></i> Registration
                                    Date
                                </div>
                                <div
                                    class="col s7 grey-text text-darken-4 right-align">
                                    <?= $details->adding_date ?>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="request" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Request asset from another store</h5>
            </li>
            <li class="collection-item">
                <form class="col s12" method="post"
                      action="<?= site_url('requisitions/store_request') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="assets" name="item_id" data-placeholder="Select items to request" class="browser-default chosen-select">
                                <option value="" selected disabled>---Select items to request---</option>
                                <?php
                                foreach ($this->crud_model->get_records('item_models') as $component) {
                                    $model = $this->items_model->get_model_details($component->item_model_id);
                                    ?>
                                    <option
                                        value="<?= $component->item_model_id ?>"><?= $model->make_name . ' ' . $model->model_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="assets" class="active">Required Asset Model</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="from-store" name="from_store" data-placeholder="Select store" class="browser-default chosen-select">
                                <option value="" selected disabled>--Select Store to request From--</option>
                                <option value="1">Main Store</option>
                                <option value="2">Assembly Store</option>
                                <option value="3">Installation Store</option>
                                <option value="4">Maintenance Store</option>
                            </select>
                            <label for="from-store" class="active">Store to request From</label>
                        </div>
                    </div>
                    <input type="hidden" name="this_store" value="<?= $details->store_type ?>"/>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="qty" name="item_qty" type="number" required>
                            <label for="qty">Required Quantity</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                                            <textarea id="description" name="item_purpose"
                                                      class="materialize-textarea"></textarea>
                            <label for="description">Request Purpose</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn orange waves-effect waves-light right" type="submit">
                                Request
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>