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
<div class="container main-screen organisation-profile">
    <div id="invite-colleague" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Invite a colleague</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form role="form">
                                <div class="form-group">
                                    <label for="name">Email</label>
                                    <input type="email" class="form-control" name="email">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-do-invite">Invite</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row center-block error-msg hidden">
        <div class="col-md-1"></div>
        <div class="col-md-11 alert alert-danger table-layout dynamic-alert" role="alert">
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
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-4">
            <h2><?php echo $title ?></h2>
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
                    <p class="web-address">
                        <span><i class="fas fa-globe-americas"></i></span>
                        <span><a class="url" href="<?php echo $organisation->applicant_web_page ?>" target="_blank"><?php echo $organisation->applicant_web_page ?></a></span>
                    </p>
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
            <button class="btn btn-primary btn-edit" type="submit">Edit</button>
        </div>
        <div class="col-md-7">
            <?php
            if ($invite){
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row" style="margin-bottom: 15px;">
                            <!--<div class="col-md-6"></div>-->
                            <div class="col-md-6">
                                <button class="btn btn-primary btn-invite" type="button" data-toggle="modal" data-target="#invite-colleague">Invite a colleague</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
            <?php
                $counter = 0;
                foreach ($organisation->pocs as $user){
                    if ($counter > 0 && $counter % 2 == 0) {
                        echo '</div>';
                        echo '<div class="row">';
                    }
                    $counter += 1;
            ?>
                <div class="col-md-6">
                    <div class="thumbnail text-center">
                        <?php
                            if ($this->auth->get_id() != $user->user_id){
                        ?>
                            <span class="user-btn delete-user" title="Delete user" data-user-id="<?php echo $user->user_id ?>" data-org-id="<?php echo $user->user_organisation ?>"><i class="far fa-times-circle"></i></span>
                        <?php
                            }
                            if ($user->user_role == 'poc'){
                        ?>
                            <i class="fas fa-user-circle"></i>
                        <?php
                            } else {
                        ?>
                            <span class="fa-stack fa-2x" title="Host">
                                <i class="fas fa-user-circle fa-stack-1x"></i>
                                <i class="fas fa-cog fa-stack-1x"></i>
                            </span>
                        <?php
                            }
                        ?>
                        <div class="caption">
                            <p class="name"><?php echo $user->user_firstname.' '.$user->user_name?></p>
                            <p><?php echo $user->user_function != '' && $user->user_function != '0' ? $user->user_function : '' ?></p>
                            <?php if ($user->user_phone != '' && $user->user_phone != '0') { ?>
                                <p><i class="fas fa-phone"></i><span class="phone-span"><?php echo $user->user_phone?></span></p>
                            <?php } ?>

                            <p><i class="fas fa-envelope"></i><a href="mailto:<?php echo $user->user_email ?>"><?php echo $user->user_email ?></a></p>
                        </div>
                    </div>
                </div>
            <?php }
                echo '</div>';
            ?>
            <!-- DELETED -->
            <?php
                if (sizeof($deleted_users) > 0) {
            ?>
            <div class="row">
                <div class="col-md-12 deleted-users-section"><i class="fas fa-caret-right"></i><span>Show deleted users</span></div>
                <div class="col-md-12 deleted-users-container">
                    <div class="row">
                        <?php
                            $counter = 0;
                            foreach ($deleted_users as $user){
                            if ($counter > 0 && $counter % 2 == 0) {
                                echo '</div>';
                                echo '<div class="row">';
                            }
                            $counter += 1;
                        ?>
                        <div class="col-md-6">
                            <div class="thumbnail text-center">
                            <?php
                                if ($this->auth->get_id() != $user->user_id){
                            ?>
                                <span class="user-btn approve-user" title="Approve user" data-user-id="<?php echo $user->user_id ?>" data-org-id="<?php echo $user->user_organisation ?>"><i class="fas fa-plus-circle"></i></span>
                            <?php
                                }
                                if ($user->user_role == 'poc'){
                            ?>
                                <i class="fas fa-user-circle"></i>
                            <?php
                                } else {
                            ?>
                                <span class="fa-stack fa-2x" title="Host">
                                    <i class="fas fa-user-circle fa-stack-1x"></i>
                                    <i class="fas fa-cog fa-stack-1x"></i>
                                </span>
                            <?php
                                }
                            ?>
                                <div class="caption">
                                    <p class="name"><?php echo $user->user_firstname.' '.$user->user_name?></p>
                                    <p><?php echo $user->user_function != '' && $user->user_function != '0' ? $user->user_function : '' ?></p>
                                    <?php if ($user->user_phone != '' && $user->user_phone != '0') { ?>
                                        <p><i class="fas fa-phone"></i><span class="phone-span"><?php echo $user->user_phone?></span></p>
                                    <?php } ?>

                                    <p><i class="fas fa-envelope"></i><a href="mailto:<?php echo $user->user_email ?>"><?php echo $user->user_email ?></a></p>
                                </div>
                            </div>
                        </div>
                    <?php }
                        echo '</div>';
                    ?>
                    </div> <!--end of the inner row-->
                </div>
            <!-- REJECTED -->
            <?php
                }
                if (sizeof($rejected_users) > 0 && $this->auth->get_role() == 'host'){
            ?>
            <div class="row">
                <div class="col-md-12 rejected-users-section"><i class="fas fa-caret-right"></i><span>Show rejected users</span></div>
                <div class="col-md-12 rejected-users-container">
                    <div class="row">
                        <?php
                            $counter = 0;
                            foreach ($rejected_users as $user){
                            if ($counter > 0 && $counter % 2 == 0) {
                                echo '</div>';
                                echo '<div class="row">';
                            }
                            $counter += 1;
                        ?>
                        <div class="col-md-6">
                            <div class="thumbnail text-center">
                            <?php
                                if ($this->auth->get_id() != $user->user_id){
                            ?>
                                <span class="user-btn approve-user" title="Approve user" data-user-id="<?php echo $user->user_id ?>" data-org-id="<?php echo $user->user_organisation ?>"><i class="fas fa-plus-circle"></i></span>
                            <?php
                                }
                                if ($user->user_role == 'poc'){
                            ?>
                                <i class="fas fa-user-circle"></i>
                            <?php
                                } else {
                            ?>
                                <span class="fa-stack fa-2x" title="Host">
                                    <i class="fas fa-user-circle fa-stack-1x"></i>
                                    <i class="fas fa-cog fa-stack-1x"></i>
                                </span>
                            <?php
                                }
                            ?>
                                <div class="caption">
                                    <p class="name"><?php echo $user->user_firstname.' '.$user->user_name?></p>
                                    <p><?php echo $user->user_function != '' && $user->user_function != '0' ? $user->user_function : '' ?></p>
                                    <?php if ($user->user_phone != '' && $user->user_phone != '0') { ?>
                                        <p><i class="fas fa-phone"></i><span class="phone-span"><?php echo $user->user_phone?></span></p>
                                    <?php } ?>

                                    <p><i class="fas fa-envelope"></i><a href="mailto:<?php echo $user->user_email ?>"><?php echo $user->user_email ?></a></p>
                                </div>
                            </div>
                        </div>
                    <?php }
                        echo '</div>';
                    ?>
                    </div> <!--end of the inner row-->
                </div>
            <?php
                }
            ?>
            </div>
        </div>
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
        var form = $('form');
        var alert =  $('.error-msg');
        <?php
            if ($this->auth->get_role() == 'poc' ){
                $url =  site_url('commitments/do_invite/');
                $delUrl = site_url('commitments/delete_user');
                $approveUrl = site_url('commitments/re_approve_user');
            } else {
                $url =  site_url('hosts/do_invite/');
                $delUrl = site_url('hosts/delete_user');
                $approveUrl = site_url('hosts/re_approve_user');
            }
            echo 'var url = \''.$url.'\';';
            echo 'var delUrl = \''.$delUrl.'\';';
            echo 'var approveUrl = \''.$approveUrl.'\';';
        ?>

        form.bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fas fa-check',
                invalid: 'fas fa-times',
                validating: 'fas fa-spinner'
            },
            fields: {
                email: {
                    validators: {
                        notEmpty: {
                            message: "Please provide your colleague's email address"
                        },
                        emailAddress: {
                            message: 'Please provide a valid email address'
                        }
                    }
                }
            }
        });
        var validator = form.data('bootstrapValidator');

        $('.phone-span').each(function(idx, el){
            var phoneNr = new libphonenumber.parsePhoneNumber($(this).text());
            $(this).text(phoneNr.formatInternational());
        });

        $('.btn-edit').click(function(){
            window.location = '<?php echo $edit_organisation ?>';
        });

        $('#invite-colleague').on('hide.bs.modal', function () {
            validator.resetField('email', true);
        });

        function submit(){
            form.bootstrapValidator('validate');
            if (form.data('bootstrapValidator').isValid() == true){
                $.ajax({
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    data: form.serializeObject(),
                    success: function (data) {
                        if (data.success) {
                            $('#invite-colleague').modal('hide');
                        }
                    }
                });
            }
        }

        OOC.registerEnterKey(form, submit);
        $('.btn-do-invite').click(function(){
            submit();
        });


        function hideAlert(type){
            setTimeout(function(){
                alert.addClass('hidden');
            }, 5000);
        }


        function deleteUser(userId, orgId){
            $.ajax({
                type: 'POST',
                url: delUrl,
                data: {
                    userId: userId,
                    orgId: orgId
                },
                success: function(data){
                    if (data.success){
                        window.location = data.url;
                    }
                },
                error: function(data){
                    alert.removeClass('hidden');
                    var cell = $('.error-msg .alert-danger .table-cell').last();
                    var span = cell.children('span');
                    span.html(data.responseJSON.message);
                    hideAlert('error');
                }
            })
        }

        $('.user-btn.delete-user').click(function(){
            var userId = $(this).attr('data-user-id');
            var orgId =  $(this).attr('data-org-id');
            deleteUser(userId, orgId);
        });

        $('.deleted-users-section').click(function(){
            var display = 'block';
            var icon = 'fa-caret-down';
            var prevIcon = 'fa-caret-right';
            var text = 'Hide deleted users';

            if ($('.deleted-users-container').css('display') === 'block'){
                display = 'none';
                icon = prevIcon;
                prevIcon = 'fa-caret-down';
                text = 'Show deleted users';
            }

            $('.deleted-users-container').css('display', display);
            $('.deleted-users-section i').addClass(icon);
            $('.deleted-users-section i').removeClass(prevIcon);
            $('.deleted-users-section span').text(text);
        });

        function approveUser(userId, orgId){
            $.ajax({
                type: 'POST',
                url: approveUrl,
                data: {
                    userId: userId,
                    orgId: orgId
                },
                success: function(data){
                    if (data.success){
                        OOC.setUserSectionState($('.deleted-users-container').css('display'), $('.rejected-users-container').css('display'));
                        window.location = data.url;
                    }
                },
                error: function(data){
                    alert.removeClass('hidden');
                    var cell = $('.error-msg .alert-danger .table-cell').last();
                    var span = cell.children('span');
                    span.html(data.responseJSON.message);
                    hideAlert('error');
                }
            })
        }

        $('.user-btn.approve-user').click(function(){
            var userId = $(this).attr('data-user-id');
            var orgId =  $(this).attr('data-org-id');
            approveUser(userId, orgId);
        });

        $('.rejected-users-section').click(function(){
            var display = 'block';
            var icon = 'fa-caret-down';
            var prevIcon = 'fa-caret-right';
            var text = 'Hide rejected users';

            if ($('.rejected-users-container').css('display') === 'block'){
                display = 'none';
                icon = prevIcon;
                prevIcon = 'fa-caret-down';
                text = 'Show rejected users';
            }

            $('.rejected-users-container').css('display', display);
            $('.rejected-users-section i').addClass(icon);
            $('.rejected-users-section i').removeClass(prevIcon);
            $('.rejected-users-section span').text(text);
        });

        function init(){
            var sectionState = OOC.getUserSectionState();
            OOC.removeUserSectionState();
            if (sectionState[0] === 'block'){
                $('.deleted-users-section').trigger('click');
            }
            if (sectionState[1] === 'block'){
                $('.rejected-users-section').trigger('click');
            }
        }

        init();
    });
</script>
