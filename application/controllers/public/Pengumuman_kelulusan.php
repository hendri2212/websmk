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

class Pengumuman_kelulusan extends Public_Controller {

	/**
	 * Constructor
	 * @access  public
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('m_kelulusan');
	}

	/**
	 * Index
	 * @return Void
	 */
	public function index() {
		// // if isset
		// $announcement_start_date = __session('announcement_start_date');
		// $announcement_end_date = __session('announcement_end_date');
		// if (NULL !== $announcement_start_date && NULL !== $announcement_end_date) {
		// 	// If not in array, redirect
		// 	$date_range = array_date($announcement_start_date, $announcement_end_date);
		// 	if (!in_array(date('Y-m-d'), $date_range)) {
		// 		redirect(base_url());
		// 	}
		// }

		$this->vars['recaptcha_site_key'] = __session('recaptcha_site_key');
		$this->vars['page_title'] = 'Informasi Kelulusan '. (__session('school_level') >= 5 ? 'Mahasiswa' : 'Peserta Didik').' Tahun '.__session('admission_year');
		$this->vars['button'] = 'Lihat Hasil';
		$this->vars['onclick'] = 'admission_selection_results()';
		$this->vars['content'] = 'themes/'.theme_folder().'/pengumuman-kelulusan';
		$this->load->view('themes/'.theme_folder().'/index', $this->vars);
	}

	public function daftarUlang() {
		$this->vars['message'] = 'Anda akan menandatangi persetujuan daftar ulang';
		$this->vars['content'] = 'pengumuman';
		$this->load->view('themes/'.theme_folder().'/index', $this->vars);
	}

	/**
    * Get Selection Results
    * @return Object
    */
   public function get_results() {
		if ($this->input->is_ajax_request()) {
			$recaptcha_status = __session('recaptcha_status');
			if (NULL !== $recaptcha_status && $recaptcha_status == 'enable') {
				$this->load->library('recaptcha');
				$recaptcha = $this->input->post('recaptcha');
				$recaptcha_verified = $this->recaptcha->verifyResponse($recaptcha);
				if ( ! $recaptcha_verified['success'] ) {
					$this->vars['status'] = 'recaptcha_error';
					$this->vars['message'] = 'Recaptcha Error!';
					$this->output
						->set_content_type('application/json', 'utf-8')
						->set_output(json_encode($this->vars, JSON_HEX_APOS | JSON_HEX_QUOT))
						->_display();
					exit;
				}
			}

			if ($this->validation()) {
				$birth_date = $this->input->post('birth_date', true);
				$registration_number = $this->input->post('registration_number', true);
				if (_isValidDate($birth_date) && strlen($registration_number) == 10 && ctype_digit((string) $registration_number)) {
					$query = $this->m_registrants->selection_result($registration_number, $birth_date);
					$this->vars['status'] = $query['status'];
					$this->vars['message'] = $query['message'];
				} else {
					$this->vars['status'] = 'error';
					$this->vars['message'] = 'Format data yang anda masukan tidak benar.';
				}
			} else {
				$this->vars['status'] = 'validation_errors';
				$this->vars['message'] = validation_errors();
			}
			$this->output
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($this->vars, JSON_HEX_APOS | JSON_HEX_QUOT))
				->_display();
			exit;
		}
   }

   public function kelulusan()
   {
	if ($this->validation()) {
		// $birth_date = $this->input->post('birth_date', true);
		$registration_number = $this->input->post('registration_number', true);
		// if (_isValidDate($birth_date) && strlen($registration_number) == 10 && ctype_digit((string) $registration_number)) {
		if (strlen($registration_number) == 10 && ctype_digit((string) $registration_number)) {
			// $query = $this->m_kelulusan->selection_result($registration_number, $birth_date);
			$query = $this->m_kelulusan->selection_result($registration_number);
			$this->vars['status'] = $query['status'];
			$this->vars['message'] = $query['message'];
		} else {
			$this->vars['status'] = 'error';
			$this->vars['message'] = 'Format data yang anda masukan tidak benar.';
		}
	} else {
		$this->vars['status'] = 'validation_errors';
		$this->vars['message'] = validation_errors();
	}
	$this->vars['content'] = 'pengumuman';
	$this->load->view('themes/'.theme_folder().'/index', $this->vars);
   }

   /**
	 * Validations Form
	 * @return Boolean
	 */
	private function validation() {
		$this->load->library('form_validation');
		$val = $this->form_validation;
		$val->set_rules('registration_number', 'Nomor NISN', 'trim|required|numeric|max_length[15]|min_length[5]');
		// $val->set_rules('birth_date', 'Tanggal Lahir', 'trim|required|callback_date_format_check');
		$val->set_message('required', '{field} harus diisi');
		$val->set_error_delimiters('<div>&sdot; ', '</div>');
		return $val->run();
	}

	/**
    * Declaration Check
    * @return Boolean
    */
	public function date_format_check($str) {
		if (!_isValidDate($str)) {
			$this->form_validation->set_message('date_format_check', 'Tanggal lahir harus diisi dengan format YYYY-MM-DD');
			return false;
		}
		return true;
	}
}
