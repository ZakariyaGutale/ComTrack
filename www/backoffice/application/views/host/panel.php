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
<div class="container">
    <div class="row user-info">
        <div class="col-md-3"></div>
        <div class="col-md-9 text-right">
            <span class="text-highlight"><?php echo $this->auth->get_complete_name() ?></span> of <span class="text-highlight"><?php echo $this->auth->get_organisation_name() ?></span>
            <span class="dropdown">
                <i class="fas fa-cog dropdown-toggle user-panel-cog" data-toggle="dropdown" role="button"></i>
                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                    <li><a href="/hosts/user_profile">My profile</a></li>
                    <li><a href="/hosts/organisation_profile">My organisation</a></li>
                    <li><a href="/hosts/settings">Settings</a></li>
                    <li><a href="/hosts/policy" target="_blank">Cookies & Data Policy</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a class="user-panel-sign-out" href="/hosts/logout" onclick="return signOutEvt"><b>Sign out</b></a></li>
                </ul>
            </span>
        </div>
    </div>
</div>
<script>
    function signOutEvt(){
        OOC.removeFromLocalStorage('ooc-dashboard');
        OOC.removeDataTablesStorage('all');
        return true;
    }
</script>
