<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();

    }

    public function admin_validate(){
    	$username = $this->input->post('username',true);
		$password = $this->input->post('password',true);
		$this->db->where('admin_name', $username);
		$this->db->where('password', $password);
		$query = $this->db->get('ci_admin');
		if ($query->num_rows() == 1) {
			return 1;
		}else{
			return 0;
		}

    }
}