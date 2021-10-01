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

class Hosts extends CI_Controller {
    const HTTP_OK = "200 OK";
    const HTTP_FORBIDDEN = "403 Forbidden";

	function __construct()
    {
        parent::__construct();
		
		$this->load->model('auth_model', 'auth');
		$this->load->model('easme_model', 'ooc');
        $this->load->model('publish_model', 'publish');
        $this->load->model('email_model', 'email');
    }
	
	public function login($target = '') { 
	
		$data = array(
			'content' => 'host/login',
			'js' => array(),
			'css' => array(),
			'target' =>  $target == '' ? site_url('hosts'): $target 
		);
		
		$this->load->view('page', $data);
	}	
	
	public function do_login() { 
	
		$email = strtolower($this->input->post('email'));
		$password = $this->input->post('password');
		$logged = $this->auth->admin_login($email,$password);
		
		echo json_encode((object) array('success' => $logged));
	}
	
	public function logout()
	{
		$this->auth->logout();
		
		$this->login();
	}

    public function reset_password($key = '') {

        if($this->auth->is_logged('host')) redirect('hosts/login', 'refresh');

        $data = array(
            'content' => 'host/recovery',
            'js' => array(),
            'css' => array()
        );

        $this->load->view('page', $data);
    }

    public function do_recovery() {

        $email = strtolower($this->input->post('email'));
        $captcha_response = $this->input->post('g-recaptcha-response');

        if($this->auth->validate_captcha($captcha_response)) {

            $user = $this->auth->get_host_by_email($email);

            $response = array();
            if ($user){
                $key = MD5(uniqid());

                $fields = array(
                    'user_recovery_code' => $key,
                    'user_recovery_date' => date("Y-m-d H:i:s")
                );

                $this->auth->set_user($user->user_id, $fields);

                $this->email->sendEmail($user->user_email, 'Reset your password', $this->load->view('host/email/reset',array('key' => $key),true));
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
            redirect('hosts/reset_password', 'refresh');
        }

    }

    public function change_password($key='') {

        $user = $this->auth->get_user_by_recovery($key);

        if($user) {
            $data = array(
                'content' => 'host/change_password',
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
                $response['url'] = site_url('hosts');
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
	    if ($key == '' || $this->ooc->get_applicant_by_code($key) == null) {
            redirect('/hosts/login', 'refresh');
        } else {
            $data = array(
                'content' => 'host/register',
                'js' => array(),
                'css' => array(),
                'organisation' => $key != '' ? $this->ooc->get_applicant_by_code($key): null,
            );

            $this->load->view('page', $data);
        }
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
                    'user_role' => 'host',
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
                $error .= 'Email is already registered!';
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
            'isHost' => true
        );
        $this->load->view('policies', $data);
    }

    public function user_profile() {
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $user = $this->auth->get_user($this->auth->get_id());

        $data = array(
            'content' => 'profile/user_profile',
            'navigation' => $this->build_navigation_items(array('dashboard','my_profile')),
            'js' => array('js/libphonenumber.js'),
            'css' => array(),
            'user' => $user,
            'edit_profile' => site_url('hosts/edit_profile')
        );

        $this->load->view('page', $data);

    }

	public function edit_profile() { 
		
		if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');
		
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

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $emailStatus = array (
            'send' => false,
            'user' => null,
            'status' => null
        );
        $fields = array(
            'user_name' => $this->input->post('name'),
            'user_firstname' => $this->input->post('firstname'),
            'user_office' => $this->input->post('office'),
            'user_function' => $this->input->post('function'),
            'user_phone' => $this->input->post('phone')
        );

        if($this->input->post('email') && $this->input->post('email') != ''){
            $fields['user_email'] = strtolower($this->input->post('email'));
        }

        if($this->input->post('password') != '' && $this->input->post('password') == $this->input->post('confirm')) {
            $fields['user_password'] = $this->input->post('password');
        }

        if ($this->input->post('user_accept_pub') != ''){
           $fields['user_accept_pub'] = $this->input->post('user_accept_pub');
        }

        if ($this->input->post('user_is_host') != ''){
            $role = 'poc';
            if ($this->input->post('user_is_host') == 1){
                $role = 'host';
            }
            $fields['user_role'] = $role;

            $user = $this->auth->get_user($id);
            if ($user->user_role != $role){
                $emailStatus['send'] = true;
                $emailStatus['user'] = $user;
                if ($role == 'host'){
                    $emailStatus['status'] = 'added_host';
                } else {
                    $emailStatus['status'] = 'removed_host';
                }
            }
        }

        if ($this->input->post('organisation') != null){
            $orgId = intval($this->input->post('organisation'));
            if ($orgId == null){
                $fields['user_organisation'] = $this->set_organisation_from_register($this->input->post('organisation'));
            } else {
                $fields['user_organisation'] = $orgId;
            }

        }

        $query = $this->auth->set_user($id, $fields);


        $response = array();
        if ($query){
            $response['success'] = true;
            $response['url'] = site_url('hosts/user_profile');
            if ($id != $this->auth->get_id()){
                $response['url'] = site_url('hosts/view_user_profile/'.$id);
            } else {
                $data = $this->session->userdata('sessinfo');
                $data['username'] = $fields['user_name'];
                $data['firstname'] = $fields['user_firstname'];
                $this->auth->update_session_data($data);
            }
            $data['status_code'] = self::HTTP_OK;

            if ($emailStatus['status']){
                $subject = 'Host privileges assigned';
                $view = 'host/email/added_host';
                if ($emailStatus['status'] == 'removed_host'){
                    $subject = 'Host privileges removed';
                    $view = 'host/email/removed_host';
                }
                $this->email->sendEmail($emailStatus['user']->user_email, $subject, $this->load->view($view,'',true));
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to update the user\'s profile.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    public function organisation_profile() {
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

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
            'edit_organisation' => site_url('hosts/edit_organisation')
        );

        $this->load->view('page', $data);
    }

    public function edit_organisation() {

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

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

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

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
            $response['success'] = true;
            $response['url'] = '/hosts/organisation_profile';
            if ($id != $this->auth->get_organisation()){
                $response['url'] = site_url('/hosts/view_organisation_profile/'.$id);
            } else {
                $data = $this->session->userdata('sessinfo');
                $data['orgname'] = $fields['applicant_legal_name'];
                $this->auth->update_session_data($data);
            }
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to update the organisation\'s profile.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    function do_invite() {

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

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

        $this->email->sendEmail($email, $user->user_firstname.' '.$user->user_name.' invites you to join the OOC platform', $this->load->view('host/email/invite',array('key' => $organisation->applicant_uid),true));

        $response = array();
        $response['success'] = true;

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
        if(!$this->auth_model->is_logged('host')) redirect('/hosts/login', 'refresh');

        $data = array(
            'content' => 'host/dashboard',
            'navigation' => $this->build_navigation_items(array('dashboard')),
            'js' => array('datatables/datatables.min.js', 'moment/min/moment.min.js', 'c3/d3.min.js', 'c3/c3.min.js'),
            'css' => array('datatables/datatables.min.css', 'c3/c3.min.css'),
            'themes' => $this->ooc->get_themes(),
            'years' => $this->ooc->get_years(),
            'status' => $this->ooc->get_available_status(),
            'completions' => $this->ooc->get_available_completions(),
            'completed' => $this->checkCompletenessOfProfile(),
            'user_count' => $this->auth->get_user_count_by_status('P'),
            'org_types' => $this->ooc->get_types(),
            'org_count' => $this->ooc->get_incomplete_applicants_count(),
            'commit_count' => sizeof($this->ooc->get_proposals_by_status('submitted')),
            'overview_configs' => $this->ooc->get_overview_configs()
        );

        if ($data['overview_configs']->active){
            $figures = $this->ooc->get_overview_figures();
            $data['overview'] = $figures;
        }

        $this->load->view('page', $data);
    }

    public function view($id) {
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');
        $countries = $this->config->item('countries');
        $commitment = $this->ooc->get_proposal($id);
        $organisation = $this->ooc->get_applicant($commitment->fk_applicant_id);
        $organisation->type = $this->ooc->get_type($organisation->applicant_type);
        $organisation->country = $countries[$organisation->applicant_country_code];

        $workflow = $this->config->item('workflow');

        if($commitment) {
            $data = array(
                'content' => 'commit/view',
                'navigation' => $this->build_navigation_items(array('dashboard','view_commitment')),
                'title' => 'Commitment',
                'js' => array('js/leaflet.js', 'moment/min/moment.min.js', 'datetimepicker/build/js/bootstrap-datetimepicker.min.js'),
                'css' => array('css/leaflet.css', 'datetimepicker/build/css/bootstrap-datetimepicker.min.css', 'css/timeline.css'),
                'controller' => 'hosts',
                'commitment' => $commitment,
                'organisation' => $organisation,
                'themes' => $this->ooc->get_themes(),
                'workflow' => $workflow[$commitment->proposal_status]->host,
                'completed' => $this->checkCompletenessOfProfile()
            );

            $this->load->view('page', $data);
        } else {
            $this->index();
        }
    }

    public function merge_organisations(){
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $from = $this->input->post('from');
        $fromOrg = $this->auth->get_organisation_by_id($from);
        $to = $this->input->post('to');


        $status = true;
        $response = array();

        $users = $this->auth->get_users_id_by_organisation($from);
        $updatedUsers = $this->auth->move_users_to_organisation($from, $to);

        if ($updatedUsers) {
            $updatedCommitments = $this->auth->move_commitments_to_organisation($from, $to);
            if (!$updatedCommitments) {
                foreach ($users as $user) {
                    $this->auth->rollback_user_organisation($user->user_id, $from);
                }
                $status = false;
                $response['message'] = 'There was an error while moving commitments between organisations. No changes were made to the organisation <b>' . $fromOrg->applicant_legal_name .'</b>.';
            } else {
                $this->auth->delete_organisation($from);
            }
        } else {
            $status = false;
            $response['message'] = 'There was an error while moving users between organisations. No changes were made to the organisation <b>' . $fromOrg->applicant_legal_name .'</b>.';
        }

        $response['pending_orgs'] =  $this->ooc->get_incomplete_applicants_count();
        if ($status){
            $response['success'] = true;
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);

        return $response;
    }

    public function activate_users() {

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $ids = $this->input->post('ids');
        $status = true;
        foreach ($ids as $id){
            $user = $this->auth->get_user($id);
            if ($user->user_status != 'A'){
                $result = $this->auth->activate_user($id);
                if (!$result){
                    $status = false;
                } else {
                    if ($user->user_status == 'P'){
                        $this->email->sendEmail($user->user_email, 'Your registration has been accepted', $this->load->view('poc/email/activation','',true));
                    }
                }
            }
        }

        $response = array();
        $response['pending_users'] =  $this->auth->get_user_count_by_status('P');
        if ($status){
            $response['success'] = true;
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'There was an error while approving ';
            if (sizeof($ids) == 1){
                $response['message'] .= 'the user.';
            } else {
                $response['message'] .= 'some of the users.';
            }
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);

        return $response;

    }

    public function deactivate_users() {

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $ids = $this->input->post('ids');
        $status = true;
        foreach ($ids as $id){
            $result = $this->auth->deactivate_user($id);
            if (!$result){
                $status = false;
            }
        }

        $response = array();
        $response['pending_users'] =  $this->auth->get_user_count_by_status('P');
        if ($status){
            $response['success'] = true;
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'There was an error while rejecting ';
            if (sizeof($ids) == 1){
                $response['message'] .= 'the user.';
            } else {
                $response['message'] .= 'some of the users.';
            }
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);

        return $response;

    }

    public function add_host() {

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $ids = $this->input->post('ids');
        $status = true;
        foreach ($ids as $id){
            $user = $this->auth->get_user($id);
            $result = $this->auth->set_user_role($id, 'host');
            if (!$result){
                $status = false;
            } else {
                if ($user->user_role != 'host'){
                    $this->email->sendEmail($user->user_email, 'Host privileges assigned', $this->load->view('host/email/added_host','',true));
                }
            }
        }

        $response = array();
        $response['pending_users'] =  $this->auth->get_user_count_by_status('P');
        if ($status){
            $response['success'] = true;
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'There was an error while setting ';
            if (sizeof($ids) == 1){
                $response['message'] .= 'the user as host.';
            } else {
                $response['message'] .= 'some of the users as hosts.';
            }
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);

        return $response;

    }

    public function remove_host() {

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $ids = $this->input->post('ids');
        $status = true;
        foreach ($ids as $id){
            $user = $this->auth->get_user($id);
            $result = $this->auth->set_user_role($id, 'poc');
            if (!$result){
                $status = false;
            } else {
                if ($user->user_role != 'poc'){
                    $this->email->sendEmail($user->user_email, 'Host privileges removed', $this->load->view('host/email/removed_host','',true));
                }
            }
        }

        $response = array();
        $response['pending_users'] =  $this->auth->get_user_count_by_status('P');
        if ($status){
            $response['success'] = true;
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'There was an error while removing ';
            if (sizeof($ids) == 1){
                $response['message'] .= 'the user as host.';
            } else {
                $response['message'] .= 'some of the users as hosts.';
            }
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);

        return $response;

    }

    public function view_user_profile($id){
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $user = $this->auth->get_user($id);
        $organisation = $this->ooc->get_applicant($user->user_organisation);

        $data = array(
            'content' => 'profile/view_user_profile',
            'navigation' => $this->build_navigation_items(array('dashboard','view_user_profile')),
            'js' => array('js/libphonenumber.js'),
            'css' => array(),
            'user' => $user,
            'organisation' => $organisation,
            'types' => $this->ooc->get_types(),
            'edit_profile' => 'edit_user_profile'
        );

        $this->load->view('page', $data);
    }

    public function edit_user_profile($id) {

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $user = $this->auth->get_user($id);
        $org = $this->auth->get_organisation_by_id($user->user_organisation);

        $data = array(
            'content' => 'profile/edit_user_profile',
            'navigation' => $this->build_navigation_items(array('dashboard','view_user_profile','edit_user_profile'), $id),
            'js' => array('js/libphonenumber.js'),
            'css' => array(),
            'title' => 'Edit User Profile',
            'user' => $user,
            'organisation' => $org
        );

        $this->load->view('page', $data);
    }

    public function delete_user(){
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $userId = $this->input->post('userId');
        $orgId = $this->input->post('orgId');

        $query = $this->auth->delete_user($userId);

        $response = array();
        if ($query){
            $response['success'] = true;
            $response['url'] = site_url('hosts/view_organisation_profile/'.$orgId);
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to delete the user.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    public function delete_users() {

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $ids = $this->input->post('ids');
        $status = true;
        foreach ($ids as $id){
            $result = $this->auth->delete_user($id);
            if (!$result){
                $status = false;
            }
        }

        $response = array();
        $response['pending_users'] =  $this->auth->get_user_count_by_status('P');
        if ($status){
            $response['success'] = true;
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'There was an error while deleteing ';
            if (sizeof($ids) == 1){
                $response['message'] .= 'the user.';
            } else {
                $response['message'] .= 'some of the users.';
            }
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);

        return $response;

    }

    public function re_approve_user(){
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $userId = $this->input->post('userId');
        $orgId = $this->input->post('orgId');

        $query = $this->auth->activate_user($userId);

        $response = array();
        if ($query){
            $response['success'] = true;
            $response['url'] = site_url('hosts/view_organisation_profile/'.$orgId);
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to approve the user.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);

    }

    public function view_organisation_profile($id){
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $organisation = $this->ooc->get_applicant($id);
        $deleted_users = $this->auth->get_users_by_status_and_organisation('D', $id);
        $rejected_users = $this->auth->get_users_by_status_and_organisation('I', $id);

        $data = array(
            'content' => 'profile/organisation_profile',
            'navigation' => $this->build_navigation_items(array('dashboard','view_organisation_profile')),
            'title' => 'Organisation Profile',
            'invite' => false,
            'js' => array('js/libphonenumber.js'),
            'css' => array(),
            'types' => $this->ooc->get_types(),
            'organisation' => $organisation,
            'deleted_users' => $deleted_users,
            'rejected_users' => $rejected_users,
            'edit_organisation' => site_url('hosts/edit_user_organisation/'.$organisation->applicant_id)
        );

        $this->load->view('page', $data);
    }

    public function edit_user_organisation($id){
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $organisation = $this->ooc->get_applicant($id);

        $data = array(
            'content' => 'profile/edit_organisation_profile',
            'navigation' => $this->build_navigation_items(array('dashboard','view_organisation_profile', 'edit_user_organisation'), $id),
            'js' => array('js/leaflet.js'),
            'css' => array('css/leaflet.css'),
            'organisation' => $organisation,
            'types' => $this->ooc->get_types()
        );

        $this->load->view('page', $data);
    }

    public function publish(){
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $success = $this->publish->do_publish(true);
        $response = array();
        if ($success){
            $response['success'] = true;
            $response['message'] = 'All eligible commitments were successfully published';
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'There was an error while publishing some of your commitments. Please check your map viewer configurations in the settings area.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);

        return $response;
    }

    public function exportCommitsCSV(){
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $filters = array(
            'q' => $this->input->post('q'),
            'area' => $this->input->post('area'),
            'year' => $this->input->post('year'),
            'status' => $this->input->post('status'),
            'completion' => $this->input->post('completion'),
            'org' => $this->input->post('organisation')
        );

        $commits = $this->ooc->lookup($filters);
        $response = array();
        if (sizeof($commits) > 0){
            $orgData = "ID\tYear\tTheme\tTitle\tBudget\tDeadline\tProgress\tAnnouncer\tLanguage\tManaged area\tProtected surface area\tWebsite\tOrganisation\tOrganisation type\tOrg_ID\n";
            foreach ($commits as $commit) {
                $active = $commit->proposal_area_active == 0 ? "No" : "Yes";
                $orgData .= $commit->proposal_id."\t".$commit->proposal_year."\t".$commit->proposal_theme->name."\t".$commit->proposal_title."\t".$commit->proposal_budget."\t";
                $orgData .= $commit->proposal_deadline."\t".$commit->proposal_completion."\t";
                $orgData .= $commit->proposal_announcer."\t".$commit->proposal_language."\t".$active."\t".$commit->proposal_area."\t";
                $orgData .= $commit->proposal_website."\t".$commit->applicant_legal_name."\t".$commit->organisation->type->name."\t".$commit->organisation->applicant_id."\n";
            }

            $response['success'] = true;
            $response['message'] = 'Commitments were exported successfuly.';
            $response['data'] = $orgData;
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'There was an error while exporting commitments.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }


        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    public function exportUsersCSV(){
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $filters = array(
            'q' => $this->input->post('q'),
            'status' => $this->input->post('status'),
            'role' => $this->input->post('role'),
            'org' => $this->input->post('organisation'),
            'start' => 0,
            'length' => 0
        );

        $users = $this->auth->get_complete_users($filters, true);
        $response = array();
        if (sizeof($users) > 0){
            $userData = "ID\tFirst Name\tLast Name\tEmail\tOrganisation\tOrg_ID\tRole\n";
            foreach ($users as $user) {
                $org = $this->ooc->get_applicant($user->user_organisation);
                $userData .= $user->user_id."\t".$user->user_firstname."\t".$user->user_name."\t".$user->user_email."\t".$org->applicant_legal_name."\t".$org->applicant_id."\t".strtoupper($user->user_role)."\n";
            }

            $response['success'] = true;
            $response['message'] = 'Users were exported successfuly.';
            $response['data'] = $userData;
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'There was an error while exporting users.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }


        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    public function exportOrgsCSV(){
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $filters = array(
            'q' => $this->input->post('q'),
            'type' => $this->input->post('type'),
            'start' => 0,
            'length' => 0
        );

        $orgs = $this->ooc->get_complete_applicants($filters);
        $response = array();
        if (sizeof($orgs) > 0){
            $orgData = "ID\tLegal Name\tType\tCountry\tCity\t#Commits\t#Users\n";
            foreach ($orgs as $org) {
                $orgData .= $org->applicant_id."\t".$org->applicant_legal_name."\t".$org->type->name."\t".$org->applicant_country_code."\t".$org->applicant_city."\t".$org->commit_count."\t".$org->user_count."\n";
            }

            $response['success'] = true;
            $response['message'] = 'Organisations were exported successfuly.';
            $response['data'] = $orgData;
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'There was an error while exporting organisations.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }


        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    private function merge_configs_campaign($configs, $campaign){
        $keys = array('camp_start', 'camp_end', 'camp_new_target', 'camp_update_target');
        foreach($campaign as $key => $value) {
            if (in_array($key, $keys)){
                $config = (object) array(
                    'config_key' => 'host_'.$key,
                    'config_value' => $value
                );
                array_push($configs, $config);
            }
        }

        return $configs;
    }

    public function settings() {
        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $publish = $this->publish->get_configs();
        $campaign = $this->ooc->get_running_campaign();
        $is_new_campaign = true;
        if (sizeof($campaign) > 0){
            $is_new_campaign = false;
            $publish = $this->merge_configs_campaign($publish, $campaign);
        }

        $data = array(
            'content' => 'host/settings',
            'navigation' => $this->build_navigation_items(array('dashboard','settings')),
            'js' => array('moment/min/moment.min.js', 'datetimepicker/build/js/bootstrap-datetimepicker.min.js', 'wysiwyg/external/jquery.hotkeys.js','wysiwyg/external/google-code-prettify/prettify.js','wysiwyg/bootstrap-wysiwyg.js'),
            'css' => array('datetimepicker/build/css/bootstrap-datetimepicker.min.css', 'wysiwyg/external/google-code-prettify/prettify.css', 'css/switch.css'),
            'controller' => 'hosts',
            'types' => $this->ooc->get_types(),
            'themes' => $this->ooc->get_themes(),
            'publish' => $publish,
            'is_new_campaign' => $is_new_campaign
        );

        $this->load->view('page', $data);
    }

    public function get_type($id){
        $result = $this->ooc->get_type($id);

        $response = array();
        if ($result){
            $response['success'] = false;
            $data['status_code'] = self::HTTP_FORBIDDEN;
        } else {
            $response['success'] = true;
            $data['status_code'] = self::HTTP_OK;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    public function get_types() {
        $data = array(
            'types' => $this->easme_model->get_types()
        );

        header('Content-Type: application/json');

        echo json_encode($data);
    }

    public function delete_type($id) {

		if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

		$query = $this->ooc->delete_type($id);

        $response = array();
        if ($query){
            $response['success'] = true;
            $response['url'] = site_url('hosts/settings');
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to delete this organisation type.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
	}

    public function set_type($id) {

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        $fields = array(
            'name' => $this->input->post('name')
        );

        $type = $this->ooc->set_type($id, $fields);

        $response = array();
        if ($type){
            $response['success'] = true;
            $response['url'] = site_url('hosts/settings');
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to update this organisation type.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    public function get_theme($id){
        $result = $this->ooc->get_theme($id);

        $response = array();
        if ($result){
            $response['success'] = false;
            $data['status_code'] = self::HTTP_FORBIDDEN;
        } else {
            $response['success'] = true;
            $data['status_code'] = self::HTTP_OK;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    public function delete_theme($id) {

		if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

		$query = $this->ooc->delete_theme($id);

        $response = array();
        if ($query){
            $response['success'] = true;
            $response['url'] = site_url('hosts/settings');
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to delete this commitment theme.';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
	}

	private function upload_theme_image($files, $name){
        $ext = $files['icon'] ? pathinfo($files['icon']['name'], PATHINFO_EXTENSION) : '.';
        $uid = uniqid();
        $uidExt = $uid.'.'.$ext;

        $config['upload_path'] = $this->config->item('upload_folder');
        $config['allowed_types'] = 'png|gif';
        $config['file_name']  = $uid;

        $this->load->library('upload', $config);


        if ( ! $this->upload->do_upload('icon')) {
            return false;
        } else {
            if ($ext != 'png'){
                $f = imagepng(imagecreatefromstring(file_get_contents($config['upload_path'].$uidExt)), $config['upload_path'].$uid.'.png');
                if (!$f){
                    return false;
                }
                unlink($config['upload_path'].$uidExt);
                $ext = 'png';
                $uidExt = $uid.'.'.$ext;
            }

            return rename($config['upload_path'].$uidExt, $_SERVER['DOCUMENT_ROOT'].'/assets/images/ooc/'.$name.'.'.$ext);
        }
    }

    public function set_theme($id) {

        if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');

        if ($_FILES['icon']['name'] != ''){
            $img = $this->upload_theme_image($_FILES, $id);
        }

        $response = array();
        $response['success'] = false;
        $data['status_code'] = self::HTTP_FORBIDDEN;
        if ($_FILES['icon']['name'] == '' || ($_FILES['icon']['name'] != '' && isset($img) && $img)) {
            $fields = array(
                'name' => $this->input->post('name')
            );

            $theme = $this->ooc->set_theme($id, $fields);

            if ($theme){
                $response['success'] = true;
                $response['url'] = site_url('hosts/settings');
                $data['status_code'] = self::HTTP_OK;
            } else {
                $response['message'] = 'It was not possible to update this commitment theme.';
            }
        } else {
            $response['message'] = 'It was not possible to update this commitment theme. Make sure that you are uploading an image file. Supported formats are PNG and GIF.';
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    public function set_host_config(){
        $url = $this->input->post('host_conf_site');
        if (substr($url, -1) == '/'){
            $url = substr($url, 0, -1);
        }

        $isCampaignActive = $this->input->post('host_camp_active') == 'on' ? 1 : 0;

        $fields = array(
            'host_flag' => $this->input->post('host_flag'),
            'host_country' => $this->input->post('host_country'),
            'host_conf_site' => $url,
            'host_camp_active' => $isCampaignActive,
            'host_conf_start' => $this->input->post('host_conf_start'),
            'host_conf_end' => $this->input->post('host_conf_end')
        );

        $result = $this->ooc->set_host_config($fields);

        $response = array();
        $response['success'] = false;
        $data['status_code'] = self::HTTP_FORBIDDEN;
        if ($result){
            if ($isCampaignActive == 1){
                $fields = array(
                    'camp_start' => $this->input->post('host_camp_start'),
                    'camp_end' => $this->input->post('host_camp_end'),
                    'camp_new_target' => $this->input->post('host_camp_new_target'),
                    'camp_update_target' => $this->input->post('host_camp_update_target')
                );

                $camp_action = $this->input->post('camp_action');
                $result = $this->ooc->set_campaign($fields, $camp_action);

                if ($result){
                    $configs = $this->publish->get_configs();
                    $campaign = $this->ooc->get_running_campaign();
                    $is_new_campaign = true;
                    if (sizeof($campaign) > 0){
                        $is_new_campaign = false;
                        $configs = $this->merge_configs_campaign($configs, $campaign);
                    }

                    $response['success'] = true;
                    $response['message'] = 'Your settings were updated successfully';
                    $response['is_new_campaign'] = $is_new_campaign;
                    $response['configs'] = $configs;
                    $data['status_code'] = self::HTTP_OK;
                } else {
                    $response['message'] = 'It was not possible to update your campaign settings';
                }
            } else {
                $response['success'] = true;
                $response['message'] = 'Your settings were updated successfully';
                $response['is_new_campaign'] = false;
                $response['configs'] = $this->publish->get_configs();
                $data['status_code'] = self::HTTP_OK;
            }
        } else {
            $response['message'] = 'It was not possible to update your host settings';
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    public function set_viewer_config() {
	    $url = $this->input->post('app_host');
	    if (substr($url, -1) == '/'){
	        $url = substr($url, 0, -1);
        }

        $fields = array(
            'app_title' => $this->input->post('app_title'),
            'app_description' => $this->input->post('app_description'),
            'app_theme' => $this->input->post('app_theme'),
            'app_host' => $url,
            'app_title' => $this->input->post('app_title'),
            'app_mode' => $this->input->post('app_mode'),
            'app_disclaimer' => $this->input->post('app_disclaimer')
        );

        $result = $this->publish->set_config($fields);

        $response = array();
        if ($result){
            $response['success'] = true;
            $response['message'] = 'Your settings were updated successfully';
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to update your map viewer settings';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    public function set_scheduler(){
        $fields = array(
            'scheduler_active' => $this->input->post('scheduler_active'),
            'scheduler_expression' => json_encode($this->input->post('scheduler_expression'))
        );

        $result = $this->publish->set_config($fields);

        $response = array();
        if ($result){
            $response['success'] = true;
            $response['message'] = 'Your scheduler settings were updated successfully';
            $data['status_code'] = self::HTTP_OK;
        } else {
            $response['success'] = false;
            $response['message'] = 'It was not possible to update your scheduler settings';
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $data['json'] = $response;
        $this->load->view('rest/json_view', $data);
    }

    private function get_user_email_from_history($commitment, $is_note){
	    $orgUsers = $this->auth_model->get_users_email_id_by_organisation($commitment->fk_applicant_id);
	    $skip_status = array('approved', 'rejected', 'closed', 'progress_changes', 'scheduled');
	    $skip_details = array('Changes Requested', 'Published');

        foreach($commitment->history as $item) {
            if($item->user_role == 'poc'){
                return $item->user_email;
            }

            if ($item->user_role == 'host' && !in_array($item->status, $skip_status) && $item->detail != 'Comment'){
                if ( ($item->status == 'draft' || $item->status == 'published') && in_array($item->detail, $skip_details)){
                    continue;
                }
                $user_id = $item->user_id;
                $user = array_filter(
                    $orgUsers,
                    function ($e) use (&$user_id) {
                        return $e->user_id == $user_id;
                    }
                );

                if (sizeof($user) > 0){
                    return $user[array_keys($user)[0]]->user_email;
                } else {
                    continue;
                }
            }
        }
    }
	
	public function flow($id) {
		
		if(!$this->auth->is_logged('host')) redirect('/hosts/login', 'refresh');
		
		$commitment = $this->ooc->get_proposal($id);
        $is_note = filter_var($this->input->post('is_note'), FILTER_VALIDATE_BOOLEAN);
		
		$response = array();
		if(!$commitment->proposal_host ||
            $commitment->proposal_host->user_id == $this->auth->get_id() ||
            ($commitment->proposal_host->user_id != $this->auth->get_id() && $this->auth->get_role() == 'host') ||
            $commitment->proposal_host->user_organisation == $this->auth->get_organisation() ||
            $commitment->proposal_host->user_id == $this->auth->get_system_user()->user_id ||
            ($commitment->proposal_host && $commitment->proposal_status == 'approved') ||
            $is_note) {
			
			$status = $this->input->post('proposal_status');
			$comment = $this->input->post('comment');
			$email = $this->input->post('email');
			$schedule = $this->input->post('schedule_date');
			
			$workflow = $this->config->item('workflow');

			if ($is_note){
			    $status = $commitment->proposal_status;
                $flow = (object) array(
                    'label' => 'Comment',
                    'icon' => 'fa-comments',
                    'class' => 'primary',
                    'is_public' => $this->input->post('is_public') ? 1 : 0
                );
            } else {
                $flow = $workflow[$status]->{$this->auth->get_role()};
            }


			
			$fields = array(
				'proposal_status' => $status,
				'proposal_host' => $this->auth->get_id(),
                'proposal_deadline' => $commitment->proposal_deadline
			);

			if ($schedule && !$is_note){
			    $fields['proposal_schedule'] = $schedule;
			    $comment = 'Commitment will only be published after '.$schedule.'. <br>'.$comment;
            }


            $query = $this->ooc->historize($id, $this->auth->get_id(), $status, $comment, $flow->label, $flow);
			if (!$is_note){
                if ($status == 'closed'){
                    $fields['proposal_updated'] = date("Y-m-d H:i:s");
                }
                $commitment = $this->ooc->set_proposal($id, $fields);
            } else {
			    $commitment = $this->ooc->get_proposal($id);
            }

            if ($query && isset($commitment)){
                $response['success'] = true;
                $response['url'] = site_url('/hosts/view/'.$id);
                $data['status_code'] = self::HTTP_OK;

                if($email && !$is_note || ($email && $is_note && $flow->is_public == 1)) {

                    $emails = $this->config->item('worflow_emails');

                    if(array_key_exists($email, $emails)) {

                        $properties = $emails[$email];

                        $to = $this->get_user_email_from_history($commitment, $is_note);

                        $this->email->sendEmail($to, $properties->title, $this->load->view($properties->content,array('commitment' => $commitment),true));
                    }
                }

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

            $item->ref = $item->ref == '' ? site_url('hosts') : site_url('hosts/'.$item->ref);
            if ($id != 0 && $counter == (sizeof($items) - 2)){
                $item->ref = $item->ref.'/'.$id;
            }
            array_push($final_items, $item);
            $counter += 1;
        }

        return $final_items;
    }
}
