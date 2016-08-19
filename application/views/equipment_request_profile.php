<?php
$man = $this->stores_model->manager($details->request_level);
$is_manager = count($man) > 0 ? (($man->user_id) == $this->users_model->user()->user_id) : false;
$component = $this->components_model->details($details->model_id);
$issued = $this->asset_model->all_assigned($details->request_category_id, $details->request_category, $details->request_asset_type);
?>

<div class="section">
    <div class="row">
        <div class="col s12 m8">
            <div class="row">
                <div class="col s12">
                    <h5 class="card-title grey-text text-darken-4">
                        <i class="mdi-navigation-chevron-right"></i>
                        <?= $component->component_name ?>
                        <p class="medium-small white-text-text"><?= $component->component_description ?></p>
                    </h5>
                </div>
                <div class="col s12">
                    <ul id="task-card" class="collection with-header">
                        <li class="collection-header">
                            <h5 class="task-card-title orange-text">
                                Available <?= $component->component_name ?>
                            </h5>
                        </li>
                        <li class="collection-item">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Equipment No.</th>
                                    <th>Condition</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->stores_model->similar_stock($details->model_id, $details->request_level, $details->request_asset_type) as $item) { ?>
                                    <tr>
                                        <?php
                                        $item_details = $this->equipment_model->details($item->asset_id);
                                        $model = $this->components_model->details($item_details->component_id);
                                        ?>
                                        <td><?= $item_details->equipment_id ?></td>
                                        <td><?= $item_details->equipment_no ?></td>
                                        <td><?= $item_details->equipment_condition ? "Operational" : "Faulty" ?></td>
                                        <td>
                                            <?php
                                            if (count($issued) < $details->request_qty) {
                                                if (!$is_manager)
                                                    echo '<span class="red-text">Requires you to be this store manager</span>';
                                                elseif ($item->confirmation) {
                                                    ?>
                                                    <button
                                                        value="assets/assign/<?= urlencode(base64_encode(json_encode(array("request_id" => $details->request_id, "asset_id" => $item->asset_id)))) ?>"
                                                        class="btn ajax green">
                                                        assign
                                                    </button>
                                                <?php } else {
                                                    echo '<span class="orange-text">Collect first</span>';
                                                }
                                            } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
                <div class="col s12">
                    <ul id="task-card" class="collection with-header z-depth-1">
                        <li class="collection-header">
                            <h5 class="task-card-title orange-text">Assigned assets</h5>
                        </li>
                        <li class="collection-item">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Equipment No</th>
                                    <th>Assigned by</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($issued as $item) {
                                    $item_details = $this->equipment_model->details($item->asset_id);
                                    ?>
                                    <tr>
                                        <td><?= $item->date ?></td>
                                        <td><?= $item_details->equipment_no ?></td>
                                        <td class="link"
                                            id="profile/user/<?= urlencode(base64_encode($item->handling_staff)) ?>"><?= $this->users_model->user($item->handling_staff)->user_name ?></td>
                                        <td>
                                            <?php
                                            if (!$item->confirmation) {
                                                if (!$is_manager)
                                                    echo '<span class="red-text">Pending collection</span>';
                                                else { ?>
                                                    <button
                                                        value="assets/cancel/<?= urlencode(base64_encode(json_encode(array("asset_id" => $item->asset_id, "asset_location_id" => $item->asset_location_id)))) ?>"
                                                        class="orange btn ajax">cancel
                                                    </button>
                                                <?php }
                                            } else { ?>
                                                <span class="green-text">Collected</span>
                                            <?php } ?>
                                        </td>
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
                <div class="col s12">
                    <ul id="task-card" class="collection with-header">
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s12 center">
                                    <h5 class="green-text">
                                        <strong>
                                            <?= (int)((count($issued) / $details->request_qty) * 100) ?>%
                                        </strong>
                                    </h5>
                                    <p class="medium">Assigned</p>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s6 center">
                                    <p class="medium">Requested</p>
                                    <h5 class="green-text">
                                        <strong>
                                            <?= $details->request_qty ?>
                                        </strong>
                                    </h5>
                                </div>
                                <div class="col s6 center">
                                    <p class="medium">Assigned</p>
                                    <h5 class="green-text">
                                        <strong>
                                            <?= count($issued) ?>
                                        </strong>
                                    </h5>
                                </div>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s12 center">
                                    <h5 class="orange-text">
                                        <strong>
                                            Normal
                                        </strong>
                                    </h5>
                                    <p class="medium">Priority</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <ul id="task-card" class="collection">
                <li class="collection-header">
                    <h5 class="task-card-title orange-text">Request details</h5>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s12 grey-text text-darken-4 center">
                            <?= $component->component_name ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i>
                            Project
                        </div>
                        <div class="col s7 grey-text text-darken-4 right-align">

                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i>
                            Quantity
                        </div>
                        <div class="col s7 grey-text text-darken-4 right-align">
                            <?= $details->request_qty ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i>
                            Requested Store
                        </div>
                        <div class="col s7 grey-text text-darken-4 right-align">
                            <?= $this->stores_model->specific($details->request_level)->store_name ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-action-verified-user"></i> Requested On
                        </div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align"><?= $details->request_date ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1">
                            <i class="mdi-editor-mode-edit"></i>
                            Request by
                        </div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align">
                            <?= $this->users_model->user($details->requesting_user)->user_name ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div
                            class="col s12 grey-text text-darken-4 right-align">
                            <?= $details->purpose ?>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
