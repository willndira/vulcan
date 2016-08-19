<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    function  __construct()
    {
        parent::__construct();
        $this->users_model->security();
    }


    public function index()
    {
        $var['page'] = 'Dashboard';
        $var['styles'] = array(
              'js/plugins/data-tables/css/jquery.dataTables.min'
        );
        $var['scripts'] = array(
            'plugins/chartist-js/chartist.min',
            'plugins/chartjs/chart.min',
            'plugins/chartjs/chart-script',
            'plugins/sparkline/jquery.sparkline.min',
            'plugins/sparkline/sparkline-script',
            'plugins/jvectormap/jquery-jvectormap-1.2.2.min',
            'plugins/jvectormap/jquery-jvectormap-world-mill-en',
            'plugins/jvectormap/vectormap-script'
        );
        $this->load->template('dashboard_view', $var);
    }
}
