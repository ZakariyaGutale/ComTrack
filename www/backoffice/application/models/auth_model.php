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
/*
	a simple auth model by M. Fallise
*/
class Auth_model extends CI_Model
{
	
	function admin_login($username, $password)
	{
		$this->db->where('user_email',$username);
		$this->db->where('user_password', MD5($password));
		$this->db->where('user_active', 1);
		$this->db->where('user_role', 'host');
		$this->db->limit(1);
		
		$query = $this->db->get('users');
		
		if($query->num_rows() == 1) { 
			
			$user = $query->row();
			$this->init_session($user->user_id, $user->user_name, $user->user_firstname, $user->user_role, $user->user_organisation);
			
			return true;
		} else 
			return false;
	}
	
	function user_login($username, $password)
	{
		$this->db->where('user_email',$username);
		$this->db->where('user_password', MD5($password));
		$this->db->where('user_active', 1);
		$this->db->where('user_activated', 1);
		//$this->db->where('user_role', 'poc');
		$this->db->limit(1);
		
		$query = $this->db->get('users');
		
		if($query->num_rows() == 1) { 
			
			$user = $query->row();
			$this->init_session($user->user_id, $user->user_name, $user->user_firstname, 'poc', $user->user_organisation);
			
			return true;
		} else 
			return false;
	}

	
	function init_session($id, $name, $firstname, $role = 'poc', $organisation = 1) {
		$org_name = $this->get_organisation_name_by_id($organisation);

		$data = array(
         	'id' => $id,
         	'username' => $name,
			'firstname' => $firstname,
			'role' => $role,
			'organisation' => $organisation,
            'orgname' => $org_name
       	);
       	
		$this->session->set_userdata('sessinfo', $data);
	}

	function update_session_data($data){
        $this->session->set_userdata('sessinfo', $data);
    }
	
	function logout()
	{
		$this->session->unset_userdata('sessinfo');
		$this->session->sess_destroy();
	}
	
	function check_email($email) {
		
		$this->db->where('user_email', $email);	
		$this->db->where('user_role', 'poc');
		
		$query = $this->db->get('users');
		
		return $query->num_rows() > 0;
	}
	
	function is_logged($role) { 
		return $this->get_role() == $role;
	}
	/*
	function is_admin() { 
		$session = $this->session->userdata('sessinfo');
		return $session ? $session['role'] == 'host': false;
	}
	*/
	function get_id() { 
		if($this->session->userdata('sessinfo')) { 
			$data = $this->session->userdata('sessinfo');
			return $data['id'];
		}
		return null;
	}
	
	function get_name() { 
		if($this->session->userdata('sessinfo')) { 	
			$data = $this->session->userdata('sessinfo');
			return $data['username'];
		}
		return null;
	}

    function get_complete_name() {
        if($this->session->userdata('sessinfo')) {
            $data = $this->session->userdata('sessinfo');
            return $data['firstname'].' '.$data['username'];
        }
        return null;
    }
		
	function get_role() { 
		$session = $this->session->userdata('sessinfo');
		return $session ? $session['role']: NULL;
	}
	
		
	function get_organisation() { 
		$session = $this->session->userdata('sessinfo');
		return $session ? $session['organisation']: NULL;
	}

    function get_organisation_name() {
        $session = $this->session->userdata('sessinfo');
        return $session ? $session['orgname']: NULL;
    }

	function get_organisation_by_id($org_id){

        $this->db->select('applicant_id, applicant_legal_name');
        $this->db->where('applicant_id', $org_id);
        $query = $this->db->get('applicants');

        return $query->row();

    }

    function get_organisation_name_by_id($org_id){
        $org_name = '';

        $this->db->select('applicant_id, applicant_legal_name');
        $this->db->where('applicant_id', $org_id);
        $query = $this->db->get('applicants');

        if ($query->num_rows() == 1){
            $row = $query->row();
            $org_name = $row->applicant_legal_name;
        }

        return $org_name;
    }

    function get_organisation_list($filters, $is_host = false){
	    $sql = "SELECT applicant_id as id, applicant_legal_name as text FROM applicants";
	    $sql .= " WHERE 1=1";
	    $sql .= " AND (applicant_type != 'SYS' OR applicant_type IS NULL)";

        if (!$is_host){
            $sql .= " AND applicant_is_host = ".$is_host;
        }

        if ($filters['term'] != ''){
            $sql .= " AND applicant_legal_name LIKE '%".$filters['term']."%'";
        }
        
        $sql .= " ORDER BY applicant_legal_name";

        $query = $this->db->query($sql);
        $total = $query->result();

        $sql .= " LIMIT ".$filters['size']." OFFSET ".(($filters['page'] - 1) * $filters['size']);
        $query = $this->db->query($sql);
        $orgs = $query->result();

        $hasMoreResults = true;

        $size = ceil(sizeof($total) / $filters['size']);
        if ( $size == $filters['page'] || $size == 0){
            $hasMoreResults = false;
        }

        return array($orgs, $hasMoreResults);
    }
	

    function get_user_by_email($email){
        $this->db->where('user_email', $email);
        $query = $this->db->get('users');

        return $query->row();
    }

    function get_users() {
		if($this->get_role() == 'host') {
			$this->db->select('user_id, user_name, user_email, user_active, user_admin');
			$query = $this->db->get('users');
			return $query->result();	
		}
	}

/*    function get_users_for_export() {
        if($this->get_role() == 'host') {
            $this->db->select('user_firstname, user_name, user_email');
            $where = "user_status = 'A' AND user_role != 'system' AND user_role != 'host'";
            $this->db->where($where);
            $query = $this->db->get('users');
            return $query->result();
        }
    }*/

	function get_user_count_by_status($status){
        if($this->get_role() == 'host') {


            $this->db->select('user_id');
            $this->db->where('user_status', $status);
            $this->db->from('users');

            return $this->db->count_all_results();
        }
    }

    function get_users_by_status_and_organisation($status, $orgId){
	    $data = array(
	        'user_status' => $status,
            'user_organisation' => $orgId
        );
        $query = $this->db->get_where('users', $data);

        return $query->result();
    }

    function get_user_count_by_organisation($orgId){
        if($this->get_role() == 'host') {


            $this->db->select('user_id');
            $this->db->where('user_organisation', $orgId);
            $this->db->from('users');

            return $this->db->count_all_results();
        }
    }

    function get_users_id_by_organisation($orgId){
        if($this->get_role() == 'host') {

            $this->db->select('user_id');
            $this->db->where('user_organisation', $orgId);
            $query = $this->db->get('users');

            return $query->result();
        }
    }

    function get_users_email_id_by_organisation($orgId){
        if($this->get_role() == 'host') {

            $this->db->select('user_id, user_email');
            $this->db->where('user_organisation', $orgId);
            $query = $this->db->get('users');

            return $query->result();
        }
    }

	function get_complete_users($filters, $includeSelf = false){
        if($this->get_role() == 'host') {
            $sql = 'SELECT * ';
            $sql.= 'FROM users ';
            $sql.= 'WHERE 1=1';
            if ($includeSelf == false){
                $sql.= ' AND user_id != '.$this->get_id();
            }
            //$sql.= " AND user_role != 'system'";

            if ($filters['role'] != ''){
                $sql.= " AND user_role IN ('".implode("','", $filters['role'])."') ";
            } else {
                $sql.= " AND user_role = ''";
            }

            if ($filters['org'] != '' && strtolower($filters['org']) != 'all'){
                $sql.= ' AND user_organisation = '.$filters['org'];
            }

            if ($filters['q'] != ''){
                $explodedFilter = explode(" ", $filters['q']);
                foreach ($explodedFilter as $element){
                    $sql.= " AND (user_name LIKE '%".$element."%' OR user_firstname LIKE '%".$element."%' OR user_email LIKE '%".$element."%') ";
                }
            }

            if($filters['status'] != ''){
                $sql.= " AND user_status IN ('".implode("','", $filters['status'])."') ";
            } else {
                $sql.= " AND user_status = ''";
            }

            $query = $this->db->query($sql);
            $users= $query->result();

            $numRecords = 0;
            if ($filters['start'] >= 0 && $filters['length'] > 0){
                $numRecords = sizeof($users);
                $sql .= ' LIMIT '.$filters['length'].' OFFSET '.$filters['start'];

                $query = $this->db->query($sql);
                $users = $query->result();
            }

            foreach($users as $user) {
                $user->organisation = $this->ooc->get_applicant($user->user_organisation);
            }

            if ($filters['start'] >= 0 && $filters['length'] > 0){
                return array($users, $numRecords);
            } else {
                return $users;
            }
        }
    }

	function get_hosts() { 
		
		$this->db->where('user_role', 'host');
		$this->db->order_by('user_name');
		$query = $this->db->get('users');
		return $query->result();	
	}
	
	
	function get_pocs() { 
		$this->db->where('user_role', 'poc');
		$this->db->where('user_activated', 1);
		$this->db->order_by('user_organisation,user_name');
		$query = $this->db->get('users');
		
		$users = $query->result();
		foreach($users as $user) {
			$user->organisation = $this->ooc->get_applicant($user->user_organisation);
		}
		return $users;	
	}
	
	function get_user($id) { 
		
		$this->db->where('user_id', $id);
		$this->db->limit(1);
		$query = $this->db->get('users');
		
		if($query->num_rows() == 1) {
			$row = $query->row();
			$row->user_prefs = json_decode($row->user_prefs);
			
			return $row;
		} else return null;	
	}

	function get_system_user (){
        $this->db->where('user_role', 'system');
        $this->db->limit(1);
        $query = $this->db->get('users');

        if($query->num_rows() == 1) {
            $row = $query->row();

            return $row;
        } else
            return null;
    }

	function get_poc_by_email($email) { 
		
		$this->db->where('user_email', $email);
		$this->db->where('user_role', 'poc');
		$this->db->limit(1);
		$query = $this->db->get('users');
		
		if($query->num_rows() == 1) {
			$row = $query->row();
			$row->user_prefs = json_decode($row->user_prefs);
			
			return $row;
		} else 
			return null;	
	}
	
	function get_host_by_email($email) { 
		
		$this->db->where('user_email', $email);
		$this->db->where('user_role', 'host');
		$this->db->limit(1);
		$query = $this->db->get('users');
		
		if($query->num_rows() == 1) {
			$row = $query->row();
			$row->user_prefs = json_decode($row->user_prefs);
			
			return $row;
		} else 
			return null;	
	}
	
	function get_user_by_recovery($key) { 
		
		$this->db->where('user_recovery_code', $key);
		$this->db->limit(1);
		$query = $this->db->get('users');
		
		if($query->num_rows() == 1) {
			$row = $query->row();
			$row->user_prefs = json_decode($row->user_prefs);
			
			return $row;
		} else 
			return null;	
	}
	
	function get_organisation_users($id) { 
		$this->db->where('user_organisation', $id);
		$this->db->where('user_active', 1);
		$this->db->where('user_activated', 1);
		$query = $this->db->get('users');
		
		return $query->result();
	}

	function set_user_role($id, $role){
	    $data = array (
	        'user_role' => $role
        );

	    $this->db->where('user_id', $id);
	    $query = $this->db->update('users', $data);

	    return $query;
    }

    function get_all_organisation_users($id) {
        $this->db->where('user_organisation', $id);
        $query = $this->db->get('users');

        return $query->result();
    }

    function move_users_to_organisation($from, $to){
	    $data = array (
	        'user_organisation' => $to
        );

	    $this->db->where('user_organisation', $from);
	    $query = $this->db->update('users', $data);

	    return $query;
    }

    function rollback_user_organisation($userId, $orgId){
	    $data = array (
            'user_organisation' => $orgId
        );

	    $this->db->where('user_id', $userId);
	    $query = $this->db->update('users', $data);

	    return $query;
    }

    function move_commitments_to_organisation($from, $to){
	    $data = array(
	        'fk_applicant_id' => $to
        );

	    $this->db->where('fk_applicant_id', $from);
	    $query = $this->db->update('proposals_applicants', $data);

	    return $query;
    }

	/*
	
	// deprecated. see set_user(...)
	function add_poc($email, $password, $name, $organisation, $function, $phone, $office) { 
		
		$prefs = (object) array(
			'tempOrganisation' => $organisation
		);
		
		$data = array(
			'user_name' => $name,
			'user_role' => 'poc',
			'user_email' => $email,
			'user_phone' => $phone,
			'user_office' => $office,
			'user_function' => $function,
			'user_password' => md5($password),
			'user_active' => 0,
			'user_prefs' => json_encode($prefs),
			'user_activated' => 0,
			'user_created' => date('Y-m-d H:i:s'),
			'user_updated' => date('Y-m-d H:i:s')
		);
		
		$this->db->insert('users', $data);
		
		return $activation;
	}
	*/
	
	function set_user($id, $fields) { 
		
		
		if(array_key_exists('user_password', $fields)) 
			$fields['user_password'] = md5($fields['user_password']);
		
		$fields['user_updated'] = date("Y-m-d H:i:s");
		
		if($id == 0) { 
			$fields['user_created'] = $fields['user_updated'];
			$fields['user_status'] = 'P';
			$query = $this->db->insert('users', $fields);
		} else {
			$this->db->where('user_id', $id);
			$query = $this->db->update('users', $fields);
		}

		return $query;
	}
	
	
	// unused
	/*function delete_user($id) {
		$this->db->where('user_id', $id);
		$query = $this->db->delete('users');
		return $query;
	}*/

	function delete_user($id) {
        $data = array(
            'user_active' => 0,
            'user_status' => 'D',
            'user_updated' => date('Y-m-d H:i:s')
        );

        $this->db->where('user_id', $id);
        $query =  $this->db->update('users', $data);

		return $query;
	}

	function delete_organisation($id){
        $this->db->where('applicant_id', $id);
        $this->db->delete('applicants');
    }
	
	function activate_user($id) {

		$data = array(
			'user_active' => 1,
			'user_activated' => 1,
            'user_status' => 'A'
		);
		
		return $this->set_user($id, $data);
	}

    function deactivate_user($id) {
	    $user = $this->get_user($id);
        $users = $this->get_user_count_by_organisation($user->user_organisation);
	    $commits = $this->get_commits_count_by_org_id($user->user_organisation);

        $data = array(
            'user_activated' => 0,
            'user_status' => 'I'
        );

	    if ($users == 1 && $commits == 0){
	        $data['user_organisation'] = null;
        }

        $result = $this->set_user($id, $data);

        if ($users == 1 && $commits == 0){
            $this->delete_organisation($user->user_organisation);
        }

        return $result;
    }

    function get_commits_count_by_org_id($orgId){
	    $this->db->select('count(pa_id) as count');
	    $this->db->where('fk_applicant_id', $orgId);
	    $this->db->from('proposals_applicants');

	    $query = $this->db->get();
	    $result = $query->row();

	    return $result->count;
    }
	
	function get_pending_registrations() { 
		$this->db->where('user_activated', 0);
		$this->db->where('user_role', 'poc');
		
		$query = $this->db->get('users');
		
		return $query->result();
		
	}
	
	function validate_captcha($response) { 
		$url = $this->config->item('recaptcha_verify_url');
		$key = $this->config->item('recaptcha_secret_key');
		
		$data = array(
			'secret' => $key,
			'response' => $response
		);
		
		$content = http_build_query($data);
		
		$options = array(
			'http' => array (
					'method' => 'POST',
					'content' => $content,
					'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                    			"Content-Length: ".strlen($content)."\r\n".
                    			"User-Agent:MyAgent/1.0\r\n",
			)
		);
		
		$context  = stream_context_create($options);
		
		$verify = file_get_contents($url, false, $context);
		
		$captcha_success=json_decode($verify);
		
		return $captcha_success->success;
	}
}
