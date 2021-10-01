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

class Publish extends CI_Controller {

	function __construct()
    {
        parent::__construct();
		
		$this->load->model('auth_model', 'auth');
		$this->load->model('publish_model', 'publish');
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
	
	public function do_publish($is_host = true){
	    $success = false;
        if ($this->check_access()){
            $success = $this->publish->do_publish(true);
        }
	    return $success;
    }

	public function publish_cron(){
	    if ($this->check_access()){
            $this->load->helper('file');

            $config = read_file('./publish_config.json');
            $config = json_decode($config);

            if ($config->scheduler_active ==  1){
                $cron = CronExpression::factory($config->scheduler_expression->{'scheduler-expression'});
                if ($cron->isDue()){
                    $this->publish->do_publish(false);
                }
            }
        }
    }
}
