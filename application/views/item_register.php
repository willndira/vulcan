<div class="container">
    <div class="row">
        <div class="col s12">
            <ul class="collection with-header z-depth-2 ">
                <li class="collection-header green">
                    <h5 class="white-text"><?= $page ?></h5>
                </li>
                <li class="collection-item">
                    <form method="post" action="<?= site_url('items/register') ?>">
                        <div class="row">
                            <div class="input-field col s12 m4">
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
                            <div class="input-field col s12 m4">
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
                            <div class="input-field col s12 m4">
                                <select id="model" name="model" required>
                                    <option>Select Model</option>
                                    <?php
                                    foreach ($this->crud_model->get_records('item_models') as $type) {
                                        ?>
                                        <option
                                            value="<?= $type->item_model_id ?>"><?= $type->model_name ?></option>
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
                                <input type="hidden" name="location" value="INSTALLED" required/>
                                <select id="project_id" name="project_id" required>
                                    <option value="" disabled selected>Installed Project</option>
                                    <?php foreach ($this->crud_model->get_records('projects') as $project) { ?>
                                        <option
                                            value="<?= $project->project_id ?>"><?= $project->project_name ?></option>
                                    <?php } ?>
                                </select>
                                <label for="project_id">Current Item Project **For items in store, record at
                                    store**</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12 model">
                                <select id="item_location" name="state" required>
                                    <option>Item State</option>
                                    <option value="1">Functional</option>
                                    <option value="0">Defective</option>
                                </select>
                                <label for="item_location">Current Item location</label>
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
                </li>
            </ul>
        </div>
    </div>
</div>