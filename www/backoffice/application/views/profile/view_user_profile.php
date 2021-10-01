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
<div class="container main-screen organisation-profile view-user-profile">
    <div class="row center-block error-msg hidden">
        <div class="col-md-3"></div>
        <div class="col-md-9 alert alert-danger table-layout" role="alert">
            <div class="table-row">
                <div class="table-cell table-cell-middle alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="table-cell">
                    <span></span>
                </div>
            </div>
        </div>
    </div>
    <?php
        if ($this->auth->get_role() == 'host'){
    ?>
            <div class="row">
                <div class="col-md-12 text-right">
                    <?php
                        if ($user->user_status == 'A'){
                            echo '<button class="btn btn-default btn-margin btn-reject">Reject</button>';
                            echo '<button class="btn btn-danger btn-delete">Delete</button>';
                        } else if ($user->user_status == 'P'){
                            echo '<button class="btn btn-default btn-margin btn-reject">Reject</button>';
                            echo '<button class="btn btn-success btn-approve">Approve</button>';
                        } else if ($user->user_status == 'D' || $user->user_status == 'I') {
                            echo '<button class="btn btn-success btn-approve">Approve</button>';
                        }
                    ?>
                </div>
            </div>

    <?php
        }
    ?>
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-3">
            <h2>User</h2>
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
        <div class="col-md-3">
            <h2>Organisation</h2>
            <?php if ($organisation == null) {?>
                <div class="alert alert-danger" role="alert">
                    <div class="table-row">
                        <div class="table-cell table-cell-middle alert-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="table-cell">
                            <span>There is no organisation associated to this user!</span>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
            <div class="data-display user-general">
                <p><?php echo $organisation->applicant_legal_name?></p>
                <?php
                foreach ($types as $type){
                    if ($type->id == $organisation->applicant_type){
                        echo '<p>'.$type->name.'</p>';
                    }
                }
                ?>
            </div>
            <div class="data-display user-contacts">
                <?php if ($organisation->applicant_web_page != '') { ?>
                    <p><i class="fas fa-globe-americas"></i><?php echo $organisation->applicant_web_page ?></p>
                <?php }
                if ($organisation->applicant_street != '') {
                    ?>
                    <p class="address">
                        <span><i class="fas fa-map-marker-alt"></i></span>
                        <span><?php echo $organisation->applicant_street ?> </span><br />
                        <span class="post-code"><?php echo $organisation->applicant_post_code.' '.$organisation->applicant_city ?></span>
                    </p>
                <?php } ?>
            </div>
            <?php } ?>
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
        var alert = $('.error-msg');
        <?php if ($user->user_phone != '' && $user->user_phone != 0) { ?>
            var phoneNr = new libphonenumber.parsePhoneNumber('<?php echo $user->user_phone ?>');
            $('.phone-span').text(phoneNr.formatInternational());
        <?php } ?>
        $('button.btn-edit').click(function(){
            window.location = '<?php echo site_url('hosts/'.$edit_profile.'/'.$user->user_id) ?>';
        });


        function approveRejectUsers(action){
            var url = '<?php echo site_url('hosts/activate_users')?>';
            if (action === 'reject'){
                url = '<?php echo site_url('hosts/deactivate_users')?>';
            } else if (action === 'delete'){
                url = '<?php echo site_url('hosts/delete_users')?>';
            }

            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: {ids: [<?php echo $user->user_id ?>]},
                success: function (data) {
                    if (data.success) {
                        window.location = '<?php echo site_url('hosts') ?>';
                    }
                },
                error: function (data) {
                    alert.removeClass('hidden');
                    var cell = $('.error-msg .alert-danger .table-cell').last()
                    var span = cell.children('span');
                    span.html(data.responseJSON.message);
                    hideAlert();
                }
            });
        }

        function hideAlert(){
            setTimeout(function(){
                alert.addClass('hidden');
            }, 5000);
        }


        $('button.btn-approve').click(function (e) {
            e.preventDefault();
            approveRejectUsers('approve');
        });

        $('button.btn-reject').click(function (e) {
            e.preventDefault();
            approveRejectUsers('reject');
        });

        $('button.btn-delete').click(function (e) {
            e.preventDefault();
            approveRejectUsers('delete');
        });
    });
</script>
