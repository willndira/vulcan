<div class="row">
    <div class="col s12 m5">
        <h5 class="grey-text">
            <i class="mdi-navigation-chevron-right"></i>
            <?= $page ?>
        </h5>
    </div>
    <div class="col s12 m7">
        <?php if ($this->users_model->requires_role(array('add_sites'))) { ?>
            <a href="#new-site" class="btn modal-trigger green right">
                New site
            </a>
        <?php } ?>
    </div>
    <div class="col s12 l8">
        <div class="row">
            <div class="col s12">
                <h5 class="card-title text-darken-4 orange-text">
                    <i class="mdi-navigation-chevron-right"></i>
                    All registered sites
                </h5>
                <ul id="task-card" class="collection">
                    <li class="collection-item">
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
                            <?php foreach ($sites as $site) { ?>
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
                                <?php
                                foreach ($trashed as $site) { ?>
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
                        foreach ($this->db->get("tbl_site_timeline")->result() as $activity) { ?>
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

<div id="new-site" data-keyboard="false" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Register new site</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('sites/register') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="first_name" value="<?= set_value("site_name") ?>" name="site_name" type="text" required>
                            <label for="first_name">Site Name</label>
                        </div>
                        <!--                        <div class="input-field col s6">-->
                        <!--                            <select id="status" name="site_status">-->
                        <!--                                <option value="" disabled selected>Site status</option>-->
                        <!--                                <option -->
                        <? //= set_value("site_status") == 1 ? "selected" : "" ?><!-- value="1">Online</option>-->
                        <!--                                <option -->
                        <? //= set_value("site_status") == 2 ? "selected" : "" ?><!-- value="2">Offline</option>-->
                        <!--                                <option -->
                        <? //= set_value("site_status") == 3 ? "selected" : "" ?><!-- value="3">Under maintenance</option>-->
                        <!--                                <option -->
                        <? //= set_value("site_status") == 4 ? "selected" : "" ?><!-- value="4">Under construction</option>-->
                        <!--                            </select>-->
                        <!--                            <label for="status">Site Status</label>-->
                        <!--                        </div>-->
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="s_zone" type="text" value="<?= set_value("site_zone") ?>" name="site_zone" required>
                            <label for="s_zone">Site Zone</label>
                        </div>
                        <div class="input-field col s6">
                            <select name="category" class="chosen-select browser-default" data-placeholder="Site SLA" id="category">
                                <option value="" disabled>--select SLA category--</option>
                                <?php
                                foreach ($this->crud_model->get_records("sla") as $sla) {
                                    printf('<option value="%d" %s>%s</option>', $sla->sla_id, (set_value('sla_id') == $sla->sla_id ? 'selected' : ''), $sla->sla_name);
                                }
                                ?>
                            </select>
                            <label for="category" class="active">Site SLA</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="s_email" type="email" value="<?= set_value("site_email") ?>" name="site_email" required>
                            <label for="s_email">Site email</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="s_geo" type="text" value="<?= set_value("site_geo_location") ?>" name="site_geo_location" required>
                            <label for="s_geo">Location Coordinates</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="description" name="site_about" class="materialize-textarea"><?= set_value("site_about") ?></textarea>
                            <label for="description">A brief Description of the sites</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn cyan waves-effect waves-light right" type="submit"
                                    name="action">Create
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>