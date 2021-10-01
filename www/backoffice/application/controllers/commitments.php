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

class Commitments extends CI_Controller {
    const HTTP_OK = "200 OK";
    const HTTP_FORBIDDEN = "403 Forbidden";

	function __construct()
    {
        parent::__construct();
		
		$this->load->model('auth_model', 'auth');
		$this->load->model('easme_model', 'ooc');
        $this->load->model('email_model', 'email');
        $this->load->model('publish_model', 'publish');
    }

    public function login() {

        $target = $this->input->get('target');

        $data = array(
            'content' => 'poc/login',
            'js' => array(),
            'css' => array(),
            'target' => $target == '' ? site_url('commitments'): $target
        );

        $this->load->view('page', $data);
    }

    public function do_login() {

        $email = strtolower($this->input->post('email'));
        $password = $this->input->post('password');
        $logged = $this->auth->user_login($email,$password);

        echo json_encode((object) array('success' => $logged));
    }

    public function logout() {

        $this->auth->logout();

        redirect('commitments', 'refresh');
    }

    public function reset_password($key = '') {

        if($this->auth->is_logged('poc')) redirect('/commitments/edit_profile', 'refresh');

        $data = array(
            'content' => 'poc/recovery',
            'js' => array(),
            'css' => array()
        );

        $this->load->view('page', $data);
    }

    public function do_recovery() {

        $email = strtolower($this->input->post('email'));
        $captcha_response = $this->input->post('g-recaptcha-response');

        if($this->auth->validate_captcha($captcha_response)) {

            $user = $this->auth->get_user_by_email($email);

            $response = array();
            if ($user){
                $key = MD5(uniqid());

                $fields = array(
                    'user_recovery_code' => $key,
                    'user_recovery_date' => date("Y-m-d H:i:s")
                );

                $this->auth->set_user($user->user_id, $fields);

                $this->email->sendEmail($user->user_email, 'Reset your password', $this->load->view('poc/email/reset',array('key' => $key),true));

                $response['success'] = true;
                $data['status_code'] = self::HTTP_OK;
            } else {
                $response['success'] = false;
                $response['message'] = 'The email is not registered in the system!';
                $data['status_code'] = self::HTTP_OK;
            }

            $data['json'] = $response;
            $this->load->view('rest/json_view', $data);

        } else {
            redirect('commitments/reset_password', 'refresh');
        }

    }

    public function change_password($key='') {

        $user = $this->auth->get_user_by_recovery($key);

        if($user) {
            $data = array(
                'content' => 'poc/change_password',
                'js' => array(),
                'css' => array(),
                'key' => $key,
            );

            $this->load->view('page', $data);
        } else {
            show_404();
        }


    }

    public function do_change_password($key='') {

        $user = $this->auth->get_user_by_recovery($key);

        $response = array();
        if($user) {

            $password = $this->input->post('password');

            if($password) {

                $fields = array(
                    'user_password' => $password,
                    'user_recovery_code' => '',
                );

                $this->auth->set_user($user->user_id, $fields);

                $response['success'] = true;
                $response['url'] = site_url('commitments');
                $data['status_code'] = self::HTTP_OK;

            } else {
                $response['success'] = false;
                $response['message'] = 'It was not possible to reset your password!';
                $data['status_code'] = self::HTTP_OK;

            }

        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to find your request for resetting the password! Please restart the reset process!';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }
	
	public function register($key = '') { 
		
		$data = array(
			'content' => 'poc/register',
			'js' => array(),
            'css' => array(),
			'organisation' => $key != '' ? $this->ooc->get_applicant_by_code($key): null,
		);
		
		$this->load->view('page', $data);
	}
	
	public function do_register() { 
		
		$registered = false;
		
		$email = strtolower($this->input->post('email'));
		$orgId = $this->input->post('organisation');
		$captcha_response = $this->input->post('g-recaptcha-response');

		$error = 'We could not submit your registration! ';
		if($this->auth->validate_captcha($captcha_response)) {
            if(!$this->auth->check_email($email)) {
                $fields = array(
                    'user_role' => 'poc',
                    'user_email' => $email,
                    'user_password' => $this->input->post('password'),
                    'user_office' => $this->input->post('office'),
                    'user_function' => $this->input->post('function'),
                    'user_name' => $this->input->post('name'),
                    'user_firstname' => $this->input->post('firstname'),
                    'user_activated' => 0,
                    'user_active' => 0,
                    'user_phone' => $this->input->post('phone'),
                    'user_organisation' => $orgId,
                );

                if ($this->auth_model->get_organisation_by_id($orgId) == null){
                    $fields['user_organisation'] = $this->set_organisation_from_register($orgId);
                }

                $this->auth->set_user(0, $fields);
                $registered = true;
            } else {
                $user = $this->auth->get_user_by_email($email);
                if ($user->user_status == 'I') {
                    $fields = array(
                        'user_role' => 'poc',
                        'user_email' => $email,
                        'user_password' => $this->input->post('password'),
                        'user_office' => $this->input->post('office'),
                        'user_function' => $this->input->post('function'),
                        'user_name' => $this->input->post('name'),
                        'user_firstname' => $this->input->post('firstname'),
                        'user_activated' => 0,
                        'user_active' => 0,
                        'user_status' => 'P',
                        'user_phone' => $this->input->post('phone'),
                        'user_organisation' => $orgId,
                    );

                    if ($this->auth_model->get_organisation_by_id($orgId) == null){
                        $fields['user_organisation'] = $this->set_organisation_from_register($orgId);
                    }

                    $this->auth->set_user($user->user_id, $fields);
                    $registered = true;
                } else if ($user->user_status == 'P'){
                    $error = "This email is already registered and pending approval!";
                } else {
                    $error .= 'Email is already registered!';
                }
            }
		} else {
			$error .= 'Captcha error!';
		}
		
		echo json_encode((object) array('registered' => $registered, 'message' => $error));
	}

    private function set_organisation_from_register($name){
        $fields = array(
            //'applicant_type' => '',
            'applicant_legal_name' => $name,
            'applicant_created' => date("Y-m-d H:i:s"),
            'applicant_updated' => date("Y-m-d H:i:s")
        );

        $org = $this->ooc->set_applicant(0, $fields);
        return $org->applicant_id;
    }

    public function policy(){
	    $data = array (
	        'isHost' => false
        );
        $this->load->view('policies', $data);
    }

    public function user_profile() {
        if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');

        $user = $this->auth->get_user($this->auth->get_id());

        $data = array(
            'content' => 'profile/user_profile',
            'navigation' => $this->build_navigation_items(array('dashboard','my_profile')),
            'js' => array('js/libphonenumber.js'),
            'css' => array(),
            'user' => $user,
            'edit_profile' => site_url('commitments/edit_profile')
        );

        $this->load->view('page', $data);

    }
	
	public function edit_profile() {
        if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');
		
		$user = $this->auth->get_user($this->auth->get_id());
		
		$data = array(
			'content' => 'profile/edit_user_profile',
            'navigation' => $this->build_navigation_items(array('dashboard','my_profile','edit_profile')),
            'title' => 'Edit My Profile',
			'js' => array('js/libphonenumber.js'),
			'css' => array(),
			'user' => $user
		);
		
		$this->load->view('page', $data);
	}
	
	public function set_profile($id) {
		
		if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');
		
		if($id == $this->auth->get_id()) { 
		
			$fields = array(
				'user_name' => $this->input->post('name'),
				'user_firstname' => $this->input->post('firstname'),
				'user_office' => $this->input->post('office'),
				'user_function' => $this->input->post('function'),
				'user_phone' => $this->input->post('phone'),
                'user_accept_pub' => $this->input->post('user_accept_pub'),
			);

			if($this->input->post('email') && $this->input->post('email') != ''){
			    $fields['user_email'] = strtolower($this->input->post('email'));
            }
			
			if($this->input->post('password') != '' && $this->input->post('password') == $this->input->post('confirm')) { 
				$fields['user_password'] = $this->input->post('password');
			}
			
			$query = $this->auth->set_user($id, $fields);

			$response = array();
			if ($query){
                $data = $this->session->userdata('sessinfo');
                $data['username'] = $fields['user_name'];
                $data['firstname'] = $fields['user_firstname'];
                $this->auth->update_session_data($data);

                $response['success'] = true;
                $response['url'] = site_url('commitments/user_profile');
                $data['status_code'] = self::HTTP_OK;
            } else {
                $response['success'] = false;
                $response['message'] = 'It was not possible to update your profile.';
                $data['status_code'] = self::HTTP_FORBIDDEN;
            }

            $data['json'] = $response;
            $this->load->view('rest/json_view', $data);
		} else {
			$this->login();
		}
	}

    public function organisation_profile() {
        if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');

        $organisation = $this->ooc->get_applicant($this->auth->get_organisation());
        $deleted_users = $this->auth->get_users_by_status_and_organisation('D', $organisation->applicant_id);
        $rejected_users = $this->auth->get_users_by_status_and_organisation('I', $organisation->applicant_id);

        $data = array(
            'content' => 'profile/organisation_profile',
            'navigation' => $this->build_navigation_items(array('dashboard','my_organisation')),
            'title' => 'My Organisation Profile',
            'invite' => true,
            'js' => array('js/libphonenumber.js'),
            'css' => array(),
            'types' => $this->ooc->get_types(),
            'organisation' => $organisation,
            'deleted_users' => $deleted_users,
            'rejected_users' => $rejected_users,
            'edit_organisation' => site_url('commitments/edit_organisation')
        );

        $this->load->view('page', $data);
    }
	
	public function edit_organisation() { 
		
		if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');
		
		$organisation = $this->ooc->get_applicant($this->auth->get_organisation());
		
		$data = array(
			'content' => 'profile/edit_organisation_profile',
            'navigation' => $this->build_navigation_items(array('dashboard','my_organisation', 'edit_organisation')),
            'js' => array('js/leaflet.js'),
            'css' => array('css/leaflet.css'),
			'organisation' => $organisation,
			'types' => $this->ooc->get_types() 
		);
		
		$this->load->view('page', $data);
	}
	
	public function set_organisation($id) {
		
		if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');
		
		if($id == $this->auth->get_organisation()) { 
		
			$fields = array(
				'applicant_type' => $this->input->post('applicant_type'),
				'applicant_street' => $this->input->post('applicant_street'),
				'applicant_city' => $this->input->post('applicant_city'),
				'applicant_post_code' => $this->input->post('applicant_post_code'),
				'applicant_country_code' => $this->input->post('applicant_country_code'),
				'applicant_legal_name' => $this->input->post('applicant_legal_name'),
				'applicant_web_page' => $this->input->post('applicant_web_page'),
				'applicant_osm_lat' => 0 + $this->input->post('applicant_osm_lat'),
				'applicant_osm_lng' => 0 + $this->input->post('applicant_osm_lng'),
				'applicant_updated' => date("Y-m-d H:i:s")
			);
			
			$query = $this->ooc->set_applicant($id, $fields);

            $response = array();
            if (isset($query)){
                $data = $this->session->userdata('sessinfo');
                $data['orgname'] = $fields['applicant_legal_name'];
                $this->auth->update_session_data($data);

                $response['success'] = true;
                $response['url'] = site_url('commitments/organisation_profile');
                $data['status_code'] = self::HTTP_OK;
            } else {
                $response['success'] = false;
                $response['message'] = 'It was not possible to update your organisation\'s profile.';
                $data['status_code'] = self::HTTP_FORBIDDEN;
            }

            $data['json'] = $response;
            $this->load->view('rest/json_view', $data);
			
		} else { 
			$this->login();
		}
	}

    public function delete_user(){
        if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');

        $userId = $this->input->post('userId');
        $orgId = $this->input->post('orgId');

        if ($this->auth->get_organisation() == $orgId){
            $query = $this->auth->delete_user($userId);
        }


        $response = array();
        if ($query){
            $response['success'] = true;
            $response['url'] = site_url('commitments/organisation_profile');
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to delete the user.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    public function re_approve_user(){
        if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');

        $userId = $this->input->post('userId');
        $orgId = $this->input->post('orgId');

        if ($this->auth->get_organisation() == $orgId){
            $query = $this->auth->activate_user($userId);
        }

        $response = array();
        if ($query){
            $response['success'] = true;
            $response['url'] = site_url('commitments/organisation_profile');
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to approve the user.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);

    }

    function do_invite() {

        if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');

        $organisation = $this->ooc->get_applicant($this->auth->get_organisation());

        if($organisation->applicant_uid == '') {
            $code = md5(uniqid());

            $fields = array(
                'applicant_uid' => $code
            );

            $applicant = $this->ooc->set_applicant($organisation->applicant_id, $fields);
            $organisation->applicant_uid = $applicant->applicant_uid;
        }

        $user = $this->auth->get_user($this->auth->get_id());

        $email = $this->input->post('email');

        $this->email->sendEmail($email, $user->user_firstname.' '.$user->user_name.' invites you to join the OOC platform', $this->load->view('poc/email/invite',array('key' => $organisation->applicant_uid),true));

        $response = array();
        $response['success'] = true;
        $response['url'] = site_url('commitments/user_profile');

        $data['status_code'] = self::HTTP_OK;
        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    private function checkCompletenessOfProfile(){
	    $completeness = array(
	        'isComplete' => true,
            'personal' => false,
            'organisation' => false
        );
        $user = $this->auth->get_user($this->auth->get_id());


        if ($user->user_name == '' || $user->user_firstname == '' || $user->user_phone == '' || $user->user_phone == '0'){
            $completeness['isComplete'] = false;
            $completeness['personal'] = true;
        }

        $organisation = $this->ooc->get_applicant($this->auth->get_organisation());
        if ($organisation->applicant_legal_name == '' || $organisation->applicant_street == '' || $organisation->applicant_city == ''
            || $organisation->applicant_country_code = '' || $organisation->applicant_post_code == '' || sizeof($organisation->type) == 0){
            $completeness['isComplete'] = false;
            $completeness['organisation'] = true;
        }


        return $completeness;
    }
	
	public function index()
	{
		if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');

		$countries = array_keys($this->config->item('countries'));
		$configs = $this->publish->get_configs();

		$viewer = array();
		foreach ($configs as $item){
		    $keys = array('app_host', 'app_mode');
		    if (in_array($item->config_key, $keys)){
                $viewer[$item->config_key] = $item->config_value;
            }
        }

		if ($viewer['app_host'] != ''){
            $years = $this->ooc->get_years();
            $yString = '';
            for ($x = 0; $x < sizeof($years); $x++) {
                $base = '';
                if ($x != 0){
                    $base = ',';
                }
                $yString .= $base.$years[$x]->year;
            }
            $viewer['years'] = $yString;
        }
		
		$data = array(
			'content' => 'poc/dashboard',
            'navigation' => $this->build_navigation_items(array('dashboard')),
			'js' => array('datatables/datatables.min.js'),
            'css' => array('datatables/datatables.min.css'),
			'themes' => $this->ooc->get_themes(),
			'years' => $this->ooc->get_years($this->auth->get_organisation()),
            'status' => $this->ooc->get_available_status(),
            'completions' => $this->ooc->get_available_completions(),
            'completed' => $this->checkCompletenessOfProfile(),
            'new_commitment' => site_url('commitments/create_commitment'),
            'countries' => implode(',', $countries),
            'viewer' => $viewer
		);
		
		$this->load->view('page', $data);
	}
	
	public function create_commitment() {
		if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');
		
		$data = array(
			'content' => 'commit/edit',
            'navigation' => $this->build_navigation_items(array('dashboard','create_commitment')),
            'title' => 'Create Commitment',
            'js' => array('js/leaflet.js', 'moment/min/moment.min.js', 'datetimepicker/build/js/bootstrap-datetimepicker.min.js'),
            'css' => array('css/leaflet.css', 'datetimepicker/build/css/bootstrap-datetimepicker.min.css', 'css/switch.css'),
			'themes' => $this->ooc->get_themes(),
            'completions' => $this->ooc->get_available_completions(),
		);
		
		$this->load->view('page', $data);
	}
	
	public function edit_commitment($id) {
		if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');
		
		$commitment = $this->ooc->get_proposal($id);
			
		if($commitment->fk_applicant_id != $this->auth->get_organisation()) { 
			return;
		}
					
		$data = array(
			'content' => $commitment->proposal_ontrack ? 'commit/edit':'commit/edit',
            'navigation' => $this->build_navigation_items(array('dashboard','my_commitment', 'edit_commitment'), $id),
            'title' => 'Edit Commitment',
            'js' => array('js/leaflet.js', 'moment/min/moment.min.js', 'datetimepicker/build/js/bootstrap-datetimepicker.min.js'),
            'css' => array('css/leaflet.css', 'datetimepicker/build/css/bootstrap-datetimepicker.min.css', 'css/switch.css'),
			'controller' => 'commitments',
			'commitment' => $commitment,
			'themes' => $this->ooc->get_themes(),
            'completions' => $this->ooc->get_available_completions()
		);
		
		$this->load->view('page', $data);
	}
	
	public function set_commitment($id) {
		
		if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');
		
		if($id != 0) {
			$commitment = $this->ooc->get_proposal($id);
            $oldCompletion = $commitment->proposal_completion;
			
			if($commitment->fk_applicant_id != $this->auth->get_organisation()) { 
				return;
			}
		}

		$status = $this->input->post('proposal_status');
		$completion = $this->input->post('proposal_completion');

        if ($id != 0 && (($status === 'published' && $commitment->proposal_completion != $completion) || $status === 'progress_changes' )){
            $status = 'progress_update';
        }
				
		$fields = array(
			'proposal_title' => $this->input->post('proposal_title'),
			'proposal_abstract' => $this->input->post('proposal_abstract'),
			'proposal_theme' => $this->input->post('proposal_theme'),
			'proposal_budget' => $this->input->post('proposal_budget') == '' ? 0 : $this->input->post('proposal_budget'),
			'proposal_announcer' => $this->input->post('proposal_announcer') == '' ? 'Unknown' : $this->input->post('proposal_announcer'),
			'proposal_language' => $this->input->post('proposal_language') == '' ? 'English' : $this->input->post('proposal_language'),
			'proposal_deadline' => $this->input->post('proposal_deadline'),
            'proposal_website' => $this->input->post('proposal_website'),
			'proposal_area_active' => $this->input->post('proposal_area_active'),
			'proposal_area' => $this->input->post('proposal_area') == ''  ? 0 : $this->input->post('proposal_area'),
			'proposal_completion' => $completion,
			'proposal_impact' => $this->input->post('proposal_impact'),
			'proposal_status' => $status,
            'proposal_updated' => date("Y-m-d H:i:s")
		);

		$commitment = $this->ooc->set_proposal($id, $fields);

		$fields = array (
		    'fk_proposal_id' => $commitment->proposal_id,
            'lat' => $this->input->post('proposal_lat'),
            'lng' => $this->input->post('proposal_lon')
        );

        if ($this->input->post('proposal_site_id')){
            $query = $this->ooc->set_proposal_site($this->input->post('proposal_site_id'), $fields);
        } else {
            $query = $this->ooc->set_proposal_site(0, $fields);
        }



        $attach_id = $this->input->post('attach_db_id');
        $attach_desc = $this->input->post('attach_desc');
        $attach_url = $this->input->post('attach_url');
        $size = sizeof($attach_id);

        $existing_attachments = $this->ooc->get_attachment_ids($id);

        //if ($attach_id != false || $attach_id == 0){
        if ($attach_desc != false){
            if (!is_array($attach_id)){
                $attach_id = array($attach_id);
                $attach_desc = array($attach_desc);
                $attach_url = array($attach_url);
            }

            $request_attachments = array();
            for ($i = 0; $i < $size; $i++){
                if ($attach_id[$i] != 0){
                    array_push($request_attachments, $attach_id[$i]);
                }

                $fields =  array(
                    'link_id' => $attach_id[$i],
                    'link_description' => $attach_desc[$i],
                    'link_url' => $attach_url[$i],
                    'proposal_id' => $commitment->proposal_id
                );

                $this->ooc->set_external_link($fields);
            }

            $attachments_to_delete = array_diff($existing_attachments, $request_attachments);

            if (sizeof($attachments_to_delete) > 0){
                $this->ooc->delete_external_link($attachments_to_delete);
            }
        } else {
            if (sizeof($existing_attachments) > 0 ){
                $this->ooc->delete_external_link($existing_attachments);
            }
        }

        $response = array();
        if ($query){
            $workflow = $this->config->item('workflow');
            $flow = $workflow[$commitment->proposal_status]->poc;

            if($id == 0) {
                $this->ooc->historize( $commitment->proposal_id, $this->auth->get_id(), $commitment->proposal_status, '', 'Created', $flow);
            } else {
                if ($status == 'progress_update'){
                    $comment = '';
                    if ($oldCompletion != $completion){
                        $comment = 'Updated progress from '.$oldCompletion.'% to '.$completion.'%.';
                    }

                    $this->ooc->historize( $commitment->proposal_id, $this->auth->get_id(), $status, $comment, 'Progress Updated', $flow);
                } else {
                    $this->ooc->historize( $commitment->proposal_id, $this->auth->get_id(), $commitment->proposal_status, '', 'Updated', $flow);
                }

            }

            $response['success'] = true;
            $response['url'] = site_url('commitments');
            $data['status_code'] = self::HTTP_OK;
            $response['url'] = site_url('commitments/view/'.$commitment->proposal_id);
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to create/update your commitment';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }
		
        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
	}

	public function delete_commitment($id){
        if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');

        $proposal = $this->ooc->get_proposal($id);

        $response = array();
        if ($this->auth->get_organisation() == $proposal->fk_applicant_id && $proposal->proposal_status == 'draft'){
            $query = $this->ooc->delete_proposal($id);
            if ($query){
                $response['success'] = true;
                $response['url'] = site_url('commitments');
                $data['status_code'] = self::HTTP_OK;
            } else {
                $response['success'] = false;
                $response['message'] = 'It was not possible to create/update your commitment';
                $data['status_code'] = self::HTTP_FORBIDDEN;
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to create/update your commitment';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        };

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }
	
	
	public function view($id) {
		if(!$this->auth->is_logged('poc')) redirect('/commitments/login?target=/commitments/view/'.$id , 'refresh');
		
		$commitment = $this->ooc->get_proposal($id);
		
		if($commitment->fk_applicant_id != $this->auth->get_organisation()) { 
			return;
		}
		
		$workflow = $this->config->item('workflow');
		
		if($commitment) {
			$data = array(
				'content' => 'commit/view',
                'navigation' => $this->build_navigation_items(array('dashboard','my_commitment')),
                'title' => 'My Commitment',
                'js' => array('js/leaflet.js', 'moment/min/moment.min.js', 'datetimepicker/build/js/bootstrap-datetimepicker.min.js'),
                'css' => array('css/leaflet.css', 'datetimepicker/build/css/bootstrap-datetimepicker.min.css', 'css/timeline.css'),
				'controller' => 'commitments',
				'commitment' => $commitment,
				'themes' => $this->ooc->get_themes(),
				'workflow' => $workflow[$commitment->proposal_status]->poc,
                'completed' => $this->checkCompletenessOfProfile()
			);
			
			$this->load->view('page', $data);
		} else {
			$this->index();	
		}
	}
	
	public function flow($id) {
		if(!$this->auth->is_logged('poc')) redirect('/commitments/login', 'refresh');
		
		$commitment = $this->ooc->get_proposal($id);

		$response = array();
		if($commitment->fk_applicant_id == $this->auth->get_organisation()) {
			
			$status = $this->input->post('proposal_status');
			$comment = $this->input->post('comment');
			
			$fields = array(
				'proposal_status' => $status,
                'proposal_deadline' => $commitment->proposal_deadline
			); 
			
			$this->ooc->set_proposal($id, $fields);
			
			$workflow = $this->config->item('workflow');
			$flow = $workflow[$status]->poc;
			
			$query = $this->ooc->historize($id, $this->auth->get_id(), $status, $comment, $flow->label, $flow);

			if($query){
                $response['success'] = true;
                $response['url'] = site_url('commitments/view/'.$id);
                if ($status == 'submitted'){
                    $response['displayMsg'] = true;
                }
                $data['status_code'] = self::HTTP_OK;
            } else {
                $response['success'] = false;
                $response['message'] = 'It was not possible to submit your changes.';
                $data['status_code'] = self::HTTP_FORBIDDEN;
            }
		}  else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to submit your changes.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);

	}

	private function build_navigation_items($items, $id=0){

        $nav_keys = array_keys($items);
        $last_key = end($nav_keys);
        reset($nav_keys);

        $nav_list = $this->ooc->get_available_navigation_items();

        $final_items = array();
        $counter = 0;
        foreach ($items as $key => $value){
            $item = $nav_list[$value];
            if ($key == $last_key){
                $item->active = true;
            }

            $item->ref = $item->ref == '' ? site_url('commitments') : site_url('commitments/'.$item->ref);
            if ($id != 0 && $counter == (sizeof($items) - 2)){
                $item->ref = $item->ref.'/'.$id;
            }
            array_push($final_items, $item);
            $counter += 1;
        }

        return $final_items;
    }
}
