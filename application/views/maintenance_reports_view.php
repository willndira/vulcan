<div class="row">
    <div class="col s12 m3">
        <h5 class="card-title grey-text">
            <i class="mdi-navigation-chevron-right"></i>
            <?= $page ?>
        </h5>
    </div>
    <div class="col s12 m8">
        <form action="<?= site_url('reports/maintenance') ?>" method="post">
            <div class="row">
                <div class="input-field col s12 m4">
                    <label for="filter" class="active">Report Type</label>
                    <select id="filter" name="filter" class="chosen-select browser-default">
                        <option value="" disabled selected>Filter Reports</option>
                        <option <?= $_POST['filter'] == "site" ? "selected" : "" ?> value="site">Sites Reports</option>
                        <option <?= $_POST['filter'] == "technician" ? "selected" : "" ?> value="technician">Technicians
                            Report
                        </option>
<!--                        <option --><?//= $_POST['filter'] == "raised" ? "selected" : "" ?><!-- value="raised">Raised Tickets</option>-->
                        <option <?= $_POST['filter'] == "closed" ? "selected" : "" ?> value="closed">Closed Tickets
                        </option>
                        <option <?= $_POST['filter'] == "remote" ? "selected" : "" ?> value="remote">Remote Tickets
                        </option>
                        <option <?= $_POST['filter'] == "non_remote" ? "selected" : "" ?> value="non_remote">Site Visit
                            Tickets
                        </option>
                        <option <?= $_POST['filter'] == "diagram" ? "selected" : "" ?> value="diagram">
                            Per Day Diagrams
                        </option>
<!--                        <option --><?//= $_POST['filter'] == "unresolved" ? "selected" : "" ?><!-- value="unresolved">Unresolved-->
<!--                            Tickets-->
<!--                        </option>-->
<!--                        <option --><?//= $_POST['filter'] == "calendar" ? "selected" : "" ?><!-- value="calendar">Tickets-->
<!--                            Calendar-->
<!--                        </option>-->
<!--                        <option --><?//= $_POST['filter'] == "asset_requests" ? "selected" : "" ?><!-- value="asset_requests">-->
<!--                            Asset Requests-->
<!--                        </option>-->
                    </select>
                </div>
                <div class="input-field col s6 m3">
                    <input type="text" class="datepicker" name="kutoka" value="<?= $_POST['kutoka'] ?>" id="from_date"
                           required/>
                    <label for="from_date">From</label>
                </div>
                <div class="input-field col s6 m3">
                    <input type="text" class="datepicker" name="mpaka" value="<?= $_POST['mpaka'] ?>" id="to_date"
                           required/>
                    <label for="to_date">To</label>
                </div>
                <div class="input-field col s12 m2">
                    <input type="submit" class="btn btn-block green white-text" value="Generate"/>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <?= $report ?>
</div>
