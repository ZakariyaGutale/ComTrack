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
	$config = array(
		'title' => '',
		'host' => '',
		'id' => 0
	); 

	if(isset($_GET["id"]) && is_numeric($_GET["id"])) { $config['id'] = 0 + $_GET["id"]; }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $config['title'] ?></title>
<link rel="stylesheet" href="src/easme-tools.css" />
</head>

<body>
<?php include('json/beneficiaries/beneficiary-'.$config['id'].'.json') ?>
</body>
</html>
