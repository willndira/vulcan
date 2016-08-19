<div class="row">
    <div class="col s12 m6 l3">
        <h5 class="card-title grey-text">
            <i class="mdi-navigation-chevron-right"></i>
            <?= $page ?>
        </h5>
    </div>
</div>
<div class="row">
    <div class="col s6 l3">
        <div class="card center link" id="items" style="background: transparent !important; border: 2px solid #2196F3; border-radius: 5px">
            <div class="card-content  blue-text">
                <p class="card-stats-title"><i class="mdi-editor-insert-drive-file"></i> Registered items</p>
                <h4 class="card-stats-number">
                    <?= count($items = $this->crud_model->get_records("items")) ?>
                </h4>
            </div>
        </div>
    </div>
    <div class="col s6 l3">
        <div class="card center link" id="tickets" style="background: transparent !important; border: 2px solid #FF6601; border-radius: 5px">
            <div class="card-content orange-text">
                <p class="card-stats-title"><i class="mdi-action-trending-up"></i> Tickets</p>
                <h4 class="card-stats-number">
                    <?= count($tickets = $this->crud_model->get_records("tickets")) ?>
                </h4>
            </div>
        </div>
    </div>
    <div class="col s6 l3">
        <div class="card center " style="background: transparent !important; border: 2px solid green; border-radius: 5px">
            <div class="card-content green-text text-darken-4">
                <p class="card-stats-title"><i class="mdi-social-group-add"></i> Asset requests</p>
                <h4 class="card-stats-number">
                    <?= count($requests = $this->crud_model->get_records("asset_request")) ?>
                </h4>
            </div>
        </div>
    </div>
    <div class="col s6 l3">
        <div class="card center link" id="projects" style="background: transparent !important; border: 2px solid #00ACC1; border-radius: 5px">
            <div class="card-content cyan-text">
                <p class="card-stats-title"><i class="mdi-editor-attach-money"></i> Projects</p>
                <h4 class="card-stats-number"><?= count($projects = $this->crud_model->get_records("projects")) ?></h4>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col s12 m12 l8">
        <div class="card" style="background: transparent !important; border: 1px solid #e0e0e0; border-radius: 5px">
            <div class="card-move-up waves-effect waves-block waves-light">
                <div class="move-up">
                    <div>
                        <span class="chart-title green-text">Ticket stats</span>
                        <div class="switch chart-revenue-switch right">
                            <label class="green-text">
                                Hourly
                                <input type="checkbox">
                                <span class="lever"></span> Daily
                            </label>
                        </div>
                    </div>
                    <div class="trending-line-chart-wrapper">
                        <canvas style="width: 670px; height: 156px;" width="670" id="trending-line-chart" height="156"></canvas>
                    </div>
                </div>
            </div>
            <div class="card-content">
                <a class="btn-floating btn-move-up waves-effect waves-light darken-2 right"><i class="mdi-content-add activator"></i></a>
                <div class="row">
                    <div class="col s12 m3 l3">
                        <div id="doughnut-chart-wrapper">
                            <canvas style="width: 148px; height: 98px;" width="148" id="doughnut-chart" height="98"></canvas>
                            <div class="doughnut-chart-status"><?= count($tickets) ?>
                                <p class="ultra-small center-align">Tickets</p>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m2 l2">
                        <ul class="doughnut-chart-legend">
                            <li class="mobile ultra-small"><span class="legend-color"></span>Pending</li>
                            <li class="kitchen ultra-small"><span class="legend-color"></span> Solved</li>
                            <li class="home ultra-small"><span class="legend-color"></span> In progress</li>
                        </ul>
                    </div>
                    <div class="col s12 m7 l6">
                        <div class="trending-bar-chart-wrapper">
                            <canvas style="width: 318px; height: 95px;" width="318" id="trending-bar-chart" height="95"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div style="display: none; transform: translateY(0px);" class="card-reveal">
                <span class="card-title grey-text text-darken-4">Tickets by month <i class="mdi-navigation-close right"></i></span>
                <table class="responsive-table">
                    <thead>
                    <tr>
                        <th data-field="month">Month</th>
                        <th data-field="item-sold">Raised</th>
                        <th data-field="item-price">Resolved</th>
                        <th data-field="total-profit">Percentage</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($monthNum = 1; $monthNum <= 12; $monthNum++) {
                        $dateObj = DateTime::createFromFormat('!m', $monthNum);
                        $monthName = $dateObj->format('F');
                        ?>
                        <tr>
                            <td><?= $monthName ?></td>
                            <td><?= $raised = count($this->tickets_model->monthly_tickets(($monthNum < 10 ? "0" . $monthNum : $monthNum), 0)) ?></td>
                            <td><?= $resolved = count($this->tickets_model->monthly_tickets(($monthNum < 10 ? "0" . $monthNum : $monthNum), 3)) ?></td>
                            <td><?= (int)(($resolved / ($raised == 0 ? 1 : $raised)) * 100) ?> %</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col s12 m12 l4">
        <ul class="collection" style=" border: 1px solid #e0e0e0; border-radius: 5px">
            <li class="collection-item">
                <h6 class="chart-title green-text">Latest tickets</h6>
            </li>
            <?php
            $this->db->order_by('ticket_id', "DESC");
            foreach ($this->crud_model->get_records('tickets', false, false, 5) as $ticket) { ?>
                <li class="collection-item">
                    <div class="row link" id="tickets/profile/<?= urlencode(base64_encode($ticket->ticket_id)) ?>">
                        <div class="col s7 medium black-text">
                            <?= $ticket->ticket_title ?><br/>
                            <span class="orange-text">
                                Site: <?= $this->crud_model->get_record('sites', 'site_id', $ticket->site_id)->site_name ?>
                            </span>
                        </div>
                        <div class="col s2">
                            <?= $this->tickets_model->priority($ticket->ticket_priority) ?>
                        </div>
                        <div class="col s3">
                            <?= $this->tickets_model->state($ticket->ticket_status) ?>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

<div id="work-collections">
    <div class="row">
        <div class="col s12 m12 l6">
            <ul class="collection" style=" border: 1px solid #e0e0e0; border-radius: 5px">
                <li class="collection-item">
                    <h5 class="task-card-title orange-text">Ongoing projects</h5>
                </li>
                <?php foreach ($projects = $this->crud_model->get_records('projects') as $project) { ?>
                    <li class="collection-item">
                        <div class="row">
                            <div class="col s6">
                                <p class="collections-title"><?= $project->project_name ?></p>
                            </div>
                            <div class="col s3">
                                <p class="collections-content"><?= $project->project_client ?></p>
                            </div>
                            <div class="col s3">
                                    <span class="task-cat">PM:
                                        <?= $this->users_model->user($project->project_manager)->user_name ?>
                                    </span>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col s12 m12 l6">
            <div class="sample-chart-wrapper">
                <div class="card" style="background: transparent !important; border: 1px solid #e0e0e0; border-radius: 5px">
                    <canvas style="width: 389px; height: 194px;" height="194" width="389" id="doughnut-chart-sample"></canvas>
                    <div class="doughnut-chart-status"><?= count($projects) ?>
                        <p class="ultra-small center-align">Projects</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$day = date('w');
$week_start = date('Y-m-d', strtotime('-' . $day . ' days'));
$week_end = date('Y-m-d', strtotime('+' . (6 - $day) . ' days'));
$sun = date('d') - $day;


?>
<script>
    var trendingLineChart;
    var data = {
        labels: ["Sun", "Mon", "Tue", "Wed", "Thur", "Fri", "Sat"],
        datasets: [
            {
                label: "Raised",
                fillColor: "rgba(128, 222, 234, 0.6)",
                strokeColor: "#ffffff",
                pointColor: "#ff6601",
                pointStrokeColor: "#ffffff",
                pointHighlightFill: "#ffffff",
                pointHighlightStroke: "#ffffff",
                data: [<?php for ($i = $sun; $i <= ($sun + 6); $i++) {
                    echo count($this->tickets_model->daily_tickets(date("Y-m-") . $i, 0)) . ", ";
                }?>]
            },
            {
                label: "Resolved",
                fillColor: "rgba(128, 222, 234, 0.6)",
                strokeColor: "#ffffff",
                pointColor: "#00bcd4",
                pointStrokeColor: "#ffffff",
                pointHighlightFill: "#ffffff",
                pointHighlightStroke: "#ffffff",
                data: [<?php for ($i = $sun; $i <= ($sun + 6); $i++) {
                    echo count($this->tickets_model->daily_closed_tickets(date("Y-m-") . $i)) . ", ";
                }?>]
            }
        ]
    };


    var doughnutData = [
        {
            value: <?=count($this->crud_model->get_records("tickets", "ticket_status", 1))?>,
            color: "#F7464A",
            highlight: "#ff6601",
            label: "New"
        },
        {
            value: <?=count($this->crud_model->get_records("tickets", "ticket_status", 2))?>,
            color: "#46BFBD",
            highlight: "#5AD3D1",
            label: "Ongoing"
        },
        {
            value: <?=count($this->crud_model->get_records("tickets", "ticket_status", 3))?>,
            color: "#FDB45C",
            highlight: "#FFC870",
            label: "Resolved"
        }

    ];

    var dataBarChart = {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [
            {
                label: "Bar dataset",
                fillColor: "#46BFBD",
                strokeColor: "#46BFBD",
                highlightFill: "rgba(70, 191, 189, 0.4)",
                highlightStroke: "rgba(70, 191, 189, 0.9)",
                data: [<?php for ($i = 1; $i <= 12; $i++) {
                    echo count($this->tickets_model->monthly_tickets(($i < 10 ? "0" . $i : $i), 0)) . ", ";
                }?>]
            }
        ]
    };

</script>