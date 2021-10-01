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
<div id="publish">
	
    <div class="sheet">
    	<h3>publish commitments</h3>
        <br/>
        <ul>
        	<li>
            	<label>last update</label>
				<?php 
                    $filename = $this->config->item('json_export_folder').'list-1.json';
                    if (file_exists($filename)) {
                        $str = date ("d/m/Y H:i:s", filemtime($filename));
                    } else { 
                        $str = "no files found!";
                    }
                ?>
            	<span><?php echo $str ?></span>
			</li>
            <li>
	            <label>available commitments for publishing</label>
    	        <span><?php echo count($published)?> published, <?php echo count($archived)?> archived.</span>
            </li>
        </ul>
        <div class="actions ontop"><a href="#" id="button-export" class="button publish">publish</a></div>
    </div>
    
    <form class="form" method="post" action="<?php echo site_url('publish/save_config') ?>">
    	<h3>datahub options</h3>
        <br/>
   	  	<ul>
            <li>
                <label>Title</label>
                <input name="app_title" type="text" value="<?php echo $config['app_title'] ?>" data-validators="required" />
                <span>Enter here the title displayed in the map tool</span>
            </li>
            <li>
                <label>description</label>
                <textarea name="app_description"><?php echo $config['app_description'] ?></textarea>
                <span>Enter here the description used for social media</span>
            </li>
            <li>
                <label>host</label>
                <input name="app_host" type="text" value="<?php echo $config['app_host'] ?>" data-validators="required validate-url" />
                <span>Enter here the url of the map tool</span>
            </li>
        </ul>
        <div class="twin-col">
            <ul>
                <li>
                	<label>piwik Host</label>
                    <input name="app_piwik_host" type="text" value="<?php echo $config['app_piwik_host'] ?>" />
                    <span>If you are using Piwik, enter here the url of the Piwik server</span>
                </li>
                <li>
                	<label>piwik ID</label>
                    <input name="app_piwik_id" type="text" value="<?php echo $config['app_piwik_id'] ?>" />
                    <span>If you are using Piwik, enter here the id of your tracker</span>
                </li>
            </ul>
        </div>
        <div class="twin-col">
            <ul>
                <li>
                    <label>theme</label>
                    
                        <select name="app_theme">
                        <option value="" <?php echo $config['app_theme'] == '' ? 'selected': '' ?>>(default)</option>
                        <option value="grey" <?php echo $config['app_theme'] == 'grey' ? 'selected': '' ?>>grey</option>
                        <option value="green" <?php echo $config['app_theme'] == 'green' ? 'selected': '' ?>>green</option>
                        <option value="orange" <?php echo $config['app_theme'] == 'orange' ? 'selected': '' ?>>orange</option>
                        <option value="jazzberry" <?php echo $config['app_theme'] == 'jazzberry' ? 'selected': '' ?>>jazzberry</option>
                        </select>
                    <span>Select the them to be used by the map tool</span>
                </li>
                <li>
                    <label>mode</label>
                    
                        <select name="app_mode">
                        <option value="7" <?php echo $config['app_mode'] == 7 ? 'selected': '' ?>>All</option>
                        <option value="1" <?php echo $config['app_mode'] == 1 ? 'selected': '' ?>>Map only</option>
                        <option value="2" <?php echo $config['app_mode'] == 2 ? 'selected': '' ?>>List only</option>
                        <option value="4" <?php echo $config['app_mode'] == 4 ? 'selected': '' ?>>Stats only</option>
                        <option value="3" <?php echo $config['app_mode'] == 3 ? 'selected': '' ?>>All but stats</option>
                        <option value="5" <?php echo $config['app_mode'] == 5 ? 'selected': '' ?>>All but list</option>
                        <option value="6" <?php echo $config['app_mode'] == 6 ? 'selected': '' ?>>All but map</option>
                        </select>
                    <span>Select the modules used by the map tool</span>
                </li>
            </ul>
        </div>
        <ul>
            <li>
                <label>disclaimer</label>
                <textarea name="app_disclaimer"><?php echo $config['app_disclaimer'] ?></textarea>
                <span>Enter here the disclaimer used by the map tool</span>
            </li>
      	</ul>
 
    </form>
    <div class="actions">
		<a href="#" id="button-config" class="button save">update</a>
    </div>
</div>
<script>
	window.addEvent('domready', function(){
		
		var context = $('publish');
		var form = context.getElement('form');
		var validator = new Form.Validator(form);
		
		context.getElement('a.button.publish').addEvent('click', function(event) {
				
			if(window.confirm('You are about to update datahub data. Continue?')) {
				event.stop();
				event.target.disabled = true;
				
				request = new Request.JSON({
					url: '<?php echo site_url('publish/export_data') ?>',
					method: 'post',
					onComplete: function (data) {
					    console.log('complete');
						$('button-export').disabled = false;
						alert('Export completed');
					},
					onError: function() { 
						$('button-export').disabled = false;
						alert('Something wrong happened. Please contact the administrator.');
                        console.log('error');
					}
				});
				
				request.send();
			}
		});
		
		
		context.getElement('a.button.save').addEvent('click', function(event) {	
			event.stop();
			
			if(validator.validate()) { 
				form.submit();
			}
		});
	});
</script>
