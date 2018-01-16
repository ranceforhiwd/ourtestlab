<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * This is the main module controller
 * @author Rance Aaron
 * @package Modules/Testmodule2
 */
class Testmodule2 extends MX_Controller {
        
        public function __construct(){
		parent::__construct();
		$this->load->helper(array('url', 'html'));
		$this->load->library('session');
                $this->load->database();
		$this->load->model('testmodule2_model');
	}
    
	public function index(){                
		$this->load->view('testmodule2_view');
	}
        
        public function getData(){
            $output = array(
                'draw'=>1,
                'recordsTotal'=>2,
                'recordsFiltered'=>2,
                'data'=>$this->testmodule2_model->get_data()
            );            
        
            exit(json_encode($output));
        }
}
