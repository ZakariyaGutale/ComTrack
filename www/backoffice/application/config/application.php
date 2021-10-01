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
// backoffice name
$config['backoffice_version'] = getenv('BO_VERSION');
// instance code
$config['backoffice_name'] = 'OOC Platform';

// server timezone configuration
$config['timezone'] = getenv('BO_APP_TIMEZONE');
date_default_timezone_set($config['timezone']);


// App folders (should end with a slash )
$config['upload_folder'] = getenv('BO_APP_UPLOADS');
$config['report_folder'] = getenv('BO_APP_REPORTS');
$config['text_folder'] = getenv('BO_APP_UPLOADS'); //FIXME

// email settings
$config['email_from'] = getenv('BO_EMAIL_FROM');
$config['email_from_name'] = getenv('BO_EMAIL_FROM_NAME');
// Publish settings 
// number of records per json file
$config['json_items_per_file'] = getenv('DH_JSON_ITEMS_PER_FILE');
//
$config['json_export_folder'] = getenv('DH_JSON_EXPORT_FOLDER');
$config['img_export_folder'] = getenv('DH_IMAGE_EXPORT_FOLDER');
//
$config['maps_folder'] = getenv('DH_MAPS_FOLDER');
//
$config['datahub_url']	= getenv('DH_APP_URL');

$config['completion'] = array('not started', '25%', '50%', '75%', 'completed');

// password recovery expiration date (in minutes)
$config['reset_expiration']	= 240;


/* 
	reCaptcha settings
	see https://www.google.com/recaptcha/ for account settings
*/ 
$config['recaptcha_site_key'] = getenv('BO_CAPTCHA_SITE_KEY');
$config['recaptcha_secret_key'] = getenv('BO_CAPTCHA_SECRET_KEY');
$config['recaptcha_verify_url'] = 'https://www.google.com/recaptcha/api/siteverify';

// SMTP SETTINGS
$config['mail_host'] = getenv('MAIL_HOST');
$config['mail_user'] = getenv('MAIL_USER');
$config['mail_pass'] = getenv('MAIL_PASS');
$config['mail_port'] = getenv('MAIL_PORT');
$config['mail_protocol'] = getenv('MAIL_PROTOCOL');

/* 
	workflow definition
	
	object action
		label	string	used as label for the action button
		target	string	target status for this action. must be one defined in the workflow
		log		string	phrase used by history log. supports wildcards {user} and {host}
		comment	boolean	set to true to allow comment when performing action
		
*/
$config['workflow'] = array(
	'draft' => (object) array(
		'poc' => (object) array(
			'editable' => true,
			'label' => 'Updated',
			'icon' => 'fa-pencil-alt',
			'class' => 'primary',
			'is_public' => 1,
			'actions' => array(
				(object) array('label' => 'Submit', 'target' => 'submitted', 'comment' => true, 'class' => 'btn-success')
			),
		),
		'host' => (object) array(
			'editable' => false,
			'label' => 'Changes Requested',
            'icon' => 'fa-question-circle',
            'class' => 'warning',
            'is_public' => 1,
			'actions' => array(),
		),
	),
	'submitted' => (object) array(
		'poc' => (object) array(
			'editable' => false,
			'label' => 'Submitted',
            'icon' => 'fa-paper-plane',
            'class' => 'primary',
            'is_public' => 1,
			'actions' => array(),
		),
		'host' => (object) array(
			'editable' => false,
			'label' => '',
			'icon' => '',
            'class' => '',
            'is_public' => 1,
			'actions' => array(
                (object) array('label' => 'Approve', 'target' => 'approved', 'comment' => true, 'email' => 'approve', 'class' => 'btn-success'),
                (object) array('label' => 'Changes requested', 'target' => 'draft', 'comment' => true, 'email' => 'change', 'class' => 'btn-warning auto-width'),
                (object) array('label' => 'Reject', 'target' => 'rejected', 'comment' => true, 'email' => 'reject', 'class' => 'btn-danger')
			),
		)
	),
	'approved' => (object) array(
        'poc' => (object) array(
            'editable' => false,
            'label' => '',
            'icon' => '',
            'class' => '',
            'is_public' => 1,
            'actions' => array()
        ),
        'host' => (object) array(
            'editable' => false,
            'label' => 'Approved',
            'icon' => 'fa-check-circle',
            'class' => 'success',
            'is_public' => 1,
            'actions' => array(
                (object) array('label' => 'Schedule Publication', 'target' => 'scheduled', 'comment' => true, 'datepicker' => true, 'class' => 'btn-success schedule')
            )
        )
    ),
    'rejected' => (object) array(
        'poc' => (object) array(
            'editable' => false,
            'label' => '',
            'icon' => '',
            'class' => '',
            'is_public' => 1,
            'actions' => array()
        ),
        'host' => (object) array(
            'editable' => false,
            'label' => 'Rejected',
            'icon' => 'fa-times-circle',
            'class' => 'danger',
            'is_public' => 1,
            'actions' => array()
        )
    ),
    'scheduled' => (object) array(
        'poc' => (object) array(
            'editable' => false,
            'label' => '',
            'icon' => '',
            'class' => '',
            'is_public' => 1,
            'actions' => array()
        ),
        'host' => (object) array(
            'editable' => false,
            'label' => 'Publication Scheduled',
            'icon' => 'fa-bullhorn',
            'class' => 'primary',
            'is_public' => 1,
            'actions' => array(
                (object) array('label' => 'Manage Schedule', 'target' => 'scheduled', 'comment' => true, 'datepicker' => true, 'class' => 'btn-success schedule')
            )
        )
    ),
    'published' => (object) array(
        'poc' => (object) array(
            'editable' => true,
            'label' => 'Updated',
            'icon' => 'fa-bullhorn',
            'class' => 'primary',
            'is_public' => 1,
            'actions' => array()
        ),
        'host' => (object) array(
            'editable' => false,
            'label' => 'Published',
            'icon' => 'fa-bullhorn',
            'class' => 'success',
            'is_public' => 1,
            'actions' => array()
        )
    ),
    'progress_update' => (object) array(
        'poc' => (object) array(
            'editable' => true,
            'label' => 'Progress Updated',
            'icon' => 'fa-chart-line',
            'class' => 'primary',
            'is_public' => 1,
            'actions' => array()
        ),
        'host' => (object) array(
            'editable' => false,
            'label' => 'Progress Updated',
            'icon' => 'fa-chart-line',
            'class' => 'primary',
            'is_public' => 1,
            'actions' => array(
                (object) array('label' => 'Approve', 'target' => 'closed', 'comment' => true, 'email' => 'progress_approve', 'class' => 'btn-success'),
                (object) array('label' => 'Changes requested', 'target' => 'progress_changes', 'comment' => true, 'email' => 'progress_changes', 'class' => 'btn-warning auto-width')
            )
        )
    ),
    'progress_changes' => (object) array(
        'poc' => (object) array(
            'editable' => true,
            'label' => 'Progress Updated',
            'icon' => 'fa-chart-line',
            'class' => 'primary',
            'is_public' => 1,
            'actions' => array()
        ),
        'host' => (object) array(
            'editable' => false,
            'label' => 'Changes Requested',
            'icon' => 'fa-chart-line',
            'class' => 'warning',
            'is_public' => 1,
            'actions' => array()
        )
    ),
    'closed' => (object) array(
        'poc' => (object) array(
            'editable' => false,
            'label' => '',
            'icon' => '',
            'class' => '',
            'is_public' => 1,
            'actions' => array()
        ),
        'host' => (object) array(
            'editable' => false,
            'label' => 'Closed',
            'icon' => 'fa-check-circle',
            'class' => 'success',
            'is_public' => 1,
            'actions' => array()
        )
    )
);

$config['worflow_emails'] = array( 
	'approve' => (object) array(
		'title' => 'Your commitment has been approved', 
		'content' => 'commit/emails/approve'
	),
	'reject' => (object) array(
		'title' => 'Your commitment has been rejected',
		'content' => 'commit/emails/reject'
	),
    'change' => (object) array(
        'title' => 'A modification request was made for your commitment',
        'content' => 'commit/emails/changes'
    ),
    'progress_approve' => (object) array(
        'title' => 'Your commitment has been approved and closed',
        'content' => 'commit/emails/progress_approve'
    ),
    'progress_changes' => (object) array(
        'title' => 'A modification request was made for your commitment',
        'content' => 'commit/emails/progress_changes'
    ),
    'comment' => (object) array(
        'title' => 'Your commitment has been commented',
        'content' => 'commit/emails/comment'
    )
);



