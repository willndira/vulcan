<div class="row">
    <div class="col s12 m8">
        <div class="row">
            <div class="col s12">
                <h5 class="card-title grey-text text-darken-4">
                    <i class="mdi-navigation-chevron-right"></i>
                    <?= $page ?>
                </h5>
                <p class="medium green-text"> Item category</p>
            </div>
            <div class="col s12">
                <ul id="task-card" class="collection with-header">
                    <li class="collection-item">
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="orange-text">Component categories</span>
                        </div>
                        <table class="responsive-table dt" cellspacing="0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Make</th>
                                <th>Model</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($this->crud_model->get_records('item_models') as $model) {
                                $model = $this->items_model->get_model_details($model->item_model_id);
                                ?>
                                <tr class="link_category" id="<?= urlencode(base64_encode($model->item_model_id)) ?>">
                                    <td><?= $model->item_model_id ?></td>
                                    <td><?= $model->it_name ?></td>
                                    <td><?= $model->make_name ?></td>
                                    <td><?= $model->model_name ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </li>
                </ul>
            </div>
            <div class="col s12">
                <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                    <li>
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="red-text">Trashed categories</span>
                        </div>
                        <div class="collapsible-body" style=" padding: 20px 10px;">
                            <table class="responsive-table dt" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>Make</th>
                                    <th>Model</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($this->crud_model->get_trash('item_models') as $model) {
                                    $model = $this->items_model->get_model_details($model->item_model_id);
                                    ?>
                                    <tr class="link_category" id="<?= urlencode(base64_encode($model->item_model_id)) ?>">
                                        <td><?= $model->item_model_id ?></td>
                                        <td><?= $model->it_name ?></td>
                                        <td><?= $model->make_name ?></td>
                                        <td><?= $model->model_name ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col s12 m4 l4">
        <ul id="task-card" class="collection with-header z-depth-1">
            <li class="collection-header">
                <h5 class="task-card-title orange-text"><?= $page ?></h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('items/define/true') ?>">
                    <div class="row">
                        <div class="input-field col s12 type">
                            <input id="type" type="text" name="type" required/>
                            <label for="type">Type e.g Smart phone</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 make">
                            <input id="make" type="text" name="make" required/>
                            <label for="make">Make e.g Sony</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 model">
                            <input id="model" type="text" name="model" required/>
                            <label for="model">Model e.g Xperia T2</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 model">
                            <input id="model" type="text" name="model_est_cost" required/>
                            <label for="model">Estimated Cost</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 model">
                            <textarea id="desc" name="description" class="materialize-textarea" required></textarea>
                            <label for="desc">Item description and its use</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <?php
                            if ($this->users_model->requires_role(array('define_assets'))
                            ) {
                                ?>
                                <button class="btn orange waves-effect waves-light right" type="submit"
                                        name="action">Define Item
                                    <i class="mdi-content-send right"></i>
                                </button>
                                <?php
                            } else {
                                ?>
                                <div id="card-alert" class="card red">
                                    <div class="card-content white-text">
                                        <p>DENIED : Sorry. No enough permissions to define item.</p>
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