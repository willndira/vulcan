<div class="container">
    <ul id="task-card" class="collection with-header">
        <li class="collection-header cyan darken-2">
            <h5 class="task-card-title"><?= $page ?></h5>
        </li>
        <li class="collection-item">
            <table id="data-table-simple" class="responsive-table display" cellspacing="0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Request time</th>
                    <th>Made By</th>
                    <th>Item Type</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Purpose</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($requests as $item) {
                    ?>
                    <tr class="item_profile"
                        id="<?= urlencode(base64_encode($item->requisition_id)) ?>">
                        <td><?= $item->requisition_id ?></td>
                        <td><?= $item->requisition_time ?></td>
                        <td><?= $this->users_model->user($item->requisition_officer)->user_name ?></td>
                        <td><?= $this->items_model->get_model_details($item->item_model_id)->it_name ?></td>
                        <td><?= $this->items_model->get_model_details($item->item_model_id)->make_name ?></td>
                        <td><?= $this->items_model->get_model_details($item->item_model_id)->model_name ?></td>
                        <td><?= $item->requisition_purpose ?></td>
                        <td><?= $this->items_model->requisition_status($item->requisition_id) ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </li>
    </ul>
</div>