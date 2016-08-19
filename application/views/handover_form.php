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
            <h4 class="grey-text right-align">Project Handover</h4>
            <h5 class="grey-text right-align">Project #<?= $details->project_id ?></h5>
            <h6 class="grey-text text-darken-4 right-align"><?= date("M, d Y") ?></h6>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col s12">
            <ul class="collection">
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Project Name:</strong></div>
                        <div class="col s6"><?= $details->project_name ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Sales Manager:</strong></div>
                        <div class="col s6"><?= $this->users_model->user()->user_name ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Client:</strong></div>
                        <div class="col s6"><?= $details->project_client ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Description:</strong></div>
                        <div class="col s6"><?= $details->project_description ?></div>
                    </div>
                </li>
            </ul>
        </div>
        <hr/>
        <div class="col s12">
            <h5 class="card-title orange-text text-darken-4">
                Installed Equipment
            </h5>
            <ul class="collection with-header">
                <table class="responsive-table dt display" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Equipment No</th>
                        <th>Model</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($assigned = $this->asset_model->all_assigned($details->project_id, 3, 2) as $eq) {
                        $equipment = $this->equipment_model->details($eq->asset_id);
                        ?>
                        <tr>
                            <td>
                                <?= $equipment->equipment_no ?>
                            </td>
                            <td>
                                <?= $this->crud_model->get_record('components', 'component_id', $equipment->component_id)->component_name ?>
                            </td>
                            <td><?= $this->equipment_model->stage($equipment->equipment_stage) ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </ul>
        </div>
        <br/>

        <div class="col s12">
            <hr/>
            <ul class="collection">
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3 offset-s6">
                            <strong>Salesperson/Project Manager</strong>
                            <br/>
                            <span class="blue-text" style="font-size: 14px; font-style: italic">
                                <?= $this->users_model->user()->user_name ?>
                            </span>
                            <hr/>
                            <br/>
                            <br/>
                            <hr/>
                            <strong>Signature</strong>
                            <br/>
                            <?=date("Y-m-d H:m")?>
                            <hr/>
                            <strong>Time & Stamp </strong>
                        </div>
                        <div class="col s3">
                            <strong>Client</strong>
                            <br/>
                            <span class="blue-text" style="font-size: 14px; font-style: italic">
                                <?= $details->project_client ?>
                            </span>
                            <hr/>
                            <br/>
                            <br/>
                            <hr/>
                            <strong>Signature</strong>
                            <br/>
                            <?=date("Y-m-d H:m")?>
                            <hr/>
                            <strong>Time & Stamp </strong>
                        </div>
                        <div class="col s2"></div>
                    </div>
                </li>
            </ul>
            <hr/>
        </div>

        <div class="col s12">
            <p>
                Confirm that the project is running as required before signing.
            </p>
            <?php if ($details->project_stage == 5 && ($this->users_model->user()->user_id == $details->project_proposer)) { ?>
                <button value="projects/confirm_handover/<?= $details->project_id ?>" <?= $details->project_stage > 5 ? "disabled" : "" ?>
                        class="ajax btn white-text cyan lighten-2 right">
                    <i class="mdi-navigation-check"></i> Handover
                </button>
            <?php } ?>
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
