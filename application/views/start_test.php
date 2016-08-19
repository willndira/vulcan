<div class="row">
    <div class="col s12 m8">
        <div class="col s12">
            <h5 class="card-title grey-text text-darken-4">
                <i class="mdi-navigation-chevron-right"></i>
                <?= $page ?>
            </h5>
            <p class="medium green-text">Equipment assembly</p>
        </div>
        <ul class="collection">
            <li class="collection-item">
                <div class="collapsible-header" style="padding: 10px;">
                    <span class="orange-text text-darken-4">My equipment assembly assignment</span>
                </div>
                <table class="responsive-table dt display" cellspacing="0">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Stage</th>
                        <th>Expected start date</th>
                        <th>Expected due date</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($this->crud_model->get_records("equipment_assembly", "assembly_manager", $this->users_model->user()->user_id) as $equipment) { ?>
                        <tr class="link"
                            id="assembly/process/<?= urlencode(base64_encode($equipment->equipment_assembly_id)) ?>">
                            <td><?php $eq = $this->crud_model->get_record("equipment", "equipment_id", $equipment->equipment_id);
                                echo $eq->equipment_no ?></td>
                            <td><?= $this->crud_model->get_record("components", "component_id", $eq->component_id)->component_name ?></td>
                            <td><?= $this->equipment_model->priority($equipment->priority) ?></td>
                            <td><?= $this->equipment_model->stage($eq->equipment_stage) ?></td>
                            <td>
                                <?php
                                $date = $this->equipment_model->assembly_date(true, $equipment->equipment_id);
                                echo is_null($date) ? "<span class='red-text'>No task defined</span>" : $date;
                                ?>
                            </td>
                            <td>
                                <?php
                                $date = $this->equipment_model->assembly_date(false, $equipment->equipment_id);
                                echo is_null($date) ? "<span class='red-text'>No task defined</span>" : $date;
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </li>
        </ul>
        <ul class="collection">
            <li class="collection-item">
                <div class="collapsible-header" style="padding: 10px;">
                    <span class="orange-text text-darken-4">All equipment assembly</span>
                </div>
                <table class="responsive-table dt display" cellspacing="0">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Type</th>
                        <th>Assembly Manager</th>
                        <th>Priority</th>
                        <th>Stage</th>
                        <th>Expected start date</th>
                        <th>Expected due date</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($this->crud_model->get_records("equipment_assembly") as $equipment) { ?>
                        <tr class="link"
                            id="assembly/process/<?= urlencode(base64_encode($equipment->equipment_assembly_id)) ?>">
                            <td><?php $eq = $this->crud_model->get_record("equipment", "equipment_id", $equipment->equipment_id);
                                echo $eq->equipment_no ?></td>
                            <td><?= $this->crud_model->get_record("components", "component_id", $eq->component_id)->component_name ?></td>
                            <td><?= $equipment->assembly_manager ? $this->users_model->user($equipment->assembly_manager)->user_name : "<span class='orange-text'>Not specified :-( </span>" ?></td>
                            <td><?= $this->equipment_model->priority($equipment->priority) ?></td>
                            <td><?= $this->equipment_model->stage($eq->equipment_stage) ?></td>
                            <td>
                                <?php
                                $date = $this->equipment_model->assembly_date(true, $equipment->equipment_id);
                                echo is_null($date) ? "<span class='red-text'>No task defined</span>" : $date;
                                ?>
                            </td>
                            <td>
                                <?php
                                $date = $this->equipment_model->assembly_date(false, $equipment->equipment_id);
                                echo is_null($date) ? "<span class='red-text'>No task defined</span>" : $date;
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </li>
        </ul>
    </div>
    <div class="col s12 m4">

        <ul class="collection">
            <li class="collection-item">
                <div class="row">
                    <div class="col s6 center">
                        <h5 class="green-text">
                            <strong>
                                <?= count($this->assembly_model->all()) ?>
                            </strong>
                        </h5>
                        <p class="medium">All assemblies</p>
                    </div>
                    <div class="col s6 center">
                        <h5 class="green-text">
                            <strong>
                                <?= count($this->equipment_model->all()) ?>
                            </strong>
                        </h5>
                        <p class="medium">All equipment</p>
                    </div>
                    <div class="col s12 center">
                        <h6>Equipment</h6>
                    </div>
                </div>
            </li>
        </ul>

        <?php if ($this->users_model->requires_role(array('initiate_assembly'))) { ?>
            <ul class="collection with-header">
                <li class="collection-item">
                    <h5 class="orange-text">Start new assembly</h5>
                </li>

                <li class="collection-item">
                    <form class="col s12" method="post" action="<?= site_url('assembly/start') ?>">
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="equipment_id" id="component">
                                    <option>--Select equipment to assemble--</option>
                                    <?php
                                    foreach ($this->crud_model->get_records('equipment', "equipment_stage", 1) as $equipment) {
                                        ?>
                                        <option <?= set_value("equipment_id") == $equipment->equipment_id ? "selected" : "" ?>
                                            value="<?= $equipment->equipment_id ?>"><?= $equipment->equipment_no ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <label for="component">Equipment</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="assembly_manager" id="assembly_manager">
                                    <option value="" disabled selected>--Select Assembly Manager--</option>
                                    <?php foreach ($this->users_model->has_powers('man_assembly') as $pm) { ?>
                                        <option <?= set_value("assembly_manager") == $pm->user_id ? "selected" : "" ?>
                                            value="<?= $pm->user_id ?>"><?= $pm->user_name ?></option>
                                    <?php } ?>
                                </select>
                                <label for="assembly_manager">Assembly Manager</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="assembly_priority" id="assembly_priority">
                                    <option value="" disabled selected>--Select assembly priority--</option>
                                    <option value="1">Very low priority</option>
                                    <option value="2">Low priority</option>
                                    <option value="3">Normal priority</option>
                                    <option value="4">High priority</option>
                                    <option value="5">Very high priority</option>

                                </select>
                                <label for="assembly_priority">Priority</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                    <textarea id="description" name="equipment_comment"
                                              class="materialize-textarea"><?= set_value("equipment_comment") ?></textarea>
                                <label for="description">Brief comments for assembly</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <button class="btn orange waves-effect waves-light right"
                                        type="submit">
                                    Initiate process
                                    <i class="mdi-content-send right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </li>
            </ul>
        <?php } ?>
    </div>
</div>


