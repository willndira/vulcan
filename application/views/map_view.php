<div id="card-widgets">
    <div class="row">
        <div class="col s12">
            <h5 class="card-title grey-text text-darken-4">
                <i class="mdi-navigation-chevron-right"></i>
                <?= $page ?>
            </h5>
            <p class="medium green-text">Sites shown on a map</p>
        </div>
        <div class="col s12">
            <div class="map-card">
                <div class="card">
                    <div class="card-image waves-effect waves-block waves-light">
                        <div id="map-canvas"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php
$sites = "";
foreach ($this->crud_model->get_records("sites") as $site) {
    $sites .= ",['" . addslashes($site->site_name) . "'," . $site->site_geo_location . "]";
}
?>
<script>
    var locations = [
        ['KAPS CENTER', -1.296572, 36.797775]<?=$sites ?>
    ];
</script>

<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAZnaZBXLqNBRXjd-82km_NO7GUItyKek"></script>