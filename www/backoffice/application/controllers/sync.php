<?php 
/* Copyright 2021 European Commission
    
Licensed under the EUPL, Version 1.2 only (the "Licence");
You may not use this work except in compliance with the Licence.

You may obtain a copy of the Licence at:
	https://joinup.ec.europa.eu/software/page/eupl5

Unless required by applicable law or agreed to in writing, software 
distributed under the Licence is distributed on an "AS IS" basis, 
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either 
express or implied.

See the Licence for the specific language governing permissions 
and limitations under the Licence. */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sync extends CI_Controller {

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
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	 
	function __construct()
    {
        parent::__construct();
		
		$this->load->model('auth_model', 'auth');
		$this->load->model('easme_model', 'ooc');
		$this->load->model('sync_model', 'sync');
		$this->load->model('publish_model', 'publish');
		$this->load->config('sync');
		
		if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');
    }
	
	public function index()
	{
		$data = array(
			'content' => 'sync/index',
			'js' => array(),
			'css' => array('commitments.css'),
			'imports' => $this->sync->get_imports(),
			
		);
		$this->load->view('page', $data);
	}
	
	public function get_file()
	{
		$data = array(
			'js' => array('Form.MultipleFileInput.js', 'Form.Upload.js', 'Request.File.js'),
			'content' => 'sync/file',
			'css' => array('commitments.css'),
		);
		$this->load->view('page', $data);
	}
	
	
	public function upload_file()
	{	
		
		$ext = $_FILES['Filedata'] ? pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION) : '.';
		
		$uid = uniqid().'.'.$ext;
		
		$config['upload_path'] = $this->config->item('upload_folder');
		$config['allowed_types'] = 'xlsx|csv';
		$config['max_size']	= '64000';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		$config['file_name']  = $uid;

		$this->load->library('upload', $config);
		

		if ( ! $this->upload->do_upload('Filedata'))
		{
			$data = array(
				'id' => $uid,
				'status' => -1,
				'desc' => $this->upload->display_errors(),
			);
		}
		else
		{
			$import_id = $this->sync->init_transaction($uid);
			
			$data = array(
				'id' => $import_id,
				'status' => 0,
				'desc' => 'Upload successfull',
				
			);
			
		}
		
		header('Content-Type: application/json');
		
		echo json_encode($data);
	}
	
	public function ftp_file()
	{	

		$origin = $this->config->item('ftp_folder').$this->input->post('file');
		
		if(file_exists($origin)) {
			
			$uid = uniqid().'.xlsx';
			
			if(rename($origin, $this->config->item('upload_folder').$uid)) {
				
				$import_id = $this->sync->init_transaction($uid);
				
				$data = (object) array(
					'id' => $import_id,
					'status' => 0,
					'desc' => 'Upload successfull',
					
				);			
			} else { 
				$data = (object) array(
					'id' => 0,
					'status' => 1,
					'desc' => 'could not move "'.$origin.'" to "'.$this->config->item('upload_folder').$uid.'"',
				);	
			}
			
		} else { 
			$data = (object) array(
				'id' => 0,
				'status' => -1,
				'desc' => 'file "'.$origin.'" not found',
			);	
		}
		
		header('Content-Type: application/json');
		
		echo json_encode($data);
	}
	
	public function map_columns($import_id) { 
		$this->load->helper('easme_helper');
		
		$this->sync->open_transaction($import_id);
		
		if($this->sync->import_status == 'init') {
			if(file_exists($this->config->item('upload_folder').$this->sync->import_file)) {
				$this->load->library('SimpleXLSX');
				
				$xlsx = new SimpleXLSX();
				$xlsx->load($this->config->item('upload_folder').$this->sync->import_file);
				
				
				
				$data = (object) array(
					'id' => $import_id,
					'status' => 0,
					'parsing completed',
					'data' => $this->sync->get_data($import_id, $xlsx),
				);
				
				$this->sync->update_status('parsed');
			}
			else { 
				$data = (object) array(
					'id' => $import_id,
					'status' => 1,
					'desc' => 'could not open file',
				);
				
				$this->sync->update_status('no file');
			}
		} else {
			if($this->sync->import_status == 'parsed') { 
				$data = (object) array(
					'id' => $import_id,
					'status' => 0,
					'parsing completed',
					'data' => $this->sync->recover($import_id),
				);
			}
		}
		
		header('Content-Type: application/json');
		
		echo json_encode($data);
	}
	
	public function recover($import_id) { 
		$this->sync->recover($import_id);
		
		$this->index();
	}
	
	public function process_line($import_id) {
		
		$line = $this->sync->process_line($import_id, $this->input->post("field"), $this->input->post("override") == '1');
		
		$response = array(
			'id' => $import_id,
			'status' => $line ? 0: 2,
			'message' => 'record processed',
			'data' => $line,			
		);
		header('Content-Type: application/json');
		
		echo json_encode($response);
	}
	
	public function status($import_id) { 
		
		$this->sync->open_transaction($import_id);
		
		$data = array(
			'content' => 'sync/report',
			'import' => $this->sync->current_import
		);
		
		$this->load->view('page', $data);
	}
	
	public function get_report($import_id) { 
	
		
	}
	
	public function undo($import_id) { 
		
		$this->sync->rollback($import_id);
		
		$this->index();
		
	}
	
	public function delete($import_id) { 
		
		$this->sync->open_transaction($import_id);
		
		if($this->sync->import_status != 'completed') $this->sync->delete_transaction($import_id);
		
		$this->index();
	}
	
	public function geocoder() {
		
		$data = array(
			'content' => 'sync/geocoder',
			'api_keys' => $this->config->item('geocode_api_keys'),
		);
		
		$this->load->view('page', $data);
	}
	
	public function process_geocode() { 
		$response = $this->sync->geocode($this->input->post("api_key"));
		
		header('Content-Type: application/json; charset=UTF-8');
		
		echo json_encode($response);
	}
}
