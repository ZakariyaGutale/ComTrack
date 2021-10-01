<!--
    Copyright 2021 European Commission
    
    Licensed under the EUPL, Version 1.2 only (the "Licence");
    You may not use this work except in compliance with the Licence.
    
    You may obtain a copy of the Licence at:
        https://joinup.ec.europa.eu/software/page/eupl5
    
    Unless required by applicable law or agreed to in writing, software 
    distributed under the Licence is distributed on an "AS IS" basis, 
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either 
    express or implied.
    
    See the Licence for the specific language governing permissions 
    and limitations under the Licence.
-->
<?php if (!defined('APP_CALL')) exit('No direct script access allowed');
$config = array(
	'title' => 'OOC Map Viewer',	// Title diplayed by the data hub
	'description' => '',
	'mode' => '7',		// Default mode (see modes). 7 is all modes 
	'statInit' => 0,									// optional. a graph index to display on openeing (1-3. 0 is none)
	'theme' => '',										// alternate theme (blank is default)
	'mobile' => false,									// is this mobile usage ?
	'allowFS' => 0,										// displays 'full screen' link
	'height' => 1045,									// default height
	'width' => 240,										// default width
	'lat' => 0, 										// map default center point latitude (Y axis) (in degrees)
	'lng' => 0,											// map default center point longitude (X axis) (in degrees)
	'host' => 'http://www.ooc-datahub.com',		// the base url
	'beneficiary' => 0,									// optional. a valid beneciary id to focus on on opening. (0 is none)
	'project' => 0,										// optional. a valid project id to focus on on opening. (0 is none)
	'themes' => array(
		'blue',
		'grey',
		'green',
		'orange',
        'jazzberry'
	),
	'piwikEnabled' => 0,
	'piwikHost' => '',
	'piwikId' => 0,
    'disclaimer' => '<div class=\'col-md-12\'><h1>Disclaimer</h1><hr><div>The designations employed and the presentation of material on this map do not imply the expression of any opinion whatsoever on the part of the European Union concerning the legal status of any country, territory, city or area or of its authorities, or concerning the delimitation of its frontiers or boundaries. Kosovo*: This designation is without prejudice to positions on status, and is in line with UNSCR 1244/1999 and the ICJ Opinion on the Kosovo declaration of independence. Palestine*: This designation shall not be construed as recognition of a State of Palestine and is without prejudice to the individual positions of the Member States on this issue.</div></div>'
); 
