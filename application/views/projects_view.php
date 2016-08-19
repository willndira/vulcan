<div class="row">
    <div class="col s12 m8">
        <div class="row">
            <div class="col s12">
                <h5 class="task-card-title grey-text">
                    <i class="mdi-navigation-chevron-right"></i>
                    <?= $page ?>
                </h5>
                <h6 class="medium green-text">
                    Projects
                </h6>
            </div>
            <div class="col s12">
                <ul id="task-card" class="collection with-header">
                    <li class="collection-item">
                        <table class="responsive-table dt display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Project Name</th>
                                <th>Site</th>
                                <th>Client</th>
                                <th>P. Manager</th>
                                <th>Salesperson</th>
                                <th>Stage</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($projects as $project) { ?>
                                <tr class="link"
                                    id="<?= 'projects/profile/' . urlencode(base64_encode($project->project_id)) ?>">
                                    <td><?= $project->project_id ?></td>
                                    <td><?= $project->project_name ?></td>
                                    <td><?= $this->sites_model->site($project->site_id)->site_name ?></td>
                                    <td><?= $project->project_client ?></td>
                                    <td><?= $project->project_manager ? $this->users_model->user($project->project_manager)->user_name : "<span class='orange-text'>Not specified :-( </span>" ?></td>
                                    <td><?= $this->users_model->user($project->project_proposer)->user_name ?></td>
                                    <td><?= $this->projects_model->stage($project->project_stage) ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </li>
                </ul>
            </div>
            <?php
            $my_projects = $this->crud_model->get_records('projects', 'project_proposer', $this->users_model->user()->user_id);
            if ($page == 'Projects Library') { ?>
                <div class="col s12">
                    <h5 class="card-title text-darken-4 orange-text">
                        <i class="mdi-navigation-chevron-right"></i>
                        My proposals
                    </h5>
                    <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                        <li>
                            <div class="collapsible-header active">
                            </div>
                            <div class="collapsible-body" style=" padding: 20px 10px;">
                                <table class="responsive-table dt display" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Site</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php foreach ($my_projects as $project) { ?>
                                        <tr class="link" id="<?= 'projects/profile/' . urlencode(base64_encode($project->project_id)) ?>">
                                            <td><?= $project->project_id ?></td>
                                            <td><?= $this->sites_model->site($project->site_id)->site_name ?></td>
                                            <td><?= $project->project_name ?></td>
                                            <td><?= $project->project_client ?></td>
                                            <td><?= $this->projects_model->stage($project->project_stage) ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col s12">
                    <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                        <li>
                            <div class="collapsible-header active" style="padding: 10px;">
                                <span class="red-text">Trashed projects</span>
                            </div>
                            <div class="collapsible-body" style=" padding: 20px 10px;">

                                <table class="responsive-table dt display" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Site</th>
                                        <th>Client</th>
                                        <th>P. Manager</th>
                                        <th>Salesperson</th>
                                        <th>Stage</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php foreach ($this->db->get_where('tbl_projects', array("deleted" => true))->result() as $project) {
                                        ?>
                                        <tr class="link"
                                            id="<?= 'projects/profile/' . urlencode(base64_encode($project->project_id)) ?>">
                                            <td><?= $project->project_id ?></td>
                                            <td><?= $project->project_name ?></td>
                                            <td><?= $this->sites_model->site($project->site_id)->site_name ?></td>
                                            <td><?= $project->project_client ?></td>
                                            <td><?= $project->project_manager ? $this->users_model->user($project->project_manager)->user_name : "<span class='orange-text'>Not specified :-( </span>" ?></td>
                                            <td><?= $this->users_model->user($project->project_proposer)->user_name ?></td>
                                            <td><?= $this->projects_model->stage($project->project_stage) ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </li>
                    </ul>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="col s12 m4">
        <div class="row">
            <div class="col s12" style="margin-top: 20px;">
                <?php
                if ($this->users_model->requires_role(array('create_ticket'))
                ) {
                    ?>
                    <a class="btn btn-xs orange right modal-trigger" href="#new-project">
                        Propose a Project
                    </a>
                <?php } ?>
            </div>
            <div class="col s12">
                <ul id="task-card" class="collection with-header">
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s6 center">
                                <h5 class="green-text">
                                    <strong>
                                        <?= count($this->crud_model->get_records('projects')) ?>
                                    </strong>
                                </h5>
                                <p class="medium">All projects</p>
                            </div>
                            <div class="col s6 center">
                                <h5 class="green-text">
                                    <strong>
                                        <?= count($my_projects) ?>
                                    </strong>
                                </h5>
                                <p class="medium">My proposals</p>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s4 center">
                                <h5 class="orange-text">
                                    <strong>
                                        <?= $approved = count($this->crud_model->get_records('projects', "project_stage > ", 2)) ?>
                                    </strong>
                                </h5>
                                <p class="medium">Approved</p>
                            </div>
                            <div class="col s4 center">
                                <h5 class="orange-text">
                                    <strong>
                                        <?= -($not_installed = count($this->crud_model->get_records('projects', "project_stage", 5))) + $approved ?>
                                    </strong>
                                </h5>
                                <p class="medium">On works</p>
                            </div>
                            <div class="col s4 center">
                                <h5 class="orange-text">
                                    <strong>
                                        <?= count($this->crud_model->get_records('projects', "project_stage", 5)) ?>
                                    </strong>
                                </h5>
                                <p class="medium">Completed</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col s12">
                <ul class="collection">
                    <li class="collection-item">
                        <h5 class="medium black-text">Latest Activities</h5>
                        <?php
                        $this->db->limit(5);
                        $this->db->order_by("pt_id", "DESC");
                        foreach ($this->db->get("tbl_project_timeline")->result() as $activity) { ?>
                            <p class="medium-small">
                                <strong class="cyan-text link" id="profile/user/<?= urlencode(base64_encode($activity->user_id)) ?>">
                                    <i class="mdi-action-verified-user"></i> <?= $this->users_model->user($activity->user_id)->user_name ?>
                                </strong>
                                <span class="right orange-text">
                                        <i class="mdi-av-timer"></i> <?= $activity->pt_time ?>
                                </span>
                                <br/>

                                <span style="padding-left: 10px">
                                    <?= $activity->pt_action ?>
                                    <span class="link blue-text" id="projects/profile/<?= urlencode(base64_encode($activity->project_id)) ?>">
                                        Project: <?= $this->projects_model->project($activity->project_id)->project_name ?>
                                    </span>
                                </span>
                            </p>
                            <br/>
                        <?php }
                        ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="new-project" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Create a project proposal</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('projects/register') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="first_name" name="project_name" type="text" required>
                            <label for="first_name">Project Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="c_name" type="text" name="client_name" required>
                            <label for="phone">Client Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="pm" name="project_manager" data-placeholder="Choose a site..." class="chosen-select browser-default">
                                <option value="" disabled selected>--Choose Project Manager--</option>
                                <?php foreach ($this->users_model->has_powers('man_project') as $pm) { ?>
                                    <option value="<?= $pm->user_id ?>"><?= $pm->user_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="pm" class="active">Project Manager</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="ps" data-placeholder="Choose a site..." class="chosen-select browser-default" name="project_site_id">
                                <option value="" disabled selected>--Choose Project Site--</option>
                                <?php foreach ($this->crud_model->get_records('sites') as $site) { ?>
                                    <option value="<?= $site->site_id ?>"><?= $site->site_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="ps" class="active">Project Site</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input type="date" id="start_date" value="<?= date('Y-m-d') ?>" name="start_date"
                                   required>
                            <label for="start_date" class="active">Project Start Date</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="end_date" value="<?= date("Y-m-d", strtotime("+3 month")) ?>"
                                   type="date" name="end_date" required>
                            <label for="end_date" class="active">Project Due Date</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="description" name="description" class="materialize-textarea"></textarea>
                            <label for="description">A brief Description of the proposal</label>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <button class="btn orange darken-4 waves-effect waves-light right" type="submit">
                                    Create
                                    <i class="mdi-content-send right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>