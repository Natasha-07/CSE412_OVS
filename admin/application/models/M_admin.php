<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class m_admin extends CI_Model{
	// CRUD
	function see($table){
		return $this->db->get($table);
	}
	function add($table, $data){
		$this->db->insert($table, $data);
	}
	function change($where, $table, $data){
		$this->db->where($where);
		$this->db->update($table, $data);
	}
	function delete($where, $table){
		$this->db->where($where);
		$this->db->delete($table);
	}

}