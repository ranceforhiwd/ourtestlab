<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Docs extends MX_Controller {
        
        public function __construct(){
		parent::__construct();
		$this->load->helper(array('url', 'html'));
		$this->load->library('session');
                $this->load->database();
		$this->load->model('docs_model');
	}
    
	public function index(){
            $this->phpdoc();
            $this->load->view('docs_view');
	}
        
        public function phpdoc(){
            $output = shell_exec('phpdoc -d application/  -t documentation');
            echo "<pre>$output</pre>";
        }
                
}
