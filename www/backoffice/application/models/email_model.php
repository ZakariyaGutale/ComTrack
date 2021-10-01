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

class Email_model extends CI_Model
{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->library('phpmailer_lib');
    }

    public function sendEmail($uemail, $subject, $body)
    {
        // PHPMailer object
        $mail = $this->phpmailer_lib->load();

        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = $this->config->item('mail_host');
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->item('mail_user');
        $mail->Password = $this->config->item('mail_pass');
        $mail->SMTPSecure = $this->config->item('mail_protocol');
        $mail->Port = $this->config->item('mail_port');


        $mail->setFrom($this->config->item('email_from'),$this->config->item('email_from_name'));

        // Add a recipient
        $mail->addAddress($uemail);


        // Email subject
        $mail->Subject = $subject;

        // Set email format to HTML
        $mail->isHTML(true);

        // Email body content
        $mail->Body = $body;

        // Send email
        if(!$mail->send()){
            error_log('Error sending email: '.$mail->ErrorInfo);
        }
    }
}
