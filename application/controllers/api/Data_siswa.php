<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data_siswa extends Public_Controller {

	/**
	 * Class Constructor
	 * @return Void
	 */
	public function __construct() {
		parent::__construct();
        $this->load->library('CORS');
		$this->load->model(['m_registrants', 'm_majors']);
		$this->pk = M_registrants::$pk;
		$this->table = M_registrants::$table;
	}

	/**
	 * Index
	 * @return Void
	 */
	public function index() {
		// $this->vars['title'] = 'Calon ' . ucwords(strtolower(__session('_student'))) . ' Baru';
		// $this->vars['admission'] = $this->vars['registrants'] = true;
		// $this->vars['major_dropdown'] = json_encode([]);
		// if (__session('major_count') > 0) {
		// 	$this->vars['major_dropdown'] = json_encode($this->m_majors->dropdown(), JSON_HEX_APOS | JSON_HEX_QUOT);
		// }
		// $this->vars['content'] = 'admission/registrants';
		// $this->load->view('backend/index', $this->vars);
		
		$data = $this->m_registrants->all();
		echo json_encode($data);
// 		header("Access-Control-Allow-Origin: *");
//         header("Access-Control-Allow-Methods: GET");
//         header("Access-Control-Allow-Methods: GET, OPTIONS");
		
		
		
// 		$this->response($data, 200);
		
// 		$data = array(
//             'full_name' => 'Hello, API'
//         );
        
        // $this->output
        //      ->set_content_type('application/json')
        //      ->set_output(json_encode($data));
// 		$this->output
// 			->set_content_type('application/json', 'utf-8')
// 			->set_output(json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT));
	}
	
	public function bank_member(){
		$data = $this->m_registrants->bank_member();
		echo json_encode($data);
	}
	
	public function bank_not_member(){
		$data = $this->m_registrants->bank_not_member();
		echo json_encode($data);
	}
	
	public function member_selected($id) {
		$data = $this->m_registrants->member_selected($id);
		echo json_encode($data);
	}
	
	public function add_member($id) {
		$this->m_registrants->add_member($id);
	}

	/**
	 * Pagination
	 * @return Object
	 */
	public function pagination() {
		if ($this->input->is_ajax_request()) {
			$keyword = trim($this->input->post('keyword', true));
			$page_number = _toInteger($this->input->post('page_number', true));
			$limit = _toInteger($this->input->post('per_page', true));
			$offset = ($page_number * $limit);
			$query = $this->m_registrants->get_where($keyword, 'rows', $limit, $offset);
			$total_rows = $this->m_registrants->get_where($keyword);
			$total_page = $limit > 0 ? ceil(_toInteger($total_rows) / _toInteger($limit)) : 1;
			$this->vars['total_page'] = _toInteger($total_page);
			$this->vars['total_rows'] = _toInteger($total_rows);
			$this->vars['rows'] = $query->result();
			$this->output
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($this->vars, JSON_HEX_APOS | JSON_HEX_QUOT))
				->_display();
			exit;
		}
	}

	/**
	 * Save | Update
	 * @return Object
	 */
	public function save() {
		if ($this->input->is_ajax_request()) {
			$id = _toInteger($this->input->post('id', true));
			if ($this->validation( $id )) {
				$fill_data = $this->fill_data();
				$fill_data[(_isNaturalNumber( $id ) ? 'updated_by' : 'created_by')] = __session('user_id');
				if (!_isNaturalNumber( $id )) $fill_data['created_at'] = date('Y-m-d H:i:s');
				$query = $this->model->upsert($id, $this->table, $fill_data);
				$this->vars['status'] = $query ? 'success' : 'error';
				$this->vars['message'] = $query ? 'Data Anda berhasil disimpan.' : 'Terjadi kesalahan dalam menyimpan data';
			} else {
				$this->vars['status'] = 'error';
				$this->vars['message'] = validation_errors();
			}
			$this->output
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($this->vars, JSON_HEX_APOS | JSON_HEX_QUOT))
				->_display();
			exit;
		}
	}

	/**
	 * Verified prospective studnets
	 * @return 	Object
	 */
	public function verified() {
		if ($this->input->is_ajax_request()) {
			$id = _toInteger($this->input->post('id', true));
			$fill_data['updated_at'] = date('Y-m-d H:i:s');
			$fill_data['updated_by'] = __session('user_id');
			$fill_data['re_registration'] = $this->input->post('re_registration', true);
			$this->vars['status'] = $this->model->update($id, $this->table, $fill_data) ? 'success' : 'error';
			$this->vars['message'] = $this->vars['status'] == 'success' ? 'updated' : 'not_updated';
			$this->output
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($this->vars, JSON_HEX_APOS | JSON_HEX_QUOT))
				->_display();
			exit;
		}
	}

	/**
	 * Fill Data
	 * @return Array
	 */
	private function fill_data() {
		return [
			'is_transfer' => $this->input->post('is_transfer', true),
			'admission_type_id' => (int) $this->input->post('admission_type_id', true),
			'prev_exam_number' => $this->input->post('prev_exam_number', true),
			'paud' => $this->input->post('paud', true),
			'tk' => $this->input->post('tk', true),
			'skhun' => $this->input->post('skhun', true),
			'prev_diploma_number' => $this->input->post('prev_diploma_number', true),
			'hobby' => $this->input->post('hobby', true),
			'ambition' => $this->input->post('ambition', true),
			'first_choice_id' => (int) $this->input->post('first_choice_id', true),
			'second_choice_id' => (int) $this->input->post('second_choice_id', true),
			'full_name' => $this->input->post('full_name', true),
			'gender' => $this->input->post('gender', true),
			'nisn' => $this->input->post('nisn') ? $this->input->post('nisn', true) : null,
			'nkk' => $this->input->post('nkk', true),
			'nik' => $this->input->post('nik', true),
			'no_akta' => $this->input->post('no_akta', true),
			'birth_place' => $this->input->post('birth_place', true),
			'birth_date' => $this->input->post('birth_date', true),
			'religion_id' => (int) $this->input->post('religion_id', true),
			'prev_school_name' => $this->input->post('prev_school_name', true),
			'tahun_lulus' => $this->input->post('tahun_lulus', true),
			'special_need_id' => (int) $this->input->post('special_need_id', true),
			'street_address' => $this->input->post('street_address', true),
			'rt' => $this->input->post('rt', true),
			'rw' => $this->input->post('rw', true),
			'sub_village' => $this->input->post('sub_village', true),
			'village' => $this->input->post('village', true),
			'sub_district' => $this->input->post('sub_district', true),
			'district' => $this->input->post('district', true),
			'postal_code' => $this->input->post('postal_code', true),
			'residence_id' => (int) $this->input->post('residence_id', true),
			'transportation_id' => (int) $this->input->post('transportation_id', true),
			'phone' => $this->input->post('phone', true),
			'mobile_phone' => $this->input->post('mobile_phone', true),
			'phone_father' => $this->input->post('phone_father', true),
			'phone_mother' => $this->input->post('phone_mother', true),
			'phone_guardian' => $this->input->post('phone_guardian', true),
			'email' => $this->input->post('email') ? $this->input->post('email', true) : null,
			'sktm' => $this->input->post('sktm', true),
			'kks' => $this->input->post('kks', true),
			'kps' => $this->input->post('kps', true),
			'kip' => $this->input->post('kip', true),
			'kis' => $this->input->post('kis', true),
			'citizenship' => $this->input->post('citizenship', true),
			'country' => $this->input->post('country', true),
			'father_name' => $this->input->post('father_name', true),
			'nik_father' => $this->input->post('nik_father', true),
			'father_birth_year' => $this->input->post('father_birth_year', true),
			'father_education_id' => (int) $this->input->post('father_education_id', true),
			'father_employment_id' => (int) $this->input->post('father_employment_id', true),
			'father_monthly_income_id' => (int) $this->input->post('father_monthly_income_id', true),
			'father_special_need_id' => (int) $this->input->post('father_special_need_id', true),
			'mother_name' => $this->input->post('mother_name', true),
			'nik_mother' => $this->input->post('nik_mother', true),
			'mother_birth_year' => $this->input->post('mother_birth_year', true),
			'mother_education_id' => (int) $this->input->post('mother_education_id', true),
			'mother_employment_id' => (int) $this->input->post('mother_employment_id', true),
			'mother_monthly_income_id' => (int) $this->input->post('mother_monthly_income_id', true),
			'mother_special_need_id' => (int) $this->input->post('mother_special_need_id', true),
			'guardian_name' => $this->input->post('guardian_name', true),
			'nik_guardian' => $this->input->post('nik_guardian', true),
			'guardian_birth_year' => $this->input->post('guardian_birth_year', true),
			'guardian_education_id' => (int) $this->input->post('guardian_education_id', true),
			'guardian_employment_id' => (int) $this->input->post('guardian_employment_id', true),
			'guardian_monthly_income_id' => (int) $this->input->post('guardian_monthly_income_id', true),
			'address_parent' => (int) $this->input->post('address_parent', true),
			'mileage' => $this->input->post('mileage', true),
			'traveling_time' => $this->input->post('traveling_time', true),
			'height' => $this->input->post('height', true),
			'weight' => $this->input->post('weight', true),
			'lingkar_kpl' => $this->input->post('lingkar_kpl', true),
			'bhs_indo' => $this->input->post('bhs_indo', true),
			'bhs_inggris' => $this->input->post('bhs_inggris', true),
			'mtk' => $this->input->post('mtk', true),
			'ipa' => $this->input->post('ipa', true),
			'sibling_number' => $this->input->post('sibling_number', true),
			'anak_ke' => $this->input->post('anak_ke', true)
		];
	}

	/**
	 * Validation Form
	 * @return Boolean
	 */
	private function validation( $id = 0 ) {
		$this->load->library('form_validation');
		$val = $this->form_validation;
		if (__session('major_count') > 0) {
			$val->set_rules('first_choice_id', 'Pilihan I', 'trim|required');
			// $val->set_rules('second_choice_id', 'Pilihan II', 'trim|required');
		}
		$val->set_rules('full_name', 'Nama Lengkap', 'trim|required');
		//$val->set_rules('email', 'Email', 'trim|required|valid_email|callback_email_exists[' . $id . ']');
		$val->set_rules('father_birth_year', 'Tahun Lahir Ayah', 'trim|numeric|min_length[4]|max_length[4]');
		$val->set_rules('mother_birth_year', 'Tahun Lahir Ibu', 'trim|numeric|min_length[4]|max_length[4]');
		$val->set_rules('guardian_birth_year', 'Tahun Lahir Wali', 'trim|numeric|min_length[4]|max_length[4]');
		$val->set_error_delimiters('<div>&sdot; ', '</div>');
		return $val->run();
	}

	/**
	 * Email Exists ?
	 * @param String $email
	 * @param Integer $id
	 * @return Boolean
	 */
	public function email_exists( $email, $id ) {
		$this->load->model(['m_students', 'm_employees', 'm_users']);
		$user_id = $this->m_users->get_user_id( $id );
		$student_exists = $this->m_students->email_exists( $email, $id );
		$employee_exists = $this->m_employees->email_exists( $email );
		$user_exists = $this->m_users->email_exists( $email, $user_id);
		if ( $student_exists || $employee_exists || $user_exists ) {
			$this->form_validation->set_message('email_exists', 'Email sudah digunakan');
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Upload
	 * @return Void
	 */
	public function upload() {
		if ($this->input->is_ajax_request()) {
			$id = _toInteger($this->input->post('id', true));
			if (_isNaturalNumber( $id )) {
				$query = $this->model->RowObject($this->pk, $id, $this->table);
				$file_name = $query->photo;
				$config = [];
				$config['upload_path'] = './media_library/students/';
				$config['allowed_types'] = 'jpg|png|jpeg|gif';
				$config['max_size'] = 0;
				$config['encrypt_name'] = true;
				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload('file') ) {
					$this->vars['status'] = 'error';
					$this->vars['message'] = $this->upload->display_errors();
				} else {
					$file = $this->upload->data();
					$update = $this->model->update($id, $this->table, ['photo' => $file['file_name']]);
					if ($update) {
						// chmood new file
						@chmod(FCPATH.'media_library/students/'.$file['file_name'], 0777);
						// chmood old file
						@chmod(FCPATH.'media_library/students/'.$file_name, 0777);
						// unlink old file
						@unlink(FCPATH.'media_library/students/'.$file_name);
						// resize new image
						$this->resize_image(FCPATH.'media_library/students', $file['file_name']);
					}
					$this->vars['status'] = 'success';
					$this->vars['message'] = 'uploaded';
				}
			} else {
				$this->vars['status'] = 'error';
				$this->vars['message'] = 'ID bukan merupakan tipe angka yang valid !';
			}
			$this->output
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($this->vars, JSON_HEX_APOS | JSON_HEX_QUOT))
				->_display();
			exit;
		}
	}

	/**
	  * Resize Image
	  * @param String $source
	  * @param String $file_name
	  * @return Void
	  */
	 private function resize_image($source, $file_name) {
		$this->load->library('image_lib');
		$config['image_library'] = 'gd2';
		$config['source_image'] = $source .'/'.$file_name;
		$config['maintain_ratio'] = false;
		$config['width'] = (int) __session('student_photo_width');
		// $config['height'] = (int) __session('student_photo_height');
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
	}

	/**
	  * Print PDF Registration Form
	  */
	public function print_registration_form() {
		if ($this->input->is_ajax_request()) {
			$id = _toInteger($this->input->post('id', true));
			$query = $this->model->RowObject($this->pk, $id, $this->table);
			if (_isNaturalNumber( $id )) {
				$this->load->model('m_registrants');
				$result = $this->m_registrants->find_registrant($query->birth_date, $query->registration_number);
				if (count($result) == 0) {
					$this->vars['status'] = 'warning';
					$this->vars['message'] = 'Data dengan tanggal lahir '.indo_date($query->birth_date).' dan nomor pendaftaran '.$query->registration_number.' tidak ditemukan.';
				} else {
					$file_name = 'formulir-penerimaan-'. (__session('school_level') >= 5 ? 'mahasiswa' : 'peserta-didik').'-baru-tahun-'.__session('admission_year');
					$file_name .= '-'.$query->birth_date.'-'.$query->registration_number.'.pdf';
					$this->load->library('admission');
					$this->admission->create_pdf($result);
					$this->vars['status'] = 'success';
					$this->vars['file_name'] = $file_name;
				}
			} else {
				$this->vars['status'] = 'error';
				$this->vars['message'] = 'Format data tidak valid.';
			}
			$this->output
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($this->vars, JSON_HEX_APOS | JSON_HEX_QUOT))
				->_display();
			exit;
		}
	}

	/**
	 * print_reg_in_web
	 * @return Void
	 */
	public function print_reg_in_web($id)
	{
		// verifikasi(daftar ulang) menjadi true
		$fill_data['re_registration'] = true;
		$this->vars['verifikasi'] = $this->model->update($id, $this->table, $fill_data) ? 'success' : 'error';
		
		$query = $this->model->RowObject($this->pk, $id, $this->table);
		$this->load->model('m_registrants');
		$this->vars['data'] = $this->m_registrants->find_registrant($query->birth_date, $query->registration_number);
		
		// $this->vars['content'] = 'admission/pdf_reg_in_web';
		$this->load->view('admission/pdf_reg_in_web', $this->vars);
	}

	/**
	 * Admission Reports
	 * @return Object
	 */
	public function admission_reports() {
		if ($this->input->is_ajax_request()) {
			$query = $this->m_registrants->admission_reports();
			$this->output
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($query->result(), JSON_HEX_APOS | JSON_HEX_QUOT))
				->_display();
			exit;
		}
	}

	/**
	 * Profile
	 * @return Void
	 */
	public function profile() {
		$id = _toInteger($this->uri->segment(4));
		if (_isNaturalNumber( $id )) {
			$this->load->model(['m_students']);
			$this->vars['student'] = $this->m_students->profile($id);
			$this->vars['title'] = 'Profil Calon ' . __session('_student') . ' Baru';
			$this->vars['photo'] = base_url('media_library/images/no-image.jpg');
			$this->vars['scholarships'] = $this->vars['achievements'] = FALSE;
			$photo_name = $this->vars['student']->photo;
			$photo = 'media_library/students/' . $photo_name;
			if ($photo_name && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $photo)) {
				$this->vars['photo'] = base_url($photo);
			}
			$this->vars['admission'] = $this->vars['registrants'] = true;
			$this->vars['content'] = 'academic/student_profile';
			$this->load->view('backend/index', $this->vars);
		} else {
			show_404();
		}
	}
}
