<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testmodule1 extends MX_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
        
        public function __construct(){
		parent::__construct();
		$this->load->helper(array('url', 'html'));
		$this->load->library('session');
                $this->load->database();
		$this->load->model('testmodule1_model');
	}
    
	public function index(){                
		$this->load->view('testmodule1_view');
	}
        
        public function getData(){
            $output = array(
                'draw'=>1,
                'recordsTotal'=>2,
                'recordsFiltered'=>2,
                'data'=>$this->testmodule1_model->get_data()
            );            
        
            exit(json_encode($output));
        }
}
