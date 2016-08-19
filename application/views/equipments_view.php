<div class="row">
    <div class="col s12 m8">
        <div class="col s12">
            <h5 class="card-title grey-text text-darken-4">
                <i class="mdi-navigation-chevron-right"></i>
                <?= $page ?>
            </h5>
            <p class="medium green-text">All registered Equipment</p>
        </div>
        <ul id="task-card" class="collection">
            <li class="collection-item">
                <table class="responsive-table dt display" cellspacing="0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>No</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Available</th>
                        <th>Condition</th>
                        <th>Stage</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($this->crud_model->get_records("equipment") as $equipment) { ?>
                        <tr class="link"
                            id="equipment/profile/<?= urlencode(base64_encode($equipment->equipment_id)) ?>">
                            <td><?= $equipment->equipment_id ?></td>
                            <td><?= $equipment->equipment_no ?></td>
                            <td><?= $this->crud_model->get_record("components", "component_id", $equipment->component_id)->component_name ?></td>
                            <td><?= $this->equipment_model->current_location($equipment->equipment_id, 2) ?></td>
                            <td><?= $equipment->equipment_availability ? "YES" : "NO" ?></td>
                            <td><?= $equipment->equipment_condition ? "Operational" : "Faulty" ?></td>
                            <td><?= $this->equipment_model->stage($equipment->equipment_stage) ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </li>
        </ul>
        <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
            <li>
                <div class="collapsible-header" style="padding: 10px;">
                    <span class="red-text">Trashed Equipment</span>
                </div>
                <div class="collapsible-body" style=" padding: 20px 10px;">
                    <table class="responsive-table dt display" cellspacing="0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>No</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Available</th>
                            <th>Condition</th>
                            <th>Stage</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($this->crud_model->get_trash("equipment") as $equipment) { ?>
                            <tr class="link"
                                id="equipment/profile/<?= urlencode(base64_encode($equipment->equipment_id)) ?>">
                                <td><?= $equipment->equipment_id ?></td>
                                <td><?= $equipment->equipment_no ?></td>
                                <td><?= $this->crud_model->get_record("components", "component_id", $equipment->component_id)->component_name ?></td>
                                <td><?= $this->equipment_model->current_location($equipment->equipment_id, 2) ?></td>
                                <td><?= $equipment->equipment_availability ? "YES" : "NO" ?></td>
                                <td><?= $equipment->equipment_condition ? "Operational" : "Faulty" ?></td>
                                <td><?= $this->equipment_model->stage($equipment->equipment_stage) ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </li>
        </ul>

    </div>
    <div class="col s12 m4">
        <ul class="collection with-header" id="task-card">
            <li class="collection-header">
                <h5 class="orange-text task-card-title">Register Equipment</h5>
            </li>

            <li class="collection-item">
                <div class="container">
                    <div class="row">
                        <form class="col s12" method="post" action="<?= site_url('equipment/register') ?>">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="eq_no" name="eq_no" type="text" required>
                                    <label for="eq_no">Equipment No</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select name="component_id" id="component">
                                        <option>Select Machine</option>
                                        <?php
                                        foreach ($this->crud_model->get_records('components') as $component) {
                                            ?>
                                            <option
                                                value="<?= $component->component_id ?>"><?= $component->component_name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <label for="component">Equipment Type</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select id="equipment_condition" name="equipment_condition">
                                        <option value="" disabled selected>--Select equipment condition--</option>
                                        <option value="1">Operational</option>
                                        <option value="0">Faulty</option>
                                    </select>
                                    <label for="equipment_condition">Condition</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select id="equipment_availability" name="equipment_availability">
                                        <option value="" disabled selected>--Select equipment availability--</option>
                                        <option value="1">Available</option>
                                        <option value="0">In Use</option>
                                    </select>
                                    <label for="equipment_availability">Availability</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea id="description" name="equipment_comment"
                                              class="materialize-textarea"></textarea>
                                    <label for="description">Comments about equipment</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button class="btn orange waves-effect waves-light right" type="submit">Register
                                        <i class="mdi-content-send right"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>