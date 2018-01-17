<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is the main module model
 * @author Rance Aaron
 * @package Modules\Testmodule2
 */
class Testmodule2_model extends CI_Model{
	function __construct(){
            parent::__construct();
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