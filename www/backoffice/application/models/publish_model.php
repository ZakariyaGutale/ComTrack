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

class Publish_model extends CI_Model {
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		
		date_default_timezone_set($this->config->item('timezone'));
        $this->load->model('auth_model', 'auth');
        $this->load->model('easme_model', 'ooc');
		
    }

    public function get_configs(){
	    $query = $this->db->get('configs');
	    return $query->result();
    }

    public function get_config() {

        $query = $this->db->get('configs');

        $config = array();

        foreach($query->result() as $row) {
            $config[$row->config_key] = $row->config_value;
        }

        return $config;
    }

    public function set_config($fields) {
        $do_export = false;
        $filename = $this->config->item('maps_folder')."config.php";

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

            if ($key == 'scheduler_active' || $key == 'scheduler_expression'){
                $do_export = true;
            }
        }

        if ($do_export){
            $this->save_configs_to_json($fields);
        }

        //Tweaking the disclaimer
        $configData = $this->get_config();
        $ec_disclaimer = "<hr><div>The designations employed and the presentation of material on this map do not imply the expression of any opinion ";
        $ec_disclaimer.= "whatsoever on the part of the European Union concerning the legal status of any country, territory, city or area or of its ";
        $ec_disclaimer.= "authorities, or concerning the delimitation of its frontiers or boundaries. Kosovo*: This designation is without prejudice to ";
        $ec_disclaimer.= "positions on status, and is in line with UNSCR 1244/1999 and the ICJ Opinion on the Kosovo declaration of independence. Palestine*: ";
        $ec_disclaimer.= "This designation shall not be construed as recognition of a State of Palestine and is without prejudice to the individual positions of ";
        $ec_disclaimer.= "the Member States on this issue.</div>";
        $configData["app_disclaimer"] = "<div class='col-md-12'><h1>Disclaimer</h1>".$configData["app_disclaimer"].$ec_disclaimer."</div>";

        file_put_contents($filename, $this->load->view('publish/config',array('config' =>$configData), true));

        return $query;

    }
	
	private function get_proposal_sites($id) {
		$this->db->where('fk_proposal_id', $id);
		$query = $this->db->get('proposals_sites');
		return $query->result(); 
	}

    public function do_publish($is_host = true){
        $success = false;
        $config = $this->get_config();
        if (isset($config['app_host']) && $config['app_host'] != ''){
            if ($is_host){
                $userId = $this->auth->get_id();
            } else {
                $sysUser = $this->auth->get_system_user();
                $userId = $sysUser->user_id;
            }
            $success = false;

            $stepStatus = array();
            array_push($stepStatus, $this->export_db($userId));
            array_push($stepStatus, $this->export_beneficiary_sheets());
            array_push($stepStatus, $this->export_projects_sheets());
            array_push($stepStatus, $this->export_filters($this->config->item('json_export_folder').'filters.json'));
            array_push($stepStatus, $this->export_lists());
            array_push($stepStatus, $this->export_sitemap('txt'));
            array_push($stepStatus, $this->export_stats());
            array_push($stepStatus, $this->export_themes());

            $status = array_unique($stepStatus);

            if (sizeof($status) == 1 && $status[0] == true){
                $success = true;
            }
        }

        return $success;
    }
	
	private function export_db($userId = 0) {
        $success = true;
        $statuses = array('scheduled', 'published', 'progress_update', 'progress_changes', 'closed');

		$sql = "SELECT DISTINCT applicant_id, applicant_legal_name, applicant_nuts_1, applicant_type, applicant_country_code, applicant_city, applicant_osm_lat, applicant_osm_lng ";
		$sql.= "FROM applicants ";
		$sql.= "WHERE 1=1 ";
		$sql.= "ORDER BY applicant_legal_name";
		
		$query = $this->db->query($sql);
		
		$itemCount = 0;
		
		$itemsPerFile = $this->config->item('json_items_per_file');
		
		$fileIndex = 1;
		
		$data = (object) array( 
			'linkage' => '',
			'created' => date("d/m/Y H:i:s"),
			'items' => array()
		);
		
		foreach($query->result() as $row) {

			$this->db->select(' proposal_id as id, proposal_abstract as abstract, proposal_year as year, proposal_theme as theme,proposal_title as title, proposal_budget as budget, proposal_status as status, proposal_schedule as schedule');
			$this->db->distinct();
			$this->db->from('proposals');
			$this->db->join('proposals_applicants', 'fk_proposal_id=proposal_id');
			$this->db->where_in('proposal_status', $statuses);
			$this->db->where('fk_applicant_id', $row->applicant_id);
			$this->db->order_by('proposal_year desc,proposal_title');
			
			$query_proposal = $this->db->get();
			$projects = $query_proposal->result();

            $workflow = $this->config->item('workflow');
            $flow = $workflow['published']->host;
            $currentDate = date('Y-m-d');
            $projToBePublished = array();
			foreach($projects as $project) {
			    $in = false;
                $fields = array(
                    'proposal_ontrack' => 1
                );
			    if ($project->status == 'scheduled' && $project->schedule <= $currentDate){
			        $in = true;
                    $fields['proposal_status'] = 'published';
                    $fields['proposal_host'] = $userId;
                    $fields['proposal_schedule'] = null;
                    $this->ooc->historize($project->id, $userId, 'published', '', $flow->label, $flow);
                } else if ($project->status != 'scheduled'){
                    $in = true;
                }

			    if ($in){
                    $this->ooc->set_proposal($project->id, $fields);
                    $project->sites = $this->get_proposal_sites($project->id);
                    unset($project->status);
                    unset($project->schedule);
                    array_push($projToBePublished, $project);
                }

			} 
			
			// only include applicants with projects
			if(count($projToBePublished) > 0) {
				$itemCount++;
				
				$item = (object) array(
					'id' => $row->applicant_id,
					'name' => $row->applicant_legal_name,
					'type' => $row->applicant_type,
					'country' => $row->applicant_country_code,
					/*'nuts' => $row->applicant_nuts_1,*/
					'city' => $row->applicant_city,
					'geo' => array($row->applicant_osm_lat,$row->applicant_osm_lng),
					'projects' => $projToBePublished,
				);
				
				$data->items[] = $item;
			}
			
			if($itemCount == $itemsPerFile) { 
			
				$filename = $this->config->item('json_export_folder').'list-'.$fileIndex.'.json';
				
				$data->linkage = '/json/list-'.($fileIndex + 1).'.json';				
				
				if(file_exists($filename)) unlink($filename);
				$f = file_put_contents($filename, json_encode($data));
				if (!$f){
				    $success = false;
                }
					
				$fileIndex++;
				$itemCount = 0;
				$data->items = array();
				$data->linkage = array();	
			}
		}


		if($data && count($data) > 0) {
			
			$filename = $this->config->item('json_export_folder').'list-'.$fileIndex.'.json';
			
			if(file_exists($filename)) unlink($filename);
			$f = file_put_contents($filename, json_encode($data));
            if (!$f){
                $success = false;
            }
		}

		return $success;
	}

    private function export_beneficiary_sheets() {
	    $success = true;

        $sql = "SELECT DISTINCT * ";
        $sql.= "FROM applicants ";
        $sql.= "WHERE 1=1 ";
        $sql.= "ORDER BY applicant_id";

        $query = $this->db->query($sql);

        foreach($query->result() as $row) {

            $row->pocs = $this->auth->get_organisation_users($row->applicant_id);
            $row->type = $this->ooc->get_type($row->applicant_type);

            $sql = "SELECT DISTINCT * ";
            $sql.= "FROM proposals, proposals_applicants, proposals_themes ";
            $sql.= "WHERE fk_proposal_id=proposal_id AND proposal_theme=id AND proposal_status in ('published', 'progress_update', 'progress_changes', 'closed') AND fk_applicant_id=".$row->applicant_id." ";
            $sql.= "ORDER BY proposal_year DESC, proposal_title";

            $query_proposal = $this->db->query($sql);
            $proposals = $query_proposal->result();

            foreach($proposals as $proposal) {

                $sql = "SELECT * FROM applicants, proposals_applicants WHERE fk_applicant_id=applicant_id AND fk_proposal_id=".$proposal->proposal_id;
                $query_benef = $this->db->query($sql);

                $proposal->beneficiaries = $query_benef->result();

            }

            $data = array(
                'row' => $row,
                'proposals' => $proposals
            );

            $filename = $this->config->item('json_export_folder').'beneficiaries/beneficiary-'.$row->applicant_id.'.json';

            if(file_exists($filename)) unlink($filename);

            $f = file_put_contents($filename, $this->load->view('publish/applicant-sheet',$data, true));
            if (!$f){
                $success = false;
            }
        }

        return $success;
    }

    private function export_projects_sheets() {
        $success = true;

        $sql = "SELECT DISTINCT * ";
        $sql.= "FROM proposals, proposals_themes ";
        $sql.= "WHERE proposal_theme=id AND proposal_status in ('published', 'progress_update', 'progress_changes', 'closed') ";
        $sql.= "ORDER BY proposal_id";

        $query = $this->db->query($sql);

        foreach($query->result() as $row) {


            $sql = "SELECT DISTINCT * ";
            $sql.= "FROM applicants, proposals_applicants ";
            $sql.= "WHERE fk_applicant_id=applicant_id AND fk_proposal_id=".$row->proposal_id." ";
            $sql.= "ORDER BY applicant_legal_name";

            $query_applicant = $this->db->query($sql);

            $sql = "SELECT * ";
            $sql.= "FROM proposals_sites ";
            $sql.= "WHERE fk_proposal_id=".$row->proposal_id." ";
            $sql.= "ORDER BY name";

            $query_site = $this->db->query($sql);

            $sql = "SELECT * ";
            $sql .= "FROM external_links ";
            $sql .= "WHERE proposal_id=".$row->proposal_id." ";

            $query_links = $this->db->query($sql);

            $row->attachments = $query_links->result();


            $data = array(
                'row' => $row,
                'beneficiaries' => $query_applicant->result(),
                'sites' => $query_site->result()
            );

            $filename = $this->config->item('json_export_folder').'projects/project-'.$row->proposal_id.'.json';

            if(file_exists($filename)) unlink($filename);

            $f = file_put_contents($filename, $this->load->view('publish/project-sheet',$data, true));
            if (!$f){
                $success = false;
            }
        }

        return $success;
    }

    private function export_filters($filename) {
	    $success = true;

        $sql = "SELECT DISTINCT id,name,grp FROM proposals_themes,proposals WHERE proposal_theme=id AND proposal_status in ('published', 'progress_update', 'progress_changes', 'closed') ORDER BY grp,name";

        $phases = $this->db->query($sql);

        $sql = "SELECT DISTINCT proposal_year as year FROM proposals WHERE proposal_status in ('published', 'progress_update', 'progress_changes', 'closed')  ORDER BY proposal_year";

        $years = $this->db->query($sql);

        $sql = "SELECT DISTINCT applicant_country_code AS code ";
        $sql.= "FROM applicants, proposals_applicants,proposals ";
        $sql.= "WHERE fk_applicant_id=applicant_id AND fk_proposal_id=proposal_id ";
        $sql.= "ORDER BY applicant_country_code";

        $codes = $this->db->query($sql);

        $list = $this->config->item('countries');

        $countries = array();

        foreach($codes->result() as $country) {
            if (!empty($country->code)){
                $countries[$country->code] = $list[$country->code];
            }
        }

        asort($countries);

        foreach($countries as $code => $label) {
            $country = (object) array(
                'name' => $label,
                'regions' => array()
            );

            $countries[$code] = $country;
        }

        $sql = "SELECT applicant_id as id, applicant_legal_name as name ";
        $sql.= "FROM applicants ";
        $sql.= "WHERE applicant_legal_name != 'System' ";
        $sql.= "ORDER BY applicant_legal_name";
        $orgs = $this->db->query($sql);

        $data = array(
            'phases' => $phases->result(),
            'countries' => $countries,
            'years' => $years->result(),
            'orgs' => $orgs->result()
        );

        if(file_exists($filename)) unlink($filename);

        $f = file_put_contents($filename, $this->load->view('publish/filters',$data, true));
        if (!$f){
            $success = false;
        }

        return $success;
    }
	
	private function export_lists() {
	    $success = true;
		
		$query = $this->config->item('countries');
		
		$data = array(
			'countries' => array(),
			'topics' => array(),
			'types' => array(),
		);
		
		foreach($query as $code => $label) {
			$data['countries'][$code] = $label;
		}
		
		$sql = "SELECT DISTINCT id,name,grp FROM proposals_themes,proposals WHERE proposal_status in ('published', 'progress_update', 'progress_changes', 'closed') AND proposal_theme=id ORDER BY grp,name";
		$query = $this->db->query($sql);
		
		foreach($query->result() as $row) { 
			$data['types'][$row->id] = (object) array(
				'name' => $row->name,
				'group' => $row->grp
			);
		}
		
		$filename = $this->config->item('json_export_folder').'countries.json';
		
		if(file_exists($filename)) unlink($filename);
		
		$f = file_put_contents($filename, $this->load->view('publish/countries',$data, true));
		if (!$f){
		    $success = false;
        }

		return $success;
	}

    private function export_sitemap($mode) {
	    $success = true;

        $sql = "SELECT DISTINCT applicant_id ";
        $sql.= "FROM applicants, proposals_applicants, proposals ";
        $sql.= "WHERE fk_applicant_id=applicant_id AND fk_proposal_id=proposal_id AND proposal_status in ('published', 'progress_update', 'progress_changes', 'closed') ";

        $query = $this->db->query($sql);

        $data = array();

        foreach($query->result() as $row) {
            $data[] = $this->config->item('datahub_url').'beneficiary/'.$row->applicant_id;
        }

        $sql = "SELECT DISTINCT proposal_id ";
        $sql.= "FROM applicants, proposals_applicants, proposals ";
        $sql.= "WHERE fk_applicant_id=applicant_id AND fk_proposal_id=proposal_id AND proposal_status in ('published', 'progress_update', 'progress_changes', 'closed') ";

        $query = $this->db->query($sql);

        foreach($query->result() as $row) {
            $data[] = $this->config->item('datahub_url').'project/'.$row->proposal_id;
        }

        switch($mode) {
            case 'txt':
                // txt is default
            default:
                $filename = $this->config->item('json_export_folder')."sitemap.txt";

                $content = implode("\n", $data);

                break;
        }

        if(file_exists($filename)) unlink($filename);

        $f = file_put_contents($filename, $content);
        if (!$f){
            $success = false;
        }

        return $success;
    }

    private function export_stats(){
	    $success = true;

        //Commits per theme
	    $sql = "SELECT proposal_theme as id, name, count(proposal_id) as total";
	    $sql.= " FROM proposals, proposals_themes";
        $sql.= " WHERE id=proposal_theme AND proposal_status in ('published', 'progress_update', 'progress_changes', 'closed')";
        $sql.= " GROUP BY proposal_theme";

        $query = $this->db->query($sql);
        $data['commits_per_theme'] = $query->result();

        //Commits per applicant type
        $sql = "SELECT id, name, count(proposal_id) as total";
        $sql.= " FROM proposals, proposals_applicants, applicants, applicants_types";
        $sql.= " WHERE fk_proposal_id = proposal_id AND fk_applicant_id = applicant_id AND id = applicant_type";
        $sql.= " AND proposal_status in ('published', 'progress_update', 'progress_changes', 'closed')";
        $sql.= " GROUP BY applicant_type";

        $query = $this->db->query($sql);
        $result = $query->result();
        $data['commits_per_applicant_type'] = $result;

        //Commits progress
        $sql = "SELECT proposal_completion as progress, count(proposal_id) as total";
        $sql.= " FROM proposals";
        $sql.= " WHERE proposal_status in ('published', 'progress_update', 'progress_changes', 'closed')";
        $sql.= " GROUP BY proposal_completion";
        $sql.= " ORDER BY proposal_completion";

        $query = $this->db->query($sql);
        $results = $query->result();

        $commits = array();
        $progress = array("0", "25", "50", "75", "100");
        foreach ($progress as $prog){
            $idx = array_search($prog, array_column($results, 'progress'));
            if ($idx === false){
                $commit = (object)array(
                    "progress" => $prog,
                    "total" => 0
                );
            } else {
                $commit = $results[$idx];
            }
            array_push($commits, $commit);
        }
        $data['commits_progress'] = $commits;

        //Commits progress per theme
        $sql = "SELECT proposal_theme as id, name,";
        $sql.= ' count(case when proposal_completion = 0 then proposal_completion end) as p0,';
        $sql.= ' count(case when proposal_completion = 25 then proposal_completion end) as p25,';
        $sql.= ' count(case when proposal_completion = 50 then proposal_completion end) as p50,';
        $sql.= ' count(case when proposal_completion = 75 then proposal_completion end) as p75,';
        $sql.= ' count(case when proposal_completion = 100 then proposal_completion end) as p100';
        $sql.= " FROM proposals, proposals_themes";
        $sql.= " WHERE id=proposal_theme AND proposal_status in ('published', 'progress_update', 'progress_changes', 'closed')";
        $sql.= " GROUP BY proposal_theme";
        $sql.= " ORDER BY proposal_theme";
        $query = $this->db->query($sql);
        $data['commits_progress_per_theme'] = $query->result();

        //Commits progress per organisation type
        $sql = "SELECT applicant_type as id, name,";
        $sql.= ' count(case when proposal_completion = 0 then proposal_completion end) as p0,';
        $sql.= ' count(case when proposal_completion = 25 then proposal_completion end) as p25,';
        $sql.= ' count(case when proposal_completion = 50 then proposal_completion end) as p50,';
        $sql.= ' count(case when proposal_completion = 75 then proposal_completion end) as p75,';
        $sql.= ' count(case when proposal_completion = 100 then proposal_completion end) as p100';
        $sql.= " FROM proposals, proposals_applicants, applicants, applicants_types";
        $sql.= " WHERE fk_proposal_id = proposal_id AND fk_applicant_id = applicant_id AND id = applicant_type";
        $sql.= " AND proposal_status in ('published', 'progress_update', 'progress_changes', 'closed')";
        $sql.= " GROUP BY applicant_type";
        $sql.= " ORDER BY applicant_type";
        $query = $this->db->query($sql);
        $data['commits_progress_per_applicant_type'] = $query->result();

        //Commits per theme per year
        $sql = "SELECT distinct(proposal_year) as year FROM proposals ORDER BY proposal_year";
        $query = $this->db->query($sql);
        $years = $query->result_array();
        $finalYears = array();

        $sql = "SELECT proposal_theme as id, name,";
        $size = sizeof($years);
        for ($i = 0; $i < $size; $i++){
            array_push($finalYears, $years[$i]['year']);
            $sql.= "count(case when proposal_year = ".$years[$i]['year']." then proposal_year end) as y".$years[$i]['year'];
            if ($i < ($size - 1)){
                $sql.=',';
            }
        }
        $sql.= " FROM proposals, proposals_themes";
        $sql.= " WHERE id = proposal_theme";
        $sql.= " AND proposal_status in ('published', 'progress_update', 'progress_changes', 'closed')";
        $sql.= " GROUP BY proposal_theme, name";
        $sql.= " ORDER BY proposal_theme";
        $query = $this->db->query($sql);
        $results = $query->result();

        $categories = array();
        foreach ($results as $result){
            array_push($categories, $result->name);
        }

        $data['commits_per_theme_per_year'] = (object) array(
            'years' => $finalYears,
            'categories' => $categories,
            'data' => $results
        );

        //Commits per organisation type per year
        $sql = "SELECT id, name,";
        for ($i = 0; $i < $size; $i++){
            $sql.= "count(case when proposal_year = ".$years[$i]['year']." then proposal_year end) as y".$years[$i]['year'];
            if ($i < ($size - 1)){
                $sql.=',';
            }
        }
        $sql.= " FROM proposals, proposals_applicants, applicants, applicants_types";
        $sql.= " WHERE fk_proposal_id = proposal_id AND fk_applicant_id = applicant_id AND id = applicant_type";
        $sql.= " AND proposal_status in ('published', 'progress_update', 'progress_changes', 'closed')";
        $sql.= " GROUP BY applicant_type";
        $sql.= " ORDER BY applicant_type";
        $query = $this->db->query($sql);
        $results = $query->result();

        $categories = array();
        foreach ($results as $result){
            array_push($categories, $result->name);
        }

        $data['commits_per_applicant_type_per_year'] = (object) array(
            'years' => $finalYears,
            'categories' => $categories,
            'data' => $results
        );


        $filename = $this->config->item('json_export_folder')."statistics.json";
        if(file_exists($filename)) unlink($filename);

        $f = file_put_contents($filename, json_encode($data));
        if (!$f){
            $success = false;
        }

        return $success;
    }

    private function export_themes(){
	    $success = true;

        $query = $this->db->get('proposals_themes');
        $themes = $query->result();

        foreach ($themes as $theme){
            $src = dirname(__FILE__, 3).'/assets/images/ooc/'.$theme->id.'.png';
            $target = $this->config->item('img_export_folder').$theme->id.'.png';
            $f = copy($src, $target);
            if (!$f){
                $success = false;
            }
        }

        return $success;
    }

	private function save_configs_to_json($fields){
        $this->load->helper('file');

        $fields['scheduler_expression'] = json_decode($fields['scheduler_expression']);

        if ( ! write_file('./publish_config.json', json_encode($fields)) ) {
            return false;
        };

        return true;
    }
}
