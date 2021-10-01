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

class Sync_model extends CI_Model {
	
	var $import_id = 0;
	var $import_file = '';
	var $import_status = '';
	var $import_header_row = 0;
	var $current_import = NULL;
	
 	var $import_table = 'imports';
	
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		
		date_default_timezone_set($this->config->item('timezone'));
		
		// set script timeout limit to 15m
		set_time_limit(60 * 15);
		
    }
    
    function init_transaction($file) {
		$data = array(
			'import_date' => date('Y-m-d H:i:s'),
			'import_status' => 'init',
			'import_status_date' => date('Y-m-d H:i:s'),
			'import_file' => $file
		);
		
		$this->db->insert($this->import_table, $data);
		
		$this->import_id = $this->db->insert_id();	
		
		return $this->import_id;
	}
	
	function open_transaction($id) {
		
		$row = $this->get_import($id);
		
		if($row) {
			
			$this->import_id = $row->import_id;	
			$this->import_file = $row->import_file;	
			$this->import_status = $row->import_status;	
			$this->import_header_row = $row->import_header_row;
		}
	}
	
	function get_ftp() { 
	
		$files = array();
		
		if ($handle = opendir($this->config->item('ftp_folder'))) {
			while (false !== ($file = readdir($handle)))
			{
				if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'xlsx')
				{
					array_push($files,$file);
				}
			}
			closedir($handle);
		}
		
		return $files;
	}
	
	function get_imports() { 
		
		$this->db->order_by('import_date desc');
		$query = $this->db->get('imports');
		
		return $query->result();
	}
	
	function get_import($import_id) { 
		$this->db->where('import_id', $import_id);
		$query = $this->db->get('imports');
		
		if($query->num_rows() > 0) { 
			
			$this->current_import = $query->row();
		}
		
		return $this->current_import;
	}
	
	function delete_transaction($import_id) { 
		
		$this->db->where('import_id', $import_id);
		$this->db->delete('imports_datas');
		
		$this->db->where('fk_import_id', $import_id);
		$this->db->delete('imports_logs');
		
		$this->db->where('import_id', $import_id);
		$this->db->delete('imports');
	}
	
	function get_call($id) { 
	
		$this->db->where('call_id', $id);
		$query = $this->db->get('calls');
		
		if($query->num_rows() > 0) { 
			
			return $query->row();	
		}
		
		return null;
	}
	
	function get_call_by_code($code) { 
	
		$this->db->where('call_full_name', $code);
		$query = $this->db->get('calls');
		
		if($query->num_rows() > 0) { 
			
			return $query->row();	
		}
		
		return null;
	}
	
	
	function set_call($id, $data) { 
		
		if($id > 0) { 
			$this->db->where('call_id', $id);
			$this->db->update('calls', $data);
		}
		else { 
			$this->db->insert('calls', $data);
			$id = $this->db->insert_id();
		}
		
		return $this->get_call($id);
	}
	
	function get_proposal($id) { 
		$this->db->where('proposal_id', $id);
		$query = $this->db->get('proposals');
		
		if($query->num_rows() > 0) { 
			
			return $query->row();	
		}
		
		return null;
	}
	
	function set_proposal($id, $data) { 
		
		if($id > 0) { 
			$this->db->where('proposal_id', $id);
			$this->db->update('proposals', $data);
			
		}
		else { 
			$this->db->insert('proposals', $data);
			$id = $data['proposal_id'];
		}
		
		return $this->get_proposal($id);
	}
	
	function get_applicant($id) { 
		$this->db->where('applicant_id', $id);
		$query = $this->db->get('applicants');
		
		if($query->num_rows() > 0) { 
			
			return $query->row();	
		}
		
		return null;
	}
	
	function set_applicant($id, $data) { 
		
		//$data['applicant_bo_status_date'] = date("Y-m-d");
		
		if($id > 0) { 
			$this->db->where('applicant_id', $id);
			$this->db->update('applicants', $data);
		}
		else { 
			$this->db->insert('applicants', $data);
			$id = $data['applicant_id'];
			//$this->history_model->log_table_action('applicants', $id, 'import');
		}
		
		return $this->get_applicant($id);
	}
	
	function get_proposal_applicant($id) { 
		$this->db->where('pa_id', $id);
		$query = $this->db->get('proposals_applicants');
		
		if($query->num_rows() > 0) { 
			
			return $query->row();	
		}
		
		return null;
	}
	
	function get_proposal_applicant_by_ref($proposal_id, $applicant_id) { 
		$this->db->where('fk_proposal_id', $proposal_id);
		$this->db->where('fk_applicant_id', $applicant_id);
		
		$query = $this->db->get('proposals_applicants');
		
		if($query->num_rows() > 0) { 
			
			return $query->row();	
		}
		
		return null;
	}
	
	function set_proposal_applicant($id, $data) {
		 
		if($id > 0) { 
			$this->db->where('pa_id', $id);
			$this->db->update('proposals_applicants', $data);
		}
		else { 
			$this->db->insert('proposals_applicants', $data);
			$id = $this->db->insert_id();
			//$this->history_model->log_table_action('proposals_applicants', $id, 'import');
		}
		
		return $this->get_proposal_applicant($id);
	}
	
	function unlink_applicant($proposal_id, $applicant_id) { 
		$data = array(
			'fk_proposal_id' => $proposal_id,
			'fk_applicant_id' => $applicant_id
		);
		$this->db->where($data);
		$this->db->delete('proposals_applicants');
	}
	
	function delete_proposal_applicant($pa_id) { 
		
		$this->db->where('pa_id',$pa_id);
		$this->db->delete('proposals_applicants');
	}
	
	function get_applicant_link($proposal_id, $applicant_id, $role) { 
		$data = array(
			'fk_proposal_id' => $proposal_id,
			'fk_applicant_id' => $applicant_id,
			'applicant_role' => $role
		);
		
		$this->db->where($data);
		$query = $this->db->get('proposals_applicants');
		
		return $query->row();
	}
	
	
	function get_proposal_site($id) { 
		$this->db->where('id', $id);
		$query = $this->db->get('proposals_sites');
		
		return $query->row();
	}
	
	function get_proposal_site_by_ref($proposal_id, $name) { 
		$data = array(
			'proposal_id' => $proposal_id,
			'name' => $name,
		);
		
		$this->db->where($data);
		$query = $this->db->get('proposals_sites');
		
		return $query->row();
	}
	
	function set_proposal_site($id, $data) {
		 
		if($id > 0) { 
			$this->db->where('id', $id);
			$this->db->update('proposals_sites', $data);
		}
		else { 
			$this->db->insert('proposals_sites', $data);
			$id = $this->db->insert_id();
			//$this->history_model->log_table_action('proposals_sites', $id, 'import');
		}
		
		return $this->get_proposal_site($id);
	}
	
	function get_topic($id) { 
	
		$this->db->where('topic_id', $id);
		$query = $this->db->get('topics');
		
		if($query->num_rows() > 0) { 
			
			return $query->row();	
		}
		
		return null;
	}
	
	function get_topic_by_code($code) { 
	
		$this->db->where('topic_code', $code);
		$query = $this->db->get('topics');
		
		if($query->num_rows() > 0) { 
			
			return $query->row();	
		}
		
		return null;
	}
	
	function set_topic($id, $data) { 
		
		if($id > 0) { 
			$this->db->where('topic_id', $id);
			$this->db->update('topics', $data);
		}
		else { 
			$this->db->insert('topics', $data);
			$id = $this->db->insert_id();
		}
		
		return $this->get_topic($id);
	}
	
	function get_type($id) { 
	
		$this->db->where('id', $id);
		$query = $this->db->get('proposals_types');
		
		if($query->num_rows() > 0) { 
			
			return $query->row();	
		}
		
		return null;
	}
	
	function set_type($id, $data) { 
		
		if(!$this->get_type($id)) { 
			
			$data['id'] = $id;
			$this->db->insert('proposals_types', $data);
		}
		
		return $this->get_type($id);
	}
	
	function update_status($status) {
		
		$data = array(
			'import_status' => $status,
			'import_status_date' => date('Y-m-d H:i:s')
		);
		
		$this->db->where('import_id', $this->import_id);	
		$this->db->update($this->import_table, $data);
	}
	
	function close_transaction() {
		$this->update_status('completed');
	}
	
	/*
	*	Saves import column mapping to database
	*/
	function set_headers($data) { 
		
		$data['import_status'] = 'mapped';
		$data['import_status_date'] = date('Y-m-d H:i:s');
		
		$this->db->where('import_id', $this->import_id);
		$this->db->update($this->import_table, $data);		
		
	}

	function rollback($import_id) { 
		
		$this->db->where('fk_import_id', $import_id);
		$this->db->order_by('log_id desc');
		$query = $this->db->get('imports_logs');
		
		foreach($query->result() as $log) { 
			
			switch($log->log_action) { 
				
				case 'add': 
					
					$this->db->where($log->log_id_field, $log->log_id_value);
					$this->db->delete($log->log_table);
					
					break;
					
				case 'edit':
					
					$this->db->set(array($log->log_field => $log->log_value));
					$this->db->where($log->log_id_field, $log->log_id_value);
					$this->db->update($log->log_table);
					
					break;
			}
			
			$this->db->where('log_id', $log->log_id);
			$this->db->delete('imports_logs');
		}
		
		$this->db->where('import_id', $import_id);
		$this->db->delete('imports');
	}
	
	function log_action($action, $table, $id_field, $id_value, $field = null, $value = null) {
		
		$data = array(
			'log_action' => $action,
			'log_table' => $table,
			'log_id_field' => $id_field,
			'log_id_value' => $id_value,
			'log_field' => $field,
			'log_value' => $value,
			'fk_import_id' => $this->import_id
		); 
		
		$this->db->insert('imports_logs', $data);	
	}
	
	function truncate_all() { 
	
		$this->db->query('truncate table proposals_applicants');
		$this->db->query('truncate table applicants');
		$this->db->query('truncate table proposals');
		$this->db->query('truncate table calls');
		$this->db->query('truncate table topics');
		$this->db->query('truncate table proposals_types');
		$this->db->query('truncate table imports_logs');
		$this->db->query('truncate table imports');
	}
	
	function get_data($import_id, $xlsx) {
		
		$report = (object) array(
			'columns' => 0,
			'lines' => 0,
			'headerAt' => 0,
			'headers' => array(),
			'records' => 0,
		);
		
		$sheet = $xlsx->rows(1);
		
		list($rows, $report->columns) = $xlsx->dimension();
		
		if($sheet && is_array($sheet)) { 
		
			$index = 0;
			
			foreach($sheet as $row) {
				
				$report->lines++;
				
				$values = array();
				for ($j = 0; $j < $report->columns; $j++) {
					$values[] = isset($row[$j]) ? $row[$j] : '';
				}
			
				$line = array_values($values);
				
				$empty = trim(implode('',$line)) == '';
				
				if(!$empty) {
					if($index == 0) {
						//$report->columns = count($line);
						$report->headers = $line;
						$report->headerAt = $report->lines;
						
						$this->db->where('import_id', $import_id);
						$this->db->update('imports', array(
							'import_header_row' => $report->lines,
							'import_headers' => json_encode($line)
						));
						
						$index++;
					} else {
						$fields = array(
							'import_id' => $import_id,
							'line' => $report->lines,
							'json' => json_encode($line),
						);
						
						$this->db->insert('imports_datas', $fields); 
						
						$report->records++;
					}
				}
			}
		}
		
		return $report;
	}
	public function recover($import_id) { 
		
		$import = $this->get_import($import_id);
		$headers = json_decode($import->import_headers);
		
		$this->db->where('import_id', $import_id);
		$this->db->select('count(*) as total');
		$query = $this->db->get('imports_datas');
		$lines = $query->row();
		
		$report = (object) array(
			'columns' => count($headers),
			'lines' => $import->import_header_row + $lines->total,
			'headerAt' => $import->import_header_row,
			'headers' => $headers,
			'records' => $lines->total,
		);
		
		return $report;
	}
	
	public function process_line($import_id, $fields, $override = false) {
		$data = NULL;
		$this->db->where('import_id',$import_id);
		$this->db->limit(1);
		$this->db->order_by('line');
		$query = $this->db->get('imports_datas');
		$row = $query->row();
		
		$this->import_id = $import_id;
		
		$data = (object) array(
			'line' => 0,
			'report' => '',
			'status' => 2,
			'changes' => (object) array(
				'projects' => array(0,0),
				'partners' => array(0,0),
				'roles' => array(0,0),
				'sites' => array(0,0),
			)
		);
			
		if($row) {
			// clean record
			$this->db->where('id',$row->id);
			$this->db->delete('imports_datas');
			
			$messages = array();
			
			$data->line = $row->line;
			$data->status = 0;
			$props = $this->config->item("fields");
			$values = array_merge(array(),json_decode($row->json));
			
			$sorted = array();
			
			for($i = 0; $i < count($fields); $i++) {
				
				$field = $fields[$i];
				
				if(property_exists($props,$field)) { 
				
					if(!array_key_exists($props->{$field}->table, $sorted)) { 
						$sorted[$props->{$field}->table] = array();
					}
					
					$value = array_key_exists($i,$values) ? $values[$i]: '';
					
					switch($props->{$field}->type) {
					case 'date': 
						if($value != '') { 
							$ts = ($value - 25569) * 86400;
							$value = date('Y-m-d', $ts);
						} else { 
							$value = null;
						}
						break;
					case 'number': 
						$value = 0 + $value;
						break;
					
					case 'boolean':
						$value = 0 + str_replace('Yes','1',$value);
						break;
					default: 
						$value = str_replace('_x000D_', ' ', $value);
					}
					
					if(array_key_exists('transform', $props->{$field})) { 
						foreach($props->{$field}->transform as $transform) { 
							$value = preg_replace($transform[0], $transform[1], $value);
						}
					}
					$sorted[$props->{$field}->table][$props->{$field}->fieldname] = $value; 
				}
					
			}
						
			if(array_key_exists('topics', $sorted)) { 
				
				$topic = $this->get_topic_by_code($sorted['topics']['topic_code']);
				
				if(!$topic) { 
					$topic = $this->set_topic(0,$sorted['topics']);
					$message[] = 'New Topic "'.$topic->topic_code.'"';
					$this->log_action('add','topics','topic_id', $topic->topic_id,'topic_code', $topic->topic_code);
				} 
				
				$sorted['topics']['topic_id'] = $topic->topic_id;
			}
			
			if(array_key_exists('proposals_types', $sorted)) { 
				
				$type = $this->get_type($sorted['proposals_types']['id']);
				
				if(!$type) { 
					$type = $this->set_type($sorted['proposals_types']['id'],array());
					$message[] = 'New Type "'.$type->id.'"';
					$this->log_action('add','proposals_types','id', $type->id,'', '');
				} 
				
				$sorted['proposals_types']['id'] = $type->id;
			}
			
			if(array_key_exists('proposals', $sorted)) { 
				
				$id = $sorted['proposals']['proposal_id'];
				if(0 + $id != 0) {
					$proposal = $this->get_proposal($id);
					
					if(array_key_exists('topics', $sorted)) { 
						$sorted['proposals']['fk_topic_id'] = $sorted['topics']['topic_id'];
					}
					
					if(array_key_exists('proposals_types', $sorted)) { 
						$sorted['proposals']['proposal_stage'] = $sorted['proposals_types']['id'];
					}
					
					if(array_key_exists('calls', $sorted)) { 
						$call = $this->get_call_by_code($sorted['calls']['call_full_name']);
					
						if(!$call) { 
							$call = $this->set_call(0,$sorted['calls']);
							$message[] = 'added call "'.$call->call_full_name.'"';
							$this->log_action('add', 'calls', 'call_id', $call->call_id, 'call_full_name', $call->call_full_name);
						} 
						
						$sorted['calls']['call_id'] = $call->call_id;
						$sorted['proposals']['fk_call_id'] = $call->call_id;
					}
					
					if(!$proposal) {
						if(count($sorted['proposals']) > 1) { 
							$proposal = $this->set_proposal(0,$sorted['proposals']);
							$message[] = 'added project "'.$proposal->proposal_id.'"';
							$data->changes->projects[0]++;
							$this->log_action('add', 'proposals', 'proposal_id', $proposal->proposal_id, 'proposal_id', $proposal->proposal_id); 
						} else { 
							$data->status = 1; 
							$message[] = 'invalid proposal ID';
						}
					} else {  
						foreach($sorted['proposals'] as $field => $value) { 
							if($field != 'proposal_id' && $value != $proposal->{$field}) { 
								$this->log_action('edit', 'proposals', 'proposal_id', $proposal->proposal_id, $field, $value);
							}
						}
						$proposal = $this->set_proposal($proposal->proposal_id,$sorted['proposals']);
						$message[] = 'updated project "'.$proposal->proposal_id.'"';
						$data->changes->projects[1]++;
						
					}
				} else {
					$data->status = 1; 
					$message[] = 'invalid proposal ID';
				}
			}
			
			if($data->status != 1 && array_key_exists('applicants', $sorted)) { 
				
				$id = $sorted['applicants']['applicant_id'];
				
				if(0 + $id != 0) {
					$applicant = $this->get_applicant($id);
					
					if(!$applicant) {

                        $dates = array(
                            'applicant_created' => date("Y-m-d H:i:s"),
                            'applicant_updated' => date("Y-m-d H:i:s")
                        );
                        $sorted['applicants'] = $sorted['applicants'] + $dates;
						$applicant = $this->set_applicant(0,$sorted['applicants']);
						$message[] = 'added partner "'.$applicant->applicant_id.'"';
						$data->changes->partners[0]++;
						$this->log_action('add', 'applicants', 'applicant_id', $applicant->applicant_id, 'applicant_id', $applicant->applicant_id);
					} else {
					    //TODO debug missing dates
						foreach($sorted['applicants'] as $field => $value) { 
							if($field != 'applicant_id' && $value != $applicant->{$field}) { 
								$this->log_action('edit', 'applicants', 'applicant_id', $applicant->applicant_id, $field, $value);
							}
						}
						$applicant = $this->set_applicant($applicant->applicant_id,$sorted['applicants']);
						$message[] = 'updated partner "'.$applicant->applicant_id.'"';
						$data->changes->partners[1]++;
						
					}
				} else { 
					$data->status = 1; 
					$message[] = 'invalid applicant ID';
				}
			}
			
			if($data->status != 1 && array_key_exists('applicants', $sorted) && array_key_exists('proposals', $sorted)) { 
				
				$role = $this->get_proposal_applicant_by_ref($sorted['proposals']['proposal_id'], $sorted['applicants']['applicant_id']);
				
				if(!$role) { 
					$sorted['proposals_applicants']['fk_proposal_id'] = $sorted['proposals']['proposal_id'];
					$sorted['proposals_applicants']['fk_applicant_id'] = $sorted['applicants']['applicant_id'];
					$role = $this->set_proposal_applicant(0,$sorted['proposals_applicants']);
					$message[] = 'added role between "'.$sorted['proposals_applicants']['fk_proposal_id'].'" and "'.$sorted['proposals_applicants']['fk_applicant_id'].'"';
					$data->changes->roles[0]++;
				} else { 
					if(array_key_exists('proposals_applicants', $sorted)) {
						foreach($sorted['proposals_applicants'] as $field => $value) { 
							if($field != 'fk_proposal_id' && $field != 'fk_applicant_id' && $value != $role->{$field}) { 
								$this->log_action('edit', 'proposals_applicants', 'pa_id', $role->pa_id, $field, $value);
							}
						}
						$role = $this->set_proposal_applicant($role->pa_id,$sorted['proposals_applicants']);
						$message[] = 'updated role between "'.$role->fk_proposal_id.'" and "'.$role->fk_applicant_id.'"';
						$data->changes->roles[1]++;
					}
				}
			}
			
			if($data->status != 1 && array_key_exists('proposals_sites', $sorted)) { 
				
				$site = $this->get_proposal_site_by_ref($sorted['proposals']['proposal_id'], $sorted['proposals_sites']['name']);
				
				if(!$site) { 
					$sorted['proposals_sites']['proposal_id'] = $sorted['proposals']['proposal_id'];
					$site = $this->set_proposal_site(0,$sorted['proposals_sites']);
					$message[] = 'added demo site to project "'.$sorted['proposals']['proposal_id'].'"';
					$data->changes->sites[0]++;
					$this->log_action('add', 'proposals_sites', 'id', $site->id, 'name', $site->name);
				} else { 
					foreach($sorted['proposals_sites'] as $field => $value) { 
						if($field != 'proposal_id' && $value != $site->{$field}) { 
							$this->log_action('edit', 'proposals_sites', 'id', $site->id, $field, $value);
						}
					}
					$site = $this->set_proposal_site($site->id,$sorted['proposals_sites']);
					$message[] = 'updated demo site.';
					$data->changes->sites[1]++;
				}
			}
			
			$data->report = implode(' / ', $message);
			
			
		} else {
			
			$this->close_transaction();
		}
		
		return $data;
	}
	
	private function getTableFields($table, $fields) { 
		$props = $this->config->item("fields");
		
		$data = array();
		
		foreach($fields as $field) { 
			if($field->table == $table) { 
				$data[] = $field;
			}
		}
	}
	
	public function geocode($key){ 
		$report = (object) array(
			'status' => 0,
			'info' => (object) array( 
				'name' => '',
				'address' => '', 
				'city' => '',
				'country' => '', 
				'gps' => 0,
				'debug' => '',
				'nuts' => ''
			),
			'remaining' => 0
		);

		$this->db->where('(applicant_osm_status=0 OR (applicant_osm_status=2 AND applicant_country_code IN ("9'.implode('","',$this->config->item('nuts_range')).'9")))');
		$this->db->limit(1);
		$query = $this->db->get("applicants");
		$row = $query->row();
		
		if($row) { 
			$report->info->name = $row->applicant_legal_name;
			$report->info->address = str_replace("'", "",$row->applicant_street);
			$report->info->city = utf8_encode(str_replace("'", "",$row->applicant_city));
			$report->info->country = $row->applicant_country_code;
			
			if($row->applicant_osm_status < 2) {
				$response = $this->resolveCoordinates($key, $report->info->address, $report->info->city, $report->info->country);
			} else { 
				$response = (object) array(
					'status' => 2,
					'lat' => $row->applicant_osm_lat,
					'lng' => $row->applicant_osm_lng,
				);
			}
			
			switch($response->status) {
			case -2:
				
			case -1: 
				//OVER API REQUEST QUOTA
				$report->status = -1;
				break;
			case 0: 
				//NO RESULT	
				$fields = array(
					'applicant_osm_status' => 1,
				);
				$this->db->where('applicant_id', $row->applicant_id);
				$this->db->update('applicants', $fields);
				
				break;
			case 1: 
				//RESOLVED
									
				$fields = array(
					'applicant_osm_status' => 2,
					'applicant_osm_lat' => $response->lat,
					'applicant_osm_lng' => $response->lng,
				);
				$this->db->where('applicant_id', $row->applicant_id);
				$this->db->update('applicants', $fields);
				
				break;					
			}
			$report->info->gps = $response->status;
			
			if($response->status > 0) {
				// SKIPPING TO NUTS
				if(in_array($report->info->country, $this->config->item("nuts_range"))) { 
							
					$response = $this->resolveNUTS($response->lat, $response->lng);
					$report->info->debug = $response;
					switch($response->status) { 
					
					case 2:
					case 1:
						//BAD REQUEST
						$fields = array(
							'applicant_osm_status' => 1,
						);
						$this->db->where('applicant_id', $row->applicant_id);
						$this->db->update('applicants', $fields);
						$report->status = 0;
						break;
					
					case 0:
						
						$this->db->where('nuts_code', $response->code);
						$query = $this->db->get('nuts');
						$nuts = $query->row();
						if(!$nuts) { 
							$fields = array(
								'nuts_code' => $response->code,
								'nuts_country' => $response->country,
								'nuts_l1_name' => $response->region,
							);
							
							$this->db->insert('nuts', $fields);
						}
						
						$fields = array(
							'applicant_osm_status' => 3,
							'applicant_nuts_1' => $response->code,
						);
						$this->db->where('applicant_id', $row->applicant_id);
						$this->db->update('applicants', $fields);
						$report->info->nuts = $response->code;
						$report->info->debug = $response->debug;
						$report->status = 1;
					}	
				} else { 
					$report->info->nuts = 'n/a';
					$report->status = 1;
				}
			}
		} else { 
			$report->status = 2;
		}
		
		$this->db->select('count(*) as total');
		$this->db->where('(applicant_osm_status=0 OR (applicant_osm_status=2 AND applicant_country_code IN ("9'.implode('","',$this->config->item('nuts_range')).'9")))');
		$query = $this->db->get("applicants");
		$row = $query->row();
		
		$report->remaining = $row->total;
		//
		return $report;
	}
	
	function resolveCoordinates($key, $street, $city, $country) {
		
		$response = (object) array(
			'lat' => 0,
			'lng' => 0,
			'status' => 0
		);
		
		$params = array(
			'address='.urlencode($street.'+'.$city.'+'.$country),
			'key='.$key
		);	
		
		$target = ($this->config->item('geocode_api_url').'?'.join($params, "&"));
		
		$json = json_decode(@file_get_contents($target));
		
		// Parsing
		if($json) {
			if($json->status != "OK") {
				if($json->status == "OVER_QUERY_LIMIT") { 
					$response->status = -1;
				} 
			}
			else {
				
				$response->lat = $json->results[0]->geometry->location->lat;
				$response->lng = $json->results[0]->geometry->location->lng; 	
				$response->status = 1;
			}
		} else {
			$response->status = 0;
		}
		
		return $response;
	}
	
	function resolveNUTS($lat, $lng) { 
	
		$response = (object) array(
			'code' => '',
			'region' => '',
			'country' => '',
			'status' => 0
		);
		
		$params = array(
			'inSR=4326',
			'outSR=3035',
			'geometries='.$lng.'%2C'.$lat,
			'f=json',
			'transformForward=true',
			'transformation=',
		);	
		
		$target = $this->config->item('nuts_api_gps_to_etrs').'?'.join($params, "&");
		$ETRS89 = json_decode(@file_get_contents($target));
		
		// Parsing
		if($ETRS89) {
			
			$params = array(
				'geometryType=esriGeometryPoint',
				'returnGeometry=false',
				'f=json',
				'tolerance=1',
				'layers=visible',
				'imageDisplay=2144%2C989%2C96',
				'geometry=%7B%22x%22'.$ETRS89->geometries[0]->x.'%2C%22y%22%3A'.$ETRS89->geometries[0]->y.'%7D',
				'mapExtent=4549431.329036186%2C3272097.375226296%2C4552483.2340353085%2C3273678.450283766',
			);	
			
			$target = $this->config->item('nuts_api_etrs_to_nuts').'?'.join($params, "&");
			//print_r($target);
			$json = json_decode(@file_get_contents($target));
			$response->debug = $target;
			$response->json = $json;
			
			if($json && isset($json->results) && count($json->results) > 0) {
				$response->code = $json->results[0]->attributes->NUTS_ID;
				$response->region = $json->results[0]->attributes->NUTS_NAME;
				$response->country = $json->results[0]->attributes->CNTR_CODE;
				//$response->debug = $json;
				$response->status = 0;
			}
			else { 
				$response->status = 1;
			}
			
		} else {
			$response->status = 2;
		}
		
		return $response;
	}
}
