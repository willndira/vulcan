<div class="row">
    <div class="col s12 m11 l11 all_requests">

        <div class="card-panel">
            <div id="table-datatables">
                <h4 class="header"><?= $page ?></h4>

                <div class="row">
                    <div class="col s12 m12 l12">
                        <table id="data-table-simple" class="responsive-table display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>P.O #</th>
                                <th>Gen Date</th>
                                <th>Supplier</th>
                                <th>Receiving Store</th>
                                <th>Due date</th>
                                <th>Total Amount</th>
                                <th>Supply</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php
                            foreach ($this->crud_model->get_records('purchase_orders') as $po) {
                                ?>
                                <tr class="po_link" id="<?= $po->po_id ?>">
                                    <td><?= $po->po_id ?></td>
                                    <td><?= $po->gen_date ?></td>
                                    <td><?= $this->crud_model->get_record('suppliers', 'supplier_id', $po->supplier_id)->supplier_name ?></td>
                                    <td><?= $this->crud_model->get_record('stores', 'store_id', $po->store_id)->store_name ?></td>
                                    <td><?= $po->po_due_date ?></td>
                                    <td>Kes <?= $this->items_model->po_total($po->po_id) ?></td>
                                    <td><?= $po->unit_cost ?></td>
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
</div>