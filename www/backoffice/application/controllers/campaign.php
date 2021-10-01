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

require_once (dirname(__FILE__, 3).'/vendor/autoload.php');
use Cron\CronExpression;

class Campaign extends CI_Controller {

	function __construct()
    {
        parent::__construct();
		
		$this->load->model('auth_model', 'auth');
        $this->load->model('easme_model', 'ooc');
    }

    private function check_access($secret = 0){
	    $status = false;
	    if (php_sapi_name() === 'cli' || $this->auth->is_logged('host')){
            $status = true;
        } else {
            redirect('/hosts/login', 'refresh');
        }

	    return $status;
    }
	
	public function campaign_cron(){
	    if ($this->check_access()){
	        $current_date = new DateTime('now');
	        $campaign = $this->ooc->get_running_campaign();
	        if (sizeof($campaign) > 0){
                $camp_start = date_create_from_format("d/m/Y", $campaign->camp_start);
                $camp_start->setTime(0,0,0);
                $camp_end = date_create_from_format("d/m/Y", $campaign->camp_end);
                $camp_end->setTime(0,0,0);
                if ($current_date >= $camp_start && $current_date < $camp_end && $campaign->existing_commitments == null){
                    $this->ooc->activate_campaign($campaign);
                }

                if ($current_date > $camp_end){
                    $this->ooc->close_campaign($campaign);
                }
            }
        }
    }
}
