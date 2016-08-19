<?php
$man = $this->stores_model->manager($details->request_level);
$is_manager = count($man) > 0 ? (($man->user_id) == $this->users_model->user()->user_id) : false;
$model = $this->items_model->get_model_details($details->model_id);
$issued = $this->asset_model->all_assigned($details->request_category_id, $details->request_category, $details->request_asset_type);
$issued_number = count($issued);
foreach ($issued as $item) {
    $item_details = $this->crud_model->get_record("items", "item_id", $item->asset_id);
    if ($item_details->model_id != $details->model_id) {
        $issued_number--;
    }
}
?>
<div class="section">
    <div class="row">
        <div class="col s12 m8">
            <div class="row">
                <div class="col s12">
                    <h5 class="card-title grey-text text-darken-4">
                        <i class="mdi-navigation-chevron-right"></i>
                        <?= $page ?>
                        <p class="medium-small white-text-text"> <?= $model->make_name . " " . $model->model_name ?></p>
                    </h5>
                </div>
                <div class="col s12">
                    <ul id="task-card" class="collection with-header">
                        <li class="collection-header">
                            <h5 class="task-card-title orange-text">
                                Available <?= $model->make_name . ' ' . $model->model_name ?>
                            </h5>
                        </li>
                        <li class="collection-item">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Serial No.</th>
                                    <th>Code</th>
                                    <th>Condition</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->stores_model->similar_stock($details->model_id, $details->request_level, $details->request_asset_type) as $item) { ?>
                                    <tr>
                                        <?php
                                        $item_details = $this->items_model->details($item->asset_id);
                                        ?>
                                        <td><?= $item_details->item_serial_no ?></td>
                                        <td><?= $item_details->item_code ?></td>
                                        <td><?= $item_details->item_condition ? "Operational" : "Faulty" ?></td>
                                        <td>
                                            <?php
                                            if ($issued_number < $details->request_qty) {
                                                if (!$is_manager)
                                                    echo '<span class="red-text">Not this store manager</span>';
                                                elseif ($item->confirmation) { ?>
                                                    <button
                                                        value="assets/assign/<?= urlencode(base64_encode(json_encode(array("request_id" => $details->request_id, "asset_id" => $item->asset_id)))) ?>"
                                                        class="btn ajax green">
                                                        assign
                                                    </button>
                                                <?php } else {
                                                    echo '<span class="orange-text">Add to your stock first</span>';
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
                                    <th>Code</th>
                                    <th>Serial No.</th>
                                    <th>Assigned by</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($issued as $item) {
                                    $item_details = $this->crud_model->get_record("items", "item_id", $item->asset_id);
                                    if ($item_details->model_id == $details->model_id) {
                                        ?>
                                        <tr>
                                            <td><?= $item->date ?></td>
                                            <td class="link"
                                                id="items/profile/<?= urlencode(base64_encode($item_details->item_id)) ?>"><?= $item_details->item_code ?></td>
                                            <td class="link"
                                                id="items/profile/<?= urlencode(base64_encode($item_details->item_id)) ?>"><?= $item_details->item_serial_no ?></td>
                                            <td class="link"
                                                id="profile/user/<?= urlencode(base64_encode($item->handling_staff)) ?>"><?= $this->users_model->user($item->handling_staff)->user_name ?></td>
                                            <td>
                                                <?php
                                                if (!$item->confirmation) {
                                                    if (!$is_manager)
                                                        echo '<span class="red-text">Pending collection</span>';
                                                    else {
                                                        ?>
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
                                    <?php }
                                } ?>
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
                                            <?= (int)(($issued_number / $details->request_qty) * 100) ?>%
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
                                            <?= $issued_number ?>
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
                                            Very High
                                        </strong>
                                    </h5>
                                    <p class="medium">Priority</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <ul id="task-card" class="collection with-header z-depth-1">
                <li class="collection-header">
                    <h5 class="task-card-title orange-text">Request details</h5>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i>
                            Model
                        </div>
                        <div class="col s7 grey-text text-darken-4 right-align">
                            <?= $model->model_name ?>
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
                        <div class="col s5 grey-text darken-1"><i class="mdi-communication-location-on"></i> Category
                        </div>
                        <div
                            class="col s7 grey-text text-darken-4 right-align"><?= $model->it_name ?></div>
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
