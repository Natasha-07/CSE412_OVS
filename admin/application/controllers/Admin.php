<?php
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Dhaka');

class Admin extends CI_Controller {

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
		$this->load->model('m_admin');
	}

	private function header($data){
		$data['jmlcandidate'] = $this->m_admin->see('candidate')->num_rows();
		$data['jmlvoter'] = $this->m_admin->see('voter')->num_rows();
		// $cpass = $this->db->query('SELECT password FROM admin WHERE id_admin='.$this->session->id)->row();
		$cpass = $this->db->select('password')->get_where('admin', ['id_admin' => $this->session->id])->row();
		if ($cpass->password == md5($this->session->username)) {
			$data['passdefault'] = TRUE;
		}
		else{
			$data['passdefault'] = FALSE;
		}
		$this->load->view('_template/_header', $data);
	}

	public function index(){
		$data['title'] = 'Online Voting System';

		$this->header($data);
		$this->load->view('main');
		$this->load->view('_template/_footer');
	}

	public function voting(){
		$data['title'] = 'Voting';
		// $data['listcandidate'] = $this->db->query('SELECT candidate_id, candidate_name FROM candidate')->result();
		$data['listcandidate'] = $this->db->select('candidate_id, candidate_name')->get('candidate')->result();
		$data['cekvoting'] = $this->m_admin->see('voting')->num_rows();
		if ($data['cekvoting'] > 0) {
			$data['voting'] = $this->m_admin->see('voting')->row();
			// $data['candidate'] = $this->db->query('SELECT candidate.*, voter_candidate.id_voting, voter_candidate.vote FROM voter_candidate INNER JOIN candidate ON voter_candidate.id_voting='.$data['voting']->id_voting.' WHERE candidate.candidate_id=voter_candidate.candidate_id')->result();
			$data['candidate'] = $this->db->select('candidate.*, voter_candidate.id_voting, voter_candidate.vote')
																	 ->where('candidate.candidate_id = voter_candidate.candidate_id')
																	 ->join('candidate', 'voter_candidate.id_voting = '.$data['voting']->id_voting, 'inner')
																	 ->get('voter_candidate')->result();

			$data['already_chosen'] = $this->db->query('SELECT voter.n_id, voter.name, voter_voting.time FROM voter JOIN voter_voting ON voter.n_id=voter_voting.n_id JOIN voting ON voter_voting.id_voting=voting.id_voting WHERE voter.n_id IN (SELECT voter_voting.n_id FROM voter_voting)')->result();

			$data['not_chosen'] = $this->db->query('SELECT n_id, name FROM voter WHERE n_id NOT IN (SELECT n_id FROM voter_voting)')->result();
		}

		$this->header($data);
		$this->load->view('voting');
		$this->load->view('_template/_footer');
	}
	function add_voting(){
		$voting = $this->input->post('voting');
		$candidate = $this->input->post('candidate[]');
		$data = ['election' => $voting];
		$this->m_admin->add('voting', $data);
		$id_voting = $this->db->insert_id();

		foreach ($candidate as $k) {
			$voter_candidate = ['id_voting' => $id_voting, 'candidate_id' => $k];
			$this->m_admin->add('voter_candidate', $voter_candidate);
		}

		$this->session->set_flashdata('add', 'Vote added');
		redirect($this->agent->referrer());
	}
	function edit_voting($id){
		$voting = $this->input->post('voting');
		$data = ['election' => $voting];
		$this->m_admin->change(['id_voting' => $id], 'voting', $data);
		$this->session->set_flashdata('change', 'Successfully changed');
		redirect($this->agent->referrer());
	}
	function delete_voting($id){
		$this->db->where(['id_voting' => $id]);
		$this->db->delete('voting');
		redirect('voting');
	}

	public function candidate(){
		$data['title'] = 'Candidate';
		//$data['candidate'] = $this->m_admin->see('candidate')->result();
		$data['candidate'] = $this->db->query('SELECT *, candidate.candidate_id as id FROM candidate LEFT JOIN voter_candidate ON voter_candidate.candidate_id=candidate.candidate_id')->result();

		$this->header($data);
		$this->load->view('candidate');
		$this->load->view('_template/_footer');
	}
	function data_candidate(){
		$data = $this->m_admin->see('candidate')->result();
		echo json_encode($data);
	}
	function get_candidate($id){
		// $d = $this->db->query('SELECT * FROM candidate WHERE candidate_id='.$id)->row();
		$d = $this->db->get_where('candidate', ['candidate_id' => $id])->row();
		echo json_encode($d);
	}
	function add_candidate()
{
    $name = $this->input->post('name');
    $ket = $this->input->post('ket');
    $foto = $_FILES['photo'];

    // Sanitize and validate input (example: using trim and htmlspecialchars)
    $name = trim(htmlspecialchars($name));
    $ket = trim(htmlspecialchars($ket));

    $config['upload_path'] = '../assets/img/candidate';
    $config['allowed_types'] = 'jpg|png|gif';
    $config['remove_spaces'] = TRUE;
    $this->load->library('upload', $config);

    if (!empty($foto['name'])) {
        if (!$this->upload->do_upload('photo')) {
            $data['error'] = $this->upload->display_errors();
            $this->session->set_flashdata('error', $data['error']);
            redirect('candidate');
        } else {
            $data = [
                'candidate_name' => $name,
                'explanation' => $ket,
                'photo' => $this->upload->data('file_name')
            ];

            // Assuming `m_admin` is a model or database access layer class
            $this->m_admin->add('candidate', $data);

            $this->session->set_flashdata('add', 'Successfully added');
            redirect('candidate');
        }
    } else {
        $this->session->set_flashdata('error', 'Select photo');
        redirect('candidate');
    }
}

	function edit_candidate(){
		$id = $this->input->post('candidate_id');
		$name = $this->input->post('name');
		$ket = $this->input->post('ket');
		$foto = $_FILES['photo'];

		if (empty($foto['name'])) {
			$data = ['candidate_name' => $name, 'explanation' => $ket];
			$this->m_admin->change(['candidate_id' => $id], 'candidate', $data);
			$this->session->set_flashdata('edit', 'Changed successfully');
			redirect($this->agent->referrer());
		}
		else{
			$config['upload_path'] = './../assets/img/candidate';
			$config['allowed_types'] = 'jpg|png|gif';
			$config['remove_spaces'] = TRUE;
			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('photo')) {
				$data['error'] = $this->upload->display_errors();
				$this->session->set_flashdata('error', 'Photo is already uploaded!');
				redirect($this->agent->referrer());
			}
			else{
				// $f = $this->db->query('SELECT foto FROM candidate WHERE candidate_id='.$id)->row();
				$f = $this->db->select('photo')->get_where('candidate', ['candidate_id' => $id])->row();
				unlink('./../assets/img/candidate/'.$f->photo);
				$data = [
					'candidate_name' => $name,
					'explanation' => $ket,
					'photo' => $this->upload->data('file_name')
				];
				$this->m_admin->change(['candidate_id' => $id], 'candidate', $data);
				$this->session->set_flashdata('edit', 'Changed successfully');
				redirect($this->agent->referrer());
			}
		}
	}
	function delete_candidate($id){
		// $d = $this->db->query('SELECT foto FROM candidate WHERE candidate_id='.$id)->row();
		$d = $this->db->select('photo')->get_where('candidate', ['candidate_id' => $id])->row();
		unlink('./../assets/img/candidate/'.$d->photo);
		$this->m_admin->delete(['candidate_id' => $id], 'candidate');
		$this->session->set_flashdata('delete', 'Removed');
		redirect($this->agent->referrer());
	}

	public function voter(){
		$data['title'] = 'Voter List';

		$this->header($data);
		$this->load->view('voter');
		$this->load->view('_template/_footer');
	}
	function data_voter(){
		$data = $this->m_admin->see('voter')->result();
		echo json_encode($data);
	}
	function get_voter($id){
		// $d = $this->db->query('SELECT n_id, name, username FROM voter WHERE n_id='.$id)->row();
		$d = $this->db->select('n_id, name, username')->get_where('voter', ['n_id' => $id])->row();
		echo json_encode($d);
	}
	function add_voter(){
		$name = $this->input->post('name');
		$username = $this->input->post('username');
		$password = md5($this->input->post('username'));
		$data = ['name' => $name, 'username' => $username, 'password' => $password];
		$this->m_admin->add('voter', $data);
	}
	function edit_voter($id){
		$name = $this->input->post('name');
		$username = $this->input->post('username');
		$where = ['n_id' => $id];
		$data = ['name' => $name, 'username' => $username];
		$this->m_admin->change($where, 'voter', $data);
	}
	function reset_pass_voter($id){
		// $p = $this->db->query('SELECT username FROM voter WHERE n_id='.$id)->row();
		$p = $this->db->select('username')->get_where('voter', ['n_id' => $id])->row();
		$pass = md5($p->username);
		$this->db->query('UPDATE voter SET password="'.$pass.'" WHERE n_id='.$id);
	}
	function delete_voter($id){
		$this->db->query('DELETE FROM voter WHERE n_id='.$id);
	}

	public function settings(){
		$data['title'] = 'Settings';

		$this->header($data);
		$this->load->view('settings');
		$this->load->view('_template/_footer');
	}
	function data_admin(){
		$d = $this->m_admin->see('admin')->result();
		echo json_encode($d);
	}
	function get_admin($id){
		// $d = $this->db->query('SELECT id_admin, name, username FROM admin WHERE id_admin='.$id)->row();
		$d = $this->db->select('id_admin, name, username')->get_where('admin', ['id_admin' => $id])->row();
		echo json_encode($d);
	}
	function add_admin(){
		$name = $this->input->post('name');
		$username = $this->input->post('username');
		$password = md5($this->input->post('username'));
		$last = Date('Y-m-d H:i:s');
		$data = [
			'name' => $name,
			'username' => $username,
			'password' => $password,
			'last_login' => $last
		];
		$this->m_admin->add('admin', $data);
	}
	function reset_pass_admin($id){
		// $d = $this->db->query('SELECT username FROM admin WHERE id_admin='.$id)->row();
		$d = $this->db->select('username')->get_where('admin', ['id_admin' => $id])->row();
		$pass = md5($d->username);
		//$this->db->query('UPDATE admin SET username="'.$pass.'" WHERE id_admin='.$id);
		$this->m_admin->change(['id_admin' => $id], 'admin', ['password' => $pass]);
	}
	function edit_admin($id){
		$name = $this->input->post('name');
		$username = $this->input->post('username');
		$where = ['id_admin' => $id];
		$data = ['name' => $name, 'username' => $username];
		$this->m_admin->change($where, 'admin', $data);
	}
	function delete_admin($id){
		$this->db->query('DELETE FROM admin WHERE id_admin='.$id);
	}

	function ganti_user_admin($id){
		$name = $this->input->post('name');
		$user = $this->input->post('username'); 
		$data = ['name' => $name, 'username' => $user];
		$this->m_admin->change(['id_admin' => $id], 'admin', $data);
		$this->session->unset_userdata(['name', 'username']);
		$this->session->set_userdata($data);
		$this->session->set_flashdata('ganti_user', 'Name/Username changed successfully');
		redirect($this->agent->referrer());
	}
	function ganti_pass_admin($id){
		// $d = $this->db->query('SELECT password FROM admin WHERE id_admin='.$id)->row();
		$d = $this->db->select('password')->get_where('admin', ['id_admin' => $id])->row();
		$pass = md5($this->input->post('passwdlama'));
		$newpass = md5($this->input->post('passwdbaru'));

		if ($d->password == $pass) {
			$data = ['password' => $newpass];
			$this->m_admin->change(['id_admin' => $id], 'admin', $data);
			$this->session->set_flashdata('ganti_pass', 'Password changed successfully');
			redirect('settings');
		}
		else{
			$this->session->set_flashdata('error_pass', 'Password lama tidak sama');
			redirect('settings');
		}
	}

	//Reset aplikasi
	function reset(){
		$this->db->query('SET FOREIGN_KEY_CHECKS = 0');
		$this->db->truncate('voter_candidate');
		$this->db->truncate('voter_voting');
		$this->db->truncate('candidate');
		$this->db->truncate('voter');
		$this->db->truncate('voting');
		$this->db->query('SET FOREIGN_KEY_CHECKS = 1');
		delete_files('./../assets/img/candidate/');
		redirect('');
	}
	//Logout
	function logout(){
		$last = Date('Y-m-d H:i:s');
		$this->m_admin->change(['id_admin' => $this->session->id], 'admin', ['last_login' => $last]);
		$this->session->sess_destroy();
		redirect('login');
	}
}
