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
                        <div class="col s3"><strong>Project No:</strong></div>
                        <div class="col s6"><?= $details->project_id ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Assembly Manager:</strong></div>
                        <div class="col s6"><?= $this->users_model->user()->user_name ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Description:</strong></div>
                        <div class="col s6"><?= $details->project_description ?></div>
                    </div>
                </li>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3"><strong>Project Stage:</strong></div>
                        <div class="col s6"><?= $this->projects_model->stage($details->project_stage) ?></div>
                    </div>
                </li>
            </ul>
        </div>
        <hr/>
        <div class="col s12">
            <ul class="collection">
                <li class="collection-item">
                    <span class="col s6 offset-s3">
                        <h3>
                            Required fields: TODO
                        </h3>
                    </span>
                </li>
            </ul>
        </div>
        <br/>

        <div class="col s12">
            <hr/>
            <ul class="collection">
                <li class="collection-item">
                    <div class="row">
                        <div class="col s3 offset-s6">
                            <strong>Assembly Manager</strong>
                            <br/>
                            <br/>
                            <span class="blue-text" style="font-size: 20px; font-style: italic">
                                <?= $this->users_model->user()->user_name ?>
                            </span>
                            <hr/>
                            <br/>
                            <br/>
                            <hr/>
                            <strong>Signature</strong>
                            <br/>
                            <br/>
                            <br/>
                            <hr/>
                            <strong>Time & Stamp </strong>
                        </div>
                        <div class="col s3">
                            <strong>Installation Manager</strong>
                            <br/>
                            <br/>
                            <span class="blue-text" style="font-size: 20px; font-style: italic"><?= $this->users_model->user()->user_name ?></span>
                            <hr/>
                            <br/>
                            <br/>
                            <hr/>
                            <strong>Signature</strong>
                            <br/>
                            <br/>
                            <br/>
                            <hr/>
                            <strong>Time & Stamp </strong>
                        </div>
                        <div class="col s2"></div>
                    </div>
                </li>
            </ul>
            <hr/>
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
