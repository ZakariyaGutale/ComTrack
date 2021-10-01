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
?>
<?php echo "<?php if (!defined('APP_CALL')) exit('No direct script access allowed');"; ?>

$config = array(
	'title' => '<?php echo $config['app_title'] ?>',	// Title diplayed by the data hub
	'description' => '<?php echo $config['app_description'] ?>',
	'mode' => '<?php echo $config['app_mode'] ?>',		// Default mode (see modes). 7 is all modes 
	'statInit' => 0,									// optional. a graph index to display on openeing (1-3. 0 is none)
	'theme' => '<?php echo $config['app_theme'] ?>',										// alternate theme (blank is default)
	'mobile' => false,									// is this mobile usage ?
	'allowFS' => 0,										// displays 'full screen' link
	'height' => 1045,									// default height
	'width' => 240,										// default width
	'lat' => 0, 										// map default center point latitude (Y axis) (in degrees)
	'lng' => 0,											// map default center point longitude (X axis) (in degrees)
	'host' => '<?php echo $config['app_host'] ?>',		// the base url
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
    'disclaimer' => '<?php echo addslashes($config['app_disclaimer']) ?>'
); 
