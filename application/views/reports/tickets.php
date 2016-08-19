<div class="col s12">
    <h5 class="card-title  orange-text" id="report-name">
        <i class="mdi-navigation-chevron-right"></i>
        <?php
        printf("<b>%s</b> Reports for Period Starting <b>%s</b> and ending <b>%s</b>", $title, $_POST['kutoka'], $_POST['mpaka'])
        ?>
    </h5>
    <button id="export" class="btn btn-flat right orange white-text">
        Excel
    </button>
    <button id="print" class="btn btn-flat right orange white-text">
        Print
    </button>
    <div style=" border-radius: 4px; border: 1px solid #e0e0e0" id="report_body">
        <table <?= $group ? 'id="data-table-row-grouping"' : "" ?>
            class=" display responsive-table report_body" cellspacing="0" width="100%">
            <thead>
            </thead>
            <tbody>
            <tr>
                <?php
                reset($header);
                foreach ($header as $th) {
                    printf('<th>%s</th>', $th);
                }
                ?>
            </tr>
            <?php foreach ($content as $tr) {
                echo '<tr>';
                reset($tr);
                foreach ($tr as $td) {
                    printf('<td>%s</td>', $td);
                }
                echo '</tr>';
            } ?>
            </tbody>
        </table>
    </div>
</div>
</div>