<?php
class Settings extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('url','html','form','file'));
		$this->load->library('session');
		$this->load->database();
		$this->load->model('user_model');
	}
	
	function index(){               
		$details = $this->user_model->get_user_by_id($this->session->userdata('uid'));
		$data['uname'] = $details[0]->fname . " " . $details[0]->lname;
		$data['uemail'] = $details[0]->email;
		$this->load->view('settings_view', $data);                
                $uploads = get_filenames(APPPATH);
                print_r($uploads);
	}
}