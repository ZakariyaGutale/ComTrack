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
<div class="container incomplete-container">
    <div class="row center-block">
        <div class="row center-block">
            <div class="col-md-12 alert alert-danger table-layout" role="alert">
                <div class="table-row">
                    <div class="table-cell table-cell-middle alert-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="table-cell">
                        <span> You won't be able to create or edit commitments until you fully complete your:<br>
                            <ul>
                                <?php
                                    if ($completed['personal'] == true){
                                        if ($this->auth->get_role() == 'poc'){
                                            echo '<li><a href="'.site_url('commitments/edit_profile').'">Personal profile</a></li>';
                                        } else {
                                            echo '<li><a href="'.site_url('hosts/edit_profile').'">Personal profile</a></li>';
                                        }
                                        
                                    }

                                    if ($completed['organisation'] == true){
                                        if ($this->auth->get_role() == 'poc'){
                                            echo '<li><a href="'.site_url('commitments/edit_organisation').'">Organisation profile</a></li>';
                                        } else {
                                            echo '<li><a href="'.site_url('hosts/edit_organisation').'">Organisation profile</a></li>';
                                        }
                                    }
                                ?>
                            </ul>

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
