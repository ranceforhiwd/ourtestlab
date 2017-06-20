<?php
class Home extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('url', 'html'));
		$this->load->library('session');
                $this->load->database();
		$this->load->model('user_model');
	}
	
	function index(){
		$this->load->view('home_view');
	}
        
        function validate_login(){             
            $details = $this->user_model->get_user_by_id($this->session->userdata('uid'));
            
            if(isset($details) && !empty($details)){
                exit(json_encode($details[0]->id));
            }else{
                exit(json_encode(false));
            }
        }
                
        function get_user_menu(){            
            //lookup modules assigned to user
            $modules = $this->user_model->get_user_modules($_POST['userid']);
            exit(json_encode($modules));
        }
	
	function logout()	{
            // destroy session
            $data = array('login' => '', 'uname' => '', 'uid' => '');
            $this->session->unset_userdata($data);
            $this->session->sess_destroy();
            redirect('home');
	}
}


