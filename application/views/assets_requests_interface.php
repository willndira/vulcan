<div class="row">
    <div class="col s12 m11">
        <div class="row">
            <div class="col s12">
                <h5 class="task-card-title grey-text">
                    <i class="mdi-navigation-chevron-right"></i>
                    <?= $page ?>
                </h5>
                <h6 class="medium green-text">
                    Asset requests
                </h6>
            </div>
            <div class="col s12">
                <ul id="task-card" class="collection with-header">
                    <li class="collection-item">
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="green-text">Pending requests</span>
                        </div>
                        <table class="dt display responsive-table">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Done by</th>
                                <th>Asset Model</th>
                                <th>Asset type</th>
                                <th>Purpose</th>
                                <th>Qty</th>
                                <th>Assigned</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($this->asset_model->store_requested(1, $store_type) as $item) { ?>
                                <tr class="link" id="requisitions/asset/<?= urlencode(base64_encode($item->request_id)) ?>">
                                    <td><?= $item->request_date ?></td>
                                    <td><?= $this->users_model->user($item->requesting_user)->user_name ?></td>
                                    <td>
                                        <?php
                                        $model = $this->items_model->get_model_details($item->model_id);
                                        echo $model->make_name . ' ' . $model->model_name;
                                        ?>
                                    </td>
                                    <td><span class="green-text"><b>Item</b></span></td>
                                    <td><?= $item->purpose ?></td>
                                    <td><?= $item->request_qty ?></td>
                                    <td>
                                        <?php $assigned_number = count($assigned = $this->asset_model->all_assigned($item->request_category_id, $item->request_category, $item->request_asset_type));
                                        foreach ($assigned as $item_one) {
                                            $item_details = $this->crud_model->get_record("items", "item_id", $item_one->asset_id);
                                            if ($item_details->model_id != $item->model_id) {
                                                $assigned_number--;
                                            }
                                        }
                                        echo $assigned_number;
                                        ?>
                                    </td>
                                </tr>
                            <?php }
                            foreach ($this->asset_model->store_requested(2, $store_type) as $item) { ?>
                                <tr class="link" id="requisitions/asset/<?= urlencode(base64_encode($item->request_id)) ?>">
                                    <td><?= $item->request_date ?></td>
                                    <td><?= $this->users_model->user($item->requesting_user)->user_name ?></td>
                                    <td>
                                        <?php
                                        $model = $this->components_model->details($item->model_id);
                                        echo $model->component_name;
                                        ?>
                                    </td>
                                    <td><span class="blue-text"><b>Equipment</b></span></td>
                                    <td><?= $item->purpose ?></td>
                                    <td><?= $item->request_qty ?></td>
                                    <td><?php $assigned_number = count($assigned = $this->asset_model->all_assigned($item->request_category_id, $item->request_category, $item->request_asset_type));
                                        //todo: Check this logic
                                        foreach ($assigned as $item_one) {
                                            $item_details = $this->crud_model->get_record("equipment", "equipment_id", $item_one->asset_id);
                                            if ($item_details->model_id != $item->model_id) {
                                                $assigned_number--;
                                            }
                                        }
                                        echo $assigned_number;
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </li>
                </ul>
            </div>
            <!-- <div class="col s12">
                <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                    <li>
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="green-text">Serviced requests</span>
                        </div>
                        <div class="collapsible-body" style=" padding: 20px 10px;">
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
                                <?php foreach ($this->asset_model->store_requested(1, $store_type) as $item) { ?>
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
            foreach ($this->asset_model->store_requested(2, $store_type) as $item) { ?>
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
                        </div>
                    </li>
                </ul>
            </div>
           <div class="col s12">
                <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                    <li>
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="red-text">Trashed requests</span>
                        </div>
                        <div class="collapsible-body" style=" padding: 20px 10px;">


                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col s12 m4">
        <div class="row">
            <div class="col s12" style="margin-top: 20px;">
               <br/>
               <br/>
            </div>
            <div class="col s12">
                <ul id="task-card" class="collection with-header">
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s6 center">
                                <h5 class="green-text">
                                    <strong>
                                        00
                                    </strong>
                                </h5>
                                <p class="medium">All Requests</p>
                            </div>
                            <div class="col s6 center">
                                <h5 class="green-text">
                                    <strong>
                                        00
                                    </strong>
                                </h5>
                                <p class="medium">Attended to</p>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s4 center">
                                <h5 class="orange-text">
                                    <strong>
                                        YY
                                    </strong>
                                </h5>
                                <p class="medium">Items</p>
                            </div>
                            <div class="col s4 center">
                                <h5 class="orange-text">
                                    <strong>
                                        XX
                                    </strong>
                                </h5>
                                <p class="medium">Equipment</p>
                            </div>
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
            $this->db->order_by("store_log_id", "DESC");
            foreach ($this->db->get("tbl_store_logs")->result() as $activity) { ?>
                            <p class="medium-small">
                                <strong class="cyan-text link" id="profile/user/<?= urlencode(base64_encode($activity->user_id)) ?>">
                                    <i class="mdi-action-verified-user"></i> <?= $this->users_model->user($activity->user_id)->user_name ?>
                                </strong>
                                <span class="right orange-text">
                                        <i class="mdi-av-timer"></i> <?= $activity->time ?>
                                </span>
                                <br/>

                                <span style="padding-left: 10px">
                                    <?= $activity->activity ?>
                                </span>
                            </p>
                            <br/>
                        <?php }
            ?>
                    </li>
                </ul>
            </div>
        </div>  -->
        </div>
    </div>