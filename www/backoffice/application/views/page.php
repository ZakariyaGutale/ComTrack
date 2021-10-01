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
 
	$this->load->view('elements/header');
	$this->load->view('elements/navigation');

    if (isset($completed) && $completed['isComplete'] == false){
	    $this->load->view('errors/incomplete_profile.php', $completed);
    }

	if($this->auth->get_role() != ''){
        $this->load->view($this->auth->get_role().'/panel');
    }

	if(isset($content)) $this->load->view($content);
	$this->load->view('elements/footer');
	
?>
