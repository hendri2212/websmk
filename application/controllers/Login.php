<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CMS Sekolahku | CMS (Content Management System) dan PPDB/PMB Online GRATIS
 * untuk sekolah SD/Sederajat, SMP/Sederajat, SMA/Sederajat, dan Perguruan Tinggi
 * @version    2.4.4
 * @author     Anton Sofyan | https://facebook.com/antonsofyan | 4ntonsofyan@gmail.com | 0857 5988 8922
 * @copyright  (c) 2014-2019
 * @link       https://sekolahku.web.id
 *
* PERINGATAN :
 * 1. TIDAK DIPERKENANKAN MENGGUNAKAN CMS INI TANPA SEIZIN DARI PIHAK PENGEMBANG APLIKASI.
 * 2. TIDAK DIPERKENANKAN MEMPERJUALBELIKAN APLIKASI INI TANPA SEIZIN DARI PIHAK PENGEMBANG APLIKASI.
 * 3. TIDAK DIPERKENANKAN MENGHAPUS KODE SUMBER APLIKASI.
 */

class Login extends CI_Controller {

	/**
	 * Class Constructor
	 *
	 * @return Void
	 */
	public function __construct() {
		parent::__construct();
		if ($this->auth->hasLogin()) redirect('dashboard', 'refresh');
		$this->load->model([
			'm_users',
			'm_user_privileges'
		]);
	}

	/**
	 * Index
	 * @return Void
	 */
	public function index() {
		$this->vars['page_title'] = 'Login to Our Site';
		// $this->vars['ip_banned'] = $this->auth->ip_banned(get_ip_address());
		// $this->vars['login_info'] = $this->vars['ip_banned'] ? 'The login page has been blocked for 10 minutes' : 'Enter your username and password to log on';
		$this->load->view('users/login', $this->vars);
	}

	/**
	 * Login Process
	 * @return Object
	 */
	public function process() {
		if ($this->input->is_ajax_request()) {
			if ($this->validation()) {
				$user_name = $this->input->post('user_name', TRUE);
				$user_password = $this->input->post('user_password', TRUE);
				$ip_address = get_ip_address();
				$logged_in = $this->auth->logged_in($user_name, $user_password, $ip_address) ? 'success' : 'error';
				$this->vars['status'] = $logged_in;
				$this->vars['message'] = $logged_in == 'success' ? 'logged_in' : 'not_logged_in';
				$this->vars['ip_banned'] = $this->auth->ip_banned($ip_address);
			} else {
				$this->vars['status'] = 'error';
				$this->vars['message'] = validation_errors();
				$this->vars['ip_banned'] = FALSE;
			}
			$this->output
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($this->vars, JSON_HEX_APOS | JSON_HEX_QUOT))
				->_display();
			exit;
		}
	}

	/**
	 * Login Process yang baru (custom)
	 * @return Object
	 */
	public function new_process() {
		$user_name = $this->input->post('user_name', TRUE);
		$user_password = $this->input->post('user_password', TRUE);
		$ip_address = get_ip_address();
		// $logged_in = $this->auth->logged_in($user_name, $user_password, $ip_address) ? 'success' : 'error';
		$query = $this->m_users->logged_in($user_name);
		if ($query->num_rows() === 1) {
			$data = $query->row();
			if (password_verify($user_password, $data->user_password)) {
				$session_data = [
					'user_id' => $data->id,
					'user_name' => $data->user_name,
					'user_full_name' => $data->user_full_name,
					'user_type' => $data->user_type,
					'user_profile_id' => $data->user_profile_id,
					'has_login' => true,
					'user_privileges' => $this->m_user_privileges->get_user_privileges($data->user_group_id, $data->user_type)
				];
				$this->session->set_userdata($session_data);
				// $this->last_logged_in($data->id);
				// $this->reset_attempts($ip_address);
				$logged_in = 'success';
			}else{
				$logged_in = 'error';
			}
		}else{
			$logged_in = 'error';
		}
		$this->vars['status'] = $logged_in;
		$this->vars['message'] = $logged_in == 'success' ? 'logged_in' : 'not_logged_in';

		if ($logged_in == 'success') {
			redirect(base_url('dashboard'));
		}else {
			$this->load->view('users/login', $this->vars);
		}
	}

	

	/**
	 * Validation Form
	 * @return Boolean
	 */
	private function validation() {
		$this->load->library('form_validation');
		$val = $this->form_validation;
		$val->set_rules('user_name', 'Nama Akun', 'trim|required');
		$val->set_rules('user_password', 'Kata Sandi', 'trim|required');
		$val->set_error_delimiters('<div>&sdot; ', '</div>');
		return $val->run();
	}
}
