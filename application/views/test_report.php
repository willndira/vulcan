<?php
$specs = $this->equipment_model->details($details->equipment_id);
?>
<div class="card-panel">
    <div class="row">
        <div class="col s6">
            <img src="<?= base_url() ?>assets/images/KAPS-logo.png" height="50px">
            <span class="grey-text">
                <br/>
                Kindaruma Lane,<br/> Nairobi,<br/> Kenya<br/>
                Phone : +254 732 146000
            </span>
        </div>
        <div class="col s6 right-align ">
            <h4 class="grey-text right-align">Equipment test report</h4>
            <h5 class="grey-text right-align">Equipment #<?= $details->equipment_id ?></h5>
            <h6 class="grey-text text-darken-4 right-align"><?= date("M, d Y") ?></h6>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col s12">
            <ul class="collection">
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Equipment No:</strong></div>
                        <div class="col s6"><?= $specs->equipment_no ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Testing Manager:</strong></div>
                        <div class="col s6"><?= $this->users_model->user($details->assembly_manager)->user_name ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Expected start Date:</strong></div>
                        <div class="col s3"><?= $this->equipment_model->assembly_date(true, $details->equipment_assembly_id)?></div>

                        <div class="col s3"><strong>Actual start Date:</strong></div>
                        <div class="col s3"><?=$this->equipment_model->start_date($details->equipment_assembly_id)->start_date ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Expected Completion Date:</strong></div>
                        <div class="col s3"><?= $this->equipment_model->assembly_date(false, $details->equipment_assembly_id) ?></div>

                        <div class="col s3"><strong>Actual Completion Date:</strong></div>
                        <div class="col s3"><?= $details->completion_date ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Report:</strong></div>
                        <div class="col s6"><?= $details->completion_report ?></div>
                    </div>
                </li>
            </ul>
        </div>
        <hr/>
        <div class="col s12">
            <h5 class="card-title orange-text text-darken-4">
               TEST TASKS
            </h5>
            <ul class="collection with-header">
                <?php
                foreach ($tasks = $this->crud_model->get_records("assembly_schedule", "equipment_assembly_id", $details->equipment_assembly_id) as $task) { ?>
                    <li class="collection-header">
                            <span class="green-text h5">
                                <?= $task->schedule_title ?>
                            </span>
                            <span class="right">
                                <?= $this->equipment_model->task_stage($task->schedule_stage) ?>
                            </span>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col l6 m12">
                                <h5 class="card-title orange-text center">Assigned staff</h5>
                                <ul class="collection">
                                    <?php
                                    foreach ($this->crud_model->get_records("assembly_team", "assembly_schedule_id", $task->assembly_schedule_id) as $staff) { ?>
                                        <li class="collection-item"><?= $this->users_model->user($staff->user_id)->user_name ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="col l6 m12">
                                <h5 class="card-title orange-text center">Predecessor tasks</h5>
                                <ul>
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
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <h6 class="card-title orange-text">
                            Procedures
                        </h6>
                        <table class="responsive-table display">
                            <thead>
                            <tr>
                                <th>Procedure</th>
                                <th>Result</th>
                                <th>Staff</th>
                                <th>Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($this->crud_model->get_records("assembly_schedule_steps", "schedule_id", $task->assembly_schedule_id) as $step) {
                                $guide = $this->equipment_model->guide_details($step->setup_guide_id);
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
                    <li class="collection-item">
                        <h6 class="card-title orange-text">
                            Completion report
                        </h6>
                        <?= $task->report; ?>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<div class="fixed-action-btn" style="bottom: 35px; right: 25px;">
    <a class="btn-floating btn-large">
        <i class="mdi-action-print"></i>
    </a>
    <ul>
        <li>
            <a href="#" class="btn-floating red darken-1">
                <i class="large mdi-communication-email"></i>
            </a>
        </li>
        <li>
            <a href="#" class="btn-floating yellow darken-1">
                <i class="large mdi-action-print"></i>
            </a>
        </li>
        <li>
            <a href="#" class="btn-floating green">
                <i class="large mdi-action-receipt"></i>
            </a>
        </li>
    </ul>
</div>
<!-- Floating Action Button -->
