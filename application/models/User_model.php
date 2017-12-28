<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model
{
	function __construct()
    {
        parent::__construct();
    }
	
	function get_user($email, $pwd){
            $this->db->where('email', $email);
            //$this->db->where('password', md5($pwd));
            $this->db->where('password', $pwd);
            $query = $this->db->get('user');            
            return $query->result();
	}
	
	// get user
	function get_user_by_id($id)
	{
		$this->db->where('id', $id);
        $query = $this->db->get('user');
		return $query->result();
	}
        
        // get user
	function get_user_modules($id){
		$this->db->where('user_id', $id);
                $this->db->join('modules', 'modules.module_id = user_module.module_id', 'left');
                $query = $this->db->get('user_module');
		return $query->result();
	}

	// get all modules
	function get_all_modules(){		
		$query = $this->db->get('modules');
		return $query->result();
	}

	
	// insert
	function insert_user($data)
    {
		return $this->db->insert('user', $data);
	}
}?>