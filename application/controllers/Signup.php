<?php
class Signup extends CI_Controller
{
    public $data = array();
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form','url'));
		$this->load->library(array('session', 'form_validation'));
		$this->load->database();
		$this->load->model('user_model');
	}
        
        function index(){           
           $this->load->view('signup_view');               
        }
        
        function subscribe(){
             $data = array(
                    'fname' => $this->input->post('fname'),
                    'lname' => $this->input->post('lname'),
                    'email' => $this->input->post('email'),
                    'password' => $this->input->post('password')
            );
             
             if($data['password'] != ''){                 
                 if ($this->user_model->insert_user($data)){
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">You are Successfully Registered! Please login to access your Profile!</div>');
                        redirect('home');
                }else{
                        // error
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Oops! Error.  Please try again later!!!</div>');
                        redirect('home');
                }
             }else{
                 exit('no pwd');
             }
        }
}