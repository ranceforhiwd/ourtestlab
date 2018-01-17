<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is the main module model
 * @author Rance Aaron
 * @package Testmodule1
 */
class Testmodule1_model extends CI_Model
{
	function __construct()
    {
        parent::__construct();
    }
	
	function get_user($email, $pwd)
	{
		$this->db->where('email', $email);
		$this->db->where('password', md5($pwd));
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
		$this->db->where('userid', $id);
                $this->db->join('modules', 'modules.moduleid = user_module.moduleid', 'left');
                $query = $this->db->get('user_module');
		return $query->result();
	}
	
	// insert
	function insert_user($data){
		return $this->db->insert('user', $data);
	}
        
        // get data generic example
	function get_data(){
                $o = array();
		$query = $this->db->get('tbl1');
		$r = $query->result_array();
                foreach ($r as $key => $value) {
                    $d = array();
                    $d['id'] = $value['id'];
                    $d['name'] = $value['name'];
                    $d['position'] = $value['position'];
                    $d['office'] = $value['office'];
                    $d['age'] = $value['age'];
                    $d['startdate'] = $value['startdate'];
                    $d['salary'] = $value['salary'];
                    $o[] = $d;
                }
               return $o; 
	}
}?>