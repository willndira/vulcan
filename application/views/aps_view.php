<div class="row">
    <div class="col s12 m5">
        <h5 class="grey-text">
            <i class="mdi-navigation-chevron-right"></i>
            <?= $page ?>
        </h5>
    </div>
    <div class="col s12 m7">
        <?php if ($this->users_model->requires_role(array('add_sites'))) { ?>
            <a href="#new-aps" class="btn modal-trigger green right">
                New APS
            </a>
        <?php } ?>
    </div>
    <div class="col s12 l8">
        <div class="row">
            <div class="col s12">
                <h5 class="card-title text-darken-4 orange-text">
                    <i class="mdi-navigation-chevron-right"></i>
                    All registered APS
                </h5>
                <ul id="task-card" class="collection">
                    <li class="collection-item">
                        <table class="responsive-table dt display" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Aps No</th>
                                    <th>Code Name</th>
                                    <th>Ip Address</th>
                                    <th>Site Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($aps as $row) { ?>
                                    <tr class="link" id="aps/profile/<?= urlencode(base64_encode($row->aps_id)) ?>">
                                        <td><?= $row->aps_id ?></td>
                                        <td><?= $row->aps_no ?></td>
                                        <td><?= $row->code_name ?></td>
                                        <td><?= $row->ip_address ?></td>
                                        <td><?= $row->site_name ?></td>
                                        <td><?= $row->status == 1 ? 'Online' : 'Offline' ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </li>
                </ul>
            </div>
            <div class="col s12">
                <ul class="collapsible collapsible-accordion" data-collapsable="accordion">
                    <li>
                        <div class="collapsible-header" style="padding: 10px;">
                            <span class="red-text">Trashed sites</span>
                        </div>
                        <div class="collapsible-body" style=" padding: 20px 10px;">
                            <table class="responsive-table dt display" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Zone</th>
                                        <th>SLA</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($trashed as $site) { ?>
                                        <tr class="link" id="sites/profile/<?= urlencode(base64_encode($site->site_id)) ?>">
                                            <td><?= $site->site_id ?></td>
                                            <td><?= $site->site_name ?></td>
                                            <td><?= $site->site_zone ?></td>
                                            <td><?= $this->sites_model->sla($site->site_category)->sla_name ?></td>
                                            <td><?= $site->site_email ?></td>
                                            <td><?= $this->sites_model->status($site->site_status) ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col l4 hide-on-med-and-down">
        <div class="row">
            <div class="col s12">
                <ul id="task-card" class="collection with-header">
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s12 center">
                                <h5 class="green-text">
                                    <strong>
                                        <?= $total = count($sites) ?>
                                    </strong>
                                </h5>
                                <p class="medium">All sites</p>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s6 green-text center">
                                <h5 class="cyan-text">
                                    <strong>
                                        <?= $total - count($coming_soon) ?>
                                    </strong>
                                </h5>
                                <p class="medium">Online</p>
                            </div>
                            <div class="col s6 center blue-text">
                                <h5 class="orange-text">
                                    <strong>
                                        <?= count($coming_soon) ?>
                                    </strong>
                                </h5>
                                <p class="medium">Coming Soon</p>
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
                        $this->db->order_by("st_id", "DESC");
                        foreach ($this->db->get("tbl_site_timeline")->result() as $activity) {
                            ?>
                            <p class="medium-small">
                                <strong class="cyan-text link" id="profile/user/<?= urlencode(base64_encode($activity->user_id)) ?>">
                                    <i class="mdi-action-verified-user"></i> <?= $this->users_model->user($activity->user_id)->user_name ?>
                                </strong>
                                <span class="right orange-text">
                                    <i class="mdi-av-timer"></i> <?= $activity->st_time ?>
                                </span>
                                <br/>

                                <span style="padding-left: 10px">
                                    <?= $activity->st_action ?>
                                    <span class="link blue-text" id="sites/profile/<?= urlencode(base64_encode($activity->site_id)) ?>">
                                        Site: <?= $this->sites_model->site($activity->site_id)->site_name ?>
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

<!------------------------------------------------APS------------------------------------------------------->
<div id="new-aps" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Add APS</h5>
            </li>
            <li class="collection-item">
                <form class="col s12" method="post" action="<?= site_url('aps/register') ?>">


                    <div class="row">
                        <div class="input-field col s6">
                            <input id="aps_no"  name="aps_no" type="text" required>
                            <label for="aps_no" class="active">APS No.</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="code_name"  name="code_name" type="text" required>
                            <label for="code_name" class="active">Code Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="ip_address"  name="ip_address" type="text" required>
                            <label for="ip_address" class="active">IP Address</label>
                        </div>

                        <div class="input-field col s6">
                            <input id="aps_os"  name="aps_os" type="text" required>
                            <label for="aps_os" class="active">OS</label>
                        </div>

                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select name="site_id" id="aps_status" class="chosen-select browser-default">
                                <option disabled="true">Select Site</option>
                                <?php
                                if (isset($sites) && count($sites)) {
                                    foreach ($sites as $row) {
                                        ?>
                                        <option  value="<?= $row->site_id ?>"><?= $row->site_name ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="aps_status" class="active">Site Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select name="aps_status" id="aps_status" class="chosen-select browser-default">
                                <option disabled="true">Aps Status</option>
                                <option value="1">Active</option>
                                <option value="0">Offline</option>
                            </select>
                            <label for="aps_status" class="active">APS Status</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea name="aps_comments" id="aps_comments" class="materialize-textarea"></textarea>
                            <label for="aps_comments" class="active">Comments</label>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn orange waves-effect waves-light right" name="action">Add
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>