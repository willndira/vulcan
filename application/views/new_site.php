<div class="row">
    <div class="col s12">
        <ul id="task-card" class="collection with-header z-depth-1">
            <li class="collection-header cyan darken-2 white-text">
                <h5><?= $page ?></h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('sites/register') ?>">
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="first_name" name="site_name" type="text" required>
                            <label for="first_name">Site Name</label>
                        </div>
                        <div class="input-field col s6">
                            <select id="status" name="site_status">
                                <option value="ONLINE">ONLINE</option>
                                <option value="OFFLINE">OFFLINE</option>
                                <option value="UNDER MAINTENANCE">UNDER MAINTENANCE</option>
                                <option value="UNDER CONSTRUCTION">UNDER CONSTRUCTION</option>
                            </select>
                            <label for="status">Site Status</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="s_zone" type="text" name="site_zone" required>
                            <label for="s_zone">Site Zone</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="s_email" type="email" name="site_email" required>
                            <label for="s_email">Site email</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="s_loc" type="text" name="site_location_name" required>
                            <label for="s_loc">Location Name</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="s_geo" type="text" name="site_geo_location" required>
                            <label for="s_geo">Location Coordinates</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="description" name="site_about" class="materialize-textarea"></textarea>
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