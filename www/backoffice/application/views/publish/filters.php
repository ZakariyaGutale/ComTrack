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
<ul>
    <li>
    	<h2>Themes</h2>
        <div id="themes-btn-container" class="filter-btn-container">
            <a href="#" id="themes-btn-select-all" title="Select all">Select all</a>
            <span> | </span>
            <a href="#" id="themes-btn-clear" title="Clear all">Clear all</a>
            <span> Themes </span>
        </div>
        <ul>
        	<?php 
				
				$oldgroup = '';
				
				foreach($phases as $phase) { 
					if($oldgroup != $phase->grp) { 
						echo '<li><label><b>'.$phase->grp.'</b></label></li>';
						$oldgroup = $phase->grp;
					}
					echo '<li>'.'<input type="checkbox" class="phase-input" name="phase" value="'.$phase->id.'" checked><label class="checked">'.$phase->name.'</label></li>';
					
				}
			?>
        </ul>
    </li>
    <li>
        <form>
            <div>
                <label >
                    <input type="radio" name="exclusive-filter" id="exclusive-filter-country" value="country">
                    Countries
                </label>
                <label>
                    <input type="radio" name="exclusive-filter" id="exclusive-filter-org" value="organisation">
                    Organisations
                </label>
            </div>
        </form>
        <div class="filter-country">
            <div id="country-btn-container" class="filter-btn-container">
                <a href="#" id="country-btn-select-all" title="Select all">Select all</a>
                <span> | </span>
                <a href="#" id="country-btn-clear" title="Clear all">Clear all</a>
                <span> Countries </span>
            </div>
            <div class="form-group">
                <select class="country-combo" multiple>
                    <?php
                    foreach($countries as $code => $country) {
                        echo '<option value="'.$code.'">'.$country->name.' ('.$code.')</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="filter-organisation" style="display: none;">
            <div id="org-btn-container" class="filter-btn-container">
                <a href="#" id="org-btn-select-all" title="Select all">Select all</a>
                <span> | </span>
                <a href="#" id="org-btn-clear" title="Clear all">Clear all</a>
                <span> Organisations </span>
            </div>
            <div class="form-group">
                <select class="org-combo" multiple>
                    <?php
                        foreach ($orgs as $organisation){
                            echo '<option value="'.$organisation->id.'">'.htmlspecialchars($organisation->name, ENT_HTML5).'</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
    </li>
    <li>
    	<h2>Commitment Year</h2>
        <div id="year-btn-container" class="filter-btn-container">
            <a href="#" id="year-btn-select-all" title="Select all">Select all</a>
            <span> | </span>
            <a href="#" id="year-btn-clear" title="Clear all">Clear all</a>
            <span> Years </span>
        </div>
        <ul>
        	<?php 
				foreach($years as $item) { 
					echo '<li>'.'<input type="checkbox" class="cutoff-input" name="year" value="'.$item->year.'" checked><label class="checked">'.$item->year.'</label></li>';
				}
			?>
        </ul>
    </li>
</ul>
