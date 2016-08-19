<div class="row">
    <div class="col s12 m11 l11 all_requests">

        <div class="card-panel">
            <div id="table-datatables">
                <h4 class="header"><?= $page ?></h4>

                <div class="row">
                    <div class="col s12 m12 l12">
                        <table id="data-table-simple" class="responsive-table display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>NAME</th>
                                <th>CLIENT</th>
                                <th>MANAGER</th>
                                <th>PROPOSED BY</th>
                                <th>SITE</th>
                                <th>TYPE</th>
                                <th>STAGE</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($projects as $project) { ?>
                                <tr class="link"
                                    id="sites/install/<?= urlencode(base64_encode(urlencode($project->project_id))) ?>">
                                    <td><?= $project->project_id ?></td>
                                    <td><?= $project->project_name ?></td>
                                    <td><?= $project->project_client ?></td>
                                    <td><?= $this->users_model->user($project->project_manager)->user_name ?></td>
                                    <td><?= $this->users_model->user($project->project_proposer)->user_name ?></td>
                                    <td><?= $this->sites_model->site($project->project_site_id)->site_name ?></td>
                                    <td><?= $project->project_type ?></td>
                                    <td><?= $project->project_stage ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>