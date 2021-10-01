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

	$config = $this->publish_model->get_config();
?>
<!DOCTYPE html>
<html lang="en">
<head>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-159869006-1"></script>
<!--<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-159869006-1');
</script>-->

<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta property="og:description" content="Our Oceans Conference - Back Office" />
<meta property="og:site_name" content="OOC" />
<meta property="fb:admins" content="USER_ID" />
<meta property="robots" content="follow,index" />
<link rel="shortcut icon" href="<?php echo base_url() ?>assets/images/ooc/favicon.ico" type="image/vnd.microsoft.icon" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta property="revisit-after" content="15 Days" />
<meta property="og:type" content="website" />
<meta name="date" content="26/05/2016" />
<meta name="description" content="Our Oceans Conference" />
<meta http-equiv="Content-Language" content="en" />
<meta name="Generator" content="Code Igniter" />
<meta name="reference" content="EASME" />
<meta name="creator" content="COMM/DG/UNIT" />
<meta name="keywords" content="OOC, European Commission, European Union, EU" />
<meta name="classification" content="25000" />
<meta property="og:title" content="<?php echo $this->config->item('backoffice_name') ?>" />
<title><?php echo $this->config->item('backoffice_name') ?></title>

<!-- INCLUDE BOOTSTRAP -->
<link type="text/css" rel="stylesheet" href="<?php echo base_url() ?>assets/bootstrap/css/bootstrap.min.css" media="all"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url() ?>assets/bootstrap/validator/dist/css/bootstrapValidator.min.css" media="all"/>
<link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,700,700i" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="<?php echo base_url() ?>assets/fontawesome/css/all.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url() ?>assets/flag/css/flag-icon.min.css" media="all" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url() ?>assets/select2/dist/css/select2.min.css" media="all" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url() ?>assets/select2/dist/css/select2-bootstrap.min.css" media="all" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url() ?>assets/cookieconsent/build/cookieconsent.min.css" media="all"/>
<link type="text/css" rel="stylesheet" href="<?php echo base_url() ?>assets/css/backoffice-boot.css?v=<?php echo $this->config->item('backoffice_version') ?>" media="all" />

<script language="javascript" src="<?php echo base_url() ?>/assets/js/polyfill.min.js"></script>
<script language="javascript" src="<?php echo base_url() ?>/assets/js/jquery.js"></script>
<script language="javascript" src="<?php echo base_url() ?>/assets/bootstrap/js/bootstrap.min.js"></script>
<script language="javascript" src="<?php echo base_url() ?>/assets/bootstrap/validator/dist/js/bootstrapValidator.min.js"></script>
<script language="javascript" src="<?php echo base_url() ?>/assets/select2/dist/js/select2.full.min.js"></script>
<script language="javascript" src="<?php echo base_url() ?>/assets/cookieconsent/build/cookieconsent.min.js"></script>
<script language="JavaScript" src="<?php echo base_url() ?>/assets/js/utils.js?v=<?php echo $this->config->item('backoffice_version') ?>"></script>

<?php
	if(isset($css)) 
	{ 
		foreach($css as $file) 
			echo '<link type="text/css" rel="stylesheet" href="'.base_url().'assets/'.$file.'" media="all" />'."\n";
	}
?>

<?php 
	if(isset($js)) 
	{ 
		foreach($js as $file) 
			echo '<script language="javascript" src="'.base_url().'assets/'.$file.'"></script>'."\n";
	}
?>
  <!-- HTML5 element support for IE6-8 -->
  <!--[if lt IE 9]>
    <script src="/easme/profiles/multisite_drupal_standard/themes/ec_resp/scripts/html5shiv.min.js"></script>
    <script src="/easme/profiles/multisite_drupal_standard/themes/ec_resp/scripts/respond.min.js"></script>
  <![endif]--> 
</head>
<body>
    <a id="top-page"></a>
    <div class="header" tabindex="-1">
        <div class="container-fluid">
            <div class="row vcenter">
                <div class="col-md-9">
                    <!--<a href="<?php /*echo $this->config->item('ooc_website_url') */?>"><img id="ooc-logo" src="<?php /*echo base_url() */?>assets/images/ooc/ooc-logo.png"/></a>-->
                    <?php
                        $isHost = true;
                        if ($this->auth->get_role() == 'poc'){
                            echo '<a href="'.site_url('commitments').'">';
                            $isHost = false;
                        } else if ($this->auth->get_role() == 'host') {
                            echo '<a href="'.site_url('hosts').'">';
                        } else {
                            $urlParts = explode('/', uri_string());
                            echo '<a href="'.site_url(site_url($urlParts[0])).'">';
                            if ($urlParts[0] !== 'hosts'){
                                $isHost = false;
                            }
                        }

                        echo '<img id="ooc-logo" src="'.base_url().'assets/images/ooc/ooc-logo.png"/></a>';
                        if ($isHost) {
                            echo '<span class="admin-title">ADMINISTRATION</span>';
                        }
                    ?>
                </div>
                <div class="col-md-3">
                    <div class="pull-right header-flag">
                        <?php
                            if ($config['host_conf_site'] != null){
                                echo '<a href="'.$config['host_conf_site'].'" target="_blank" title="Visit Our Ocean Conference official website"><span class="flag-container flag-icon-'.strtolower($config['host_flag']).'"></span></a>';
                            } else {
                                echo '<span class="flag-container flag-icon-'.strtolower($config['host_flag']).'"></span>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
