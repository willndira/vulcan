<?php
$is_allowed = true;
$stage = $this->projects_model->stage($details->project_id);
$handover = $this->crud_model->get_record('project_handover', 'project_id', $details->project_id);
if ($handover->receiving_user != $this->users_model->user()->user_id) {
    $is_allowed = false; ?>
    <div class="row">
        <div class="col s12">
            <div id="card-alert" class="card orange lighten-5">
                <div class="card-content orange-text">
                    Can't perform any action because you have not been assigned this project.
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div id="profile-page-wall-share">
    <div class="container">
        <div class="row">
            <div class="col s6 ">
                <h4 class="header2"><?= $page ?></h4>
            </div>
            <div class="col s6">
                <ul class="tabs tab-profile green darken-4 z-depth-5">
                    <li class="tab col s3">
                        <a class=" waves-effect white-text waves-light" href="#review">
                            <i class="mdi-image-timelapse"></i>
                            PREPARE
                        </a>
                    </li>
                    <li class="tab col s3">
                        <a class="waves-effect white-text waves-light" href="#assembly">
                            <i class="mdi-editor-border-color"></i>
                            INSTALL
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- UpdateStatus-->
        <div id="review" class="tab-content lighten-4">
            <div id="card-widgets">
                <div class="row">
                    <div class="col s12 m7">
                        <ul id="task-card" class="collection with-header z-depth-3">
                            <li class="collection-header green darken-2">
                                <h5 class="task-card-title">Installation Materials</h5>
                            </li>
                            <?php foreach ($this->projects_model->components($details->project_id) as $req) {
                                $components = $this->db->get_where('tbl_component_items', array(
                                    'component_id' => $req->component_id,
                                    'component_type' => false
                                ))->result();
                                foreach ($components as $component) { ?>
                                    <li class="collection-item">
                                        <div class="row">
                                            <div class="col s10">
                                                <input type="checkbox" <?= !$is_allowed ? 'disabled' : '' ?>
                                                       id="<?= $component->model_id ?>"/>
                                                <label for="<?= $component->model_id ?>">
                                                    <?= $component->model_qty * $req->component_qty; ?>
                                                    <?php $model = $this->items_model->get_model_details($component->model_id);
                                                    echo $model->make_name . ' ' . $model->model_name;
                                                    ?>
                                                </label>

                                                <p class="task-cat orange"><?= $component->component_description ?></p>
                                            </div>
                                            <div class="col s2 center-align">
                                                <div class="switch">
                                                    <label>
                                                        Request
                                                        <input
                                                            class="receive-site" <?= !$is_allowed ? 'disabled' : '' ?>
                                                            type="checkbox">
                                                        <span class="lever"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <?php
                                }
                            } ?>
                        </ul>
                    </div>
                    <div class="col s12 m5">
                        <ul id="task-card" class="collection with-header z-depth-3">
                            <li class="collection-header green darken-2">
                                <h5 class="task-card-title">Equipment Handover</h5>
                            </li>
                            <li class="collection-item">
                                <div class="row">
                                    <div class="col s5 grey-text darken-1">
                                        Confirmation:
                                    </div>
                                    <div
                                        class="col s7 grey-text text-darken-4 left-align">
                                        <div class="switch">
                                            <label>
                                                Waiting
                                                <input class="receive-site" <?= !$is_allowed ? 'disabled' : '' ?>
                                                       type="checkbox">
                                                <span class="lever"></span> Received
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="collection-item">
                                <div class="row">
                                    <div class="col s5 grey-text darken-1">
                                        Handed Over By:
                                    </div>
                                    <div
                                        class="col s7 grey-text text-darken-4 left-align">
                                        <?= $this->users_model->user($handover->handing_user)->user_name ?>
                                    </div>
                                </div>
                            </li>
                            <li class="collection-item">
                                <div class="row">
                                    <div class="col s5 grey-text darken-1">
                                        Received By:
                                    </div>
                                    <div
                                        class="col s7 grey-text text-darken-4 left-align">
                                        <?= $this->users_model->user($handover->receiving_user)->user_name ?>
                                    </div>
                                </div>
                            </li>
                            <li class="collection-item">
                                <div class="row">
                                    <div class="col s5 grey-text darken-1">
                                        Handover Date:
                                    </div>
                                    <div
                                        class="col s7 grey-text text-darken-4 left-align">
                                        <?= $handover->handover_time ?>
                                    </div>
                                </div>
                            </li>
                            <li class="collection-item">
                                <div class="row">
                                    <div class="col s5 grey-text darken-1">
                                        Comment:
                                    </div>
                                    <div
                                        class="col s7 grey-text text-darken-4 left-align">
                                        <?= $handover->handover_comments ?>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- CreateAlbum -->
        <div id="assembly" class="tab-content lighten-4">
            <div id="card-widgets">
                <div class="row">
                    <div class="col s12 m6">
                        <div class="row">
                            <div class="col s12">
                                <ul id="task-card" class="collection with-header">
                                    <li class="collection-header green darken-2">
                                        <h5 class="task-card-title">INSTALLATION GUIDE</h5>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php foreach ($this->projects_model->components($details->project_id) as $component) { ?>
                            <div class="row">
                                <div class="col s12">
                                    <ul id="task-card" class="collection with-header z-depth-3">
                                        <li class="collection-header orange">
                                            <h6 class="task-card-title"><?= $component->component_name ?></h6>
                                        </li>
                                        <?php
                                        foreach ($this->crud_model->get_records('component_install_plan', 'component_id', $component->component_id) as $guide) { ?>
                                            <li class="collection-item">
                                                <div class="row">
                                                    <div class="col s12">
                                                        <input id="install_step" <?= !$is_allowed ? 'disabled' : '' ?>
                                                               type="checkbox">
                                                        <label for="install_step">
                                                            <?= $guide->plan_guide ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- TESTING -->
                    <div class="col s12 m6">
                        <div class="row">
                            <div class="col s12">
                                <ul id="task-card" class="collection with-header">
                                    <li class="collection-header green darken-2">
                                        <h5 class="task-card-title">TESTING GUIDE</h5>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php foreach ($this->projects_model->components($details->project_id) as $component) { ?>
                            <div class="row">
                                <div class="col s12">
                                    <ul id="task-card" class="collection with-header z-depth-3">
                                        <li class="collection-header orange">
                                            <h6 class="task-card-title"><?= $component->component_name ?></h6>
                                        </li>
                                        <?php
                                        foreach ($this->crud_model->get_records('component_install_test', 'component_id', $component->component_id) as $guide) { ?>
                                            <li class="collection-item">
                                                <div class="row">
                                                    <div class="col s12">
                                                        <input id="test_step" <?= !$is_allowed ? 'disabled' : '' ?>
                                                               type="checkbox">
                                                        <label for="test_step">
                                                            <?= $guide->cit_guide ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Floating Action Button -->
<div class="fixed-action-btn" style="bottom: 35px; right: 25px;">
    <a href="<?= site_url('projects/profile/' . $details->project_id) ?>" title="Review Project"
       class="btn-floating btn-large">
        <i class="mdi-action-info-outline"></i>
    </a>
</div>
<!-- Floating Action Button -->