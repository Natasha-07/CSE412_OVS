<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class User extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct(){
		parent::__construct();
		if (!$this->session->login) {
			redirect('login');
		}
	}
	private function header($data){
		$p = $this->db->query('SELECT password FROM voter WHERE n_id='.$this->session->id)->row();
		if ($p->password == md5($this->session->username)) {
			$data['passdefault'] = TRUE;
		}
		else{
			$data['passdefault'] = FALSE;
		}

		$this->load->view('_template/_header', $data);
	}

	public function index(){
		$data['title'] = 'Online Voting System';
		$data['voting'] = $this->db->get('voting')->row();

		$cekvoting = $this->db->get('voting')->num_rows();
		if ($cekvoting > 0) {
			$data['voting'] = $this->db->get('voting')->row();
			$data['candidate'] = $this->db->query('SELECT voter_candidate.id_voting, candidate.* FROM voter_candidate INNER JOIN candidate ON voter_candidate.id_voting='.$data['voting']->id_voting.' WHERE candidate.candidate_id=voter_candidate.candidate_id')->result();
		}
		$check = $this->db->query('SELECT * FROM voter_voting WHERE n_id='.$this->session->id)->num_rows();
		// if ($check > 0) {
		// 	$data['check'] = TRUE;
		// }
		// else{
		// 	$data['check'] = FALSE;
		// }
		($check > 0) ? $data['check'] = TRUE : $data['check'] = FALSE;

		$this->header($data);
		$this->load->view('main');
		$this->load->view('_template/_footer');
	}
	function check($voting, $id){
		$c = $this->db->get_where('voting', ['id_voting' => $voting]);
		if ($c->num_rows() > 0) {
			$this->db->query('UPDATE voter_candidate SET vote = vote+1 WHERE candidate_id='.$id);
			$check = ['id_voting' => $voting, 'n_id' => $this->session->id, 'time' => Date('y-m-d H:i:s')];
			$this->db->insert('voter_voting', $check);
			echo 1;
		}
	}


	function set_pass_id($id){
		$passLama = md5($this->input->post('passwdLama'));
		$passBaru = md5($this->input->post('passwdBaru'));
		$p = $this->db->query('SELECT password FROM voter WHERE n_id='.$id)->row();
		if ($passLama == $p->password) {
			$this->db->query('UPDATE voter SET password="'.$passBaru.'" WHERE n_id='.$id);
			echo 1;
		}
		else{
			echo 0;
		}
	}
	function logout(){
		$this->session->sess_destroy();
		redirect('login');
	}
}
