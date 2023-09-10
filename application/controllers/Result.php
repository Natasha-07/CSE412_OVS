<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
/**
 * 
 */
class Result extends CI_Controller{
	
	public function index(){
		$data['title'] = 'Voting Result';
		$v = $this->db->query('SELECT * FROM voting');
		if ($v->num_rows() < 1) {
			$data['voting'] = '';
		}
		else{
			$data['voting'] = $v->row();
			$data['candidate'] = $this->db->query('SELECT * FROM candidate JOIN voter_candidate ON voter_candidate.candidate_id=candidate.candidate_id WHERE id_voting='.$data["voting"]->id_voting)->result();
		}

		$this->load->view('_result_voting/result', $data);
	}

}