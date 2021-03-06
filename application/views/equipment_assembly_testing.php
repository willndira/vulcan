<?php
$specs = $this->equipment_model->details($details->equipment_id);
$components = $this->components_model->assets($specs->component_id);
$assembly_steps = $this->projects_model->equipment_steps($specs->component_id, $test_stage, 1);
$is_pm = $this->users_model->user()->user_id == $details->assembly_manager;
$tasks_complete = true;
$procedure_complete = true;
foreach ($assembly_steps as $step) {
    if (!$this->assembly_model->is_tasked($step->ag_id, $details->equipment_assembly_id)) {
        $procedure_complete = false;
    }
}
?>
<div class="section" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div class="row">
        <div class="col s12 m8">
            <div class="row">
                <div class="col s12">
                    <h5 class="card-title grey-text text-darken-4">
                        <i class="mdi-navigation-chevron-right"></i>
                        <?= $page ?> - <?= $specs->equipment_no ?>
                    </h5>
                    <p class="medium green-text">
                        <?= $page ?>
                    </p>
                </div>
                <div class="col s12">
                    <div class="row">
                        <div class="col s6">
                            <h5 class="card-title orange-text text-darken-4">
                                Testing tasks
                            </h5>
                        </div>
                        <?php if ($is_pm && $specs->equipment_stage < ($test_stage == 1 ? 4 : 8)) { ?>
                            <div class="col s6">
                                <button class="btn white-text green lighten-2 right modal-trigger" href="#new_task">
                                    New task
                                </button>
                            </div>
                        <?php } ?>
                    </div>
                    <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                        <?php
                        foreach ($tasks = $this->crud_model->get_records("assembly_schedule", "equipment_assembly_id", $details->equipment_assembly_id) as $task) {
                            $is_team = $this->equipment_model->is_team($this->users_model->user()->user_id, $task->assembly_schedule_id);
                            $p_complete = true;
                            $steps_complete = true;
                            $tasks_complete = $task->schedule_stage == 2;
                            ?>
                            <li>
                                <div class="collapsible-header <?= $task->schedule_stage == 1 ? 'active' : '' ?>" style="padding: 10px;">
                                    <span class="green-text h5"><?= $task->schedule_title ?></span>
                                    <span class="chip right">
                                        <?= $this->equipment_model->task_stage($task->schedule_stage) ?>
                                    </span>
                                </div>
                                <div class="collapsible-body" style="padding: 10px !important">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col s12">
                                                <?php if (!$is_team) { ?>
                                                    <div class="card yellow center">
                                                        <b>ATTENTION!!! </b> You are not assigned this task
                                                    </div>
                                                <?php }
                                                if ($task->schedule_stage == 0 && $is_team) { ?>
                                                    <button
                                                        value="projects/start_task/<?= urlencode(json_encode(array("assembly_schedule_id" => $task->assembly_schedule_id))) ?>"
                                                        class="ajax btn orange waves-effect waves-light right">
                                                        Start task
                                                        <i class="mdi-content-send right"></i>
                                                    </button>
                                                <?php } ?>
                                            </div>
                                            <ul class="collection col s6">
                                                <li class="collection-item">
                                                    <h6 class="orange-text text-darken-4">Assigned staff</h6>
                                                </li>
                                                <?php
                                                foreach ($this->crud_model->get_records("assembly_team", "assembly_schedule_id", $task->assembly_schedule_id) as $staff) { ?>
                                                    <li class="collection-item"><?= $this->users_model->user($staff->user_id)->user_name ?></li>
                                                <?php } ?>
                                            </ul>
                                            <ul class="col s6 collection">
                                                <li class="collection-item">
                                                    <h6 class="orange-text text-darken-4">Predecessors tasks</h6>
                                                </li>
                                                <?php
                                                foreach ($this->crud_model->get_records("schedule_predicessor", "schedule_id", $task->assembly_schedule_id) as $predecessor) { ?>
                                                    <li class="collection-item">
                                                        <?php
                                                        $p_task = $this->equipment_model->task($predecessor->predicessor_id);
                                                        $p_task->schedule_stage != 2 ? $p_complete = false : '';
                                                        echo $p_task->schedule_title . " - " . $this->equipment_model->task_stage($p_task->schedule_stage)
                                                        ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                            <div class="col s12" style="padding-top: 10px !important;">
                                                <hr/>
                                                <?php if (!$p_complete) { ?>
                                                    <div class="col s12">
                                                        <div class="card orange center">
                                                            <b>ATTENTION!!! </b> Some or all predecessor tasks have not been completed
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <h5 class="orange-text text-darken-4">Instructions</h5>
                                                <?= $task->schedule_comment ?>
                                            </div>
                                            <div class="col s12" style="padding-top: 10px !important;">
                                                <h5 class="orange-text text-darken-4">Procedures to follow</h5>
                                                <ul class="collection">
                                                    <?php
                                                    foreach ($this->crud_model->get_records("assembly_schedule_steps", "schedule_id", $task->assembly_schedule_id) as $step) { ?>
                                                        <li class="collection-item">
                                                            <div class="row">
                                                                <div class="col s10">
                                                                    <?php
                                                                    $guide = $this->equipment_model->guide_details($step->setup_guide_id);
                                                                    $requirements = $this->projects_model->step_requirements($details->equipment_id, $guide->ag_id);
                                                                    if ($guide->result_type != 1) { ?>
                                                                    <form
                                                                        action="<?= site_url('projects/post_text/' . $step->as_id) . '/' . $details->equipment_assembly_id ?>"
                                                                        method="post">
                                                                        <div class="input-field">
                                                                            <input <?= ((!$requirements || !$is_team || !$p_complete || $task->schedule_stage != 1) ? "readonly" : "") ?>
                                                                                id="step-<?= $step->as_id ?>"
                                                                                type="<?= $guide->result_type == 2 ? 'text' : 'number' ?>"
                                                                                name="result"
                                                                                value="<?= is_null($step->perform_result) ? '<strong>PENDING</strong>' : $step->perform_result ?>"
                                                                                required>
                                                                            <label class="black-text" for="step-<?= $step->as_id ?>">
                                                                                <h6>
                                                                                    <strong>
                                                                                        <?= $guide->step_description ?>
                                                                                    </strong>
                                                                                </h6>
                                                                            </label>
                                                                        </div>
                                                                        <?php } else {
                                                                            echo $guide->step_description;
                                                                        }
                                                                        if (!$requirements) { ?>
                                                                            <span
                                                                                class="right orange-text">Required components not available</span>
                                                                        <?php } ?>
                                                                </div>
                                                                <div class="col s2">
                                                                    <?php
                                                                    if (is_null($step->performed_by))
                                                                        $steps_complete = false;
                                                                    if ($guide->result_type == 1) { ?>
                                                                        <div class="right">
                                                                            <input
                                                                                value="projects/perform_procedure/<?= urlencode(json_encode(array("guide" => $step->as_id, 'is_pm' => $is_team, 'setup_result' => true))) ?>"
                                                                                <?= ((!$requirements || !$is_team || !$p_complete || $task->schedule_stage != 1) ? "disabled" : "") . ' ' . (!is_null($step->performed_by) ? "checked" : "") ?>
                                                                                class="ajax right-align"
                                                                                id="assemble-step_<?= $guide->ag_id ?>"
                                                                                type="checkbox">
                                                                            <label for="assemble-step_<?= $guide->ag_id ?>"> </label>
                                                                        </div>
                                                                    <?php } else { ?>
                                                                        <div class="right input-field">
                                                                            <button <?= ((!$requirements || !$is_team || !$p_complete || $task->schedule_stage != 1) ? "disabled" : "") ?>
                                                                                class="orange btn" id="step-<?= $step->as_id ?>"
                                                                                type="submit">post
                                                                            </button>
                                                                        </div>
                                                                        </form>
                                                                        <?php
                                                                    } ?>

                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                            <?php if ($task->schedule_stage == 2 || ($steps_complete && $is_team && $p_complete)) {
                                                ?>
                                                <form
                                                    action="<?= site_url('equipment/complete/' . urlencode(base64_encode($task->assembly_schedule_id))) ?>"
                                                    method="post">
                                                    <div class="col s12 input-field">
                                                        <h5 class="orange-text text-darken-4">Completion report</h5>
                                                        <input type="hidden" name="redirect" value="/assembly/test/two/" required/>
                                                    </div>
                                                    <?php if ($task->schedule_stage != 2) { ?>
                                                        <div class="col s12 input-field">
                                                            <textarea class="materialize-textarea" name="report" id="report" required></textarea>
                                                            <label for="report">Task completion report</label>
                                                        </div>
                                                        <div class="input-field col s12">
                                                            <button class="btn orange waves-effect waves-light right" type="submit">
                                                                Complete task
                                                                <i class="mdi-content-send right"></i>
                                                            </button>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="input-field col s12">
                                                            <?= $task->report; ?>
                                                        </div>
                                                    <?php } ?>

                                                </form>

                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <div class="col s12">
                    <div class="row">
                        <div class="col s12">
                            <h5 class="orange-text text-darken-4 card-title">
                                <?= $page ?> procedure guide
                            </h5>
                        </div>
                    </div>
                    <ul class="collection">
                        <li class="collection-item">
                            <ul>
                                <li class="collection-item">
                                    <table class="responsive-table dt display">
                                        <thead>
                                        <tr>
                                            <th>Procedure</th>
                                            <th>Result</th>
                                            <th>Staff</th>
                                            <th>Time</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($assembly_steps as $guide) {
                                            $requirements = $this->projects_model->step_requirements($details->equipment_id, $guide->ag_id);
                                            $performed = $this->assembly_model->procedure_progress($guide->ag_id, $details->equipment_assembly_id);
                                            ?>
                                            <tr>
                                                <td><?= $guide->step_description; ?></td>
                                                <td>
                                                    <?php
                                                    if (!$requirements) { ?>
                                                        <span class="right orange-text">Required components not available</span>
                                                    <?php } elseif (count($performed) > 0 && !is_null($performed->performed_by)) {
                                                        if ($guide->result_type == 1)
                                                            echo $performed->perform_result ? '<span class="blue-text">Passed</span>' : '<span class="red-text">Failed</span>';
                                                        else
                                                            echo $performed->perform_result;
                                                    } else {
                                                        echo '<span class="orange-text right">Pending</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= count($performed) > 0 ? (is_null($performed->performed_by) ? 'Pending' : $this->users_model->user($performed->performed_by)->user_name) : "No assigned task" ?></td>
                                                <td><?= count($performed) > 0 ? (is_null($performed->performed_by) ? 'Pending' : $performed->perform_time) : "No assigned task" ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col s12 m4">
            <div class="col s12 right-align">
                <?php if ($procedure_complete && $tasks_complete && $is_pm && $specs->equipment_stage < ($test_stage == 1 ? 4 : 8)) { ?>
                    <button class="ajax btn white-text orange lighten-2" value="projects/assembly_complete/<?= $details->equipment_id ?>">
                        Complete Testing
                    </button>
                <?php }
                if ($specs->equipment_stage >= ($test_stage == 1 ? 4 : 8)) {
                    ?>
                    <button id="equipment/test_report/<?= urlencode(base64_encode($details->equipment_assembly_id)) ?>"
                            class="link btn white-text cyan">
                        Test report
                    </button>
                    <?php
                }
                ?>
            </div>
            <ul class="collection">
                <li class="collection-item center">
                    <h5 class="card-title ">
                        <?= $specs->equipment_stage < ($test_stage == 1 ? 4 : 8) ? $this->equipment_model->stage($specs->equipment_stage) : '<span class="green-text text-darken-4">COMPLETE :-)</span>' ?>
                    </h5>
                    Current Stage
                </li>
                <?php
                if ($specs->equipment_stage < ($test_stage == 1 ? 4 : 8)) {
                    ?>
                    <li class="collection-item center">
                        <h5 class="card-title ">
                            <?= $this->equipment_model->priority($details->priority) ?>
                        </h5>
                        Priority
                    </li>
                    <?php
                }
                ?>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s6 center">
                            <p class="medium">Start date</p>
                            <h6 class="green-text">
                                <strong>
                                    <?php
                                    $date = $this->equipment_model->assembly_date(true, $details->equipment_id);
                                    echo is_null($date) ? "<span class='red-text'>No task defined</span>" : $date;
                                    ?>
                                </strong>
                            </h6>
                        </div>
                        <div class="col s6 center">
                            <p class="medium">Completion date <b>(E)</b></p>
                            <h6 class="green-text">
                                <strong>
                                    <?php
                                    $date = $this->equipment_model->assembly_date(false, $details->equipment_id);
                                    echo is_null($date) ? "<span class='red-text'>No task defined</span>" : $date;
                                    ?>
                                </strong>
                            </h6>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s10 center">
                            <h6 class="orange-text">
                                <strong>
                                    <?= $this->users_model->user($details->assembly_manager)->user_name ?>
                                </strong>
                            </h6>
                            <span class="black-text">Testing Manager </span>
                        </div>
                        <div class="col s2 blue-text">
                            <?php if ($this->users_model->requires_role(array('update_proj'))) { ?>
                                <i class="mdi-editor-border-color modal-trigger" href="#edit-manager"></i>
                            <?php } ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s12 black-text center">
                            Equipment No:
                            <?= $specs->equipment_no ?>
                        </div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s12 black-text center">
                            Equipment type.
                            <?php $component = $this->components_model->details($specs->component_id);
                            echo $component->component_name ?>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>


<div id="edit-manager" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Change test manager</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('assembly/change_manager/' . urlencode(base64_encode($details->equipment_assembly_id))) ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="pm" name="assembly_manager">
                                <option value="" disabled selected>--Choose new testing Manager--</option>
                                <?php foreach ($this->users_model->has_powers('test_equipment') as $pm) { ?>
                                    <option value="<?= $pm->user_id ?>"><?= $pm->user_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="pm">Test Manager</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn orange darken-4 waves-effect waves-light right" type="submit">
                                Change manager
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>
<div id="new_task" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">New test task</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('assembly/new_task/' . urlencode(base64_encode($details->equipment_assembly_id))) ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="hidden" name="redirect" value="/assembly/test/<?= $test_stage == 1 ? 'one' : 'two' ?>/" required/>
                            <input id="title" type="text" name="title" required/>
                            <label for="title">Task title</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="sdate" type="text" name="sdate" value="<?= date("Y-m-d") ?>" required/>
                            <label for="sdate">Expected Start date</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="edate" type="text" name="edate" value="<?= date("Y-m-d") ?>" required/>
                            <label for="edate">Expected Due date</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="pm" multiple name="assigned_staff[]" data-placeholder="Assign staff this task"
                                    class="browser-default chosen-select">
                                <option value="" disabled>--Choose staff--</option>
                                <?php foreach ($this->users_model->has_powers('test_equipment') as $pm) { ?>
                                    <option value="<?= $pm->user_id ?>"><?= $pm->user_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="pm" class="active">Assigned staff</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="steps" multiple name="required_steps[]" data-placeholder="required guidelines for this task"
                                    class="browser-default chosen-select">
                                <option value="" disabled>--Procedure to follow--</option>
                                <?php foreach ($assembly_steps as $step) {
                                    if (!$this->assembly_model->is_tasked($step->ag_id, $details->equipment_assembly_id)) {
                                        ?>
                                        <option value="<?= $step->ag_id ?>"><?= $step->step_description ?></option>
                                    <?php }
                                } ?>
                            </select>
                            <label for="steps" class="active">Procedure to follow</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="predecessor" multiple name="predecessor[]" data-placeholder="select predecessor tasks"
                                    class="browser-default chosen-select">
                                <option value="" disabled>--Predecessor task--</option>
                                <?php foreach ($tasks as $task) { ?>
                                    <option value="<?= $task->assembly_schedule_id ?>"><?= $task->schedule_title ?></option>
                                <?php } ?>
                            </select>
                            <label for="predecessor" class="active">Predecessor tasks</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="instructions" class="materialize-textarea" name="instructions" required></textarea>
                            <label for="instructions">Task instructions</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn orange darken-4 waves-effect waves-light right" type="submit">
                                Create task
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>

