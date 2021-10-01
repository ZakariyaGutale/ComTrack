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

class Nuts_model extends CI_Model {
	
	var $_fetchMethod = '';
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		
		date_default_timezone_set($this->config->item('timezone'));
		
		// sets the function to use as service
		$this->_fetchMethod = 'Nuts_model::fetchGisko';
		//$this->_fetchMethod = 'Nuts_model::fetchNominatim';
		
    }
	function crt_out($str) { 
		//print_r('<div>'.$str.'</div>');
	}
	
	function fetch() { 
	
		ini_set('max_execution_time', 300);

		$script_start = microtime(true); 
		
		$report_url = $this->config->item('report_folder')."nuts-".date('Y-m-d_H-m-s').".log";
		
		$successRows = 0;
		$totalRows = 0;
		
		$report = fopen($report_url, "a");
		fwrite($report, date('Y-m-d H:i:s').' => starting reverse geocoding using '.$this->_fetchMethod.' ...'."\r\n");
		$this->crt_out($report, date('Y-m-d H:i:s').' => starting reverse geocoding using '.$this->_fetchMethod.' ...'."\r\n");
		$this->db->where("applicant_osm_status",2);
		//$this->db->limit(0);
		$applicants = $this->db->get("applicants");
		$log = '';
		$abort_loop = false;
		
		foreach($applicants->result() as $row) {
			$totalRows++;
			$log = 'testing applicant '.$row->applicant_legal_name.' ('.$row->applicant_osm_lat.','.$row->applicant_osm_lng.'): ';
			$this->crt_out('testing applicant '.$row->applicant_legal_name.' ('.$row->applicant_osm_lat.','.$row->applicant_osm_lng.'): ');
			$response = call_user_func_array($this->_fetchMethod, array($row->applicant_osm_lat, $row->applicant_osm_lng));
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
					
					break;
				case 1: 
					$log = '+ '.$log.'received ('.$response['region'].') ';
					$successRows++;
					
					$this->db->where("nuts_l1_name",$response['region']);
					$this->db->or_where("nuts_tags LIKE '%".addslashes($response['region'])."%'");
					$nuts_q = $this->db->get('nuts');
					
					$nuts = $nuts_q->row();
					
					if($nuts) {
						$fields = array(
							'applicant_osm_status' => 3,
							'applicant_nuts_1' => $nuts->nuts_code,
						);
						$this->db->where('applicant_id', $row->applicant_id);
						$this->db->update('applicants', $fields);
					}
					else { 
						$log = '=> '.$log.$response['target']."\r\n";
						$log = 'X '.$log.'could not find code!';
						
					}
					
					break;
				case 2:
					
					$this->db->where('nuts_code', $response['code']);
					$query = $this->db->get('nuts');
					$nuts = $query->row();
					if(!$nuts) { 
						$fields = array(
							'nuts_code' => $response["code"],
							'nuts_country' => $response["country"],
							'nuts_l1_name' => $response["region"],
						);
						
						$this->db->insert('nuts', $fields);
					}
					
					$fields = array(
						'applicant_osm_status' => 3,
						'applicant_nuts_1' => $response["code"],
						//'applicant_osm_lng' => $response['lng'],
					);
					$this->db->where('applicant_id', $row->applicant_id);
					$this->db->update('applicants', $fields);
					
					$log = '+ '.$log.'received ('.$response['code'].' - '.$response['region'].') ';
			}
			
			fwrite($report, $log."\r\n");
			$this->crt_out($report, $log."\r\n");
			if($abort_loop) break;
		} 
		
		$script_end = microtime(true);
		
		fwrite($report, date('Y-m-d H:i:s').': end of process. duration: '.round($script_end - $script_start, 2)."s.\r\n");
		$this->crt_out($report, date('Y-m-d H:i:s').': end of process. duration: '.round($script_end - $script_start, 2)."s.\r\n");
		fwrite($report, 'Success rate : '.($totalRows > 0 ? number_format(($successRows/($totalRows))*100,0)."%": "-" )."\r\n");
		$this->crt_out($report, 'Success rate : '.($totalRows > 0 ? number_format(($successRows/($totalRows))*100,0)."%": "-" )."\r\n");
		fwrite($report, "++++++++++++++++++++++++ end of report ++++++++++++++++++++++++++\r\n");
		$this->crt_out($report, "++++++++++++++++++++++++ end of report ++++++++++++++++++++++++++\r\n");
		
		fclose($report);
		
		echo '<<done geocoding. you may close this window>>\r\n';
		echo file_get_contents($report_url);
		
	}
	/*
	GISKO Servie
	*/
	function fetchGisko($lat, $lng) { 
	
		$response = array(
			//'country' => '',
			'code' => '',
			'region' => '',
			'country' => '',
			'target' => '',
			'success' => 0
		);
		
		$params = array(
			'inSR=4326',
			'outSR=3035',
			'geometries='.$lng.'%2C'.$lat,
			'f=json',
			'transformForward=true',
			'transformation=',
		);	
		
		$target = 'https://webgate.ec.europa.eu/estat/inspireec/gis/arcgis/rest/services/Utilities/Geometry/GeometryServer/project?'.join($params, "&");
		
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
			
			$target = 'https://webgate.ec.europa.eu/estat/inspireec/gis/arcgis/rest/services/nuts/Download_Level_2_2013/MapServer/identify?'.join($params, "&");
			
			$json = json_decode(@file_get_contents($target));
			
			if($json && $json->results && count($json->results) > 0) {
				$response['code'] = $json->results[0]->attributes->NUTS_ID;
				$response['region'] = $json->results[0]->attributes->NUTS_NAME;
				$response['country'] = $json->results[0]->attributes->CNTR_CODE;
				$response['success'] = 2;
			}
			else { 
				$response['target'] = $target;
				$response['success'] = -2;
			}
			
		} else {
			$response['target'] = $target;
			$response['success'] = -2;
		}
		
		return $response;
	}
	/*
	Resolves geocoding using Google Maps API
	*/
	function fetchGoogleMaps($lat, $lng) {
		
		$response = array(
			//'country' => '',
			'region' => '',
			'target' => '',
			'success' => 0
		);
		
		$params = array(
			'language=en',
			'latlng='.urlencode($lat.','.$lng),
			'key='.$this->config->item('geocode_api_key')
		);	
		
		$target = 'https://maps.googleapis.com/maps/api/geocode/json?'.join($params, "&");
		
		$json = json_decode(@file_get_contents($target));
		
		// Parsing
		if($json) {
			if($json->status != "OK") {
				if($json->status == "OVER_QUERY_LIMIT") { 
					$response['success'] = -1;
				} 
			}
			else {
				foreach($json->results as $result) { 
					
					foreach($result->address_components as $component) { 
						if(array_search('administrative_area_level_1',$component->types) !== FALSE) {
							$response['region'] = $component->long_name;
							break;
						}
					}
				}
				
				//$response['country'] = $json->results[0]->geometry->location->lat;
				//$response['region'] = $json->results[0]->geometry->location->lng; 	
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
	function fetchNominatim($lat, $lng) { 
		
		$response = array(
			//'country' => '',
			'region' => '',
			'target' => '',
			'success' => 0
		);
		
		$params = array(
			'format=json',
			'lat='.$lat,
			'lon='.$lng,
		);	
		
		$target = 'http://nominatim.openstreetmap.org/reverse?'.join($params, "&");
		//$json = json_decode(@file_get_contents($target));
		$json = (@file_get_contents($target));
		$this->crt_out($json);
		/*
		$xml = simplexml_load_file($target);
		
		$xml = simplexml_load_file($target);
		
		// XML Parsing
		if($xml !== false && count($xml->children()) > 0) {

			$response['lat'] = $xml->place['lat'];
			$response['lng'] = $xml->place['lon']; 	
			$response['success'] = 1;
			
			$successRows++;			
		}
		*/
		// no more than 1 request per second => see "Nominatim usage policy" 
		sleep(1);
		
		return $response;
	}
	
}
