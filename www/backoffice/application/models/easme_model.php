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

class Easme_model extends CI_Model {
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		
		date_default_timezone_set($this->config->item('timezone'));

    }

	function lookup($filters) {
		
		$sql = 'SELECT * ';
		$sql.= 'FROM proposals, proposals_applicants, applicants ';
		$sql.= 'WHERE proposal_id=fk_proposal_id AND fk_applicant_id=applicant_id ';
		
		if($this->auth_model->get_role() == 'poc') {
			$sql.= ' AND fk_applicant_id='.$this->auth->get_organisation();
		}
		
		/*if($this->auth_model->get_role() == 'host') {
			$sql.= ' AND (proposal_host is null OR proposal_host='.$this->auth->get_id().') ';
		}*/
		
		if($filters['org'] != '' && strtolower($filters['org']) != 'all')
		    $sql.= " AND applicant_id = ".$filters['org'];
			
		if($filters['q'] != '')
			$sql.= " AND (proposal_title LIKE '%".$filters['q']."%' OR proposal_abstract LIKE '%".$filters['q']."%' OR proposal_impact LIKE '%".$filters['q']."%') ";
		
		if($filters['status'] != '') {
            $sql.= " AND proposal_status IN ('".implode("','", $filters['status'])."') ";
        } else {
            $sql.= " AND proposal_status = ''";
        }

		
		if($filters['completion'] != '' && strtolower($filters['completion']) != 'all')
			$sql.= " AND proposal_completion='".$filters['completion']."' ";
			
		if($filters['area'] != '') {
            $sql .= " AND proposal_theme IN ('" . implode("','", $filters['area']) . "') ";
        } else {
            $sql .= " AND proposal_theme = ''";
        }


		if($filters['year'] != '' && strtolower($filters['year']) != 'all')
		    $sql.= " AND proposal_year = '".$filters['year']."'";
		
		$sql .= ' ORDER BY fk_applicant_id asc, proposal_updated desc';
		$query = $this->db->query($sql);
        $items = $query->result();

        $numRecords = 0;
		if (isset($filters['start']) && isset($filters['length'])){
		    $numRecords = sizeof($items);
		    $sql .= ' LIMIT '.$filters['length'].' OFFSET '.$filters['start'];

            $query = $this->db->query($sql);
            $items = $query->result();
        }


		
		foreach($items as $item) { 
			$item->proposal_theme = $this->get_theme($item->proposal_theme);
			$item->organisation = $this->get_applicant($item->fk_applicant_id);
		}

        if (isset($filters['start']) && isset($filters['length'])){
            return array($items, $numRecords);
        } else {
            return $items;
        }

	}
	
	function organisation_lookup($str) { 

		$this->db->select('applicant_id, applicant_legal_name');
		$this->db->like('applicant_legal_name',$str);
		$query = $this->db->get('applicants');
		
		return $query->result();
	
	}
	
	function get_applicant($id) { 
		
		$this->db->where('applicant_id', $id);
		$query = $this->db->get('applicants');
		
		if($query->num_rows() > 0) { 
			$row = $query->row();
			$row->pocs = $this->auth->get_organisation_users($id);
			$row->type = $this->get_type($row->applicant_type);
			
			return $row;
		}
		
		return NULL;
	}
	
	function get_applicant_by_code($key) { 
		
		$this->db->where('applicant_uid', $key);
		$query = $this->db->get('applicants');
		
		if($query->num_rows() > 0) { 
			$row = $query->row();
			$row->pocs = $this->auth->get_organisation_users($row->applicant_id);
			$row->type = $this->get_type($row->applicant_type);
			
			return $row;
		}
		
		return NULL;
	}

	function get_countries($filters){
	    $results = array();
	    //$sql = 'SELECT country_name AS name, country_alpha_2 AS code FROM countries';
        // $this->db->like('title', 'match', 'after');
        // Produces: WHERE title LIKE 'matc

        $sql = 'SELECT country_alpha_2 as id, country_name as text FROM countries';
        $sql .= " WHERE 1=1";

        if ($filters['term'] != ''){
            $sql .= " AND country_name like '".$filters['term']."%'";
        }

        $sql .= " ORDER BY country_name";

        $query = $this->db->query($sql);
        $total = $query->result();

        $sql .= " LIMIT ".$filters['size']." OFFSET ".(($filters['page'] - 1) * $filters['size']);
        $query = $this->db->query($sql);
        $countries = $query->result();

        $hasMoreResults = true;
        $size = ceil(sizeof($total) / $filters['size']);

        if ( $size == $filters['page'] || $size == 0){
            $hasMoreResults = false;
        }

        return array($countries, $hasMoreResults);
    }

	function get_complete_applicants($filters){
        $results = array();
        if ($filters['type']){
            $sql = 'SELECT * ';
            $sql .= 'FROM applicants ';
            $sql .= 'WHERE applicant_id !='.$this->auth->get_organisation();

            if ($filters['q'] != ''){
                $explodedFilter = explode(" ", $filters['q']);
                foreach ($explodedFilter as $element){
                    $sql.= " AND (applicant_legal_name LIKE '%".$element."%' OR applicant_city LIKE '%".$element."%' OR applicant_country_code LIKE '%".$element."%') ";
                }
            }

            if($filters['type'] != '') {
                $undefinedInArray = in_array('undefined', $filters['type']);

                if (($key = array_search('undefined', $filters['type'])) !== false) {
                    unset($filters['type'][$key]);
                }
                $sql .= " AND (";

                if ($undefinedInArray){
                    $sql .= "applicant_type IS NULL OR";
                }
                $sql.= " applicant_type IN ('".implode("','", $filters['type'])."')) ";

            }


            $query = $this->db->query($sql);
            $results= $query->result();

            $numRecords = 0;
            if ($filters['start'] >= 0 && $filters['length'] > 0){
                $numRecords = sizeof($results);
                $sql .= ' LIMIT '.$filters['length'].' OFFSET '.$filters['start'];

                $query = $this->db->query($sql);
                $results = $query->result();
            }
        }

        foreach ($results as $result){
            $result->user_count = sizeof($this->auth->get_all_organisation_users($result->applicant_id));
            $result->type = $this->get_type($result->applicant_type);
            $result->commit_count = $this->get_commit_count_by_organisation($result->applicant_id);
            $result->complete = true;
            if ($result->applicant_type == '' || $result->applicant_country_code == ''){
                $result->complete = false;
            }
            if (sizeof($result->type) == 0){
                $result->type['name'] = '';
            }
        }

        if ($filters['start'] >= 0 && $filters['length'] > 0 && sizeof($results) > 0){
            return array($results, $numRecords);
        } else {
            return $results;
        }
    }

    function get_commit_count_by_organisation($id){
        $sql = 'SELECT fk_proposal_id ';
        $sql .= 'FROM proposals_applicants ';
        $sql .= 'WHERE fk_applicant_id ='.$id;

        $query = $this->db->query($sql);

        return sizeof($query->result());
    }

    function get_incomplete_applicants_count(){
        $sql = 'SELECT applicant_id  ';
        $sql .= 'FROM applicants ';
        $sql .= 'WHERE applicant_id !='.$this->auth->get_organisation();
        $sql .= ' AND (applicant_country_code = "" OR applicant_type = "")';

        $query = $this->db->query($sql);

        return sizeof($query->result());
    }
	
	function set_applicant($id, $fields) {
		 
		if($id != 0) {
			$this->db->where('applicant_id', $id);
			$this->db->update('applicants', $fields);
		} else {
			$this->db->insert('applicants', $fields);
			$id = $this->db->insert_id();
		}
		
		return $this->get_applicant($id);
	}
	
	
	
	function search_applicant($needle) { 
		
		$sql = "SELECT applicant_id, applicant_legal_name,MATCH(applicant_legal_name) AGAINST ('".$needle."') AS matching FROM applicants WHERE MATCH(applicant_legal_name) AGAINST ('".$needle."') ORDER BY matching";
		
		$query = $this->db->query($sql);
		
		return $query->result();
	}
	/**/
	
	function get_proposal($id) { 
		
		$this->db->select('*');
		$this->db->from('proposals');
		$this->db->join('proposals_applicants', 'proposal_id=fk_proposal_id');
		$this->db->where('proposal_id', $id);
		
		if($this->auth_model->get_role() == 'poc') {
			$this->db->where('fk_applicant_id', $this->auth_model->get_organisation());
		}
		
		$query = $this->db->get();
		
		$row = $query->row();
		
		if($row) { 
			$row->proposal_host = $this->auth_model->get_user($row->proposal_host);
			$row->sites = $this->get_proposal_sites($id);
			$row->proposal_theme = $this->get_theme($row->proposal_theme);
			$row->history = $this->get_proposal_history($id);
			$row->attachments = $this->get_attachments($id);
		}
		
		return $row;
	}

	function get_attachments($id){
	    $this->db->where('proposal_id', $id);
	    $this->db->from('external_links');
	    $query = $this->db->get();
	    return $query->result();
    }

    function get_attachment_ids($id){
	    $this->db->select('link_id');
        $this->db->where('proposal_id', $id);
        $this->db->from('external_links');
        $query = $this->db->get();
        $results = $query->result();

        $data = array();
        foreach ($results as $result){
            array_push($data, $result->link_id);
        }

        return $data;
    }

	function get_proposal_status($id){
	    $this->db->select('proposal_status');
	    $this->db->where('proposal_id', $id);
	    $this->db->from('proposals');

	    $query = $this->db->get();

	    return $query->row(0);
    }
	
	function get_proposals_by_status($status) { 
		
		$this->db->select('proposal_id');
		$this->db->distinct();
		$this->db->where('proposal_status', $status);
		$this->db->from('proposals');
		
		$query = $this->db->get();
		
		return $query->result();
	}
	/*
	// deprecated
	function get_proposal_applicants($id) { 
		
		$this->db->select('pa_id, applicant_id, applicant_legal_name, applicant_role, applicant_grant, applicant_terminated');
		$this->db->from('proposals_applicants');
		$this->db->where('fk_proposal_id',$id);
		$this->db->join('applicants', 'fk_applicant_id=applicant_id');
		$query = $this->db->get();
		
		return $query->result();		
	}
	
	// deprecated
	function get_applicant_proposals($id) { 
		$sql = "SELECT pa_id, proposal_id, proposal_acronym, applicant_role, applicant_grant, applicant_terminated ";
		$sql.= "FROM proposals_applicants, applicants, proposals ";
		$sql.= "WHERE fk_applicant_id=".$id." AND fk_applicant_id=applicant_id AND fk_proposal_id=proposal_id ";
		$query = $this->db->query($sql);
		return $query->result();
	}
	*/
	
	function get_proposal_history($id) { 
		
		$this->db->where('proposal_id', $id);
		$this->db->from('logs');
		$this->db->join('users', 'users.user_id=logs.user_id');
		$this->db->order_by('id desc, date desc');
		$query = $this->db->get();
		
		return $query->result();
	}
	
	function set_proposal_applicant($id, $fields) { 
		
		if($id != 0) {
			$this->db->where('pa_id', $id);
			$this->db->update('proposals_applicants', $fields);
		} else { 
			$this->db->insert('proposals_applicants', $fields);
			$id = $this->db->insert_id();
		}
		
		return $this->get_proposal($id);
	}
	
	function set_proposal($id, $fields) { 
		
		//$fields['proposal_updated'] = date("Y-m-d H:i:s");
		if (!array_key_exists('proposal_deadline', $fields) || $fields['proposal_deadline'] == ''){
            $fields['proposal_deadline'] = NULL;
        }
		
		if($id != 0) {
			$this->db->where('proposal_id', $id);
			$this->db->update('proposals', $fields);
		} else { 
			$fields['proposal_created'] = date("Y-m-d H:i:s");
            $fields['proposal_updated'] = $fields['proposal_created'];
			$fields['proposal_year'] = date("Y");
			$this->db->insert('proposals', $fields);
			$id = $this->db->insert_id();
			
			$fields = array(
				'fk_proposal_id' => $id,
				'fk_applicant_id' => $this->auth->get_organisation(),
			);
			
			$this->set_proposal_applicant(0,$fields);
		}
		 return $this->get_proposal($id);
	}

    function set_commitment_year($id, $year) {
	    $commit = $this->get_proposal($id);

        if($id != 0 && $year != $commit->proposal_year){
            $data = array(
                'proposal_year'=> $year
            );
            $this->db->where('proposal_id', $id);
            $this->db->update('proposals', $data);

            $this->update_history_when_date_changed_by_host($id, $commit->proposal_year, $year);

            return true;
        }

        return false;
    }

    function update_history_when_date_changed_by_host($proposalId, $from, $to){
        $history = $this->get_proposal_last_history_entry($proposalId);

	    $data = array(
            'user_id' => $this->auth->get_id(),
            'date' => date('Y-m-d H:i:s'),
            'proposal_id' => $proposalId,
            'status' => $history->status,
            'comment' => 'Changed commitment year from ' . $from . ' to ' . $to,
            'detail' => 'Updated',
            'icon' => 'fa-bullhorn',
            'class' => 'primary'
        );

        $this->db->insert('logs', $data);
    }

    function get_proposal_last_history_entry($proposal_id){
	    $this->db->where('proposal_id', $proposal_id);
	    $this->db->order_by('date', 'asc');
        $this->db->limit(1);

        $query = $this->db->get('logs');
        return $query->row();
    }
	
	// unused
	function delete_proposal($id) {
		
		$this->db->where('proposal_id', $id);
		$this->db->delete('logs');
		
		$this->db->where('fk_proposal_id', $id);
		$this->db->delete('proposals_applicants');

        $this->db->where('fk_proposal_id', $id);
        $this->db->delete('proposals_sites');
		 
		$this->db->where('proposal_id', $id);
		$query = $this->db->delete('proposals');

		return $query;
	}
	
	function get_proposal_sites($id) { 
		$this->db->where_in('fk_proposal_id', $id);
		$query = $this->db->get('proposals_sites');
		return $query->result();
	}

	/*function get_proposal_years(){
	    $this->db->distinct('proposal_year');
	    $query = $this->db->get('proposals');
        return $query->result();
    }*/
	
	function set_proposal_site($id, $fields) { 
		
		if($id != 0) {
			$this->db->where('id', $id);
            $query = $this->db->update('proposals_sites', $fields);
		} else { 
			$this->db->where('id', $id);
			$query = $this->db->insert('proposals_sites', $fields);
		}

		return $query;
	}

	function set_external_link($fields){
	    if ($fields['link_id'] == 0){
            $query = $this->db->insert('external_links', $fields);
        } else {
            $this->db->where('link_id', $fields['link_id']);
            $query = $this->db->update('external_links', $fields);
        }

	    return $query;
    }

    function delete_external_link($ids){
	    $this->db->where_in('link_id', $ids);
	    $this->db->delete('external_links');
    }


	// unused
	function delete_proposal_site($id) { 
	
		$this->db->where('id', $id);
		$this->db->delete('proposals_sites');
		
		//$this->history_model->log_table_action('proposals_sites', $id, 'delete');
	}
	
	
	function delete_proposal_sites($id, $exclude = array()) { 

		if(count($exclude) > 0)
			$this->db->where_not_in('id', $exclude);
		$this->db->where('fk_proposal_id', $id);
		$this->db->delete('proposals_sites');

	}
	/*
	// unused
	function delete_link($id) { 
	
		$this->db->where('pa_id', $id);
		$this->db->delete('proposals_applicants');
	}
	
	// unused
	function get_nuts($country = '') { 
	
		if($country != '') { 
			$this->db->where('nuts_country', $country);
		}
		
		$query = $this->db->get('nuts');
		
		return $query->result();
	}
	*/
	
	function get_years($applicant_id = 0) { 
		$this->db->select('proposal_year as year');
		$this->db->distinct();
		$this->db->from('proposals');
		$this->db->join('proposals_applicants', 'fk_proposal_id=proposal_id');
		if($applicant_id != 0)
			$this->db->where('fk_applicant_id', $applicant_id);
		$this->db->order_by('proposal_year asc');
		$query = $this->db->get();
		
		return $query->result();
	}
	
	/*
	
	// unused
	function get_topics() { 
		
		$this->db->order_by('topic_short_name, topic_name, topic_code');
		$query = $this->db->get('topics');
		
		$list = $query->result();
		
		foreach($list as $item) { 
			$this->db->where('fk_topic_id',$item->topic_id);
			$this->db->from('proposals');
			$item->projects = $this->db->count_all_results();
		}
		return $list;
	}
	
	function set_topic($id, $fields) { 
	
		$this->db->where('topic_id', $id);
		$this->db->update('topics', $fields);
		
		return $id;
	}
	
	function delete_topics($ids) { 
		
		foreach($ids as $id) { 
			$this->db->where('fk_topic_id',$id);
			$this->db->from('proposals');
			
			if($this->db->count_all_results() == 0) { 
				$this->db->where('topic_id', $id);
				$this->db->delete('topics');
			}
		}
	}
	
	function merge_topics($ids, $dest) { 
		
		foreach($ids as $id) { 
			$data = array(
				'fk_topic_id' => $dest
			);
			$this->db->where('fk_topic_id', $id);
			$this->db->update('proposals', $data);
		}
	}
	*/
	
	function get_theme($id) { 
		$this->db->where('id', $id);
		$query = $this->db->get('proposals_themes');
		
		return $query->row();
	}
	
	function get_themes() { 
		
		$this->db->order_by('name');
		$query = $this->db->get('proposals_themes');
		
		$list = $query->result();
		
		foreach($list as $item) { 
			$this->db->where('proposal_theme',$item->id);
			$this->db->from('proposals');
			$item->count = $this->db->count_all_results();
		}
		
		return $list;
	}
	
	function set_theme($id, $fields) { 
		
		$theme = $this->get_theme($id);
		
		if($theme) {
			$this->db->where('id', $id);
			$this->db->update('proposals_themes', $fields);
		} else { 
			$fields['id'] = strtoupper($id);
			$this->db->insert('proposals_themes', $fields);
		} 
		
		return $this->get_theme($id);
	}
	
	function delete_theme($id) {
        $deleted = false;
		$this->db->where('proposal_theme',$id);
		$this->db->from('proposals');
		
		if($this->db->count_all_results() == 0) { 
			$this->db->where('id', $id);
			$this->db->delete('proposals_themes');
            $deleted = true;
		}

		return $deleted;
	}
	
	function get_type($id) { 
		$this->db->where('id', $id);
		$query = $this->db->get('applicants_types');
		
		return $query->row();
	}
	
	function get_types() { 
		
		$this->db->order_by('grp,name');
		$this->db->where('id !=', 'SYS');
		$query = $this->db->get('applicants_types');
		
		$list = $query->result();
		
		foreach($list as $item) { 
			$this->db->where('applicant_type',$item->id);
			$this->db->from('applicants');
			$item->count = $this->db->count_all_results();
		}
		
		return $list;
	}

	function set_type($id, $fields) {
		
		$type = $this->get_type($id);
		
		if($type) {
			$this->db->where('id', $id);
			$this->db->update('applicants_types', $fields);
		} else {
			$fields['id'] = strtoupper($id);
			$this->db->insert('applicants_types', $fields);
		}
		
		return $this->get_type($id);
	}
	
	function delete_type($id) { 
		$deleted = false;
		$this->db->where('applicant_type',$id);
		$this->db->from('applicants');
		
		if($this->db->count_all_results() == 0) { 
			$this->db->where('id', $id);
			$query = $this->db->delete('applicants_types');
			if ($query){
			    $deleted = true;
            }
		}

		return $deleted;
	}

	public function get_running_campaign(){
	    $this->db->where('camp_is_active', 1);
        $this->db->where('camp_is_finished', 0);
        $query = $this->db->get('campaigns');

        $result = $query->row();

        return $result;
    }

    public function get_last_campaign(){
        $sql = "SELECT * FROM campaigns WHERE ";
        $sql .= "camp_updated = ( ";
        $sql .=  "SELECT MAX(camp_updated) FROM campaigns WHERE ";
        $sql .= "camp_is_active = 0 ";
        $sql .= "AND camp_is_finished = 1 ";
        $sql .= "AND statistics IS NOT NULL";
        $sql .= ");";
        $query = $this->db->query($sql);

        return $query->row();
    }

    public function activate_campaign($campaign){
        $src_format = 'd/m/Y';
        $target_format = 'Y-m-d';
        $camp_start = date_format(DateTime::createFromFormat($src_format, $campaign->camp_start), $target_format);
        $commitments = $this->get_existing_commitments($camp_start);

        $fields = array (
            'camp_is_active' => 1,
            'existing_commitments' => $commitments
        );

        $this->db->where('camp_id', $campaign->camp_id);
        $this->db->update('campaigns', $fields);

    }

    public function close_campaign($campaign){

	    $overview = $this->get_overview_figures();
	    $stats = array(
	        "stats" =>  $overview->new_stats,
            "budget" => $overview->budget_stats
        );

	    $data = array(
            'updated_commitments' => $overview->updated,
            'closed_commitments' => $overview->closed,
            'new_commitments' => $overview->new,
            'draft_commitments' => $overview->draft,
            'approved_commitments' => $overview->approved,
            'statistics' => json_encode($stats),
            'camp_is_active' => 0,
            'camp_is_finished' => 1,
            'camp_updated' => date("Y-m-d H:i:s")
        );

        $this->db->where('camp_id', $campaign->camp_id);
        $this->db->update('campaigns', $data);

        $this->db->where('config_key', 'host_camp_active');
        $this->db->update('configs', array( 'config_value' => 0 ));
    }

    function deactivate_campaigns(){
        $data = array(
            'camp_is_active' => 0
        );
	    $this->db->where('camp_is_active', 1);
	    $this->db->update('campaigns', $data);
    }

    public function set_campaign($fields, $action){
	    $current_date = date("Y-m-d H:i:s");
        $fields['camp_updated'] = $current_date;
        if ($action == 'update'){
            unset($fields['camp_start']);
            $this->db->where('camp_is_active', 1);
            $this->db->where('camp_is_finished', 0);
            $this->db->update('campaigns', $fields);
        } else {
            $this->deactivate_campaigns();
            $fields['camp_is_active'] = 1;
            $fields['camp_created'] = $current_date;
            $this->db->insert('campaigns', $fields);
        }

        return $this->get_running_campaign();
    }

    public function get_overview_configs(){
        $date_keys = array("host_conf_start", "host_conf_end");
        $query = $this->db->get('configs');

        $config = (object) array();

        foreach($query->result() as $row) {
            if ($row->config_key == "host_camp_active"){
                $config->active = boolval($row->config_value);
            }
            if (in_array($row->config_key, $date_keys)){
                $key = $row->config_key;
                $config->$key = $row->config_value;
            }
        }

        $campaign_keys = array('camp_start', 'camp_end', 'camp_new_target', 'camp_update_target');
        $campaign = $this->get_running_campaign();

        if (sizeof($campaign) == 0){
            $campaign = $this->get_last_campaign();
        }

        foreach ($campaign as $key => $value){
            if (in_array($key, $campaign_keys)){
                $name = 'host_'.$key;
                $config->$name = $value;
            }

        }

        return $config;
    }

    private function get_existing_commitments($start_date){
        $sql = "SELECT COUNT(proposal_id) AS total FROM proposals WHERE ";
        $sql .= "proposal_status IN ('published', 'progress_update', 'progress_changes') ";
        $sql .= "AND date(proposal_created) < '".$start_date."' ";
        $sql .= "AND date(proposal_updated) < '".$start_date."' ";
        $query = $this->db->query($sql);
        return $query->row()->total;
    }

    private function get_updated_commitments($start_date, $end_date){
	    $sql = "SELECT COUNT(proposal_id) AS total FROM proposals WHERE ";
	    $sql .= "proposal_status IN ('published', 'progress_update', 'progress_changes') ";
	    $sql .= "AND date(proposal_created) < '".$start_date."' ";
        $sql .= "AND date(proposal_updated) >= '".$start_date."' ";
        $sql .= "AND date(proposal_updated) <= '".$end_date."' ";
        $query = $this->db->query($sql);
        return $query->row()->total;
    }

    private function get_closed_commitments($start_date, $end_date){
        $sql = "SELECT COUNT(proposal_id) AS total FROM proposals WHERE ";
        $sql .= "proposal_status = 'closed' ";
        $sql .= "AND date(proposal_created) < '".$start_date."' ";
        $sql .= "AND date(proposal_updated) >= '".$start_date."' ";
        $sql .= "AND date(proposal_updated) <= '".$end_date."' ";
        $query = $this->db->query($sql);
        return $query->row()->total;
    }

    private function get_new_commitments($start_date, $end_date){
        $sql = "SELECT COUNT(proposal_id) AS total FROM proposals WHERE ";
        $sql .= "proposal_status != 'rejected' ";
        $sql .= "AND date(proposal_created) >= '".$start_date."' ";
        $sql .= "AND date(proposal_created) <= '".$end_date."' ";
        $query = $this->db->query($sql);
        return $query->row()->total;
    }

    private function get_new_commitments_by_status ($start_date, $end_date, $is_in, $status){
        $sql = "SELECT COUNT(proposal_id) AS total FROM proposals WHERE ";
        $in_clause = 'in';
        if (!$is_in){
            $in_clause = 'not in';
        }
        if (is_array($status)){
            $sql .= "proposal_status ".$in_clause." ('".implode("','",$status)."') ";
        } else {
            $sql .= "proposal_status = '".$status."' ";
        }
        $sql .= "AND date(proposal_created) >= '".$start_date."' ";
        $sql .= "AND date(proposal_created) <= '".$end_date."' ";
        $sql .= "AND date(proposal_updated) <= '".$end_date."' ";
        $query = $this->db->query($sql);
        return $query->row()->total;
    }

    private function get_new_commitments_per_theme($start_date, $end_date){
        $sql = "SELECT proposal_theme as theme, proposal_status as status, count(proposal_id) as total FROM proposals WHERE ";
        $sql .= "proposal_status != 'rejected' ";
        $sql .= "AND date(proposal_created) >= '".$start_date."' ";
        $sql .= "AND date(proposal_created) <= '".$end_date."' ";
        $sql .= "AND date(proposal_updated) <= '".$end_date."' ";
        $sql .= "GROUP BY proposal_theme, proposal_status";
        $query = $this->db->query($sql);
        return $query->result();
    }

    private function get_new_commitments_budget($start_date, $end_date){
	    $sql = "SELECT a.applicant_type as type, b.theme as theme, SUM(b.budget) as budget, COUNT(b.pid) as commits ";
	    $sql .= "FROM applicants as a INNER JOIN (";
	    $sql .= "SELECT p.proposal_id as pid, pa.fk_applicant_id as paid, p.proposal_theme as theme, p.proposal_budget as budget ";
	    $sql .= "FROM proposals p INNER JOIN proposals_applicants pa on p.proposal_id = pa.fk_proposal_id ";
	    $sql .= "WHERE p.proposal_status != 'rejected' ";
	    $sql .= "AND p.proposal_created  >= '".$start_date."' ";
	    $sql .= "AND p.proposal_created <= '".$end_date."' ";
	    $sql .=") as b ON a.applicant_id = b.paid ";
	    $sql .= "GROUP BY a.applicant_type, b.theme";
        $query = $this->db->query($sql);
        return $query->result();
    }

    private function get_prepared_new_commitments_per_theme($start_date, $end_date){
        $new_commitments = $this->get_new_commitments_per_theme($start_date, $end_date);
        $themes = $this->get_themes();

        $data = array();
        $labels = array();

        foreach($themes as $theme) {
            $data[$theme->id] = array(
                "draft" => 0,
                "approved" => 0,
                "total" => 0
            );
            $labels[$theme->id] = $theme->name;
        }

        foreach ($new_commitments as $commit){
            if ($commit->status == 'draft' || $commit->status == 'submitted'){
                $data[$commit->theme]['draft'] += $commit->total;
            } else {
                $data[$commit->theme]['approved'] += $commit->total;
            }
            $data[$commit->theme]['total'] += $commit->total;
        }

        $final = array(
            "data" => $data,
            "labels" => $labels
        );

        return $final;
    }

    private function get_budget_per_type_and_theme($start_date, $end_date){
	    $types = $this->get_types();
	    $themes = $this->get_themes();
        $src_data = $this->get_new_commitments_budget($start_date, $end_date);

	    $theme_data = array();
	    foreach ($themes as $theme){
	        $theme_data[$theme->id] = $theme->name;
        }

	    $type_data = array();
        foreach ($types as $type){
            $type_data[$type->id] = $type->name;
        }

        $budget_type_data = array();
        $budget_theme_data =array();
        $total_budget = 0;
        foreach ($src_data as $item){
            if (array_key_exists($item->theme, $budget_theme_data)){
                $budget_theme_data[$item->theme]["budget"] += floatval($item->budget);
                $budget_theme_data[$item->theme]["commits"] += floatval($item->commits);
            } else {
                $budget_theme_data[$item->theme] = array(
                    "budget" => floatval($item->budget),
                    "commits" => intval($item->commits)
                );
            }

            if (array_key_exists($item->type, $budget_type_data)){
                $budget_type_data[$item->type]["budget"] += floatval($item->budget);
                $budget_type_data[$item->type]["commits"] += floatval($item->commits);
            } else {
                $budget_type_data[$item->type] = array(
                    "budget" => floatval($item->budget),
                    "commits" => intval($item->commits)
                );
            }

            $total_budget += floatval($item->budget);
        }

        $final = (object) array(
            "types" => $type_data,
            "themes" => $theme_data,
            "total" => $total_budget,
            "budget_type" => $budget_type_data,
            "budget_theme" => $budget_theme_data
        );


	    return $final;

    }

    public function get_overview_figures(){
	    $is_from_table = false;
	    $src_format = 'd/m/Y';
        $target_format = 'Y-m-d';
	    $campaign = $this->get_running_campaign();
	    if (sizeof($campaign) == 0){
            $is_from_table = true;
	        $campaign = $this->get_last_campaign();
        }

	    $camp_start = date_format(DateTime::createFromFormat($src_format, $campaign->camp_start), $target_format);
        $camp_end = date_format(DateTime::createFromFormat($src_format, $campaign->camp_end), $target_format);

        if ($is_from_table){
            $stats = json_decode($campaign->statistics);
            $updated_commitments = $campaign->updated_commitments;
            $closed_commitments = $campaign->closed_commitments;
            $new_commitments = $campaign->new_commitments;
            $draft_commitments = $campaign->draft_commitments;
            $approved_commitments = $campaign->approved_commitments;
            $new_commitments_stats = $stats->stats;
            $budget_stats = $stats->budget;
        } else {
            $updated_commitments = $this->get_updated_commitments($camp_start, $camp_end);
            $closed_commitments = $this->get_closed_commitments($camp_start, $camp_end);
            $new_commitments = $this->get_new_commitments($camp_start, $camp_end);
            $draft_commitments = $this->get_new_commitments_by_status($camp_start, $camp_end, true, ['draft', 'submitted']);
            $approved_commitments = $this->get_new_commitments_by_status($camp_start, $camp_end, false, array('draft', 'submitted', 'rejected'));
            $new_commitments_stats = $this->get_prepared_new_commitments_per_theme($camp_start, $camp_end);
            $budget_stats = $this->get_budget_per_type_and_theme($camp_start, $camp_end);
        }

	    $data = (object) array(
	        'update_target' => $campaign->camp_update_target,
	        'new_target' => $campaign->camp_new_target,
	        'existing' => $campaign->existing_commitments,
            'updated' => $updated_commitments,
            'updated_p' => $campaign->existing_commitments > 0 ? round($updated_commitments * 100 / $campaign->existing_commitments, 1) : 0,
            'closed' => $closed_commitments,
            'closed_p' => $campaign->existing_commitments > 0 ? round($closed_commitments * 100 / $campaign->existing_commitments, 1) : 0,
            'update_total_p' => round(($closed_commitments + $updated_commitments) * 100 / $campaign->camp_update_target, 1),
            'new' => $new_commitments,
            'new_p' => round($new_commitments * 100 / $campaign->camp_new_target, 1),
            'draft' =>$draft_commitments,
            'draft_p' => $new_commitments > 0 ? round($draft_commitments * 100 / $new_commitments, 1) : 0,
            'approved' => $approved_commitments,
            'approved_p' => $new_commitments > 0 ? round($approved_commitments * 100 / $new_commitments, 1): 0,
            'new_stats' => $new_commitments_stats,
            'budget_stats' => $budget_stats
        );

	    return $data;
    }

//	public function get_host_configs(){
//	    $date_keys = array("host_conf_start", "host_conf_end");
//        $query = $this->db->get('configs');
//
//        $config = (object) array();
//
//        foreach($query->result() as $row) {
//            if ($row->config_key == "host_camp_active"){
//                $config->active = boolval($row->config_value);
//            }
//            if (in_array($row->config_key, $date_keys)){
//                $key = $row->config_key;
//                $config->$key = $row->config_value;
//            }
//        }
//
//        return $config;
//    }

    public function set_host_config($fields){
        foreach($fields as $key => $value) {

            $this->db->where('config_key', $key);
            $query = $this->db->get('configs');

            $row = $query->row();

            if($row) {
                $this->db->where('config_key', $key);
                $query = $this->db->update('configs', array( 'config_value' => $value ));
                if (!$query){
                    break;
                }

            } else {
                $query = $this->db->insert('configs', array( 'config_key' => $key, 'config_value' => $value ));
                if (!$query){
                    break;
                }
            }
        }

        return $query;
    }

	
	function historize($proposal_id, $user_id, $status, $comment, $detail, $flow) {
		
		$fields = array(
			'user_id' => $user_id,
			'date' => date('Y-m-d H:i:s'),
			'proposal_id' => $proposal_id,
			'status' => $status,
			'comment' => $comment,
			'detail' => $detail,
            'icon' => $flow->icon,
            'class' => $flow->class,
            'is_public' => $flow->is_public
		);
		
		$query = $this->db->insert('logs', $fields);

		return $query;
	}

	function get_available_status(){
        $workflow = $this->config->item('workflow');
        return array_keys($workflow);
    }

    function get_available_completions(){
	    return $this->config->item('completions');
    }

    function get_available_navigation_items(){
        return $this->config->item('navigation');
    }
}
