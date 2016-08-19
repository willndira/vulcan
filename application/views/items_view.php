<div class="row">
    <div class="col s12 m8">
        <div class="col s12">
            <h5 class="card-title grey-text text-darken-4">
                <i class="mdi-navigation-chevron-right"></i>
                <?= $page ?>
            </h5>
            <p class="medium green-text">All registered items</p>
        </div>
        <ul id="task-card" class="collection">
            <li class="collection-item">
                <table class="responsive-table dt display" cellspacing="0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Model</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    foreach ($this->crud_model->get_records('items') as $item) {
                        $model = $this->items_model->get_model_details($item->model_id);
                        ?>
                        <tr class="link" id="items/profile/<?= urlencode(base64_encode($item->item_id)) ?>">
                            <td><?= $item->item_id ?></td>
                            <td class="capitalize"><?= $item->item_code ?></td>
                            <td><?= $model->make_name . ' ' . $model->model_name ?></td>
                            <td><?= $this->equipment_model->current_location($item->item_id, 1) ?></td>
                            <td>
                                <?= $item->item_condition ? '<span class="green-text"> Functional </span>' : '<span class="orange-text"> Faulty</span>' ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </li>
        </ul>
        <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
            <li>
                <div class="collapsible-header" style="padding: 10px;">
                    <span class="red-text">Trashed items</span>
                </div>
                <div class="collapsible-body" style=" padding: 20px 10px;">
                    <table class="responsive-table dt display" cellspacing="0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Model</th>
                            <th>Last Location</th>
                            <th>Last Status</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($this->db->get_where('tbl_items', array("deleted" => true))->result() as $item) {
                            $model = $this->items_model->get_model_details($item->model_id);
                            ?>
                            <tr class="link" id="items/profile/<?= urlencode(base64_encode($item->item_id)) ?>">
                                <td><?= $item->item_id ?></td>
                                <td class="capitalize"><?= $item->item_code ?></td>
                                <td><?= $model->make_name . ' ' . $model->model_name ?></td>
                                <td><?= $this->equipment_model->current_location($item->item_id, 1) ?></td>
                                <td>
                                    <?= $item->item_condition ? '<span class="green-text"> Functional </span>' : '<span class="orange-text"> Faulty</span>' ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </li>
        </ul>
    </div>

    <div class="col s12 m4">
        <div class="row">
            <div class="col s12" style="margin-top: 20px;">
                <a class="btn green right modal-trigger" href="#new-item">
                    <i class="mdi-content-add"></i> New item
                </a>
            </div>
            <div class="col s12">
                <ul id="task-card" class="collection with-header">
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s6 center">
                                <h5 class="cyan-text">
                                    <strong>
                                        <?= count($this->crud_model->get_records('items')) ?>
                                    </strong>
                                </h5>
                                <p class="medium">All Items</p>
                            </div>
                            <div class="col s6 center">
                                <h5 class="cyan-text">
                                    <strong>
                                        <?=
                                        count($this->crud_model->get_records("items", "item_condition", 1))
                                        ?>
                                    </strong>
                                </h5>
                                <p class="medium">Functional Items</p>
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
                        $this->db->order_by("item_timeline_id", "DESC");
                        foreach ($this->db->get("tbl_item_timeline")->result() as $activity) { ?>
                            <p class="medium-small">
                                <strong class="cyan-text link" id="profile/user/<?= urlencode(base64_encode($activity->activity_by)) ?>">
                                    <i class="mdi-action-verified-user"></i> <?= $this->users_model->user($activity->activity_by)->user_name ?>
                                </strong>
                                <span class="right orange-text">
                                        <i class="mdi-av-timer"></i> <?= $activity->time ?>
                                </span>
                                <br/>

                                <span style="padding-left: 10px">
                                    <?= $activity->item_activity ?>
                                    <span class="link blue-text" id="items/profile/<?= urlencode(base64_encode($activity->item_id)) ?>">
                                        Item code: <?= $this->items_model->details($activity->item_id)->item_code ?>
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