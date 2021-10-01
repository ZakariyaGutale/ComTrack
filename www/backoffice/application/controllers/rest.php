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

class Rest extends CI_Controller {
    const HTTP_OK = "200 OK";
    const HTTP_FORBIDDEN = "403 Forbidden";

    function __construct()
    {
        parent::__construct();

        $this->load->model('auth_model', 'auth');
        $this->load->model('easme_model', 'ooc');
    }

    private function checkAuth(){
        $status = true;
        if (!$this->auth_model->get_role() != null){
            $status = false;
        }

        return $status;
    }

    private function checkIsPost(){
        $status = true;
        if ($this->input->server('REQUEST_METHOD') != 'POST'){
            $status = false;
        }

        return $status;
    }

    private function loadView($status, $results = array(), $page_target=null){
        if ($status){
            $data['status_code'] = self::HTTP_OK;
        } else {
            $data['status_code'] = self::HTTP_FORBIDDEN;
        }

        $response = array();
        $response['items'] = $results;
        if ($page_target != null){
            if ($page_target == 'table'){
                $response['recordsTotal'] = 0;
                $response['recordsFiltered'] = 0;
                $response['items'] = array();
                if (sizeof($results) > 1){
                    $response['items'] = $results[0];
                    $response['recordsTotal'] = $results[1];
                    $response['recordsFiltered'] = $results[1];
                }
            } else if ($page_target == 'combo'){
                $response['items'] = $results[0];
                $response['pagination']['more'] = $results[1];
            }
        }

        $response['success'] = $status;
        $data['json'] = $response;

        $this->load->view('rest/json_view', $data);
    }

    public function get_organisation_list(){
        //TODO check if this is desired
        /*$is_host = false;
        if ($this->auth->get_role() == 'host' ){
            $is_host = true;
        }*/
        $is_host = true;

        $filters = array(
            'term' => $this->input->post('term'),
            'page' => intval($this->input->post('page')),
            'size' => 6
        );

        $results = $this->auth_model->get_organisation_list($filters, $is_host);

        $this->loadView(true, $results, 'combo');
    }

    public function commitments(){
        if ($this->checkAuth() && $this->checkIsPost()){
            $filters = array(
                'q' => $this->input->post('q'),
                'area' => $this->input->post('area'),
                'year' => $this->input->post('year'),
                'status' => $this->input->post('status'),
                'completion' => $this->input->post('completion'),
                'org' => $this->input->post('organisation')
            );

            if (isset($_POST['start'])){
                $filters['start'] = intval($this->input->post('start'));
                $filters['length'] = intval($this->input->post('length'));
            }

            $results = $this->ooc->lookup($filters);

            if ($this->auth->get_role() == 'host'){
                $this->loadView(true, $results, 'table');
            } else {
                $this->loadView(true, $results, 'table');
            }

        } else {
            $this->loadView(false);
        }
    }

    public function users(){
        if ($this->checkAuth() && $this->checkIsPost()){
            $filters = array(
                'q' => $this->input->post('q'),
                'status' => $this->input->post('status'),
                'role' => $this->input->post('role'),
                'org' => $this->input->post('organisation'),
                'start' => intval($this->input->post('start')),
                'length' => intval($this->input->post('length'))
            );

            $results = $this->auth->get_complete_users($filters);
            $this->loadView(true, $results, 'table');
        } else {
            $this->loadView(false);
        }
    }

    public function countries(){
        if ($this->checkAuth() && $this->checkIsPost()){
            $filters = array(
                'term' => $this->input->post('term'),
                'page' => intval($this->input->post('page')),
                'size' => 6
            );
            $results = $this->ooc->get_countries($filters);
            $this->loadView(true, $results, 'combo');
        } else {
            $this->loadView(false);
        }
    }

    public function organisations(){
        if ($this->checkAuth() && $this->checkIsPost()){
            $filters = array(
                'q' => $this->input->post('q'),
                'type' => $this->input->post('type'),
                'start' => intval($this->input->post('start')),
                'length' => intval($this->input->post('length'))
            );

            $results = $this->ooc->get_complete_applicants($filters);
            $this->loadView(true, $results, 'table');
        } else {
            $this->loadView(false);
        }
    }

    public function set_commitment_year(){
        if ($this->checkAuth() && $this->auth_model->get_role() == 'host') {
            $results = $this->ooc->set_commitment_year($this->input->post('proposal_id'), $this->input->post('proposal_year'));
            if ($results){
                $this->loadView(true);
            } else {
                $this->loadView(false);
            }
        } else {
            $this->loadView(false);
        }
    }

    public function delete_organisation(){
        if ($this->checkAuth() && $this->auth_model->get_role() == 'host') {
            $id = $this->input->post('id');
            $user_count = sizeof($this->auth->get_all_organisation_users($id));
            $commit_count = $this->ooc->get_commit_count_by_organisation($id);

            if ($user_count == 0 && $commit_count == 0){
                $this->auth->delete_organisation($id);
                $this->loadView(true);
            } else {
                $this->loadView(false);
            }
        } else {
            $this->loadView(false);
        }
    }

}
