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
<div class="container main-screen">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <h2>My Profile</h2>
            <div class="data-display user-general">
                <p><?php echo $user->user_firstname.' '.$user->user_name ?></p>
                <?php
                    if ($user->user_function != '' && $user->user_function != '0'){
                ?>
                    <p><?php echo $user->user_function ?></p>
                <?php } ?>
            </div>
            <?php
            if ($user->user_role == 'host'){
                ?>
                <div class="user-is-host">
                    <p><i class="fas fa-user-cog"></i>Host privileges</p>
                </div>
                <?php
            }
            ?>
            <div class="data-display user-contacts">
                <?php if ($user->user_office != '' && $user->user_office != '0') {?>
                    <p><i class="fas fa-building"></i><?php echo $user->user_office ?></p>
                <?php }
                    if ($user->user_phone != '' && $user->user_phone != '0') {
                ?>
                <p><i class="fas fa-phone"></i><span class="phone-span"></span></p>
                <?php }
                    if ($user->user_email != '' && $user->user_email != '0') {
                ?>
                <p><i class="fas fa-envelope"></i><?php echo $user->user_email ?></p>
                <?php } ?>
            </div>
            <button class="btn btn-primary btn-edit" type="submit">Edit</button>
        </div>
        <div class="col-md-3"></div>
    </div>
</div>
<script>
    window.cookieconsent.initialise({
        "palette": {
            "popup": {
                "background": "#252e39"
            },
            "button": {
                "background": "#14a7d0"
            }
        },
        "theme": "classic",
        "content": {
            "message": "By using our site, you acknowledge that you have read and agreed our Cookies and Data Policies.",
            "dismiss": '<i class="fas fa-times"></i>',
            "link": "Learn more",
            "href": "<?php
                if ($this->auth->get_role() == 'host' ){
                    echo site_url('hosts/policy');
                } else {
                    echo site_url('commitments/policy');
                }
            ?>"
        },
        "cookie": {
            "name": "<?php echo $this->config->item('cookie_prefix').$this->config->item('cookie_consent') ?>",
            "domain": "<?php echo $this->config->item('cookie_domain') ?>"
        }
    });

    jQuery(document).ready(function(){
        <?php if ($user->user_phone != '' && $user->user_phone != 0) { ?>
            var phoneNr = new libphonenumber.parsePhoneNumber('<?php echo $user->user_phone ?>');
            $('.phone-span').text(phoneNr.formatInternational());
        <?php } ?>
        $('button').click(function(){
            window.location = '<?php echo $edit_profile ?>';
        });
    });
</script>
