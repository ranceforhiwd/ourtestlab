<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * This is the main module controller
 * @author Rance Aaron
 * @package Modules\Testmodule3
 */
class Testmodule3 extends MX_Controller {
        
        public function __construct(){
		parent::__construct();
		$this->load->helper(array('url', 'html'));
		$this->load->library('session');
                $this->load->database();
		$this->load->model('testmodule3_model');
	}
    
	public function index(){                
		$this->load->view('testmodule3_view');
	}
                
}
