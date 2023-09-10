<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
/**
 * 
 */
class Auth extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		if ($this->session->login) {
			redirect('');
		}
	}

	public function index(){
		$data['title'] = 'Login';

		$this->load->view('_template/_header', $data);
		$this->load->view('login');
		$this->load->view('_template/_footer');
	}
	function actlogin(){
		$username = $this->input->post('username');
		$passwd = md5($this->input->post('password'));
		if(isset($_POST['rand'])) $rand=$_POST['rand'];
		$otp = $this->input->post('otp');
		// $d = $this->db->query('SELECT * FROM voter WHERE username="'.$username.'" AND password="'.$passwd.'"')->row();
		$d = $this->db->get_where('voter', ['username' => $username, 'password' => $passwd])->row();
		if ($username == $d->username && $passwd == $d->password && $otp == $rand) {
			$v = $this->db->query('SELECT * FROM voting')->num_rows();
			if ($v < 1) {
				$this->session->set_flashdata('novoting', 'No voting !');
				redirect('login');
			}
			else{
				$user = ['id' => $d->n_id, 'name' => $d->name, 'username' => $d->username, 'login' => TRUE];
				$this->session->set_userdata($user);
				redirect('');
			}
		}
		else{
			$this->session->set_flashdata('login_gagal', 'Username/Password/OTP is wrong !');
			redirect('login');
		}
	}
}