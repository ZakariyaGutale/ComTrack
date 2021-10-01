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
<?php 
/*
	EASME SME Instrument data hub
	
	Main entry for data hub. 
	Supported modes (combine by adding vaues. ex: all modes = 1 + 2 + 4 = 7) : 
	1 - Map view
	2 - List view 
	4 - Stats view 
	
	Author: Michael Fallise (michael dot fallise at gmail dot com) on behalf of E.S.N.
	Version: 1.0
	
*/	
	define('APP_CALL', TRUE);
	
	include('config.php');
	
	$options = (object) array(
		'host' => $config['host'],
		'themes' => array_keys($config['themes']),
		'registerServices' => array(),
		'mapCenter' => array(floatval($config['lat']), floatval($config['lng'])),
		'filters' => (object) array(),
	);
	
	
	// get url parameters
	if(isset($_GET["lt"]) && is_numeric($_GET["lt"])) { $config['lat'] = 0 + $_GET["lt"]; }
	if(isset($_GET["lg"]) && is_numeric($_GET["lg"])) { $config['lng'] = 0 + $_GET["lg"]; }
	if(isset($_GET["w"]) && is_numeric($_GET["w"])) { $config['width'] = 0 + $_GET["w"]; }
	if(isset($_GET["h"]) && is_numeric($_GET["h"])) { $config['height'] = 0 + $_GET["h"]; }
	if(isset($_GET["mode"]) && is_numeric($_GET["mode"])) { $config['mode'] = 0 + $_GET["mode"]; }
	//if(isset($_GET["t"]) && is_numeric($_GET["t"])) { $config['title'] = $_GET["t"]; }
	if(isset($_GET["m"])) { $config['mobile'] = true; }
	if(isset($_GET["allowFS"])) { $config['allowFS'] = 0 + $_GET["allowFS"]; }
	if(isset($_GET["theme"])) { $config['theme'] = $_GET["theme"]; }
	if(isset($_GET["statInit"])) { $config['statInit'] = 0 + $_GET["statInit"]; }
	//if(isset($_GET["b"])) { $config['beneficiary'] = 0 + $_GET["b"]; }
	if(isset($_GET["p"])) { $config['partner'] = 0 + $_GET["p"]; }
	
	$filters = array();
	
	if(isset($_GET["fullText"])) { $filters['fullText'] = $_GET["fullText"]; }
	if(isset($_GET["p"])) { $filters['fullText'] = $_GET["p"]; }
	if(isset($_GET["b"])) { $filters['fullText'] = $_GET["b"]; }
	if(isset($_GET["cutoffs"])) { $filters['cutoffs'] = explode(',',$_GET["cutoffs"]); }
	if(isset($_GET["budget_min"])) { $filters['budgetMin'] = floatval($_GET["budget_min"]); }
	if(isset($_GET["budget_max"])) { $filters['budgetMax'] = floatval($_GET["budget_max"]); }
	if(isset($_GET["from_date"])) { $filters['from'] = $_GET["from_date"]; }
	if(isset($_GET["to_date"])) { $filters['to'] = $_GET["to_date"]; }
	if(isset($_GET["phases"])) { $filters['phases'] = explode(',',$_GET["phases"]); }
	if(isset($_GET["roles"])) { $filters['roles'] = explode(',',$_GET["roles"]); }
	if(isset($_GET["countries"])) { $filters['countries'] = explode(',',$_GET["countries"]); }
    if(isset($_GET["orgs"])) { $filters['orgs'] = explode(',',$_GET["orgs"]); }
	if(isset($_GET["regions"])) { $filters['regions'] = explode(',',$_GET["regions"]); }
	if(isset($_GET["topics"])) { $filters['topics'] = explode(',',str_replace('"','',$_GET["topics"])); }
	
	// force mode if mobile
	/*
	if($config['mobile']) { 
		$config['theme'] = 'mobile';
		$config['mode'] = 2;
	}
	*/
	$options->filters = (object) $filters;
	$options->config = (object) $config;
	$options->mode = $config['mode'];
	
	if($options->mode & 1) { 
		$options->registerServices[] = (object) array(
			'id' => 'map',
			'title' => 'Map of partners',
			'options' => (object) array()
		);
	}
	
	if($options->mode & 2) { 
		$options->registerServices[] = (object) array(
			'id' => 'list',
			'Partners & Project list',
			'options' => (object) array()
		);
	}
	
	if($options->mode & 4) { 
		$options->registerServices[] = (object) array(
			'id' => 'charts',
			'Statistics on Partners & Projects',
			'options' => (object) array()
		);
	}

?>
<!doctype html>
<html>
<head>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-159869006-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-159869006-1');
</script>

<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta content="width=device-width,height=device-height,initial-scale=1.0,maximum-scale=1.0, user-scalable=no" name="viewport">
<meta name="MobileOptimized" content="height">
<meta name="HandheldFriendly" content="true">
<meta http-equiv="cleartype" content="on">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta property="og:description" content="<?php echo $config['description'] ?>">
<meta name="description" content="<?php echo $config['description'] ?>">
<meta property="robots" content="follow,index">
<!--<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css"
   integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
   crossorigin=""/>-->
<title><?php echo $config['title'] ?></title>
<link rel="shortcut icon" href="<?php echo $config['host'] ?>/src/images/ooc/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="stylesheet" href="src/leaflet.css"/>
<link rel="stylesheet" href="/src/bootstrap.min.css" />
<link rel="stylesheet" href="<?php echo $config['host'] ?>/src/vanillaSelectBox/vanillaSelectBox.css">
<link rel="stylesheet" href="src/fontawesome/css/all.css"/>
<link rel="stylesheet" href="/src/wt/data-hub.css" />
    <link rel="stylesheet" href="/src/easme-tools.css" />
<link rel="stylesheet" href="/src/c3.min.css" />
<?php if($config['theme'] != '') { ?><link rel="stylesheet" href="<?php echo $config['host'] ?>/src/easme-tools-<?php echo $config['theme']; ?>.css" /><?php } ?>
<script type="text/javascript" src="<?php echo $config['host'] ?>/src/world-countries.geojson.js"></script>
<script type="text/javascript" src="<?php echo $config['host'] ?>/json/countries.json"></script>
<script type="text/javascript" src="<?php echo $config['host'] ?>/src/d3.min.js"></script>
<script type="text/javascript" src="<?php echo $config['host'] ?>/src/c3.min.js"></script>
<!--<script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"
   integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA=="
   crossorigin=""></script>-->
<script defer src="/src/leaflet.js" type="text/javascript"></script>
<!--<script defer src="/src/leaflet-providers.js" type="text/javascript"></script>-->
<script defer src="/src/leaflet.markercluster-src.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $config['host'] ?>/src/jquery.js"></script>
<script type="text/javascript" src="<?php echo $config['host'] ?>/src/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $config['host'] ?>/src/vanillaSelectBox/vanillaSelectBox.js"></script>
<script defer src="src/wt/load.js" type="text/javascript"></script>
<script src="src/wt/data-hub.js" type="text/javascript"></script>

</head>

<body>
<div id="header">
    <h1><?php echo $config['title'] ?></h1>
</div>
<div id="data-hub"></div>
<div id="footer">
	<div id="data-info"></div>
    <div id="credits">
        <span class="copyrights">
<!--        <a href="#" id="button-disclaimer">Disclaimer</a> | <a href="https://leafletjs.com/" title="Leaflet JS homepage" target="_blank">Leaflet</a> | <a href="https://www.openstreetmap.org/" title="OpenStreetMap project" target="_blank">OpenStreetMap</a> | <a href="https://carto.com/attributions" target="_blank">CARTO</a> , Credit: <a href="http://ec.europa.eu/eurostat/web/gisco/overview" target="_blank">EC-GISCO</a>, © EuroGeographics for the administrative boundaries</span>-->
        <a href="#" id="button-disclaimer">Disclaimer</a> | <a href="https://leafletjs.com/" title="Leaflet JS homepage" target="_blank">Leaflet</a> | <a href="https://www.openstreetmap.org/" title="OpenStreetMap project" target="_blank">&copy; OpenStreetMap contributors</a> , Credit: <a href="http://ec.europa.eu/eurostat/web/gisco/overview" target="_blank">EC-GISCO</a>, © EuroGeographics for the administrative boundaries</span>
        <!--<span class="easme">Powered by <a href="https://ec.europa.eu/easme" target="_blank">EASME</a> and <a href="https://ec.europa.eu/maritimeaffairs/" target="_blank">DG MARE</a></span>--->
        <span class="easme">Powered by the <a href="https://europa.eu/european-union/index_en" target="_blank">European Union</a></span>
    </div>
</div>
<div id="disclaimer"><?php echo $config['disclaimer'] ?></div>
<script>
	document.addEventListener('DOMContentLoaded',function() {
		
		var hub = new DataHub('data-hub', <?php echo json_encode($options); ?>);

        $wt.load(['/src/easme-map.js?v=1.1']);
        $wt.load(['/src/wt/data-hub-lists.js']);
        $wt.load(['/src/wt/data-hub-charts.js']);

		document.getElementById('button-disclaimer').addEventListener('click', hub.openDisclaimer.bind(hub));
	});
</script>
</body>
</html>
