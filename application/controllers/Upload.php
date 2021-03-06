<?php

class Upload extends CI_Controller {

        public function __construct(){
                parent::__construct();
                $this->load->helper(array('form', 'url'));
        }

        public function index(){
                $this->load->view('upload_form', array('error' => ' ' ));
        }

        public function do_upload(){
            if (!file_exists('application/modules/custom')) {
                mkdir('application/modules/custom', 0777, true);
            }
                
                $config['upload_path']          = "application/modules/custom";
                $config['allowed_types']        = 'gif|jpg|png|txt|zip';
                $config['max_size']             = 100000;
                $config['max_width']            = 1024;
                $config['max_height']           = 768;

                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload('userfile')){
                        $error = array('error' => $this->upload->display_errors());
                        $this->load->view('upload_form', $error);
                }else{
                        $data = array('upload_data' => $this->upload->data());                        
                        redirect(site_url());                        
                }
        }
}
?>
