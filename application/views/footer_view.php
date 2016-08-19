<!-- START RIGHT SIDEBAR NAV-->
<aside id="right-sidebar-nav">
    <ul id="chat-out" class="side-nav rightside-navigation">
        <li class="li-hover">
            <ul class="chat-collapsible" data-collapsible="expandable">
                <li>
                    <div class="collapsible-header teal white-text active">
                        <i class="mdi-social-whatshot"></i>
                        Notifications
                    </div>
                    <div class="collapsible-body recent-activity">
                        <?php foreach ($this->crud_model->get_records('notification_user', 'user_id', $this->users_model->user()->user_id) as $notification) {
                            $noti = $this->crud_model->get_record('notifications', 'notification_id', $notification->notification_id);
                            ?>
                            <div class="recent-activity-list chat-out-list row">
                                <div class="col s1 recent-activity-list-icon">
                                    <i class="mdi-social-notifications"></i>
                                </div>
                                <div class="col s11 recent-activity-list-text">
                                    <a><?= $noti->notification_header ?></a>
                                    <a><?= $noti->notification_time ?></a>

                                    <p><?= $noti->notification_message ?></p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</aside>
<!-- LEFT RIGHT SIDEBAR NAV-->


</div>
<!-- END WRAPPER -->

<!--end container-->
<br/>
<br/>
</section>
<!-- END CONTENT -->

<!-- //////////////////////////////////////////////////////////////////////////// -->
<!-- START FOOTER -->
<footer class="page-footer grey lighten-4">
    <div class="footer-copyright text-black">
        <div class="container">
            <div class="row hide-on-small-and-down">
                <div class="col m3">
                    <h5 class="card-title orange-text">Quick links</h5>
                    <ul class="left">
                        <li>
                            <a href="<?= site_url('help') ?>" target="_blank">Documentation</a>
                        </li>
                        <li>
                            <a href="<?= site_url('app/tech') ?>" target="_blank">Technician App</a>
                        </li>
                        <li>
                            <a href="<?= site_url('app/site') ?>" target="_blank">Site Supervisor App</a>
                        </li>
                    </ul>
                </div>
                <div class="col m3">
                    <h5 class="card-title orange-text">Other Systems</h5>
                    <ul class="left">
                        <li>
                            <a href="http://kapsportal.com" target="_blank">Kaps Portal</a>
                        </li>
                        <li>
                            <a href="http://kapsportal.com" target="_blank">Kaps Tickets</a>
                        </li>
                        <li>
                            <a href="http://kapsportal.com" target="_blank">Narok Epayment</a>
                        </li>
                    </ul>
                </div>
                <div class="col m3">

                </div>
            </div>
            <div class="row center">
            <span>Copyright © <?= date('Y') ?>
                <a class="orange-text text-darken-4" href="http://kaps.co.ke"
                   target="_blank">
                    KAPS
                </a>
                All rights reserved.
                <br/>
                UI and UX by
                <a class="blue-text text-darken-4" target="_blank" href="http://bluewave.co.ke/">Bluewave Int.</a>
            </span>
            </div>
        </div>
    </div>
</footer>
</div>
<!-- END MAIN -->
</div>
<!-- END FOOTER -->
<div id="new-item" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Register new item</h5>
            </li>
            <li class="collection-item">
                <form method="post" action="<?= site_url('items/register/yes') ?>">
                    <input type="hidden" name="store_id" value="1"/>
                    <div class="row">
                        <div class="input-field col s12 model">
                            <select id="model" name="model" data-placeholder="Select item model" class="browser-default chosen-select">
                                <option value="" selected disabled>---Select Model---</option>
                                <?php
                                foreach ($this->crud_model->get_records('item_models') as $type) {
                                    $model = $this->items_model->get_model_details($type->item_model_id);
                                    ?>
                                    <option
                                        value="<?= $type->item_model_id ?>"><?= $model->make_name . " " . $type->model_name ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <label for="model" class="active">Model e.g Xperia T2</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 type">
                            <input id="serial_no" type="text" name="serial_no" required/>
                            <label for="serial_no">Serial No.</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 make">
                            <input id="code" type="text" name="code" required/>
                            <label for="code">Unique Code</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="item_location" name="state" required>
                                <option value="" selected disabled>---Item State---</option>
                                <option value="1">Functional</option>
                                <option value="0">Defective</option>
                            </select>
                            <label for="item_location">Asset State</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn orange waves-effect waves-light right" type="submit"
                                name="action">Register
                            <i class="mdi-editor-mode-edit right"></i>
                        </button>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>
<div id="new-equipment" class="modal bottom-sheet">
    <div class="modal-content" style="padding: 5px !important;">
        <ul class="collection">
            <li class="collection-item">
                <h5 class="orange-text center">Register Equipment</h5>
            </li>
            <li class="collection-item">
                <form class="col s12" method="post" action="<?= site_url('equipment/register') ?>">
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="eq_no" name="eq_no" type="text" required>
                            <label for="eq_no">Equipment No</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select name="component_id" id="component" data-placeholder="Select equipment category"
                                    class="browser-default chosen-select">
                                <option value="" disabled>--Select equipment--</option>
                                <?php
                                foreach ($this->crud_model->get_records('components') as $component) {
                                    ?>
                                    <option
                                        value="<?= $component->component_id ?>"><?= $component->component_name ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <label for="component" class="active">Equipment Type</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="equipment_condition" name="equipment_condition">
                                <option value="" disabled selected>--Select equipment condition--</option>
                                <option value="1">Operational</option>
                                <option value="0">Faulty</option>
                            </select>
                            <label for="equipment_condition">Condition</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="equipment_availability" name="equipment_availability">
                                <option value="" disabled selected>--Select equipment availability--</option>
                                <option value="1">Available</option>
                                <option value="0">In Use</option>
                            </select>
                            <label for="equipment_availability">Availability</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                                    <textarea id="description" name="equipment_comment"
                                              class="materialize-textarea"></textarea>
                            <label for="description">Comments about equipment</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn orange waves-effect waves-light right" type="submit">Register
                                <i class="mdi-content-send right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript" src="<?= base_url() ?>assets/js/plugins/jquery-1.11.2.min.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/materialize.min.js"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/js/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/js/plugins/data-tables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/js/plugins/data-tables/data-tables-script.js"></script>
<script src="<?= base_url() ?>assets/js/plugins/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/plugins/prism/prism.js" type="text/javascript" charset="utf-8"></script>

<?php
if (isset($scripts)) {
    foreach ($scripts as $script) {
        ?>
        <script type="text/javascript" src="<?= base_url() ?>assets/js/<?= $script ?>.js"></script>
        <?php
    }
}
?>
<script>
    var base_url = '<?=site_url()?>';
</script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/plugins.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/custom-script.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.table2excel.js"></script>
<script type="text/javascript">
    var config = {
        '.chosen-select': {width: "100%"},
        '.chosen-select-deselect': {allow_single_deselect: true},
        '.chosen-select-no-single': {disable_search_threshold: 10},
        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
        '.chosen-select-width': {width: "95%"}
    };
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
    $("#export").on('click', function (e) {$("#report_body").table2excel({
        name: "Excel Document Name",
        filename: "Report.xls"
    });
    });
    $("#print").on('click', function () {
        var toPrint = document.getElementById("report_body");
        newWin = window.open("");
        newWin.document.write(toPrint.outerHTML);
        newWin.print();
        newWin.close();
    })
    ;
</script>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
    (function () {
        var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/5739b0fa77225b396dfd1ff4/default';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>
<!--End of Tawk.to Script-->
</body>

</html>