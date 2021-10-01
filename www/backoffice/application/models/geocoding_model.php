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

class Geocoding_model extends CI_Model {
	
	var $_fetchMethod = '';
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		
		date_default_timezone_set($this->config->item('timezone'));
		
		// sets the function to use as service
		$this->_fetchMethod = 'Geocoding_model::fetchGoogleMaps';
    }
	
	function geocode() { 
	
		ini_set('max_execution_time', 300);

		$script_start = microtime(true); 
		
		$report_url = $this->config->item('report_folder')."geocoding-".date('Y-m-d_H-m-s').".log";
		
		$successRows = 0;
		$totalRows = 0;
		
		
		$this->db->where("applicant_osm_lat",0);
		$this->db->where("applicant_osm_lng",0);
		//$this->db->limit(10);
		$query = $this->db->get("applicants");
		
		$report = fopen($report_url, "a");
		
		fwrite($report, date('Y-m-d H:i:s').' => starting geocoding using '.$this->_fetchMethod.' ...'."\r\n");
		
		foreach($query->result() as $row) {
			 
			$time_start = microtime(true); 
			$totalRows++;
			$street = utf8_encode(str_replace("'", "",$row->applicant_street));
			$city = utf8_encode(str_replace("'", "",$row->applicant_city));
			
			$abort_loop = false;
			
			$log = 'testing applicant id '.$row->applicant_id.' (sent address: '.$street.'|'.$city.'|'.$row->applicant_country_code.'): ';
			
			$response = call_user_func_array($this->_fetchMethod, array($street, $city, $row->applicant_country_code));
			
			switch($response["success"]) { 
				case -2:
					$log = '! '.$log.'BAD REQUEST ('.$response['target'].')';
					break;
				case -1: 
					$log = '! '.$log.'OVER API REQUEST QUOTA';
					$abort_loop = true;
					break;
				case 0: 
					$log = 'X '.$log.'found nothing!';	
					
					$fields = array(
						'applicant_osm_status' => 1,
						'applicant_osm_lat' => 0,
						'applicant_osm_lng' => 0,
					);
					$this->db->where('applicant_id', $row->applicant_id);
					$this->db->update('applicants', $fields);
					
					break;
				case 1: 
					$log = '+ '.$log.'received ('.$response['lat'].','.$response['lng'].') ';
					$successRows++;
					
					$fields = array(
						'applicant_osm_status' => 2,
						'applicant_osm_lat' => $response['lat'],
						'applicant_osm_lng' => $response['lng'],
					);
					$this->db->where('applicant_id', $row->applicant_id);
					$this->db->update('applicants', $fields);
					
					break;
			}
			
			
			$time_end = microtime(true);
			
			$execution_time = ($time_end - $time_start);
			
			$log .= ' ('.round($execution_time,3).'s.)';
			
			fwrite($report, $log."\r\n");
			
			if($abort_loop) break;
		} 
		
		$script_end = microtime(true);
		
		fwrite($report, date('Y-m-d H:i:s').': end of process. duration: '.round($script_end - $script_start, 2)."s.\r\n");
		fwrite($report, 'Success rate : '.($totalRows > 0 ? number_format(($successRows/($totalRows))*100,0)."%": "-" )."\r\n");
		fwrite($report, "++++++++++++++++++++++++ end of report ++++++++++++++++++++++++++\r\n");
		
		fclose($report);
		
		echo 'done geocoding. you may close this window.';
		
	}
	/*
	Resolves geocoding using Google Maps API
	*/
	function fetchGoogleMaps($street, $city, $country) {
		
		$response = array(
			'lat' => 0,
			'lng' => 0,
			'target' => '',
			'success' => 0
		);
		
		$params = array(
			'address='.urlencode($street.'+'.$city.'+'.$country),
			'key='.$this->config->item('geocode_api_key')
		);	
		
		$target = ($this->config->item('geocode_api_url').'?'.join($params, "&"));
		
		$json = json_decode(@file_get_contents($target));
		
		// Parsing
		if($json) {
			if($json->status != "OK") {
				if($json->status == "OVER_QUERY_LIMIT") { 
					$response['success'] = -1;
				} 
			}
			else {
				
				$response['lat'] = $json->results[0]->geometry->location->lat;
				$response['lng'] = $json->results[0]->geometry->location->lng; 	
				$response['success'] = 1;
			}
		} else {
			$response['target'] = $target;
			$response['success'] = -2;
		}
		
		return $response;
	}
		
	/*
		Resolves geocoding using Nominatim API
	*/
	function fetchNominatim($street, $city, $country) {
		
		$response = array(
			'lat' => 0,
			'lng' => 0,
			'suceess' => 0
		);
		
		$params = array(
			'street='.$street,
			'city='.$city,
			'countrycodes='.$country,
			'format=xml'
		);	
		
		$target = $this->config->item('geocode_api_url').'?'.join($params, "&");
		$xml = simplexml_load_file($target);
		
		// XML Parsing
		if($xml !== false && count($xml->children()) > 0) {

			$response['lat'] = $xml->place['lat'];
			$response['lng'] = $xml->place['lon']; 	
			$response['success'] = 1;
			
			$successRows++;			
		}
		// no more than 1 request per second => see "Nominatim usage policy" 
		sleep(1);
		
		return $response;
	}
	
}
