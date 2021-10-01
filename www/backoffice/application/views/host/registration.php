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
<div id="registration">
	<div class="sheet">
	<h2>registration details</h2>
	<ul>
    	<li>
    		<label>full name</label><span><?php echo $user->user_firstname ?> <?php echo $user->user_name ?></span>
        </li>
        
        <li>
    		<label>organisation</label><span><?php echo $user->user_organisation ? $user->user_organisation: $user->user_prefs->tempOrganisation ?></span>
        </li>
        <li>
    		<label>office</label><span><?php echo $user->user_office ?></span>
        </li>
        <li>
    		<label>function</label><span><?php echo $user->user_function ?></span>
        </li>
        <li>
    		<label>phone</label><span><?php echo $user->user_phone ?></span>
        </li>
        <li>
    		<label>email</label><span><?php echo $user->user_email ?></span>
        </li>
        <li>
    		<label>submitted on</label><span><?php echo $user->user_created ?></span>
        </li>
    </ul>
</div>
<div>
	
	<form class="form"><h3>To which organisation should we link this user?</h3>
    <?php if($user->user_organisation)  { ?>
    <input type="hidden" name="applicant_id" value="<?php echo $user->user_organisation ?>" />
    <?php } else { ?>
    <ul class="list">
        
        <li>
        	<ul>
            	<li><input type="radio" checked name="applicant_id" value="0" /><label>create a <b>new organisation</b> "<?php echo $user->user_prefs->tempOrganisation ?>"</label></li>
                <?php 
					foreach($organisation_matching as $item) { 
						echo '<li>'.'<input type="radio" name="applicant_id" value="'.$item->applicant_id.'" />'.'<label>use existing "<b>'.$item->applicant_legal_name.'</b>"</label>'.'</li>';
					}
				?>
            </ul>
        </li>
        <li><input type="text" name="lookup" value="" placeholder="search for more..." /></li>
        <li>
        	<ul class="lookup">
            
            </ul>
        </li>
    </ul>
    </form>
    <?php } ?>
    <div class="actions">
        <a href="<?php echo site_url('hosts') ?>" class="button back">cancel</a>
        <?php if($user->user_activated) { ?>
        <a href="<?php echo site_url('hosts/block_poc/'.$user->user_id) ?>" class="button block">block account</a>
        <a href="<?php echo site_url('hosts/unblock_poc/'.$user->user_id) ?>" class="button unblock">unblock account</a>
        <?php } else { ?>
        <a href="#" class="button accept">accept registration</a>
        <?php } ?>
    </div>
</div>
</div>
<script>
	window.addEvent('domready', function() { 
		var context = $('registration');
		var form = context.getElement('.form');
		
		context.getElement('a.button.accept').addEvent('click', function(event){
			event.stop();
			var request = new Request.JSON({
				url: '<?php echo site_url('hosts/validate_registration/'.$user->user_id) ?>/' + form.getElement('input[name=applicant_id]:checked, input[type=hidden][name=applicant_id]').value,
				method: 'get',
				onSuccess: function(data) { 
					window.location = '<?php echo site_url('hosts') ?>';
				}
			});
			
			request.send();
		});
		
		context.getElement('input[name=lookup]').addEvent('keyup', function(event) { 
			var list = context.getElement('ul.lookup');
			
			if(event.target.value.length > 2) { 
				
				list.empty();
				
				var request = new Request.JSON( {
					url: '<?php echo site_url('hosts/organisation_lookup') ?>',
					method: 'post', 
					onSuccess: function(data) { 
						for(var i = 0; i < data.length; i++) { 
							var item = data[i];
							var li = new Element('li');
							var input = new Element('input', {type: 'radio', name: 'applicant_id', value: item.applicant_id});
							li.grab(input);
							var label = new Element('label', {html: 'use existing "<b>' + item.applicant_legal_name + '"</b>'});
							li.grab(label);
							list.grab(li);
						}
					}
				});
				
				request.send({data: 'str=' + event.target.value});
			}
		});
	
	});
</script>
