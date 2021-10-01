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

// geocoding service 
$config['geocode_api_url'] = 'https://maps.googleapis.com/maps/api/geocode/json';
$config['geocode_api_keys'] = array(); 

$config['nuts_api_gps_to_etrs'] = '';
$config['nuts_api_etrs_to_nuts'] = '';
$config['nuts_range'] = array('AT','BE','BG','CH','CY','CZ','DE','DK','EL','ES','FI','FR','HR','HU','IE','IT','LT','LU','LV','MK','MT','NL','NO','PL','PT','RO','SE','SI','SK','TR','UK');

$config['fields'] = (object) array(

	'number' => (object) array(
		'name' => 'Commitment ID',
		'needle' => "commitment id",
		'table' => 'proposals', 
		'fieldname' => 'proposal_id',
		'type' => 'number',
		'requires' => array(),
	),
	'deadline' => (object) array(
		'name' => 'Commitment deadline',
		'needle' => "deadline",
		'table' => 'proposals', 
		'fieldname' => 'proposal_deadline',
		'type' => 'date',
		'requires' => array('number'),
	),
	'theme' => (object) array(
		'name' => 'Commitment Theme',
		'needle' => "theme",
		'table' => 'proposals', 
		'fieldname' => 'proposal_theme',
		'type' => '',
		'requires' => array('number'),
	),
	'year' => (object) array(
		'name' => 'Commitment year',
		'needle' => "year",
		'table' => 'proposals', 
		'fieldname' => 'proposal_year',
		'type' => 'number',
		'requires' => array('number'),
	),
	'title' => (object) array(
		'name' => 'Commitment Title',
		'needle' => "title",
		'table' => 'proposals', 
		'fieldname' => 'proposal_title',
		'type' => '',
		'requires' => array('number'),
	),
	'language' => (object) array(
		'name' => 'Language',
		'needle' => "language",
		'table' => 'proposals', 
		'fieldname' => 'proposal_language',
		'type' => '',
		'requires' => array('number'),
	),
	'announcer' => (object) array(
		'name' => 'Commitment announcer',
		'needle' => "announc",
		'table' => 'proposals', 
		'fieldname' => 'proposal_announcer',
		'type' => '',
		'requires' => array('number'),
	),
	'ontrak' => (object) array(
		'name' => 'Commitment on track',
		'needle' => "track",
		'table' => 'proposals', 
		'fieldname' => 'proposal_ontrack',
		'type' => 'number',
		'requires' => array('number'),
	),
	'area_active' => (object) array(
		'name' => 'Commitment is area protecting',
		'needle' => "protecting",
		'table' => 'proposals', 
		'fieldname' => 'proposal_area_active',
		'type' => 'number',
		'requires' => array('number'),
	),
	'area' => (object) array(
		'name' => 'Area protected',
		'needle' => "area",
		'table' => 'proposals', 
		'fieldname' => 'proposal_area',
		'type' => 'number',
		'requires' => array('number'),
	),
	'completion' => (object) array(
		'name' => 'Commitment completion',
		'needle' => "compl",
		'table' => 'proposals', 
		'fieldname' => 'proposal_completion',
		'type' => 'number',
		'requires' => array('number'),
	),
	'impact' => (object) array(
		'name' => 'Commitment impact',
		'needle' => "impact",
		'table' => 'proposals', 
		'fieldname' => 'proposal_impact',
		'type' => '',
		'requires' => array('number'),
	),
	'commit_status' => (object) array(
		'name' => 'Commitment Status',
		'needle' => "status",
		'table' => 'proposals', 
		'fieldname' => 'proposal_status',
		'type' => '',
		'requires' => array('number'),
	),
	'abstract' => (object) array(
		'name' => 'Commitment Description',
		'needle' => "abstract",
		'table' => 'proposals', 
		'fieldname' => 'proposal_abstract',
		'type' => '',
		'requires' => array('number'),
	),
	'website' => (object) array(
		'name' => 'Commitment website',
		'needle' => "website",
		'table' => 'proposals', 
		'fieldname' => 'proposal_website',
		'type' => '',
		'requires' => array('number'),
	),
	'budget' => (object) array(
		'name' => 'Commitment budget',
		'needle' => "budget",
		'table' => 'proposals', 
		'fieldname' => 'proposal_budget',
		'type' => 'number',
		'requires' => array('number'),
	),
	'applicant_pic' => (object) array(
		'name' => 'Organisation ID',
		'needle' => 'organisation id',
		'table' => 'applicants', 
		'fieldname' => 'applicant_id',
		'type' => 'number',
		'requires' => array(),
	),
	'applicant_type' => (object) array(
		'name' => 'Organisation Type',
		'needle' => "type",
		'table' => 'applicants', 
		'fieldname' => 'applicant_type',
		'type' => '',
		'transform' => (object) array(),
		'requires' => array('number', 'applicant_pic'),
	),
	'applicant_name' => (object) array(
		'name' => 'Organisation Name',
		'needle' => "name",
		'table' => 'applicants', 
		'fieldname' => 'applicant_legal_name',
		'type' => '',
		'transform' => (object) array(),
		'requires' => array('applicant_pic'),
	),
	'applicant_street' => (object) array(
		'name' => 'Organisation Street',
		'needle' => "street",
		'table' => 'applicants', 
		'fieldname' => 'applicant_street',
		'type' => '',
		'requires' => array('applicant_pic'),
	),
	'applicant_city' => (object) array(
		'name' => 'Organisation City',
		'needle' => "city",
		'table' => 'applicants', 
		'fieldname' => 'applicant_city',
		'type' => '',
		'requires' => array('applicant_pic'),
	),
	'applicant_country_code' => (object) array(
		'name' => 'Organisation Country code',
		'needle' => "country",
		'table' => 'applicants', 
		'fieldname' => 'applicant_country_code',
		'type' => '',
		'requires' => array('applicant_pic'),
	),
	'applicant_web_page' => (object) array(
		'name' => 'Organisation website',
		'needle' => "website",
		'table' => 'applicants', 
		'fieldname' => 'applicant_web_page',
		'type' => '',
		'requires' => array('applicant_pic'),
	),
	'site_name' => (object) array(
		'name' => 'Site name',
		'needle' => "site name",
		'table' => 'proposals_sites', 
		'fieldname' => 'name',
		'type' => '',
		'requires' => array('number'),
	),
	'site_lat' => (object) array(
		'name' => 'Site latitude',
		'needle' => "lat",
		'table' => 'proposals_sites', 
		'fieldname' => 'lat',
		'type' => '',
		'requires' => array('number','site_name'),
	),
	'site_lng' => (object) array(
		'name' => 'Site longitude',
		'needle' => "lng",
		'table' => 'proposals_sites', 
		'fieldname' => 'lng',
		'type' => '',
		'requires' => array('number','site_name'),
	),
);

