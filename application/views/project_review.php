<div class="row">
    <div class="col s12 m8">
        <div class="row">
            <div class="col s12 ">
                <h5 class="task-card-title grey-text">
                    <i class="mdi-navigation-chevron-right"></i>
                    <?= $page ?>
                </h5>
                <h6 class="medium green-text">
                    Project review
                </h6>
            </div>
            <div class="col s12">
                <ul id="task-card" class="collection with-header ">
                    <li class="collection-header">
                        <h5 class="task-card-title orange-text">Reviews</h5>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s2">Date</div>
                            <div class="col s3">Staff</div>
                            <div class="col s5">Review</div>
                            <div class="col s2">Verdict</div>
                        </div>
                    </li>
                    <?php foreach ($this->crud_model->get_records("project_review", "project_id", $details->project_id) as $review) { ?>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s2"><?= $review->pr_time ?></div>
                                <div class="col s3"><?= $this->users_model->user($review->user_id)->user_name ?></div>
                                <div class="col s5"><?= $review->pr_comment ?></div>
                                <div class="col s2"><?= $review->pr_verdict ? "Approve" : "Reject" ?></div>
                            </div>
                        </li>
                    <?php } ?>
                    <?php if ($this->users_model->requires_role(array('auth_proj'))) { ?>
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s12">
                                    <?php if ($details->project_stage == 1 || $details->project_stage == 2) { ?>
                                        <button class="btn cyan waves-effect waves-light right link"
                                                id="projects/authorize/<?= urlencode(base64_encode($details->project_id)) ?>">
                                            Authorize
                                            <i class="mdi-navigation-check right"></i>
                                        </button>
                                    <?php }
                                    if ($details->project_stage == 1 || $details->project_stage == 3) { ?>
                                        <button class="btn orange waves-effect waves-light left link"
                                                id="projects/authorize/<?= urlencode(base64_encode($details->project_id)) ?>/rejected">
                                            Reject
                                            <i class="mdi-navigation-cancel right"></i>
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col s12 m4">
        <ul id="task-card" class="collection with-header">
            <li class="collection-header">
                <h5 class="task-card-title orange-text">Review</h5>
            </li>
            <li class="collection-item">
                <div class="row">
                    <form method="post" action="<?= site_url('projects/submit_review') ?>">
                        <input type="hidden" name="project_id"
                               value="<?= $details->project_id ?>" required/>

                        <div class="input-field col s12">
                            <select id="verdict" name='pr_verdict'>
                                <option value="" disabled selected>What's your view?</option>
                                <option value="0">Reject and return for review</option>
                                <option value="1">Approve and move to Level 2</option>
                            </select>
                            <label for="verdict">Type</label>
                        </div>
                        <div class="input-field col s12">
                            <textarea id="description" name="pr_comment" class="materialize-textarea"></textarea>
                            <label for="description">Review Comment</label>
                        </div>
                        <div class="input-field col s12">
                            <?php if (!$this->users_model->requires_role(array('revProj')) || $details->project_stage > 2) { ?>
                                <div id="card-alert" class="card orange">
                                    <div class="card-content  white-text">
                                        Not permitted/Past review
                                    </div>
                                </div>
                            <?php } else { ?>
                                <button class="btn cyan darken-4 waves-effect waves-light right" type="submit">
                                    Review
                                    <i class="mdi-content-send right"></i>
                                </button>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</div>