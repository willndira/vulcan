<div class="row">
    <div class="col s12 m8">
        <ul class="collection with-header z-depth-1 ">
            <li class="collection-header cyan darken-2">
                <h5 class="white-text">My Proposed projects</h5>
            </li>
            <li class="collection-item">
                <table id="data-table-simple" class="responsive-table display" cellspacing="0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Client</th>
                        <th>Status</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($this->crud_model->get_records('projects', 'project_proposer', $this->users_model->user()->user_id) as $project) { ?>
                        <tr class="link" id="<?= 'projects/profile/' . $project->project_id ?>">
                            <td><?= $project->project_id ?></td>
                            <td><?= $project->project_name ?></td>
                            <td><?= $project->project_client ?></td>
                            <td><?= $this->projects_model->stage($project->project_stage) ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </li>
        </ul>
    </div>
    <div class="col s12 m4">
        <ul class="collection with-header z-depth-1 ">
            <li class="collection-header cyan darken-2">
                <h5 class="white-text"><?= $page ?></h5>
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
                        <div class="input-field col s12 m6">
                            <select id="pm" name="project_manager">
                                <option value="" disabled selected>--Choose Project Manager--</option>
                                <?php foreach ($this->users_model->has_powers('man_project') as $pm) { ?>
                                    <option value="<?= $pm->user_id ?>"><?= $pm->user_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="pm">Project Manager</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <select id="ps" name="project_site_id">
                                <option value="" disabled selected>--Choose Project Site--</option>
                                <?php foreach ($this->crud_model->get_records('sites') as $site) { ?>
                                    <option value="<?= $site->site_id ?>"><?= $site->site_name ?></option>
                                <?php } ?>
                            </select>
                            <label for="ps">Project Site</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input type="date" class="start_date" value="<?= date('Y-m-d') ?>" name="start_date"
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