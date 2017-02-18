<?php
class Startpage extends CI_Controller{
	public function __construct(){
            parent::__construct();
            $this->load->helper(array('url', 'html'));
            $this->load->library('session');
	}
	
	function index(){
            $this->load->view('startpage_view');
	}
	
	function logout(){
            // destroy session
            $data = array('login' => '', 'uname' => '', 'uid' => '');
            $this->session->unset_userdata($data);
            $this->session->sess_destroy();
            redirect('home');
	}
}


