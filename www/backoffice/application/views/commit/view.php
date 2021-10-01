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

setlocale(LC_MONETARY, 'en_US.UTF-8');
$completions = $this->config->item('completions');
$statusObj = (object)array(
    'draft' => (object)array(
        'class' => 'label-info',
        'text' => 'Draft'
    ),
    'submitted' => (object)array(
        'class' => 'label-info',
        'text' => 'Submitted'
    ),
    'approved' => (object)array(
        'class' => 'label-success',
        'text' => 'Submitted'
    ),
    'rejected' => (object)array(
        'class' => 'label-danger',
        'text' => 'Rejected'
    ),
    'scheduled' => (object)array(
        'class' => 'label-info',
        'text' => 'Scheduled'
    ),
    'published' => (object)array(
        'class' => 'label-success',
        'text' => 'Published'
    ),
    'progress_update' => (object)array(
        'class' => 'label-info',
        'text' => 'Progress Updated'
    ),
    'progress_changes' => (object)array(
        'class' => 'label-warning',
        'text' => 'Changes Requested'
    ),
    'closed' => (object)array(
        'class' => 'label-success',
        'text' => 'Closed'
    )
);
?>
<div id="comments-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Comments</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form role="form">
                            <div class="form-group comment-area">
                                <textarea class="form-control" rows="10" name="comment"></textarea>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="checkbox hidden">
                                <input type="checkbox" id="check-is-public" name="is_public" checked/>
                                <label class="check-label" for="check-is-public">Is publicly visible</label>
                            </div>
                            <div class="form-group with-addon <?php echo $commitment->proposal_status == 'approved' || $commitment->proposal_status == 'scheduled' ? '' : 'hidden' ?>">
                                <label for="proposal_deadline">Under embargo until</label>
                                <div class='input-group date' id='schedule_date'>
                                    <input type='text' class="form-control" name="schedule_date"/>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <input type="hidden" name="proposal_status" value=""/>
                            <input type="hidden" name="email" value=""/>
                            <input type="hidden" name="is_note" value=""/>
                        </form>
                    </div>
                </div>
                <div class="row center-block error-msg hidden">
                    <div class="col-md-12 alert alert-danger table-layout modal-alert" role="alert">
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-do-submit"><i
                            class="fas fa-spinner fa-spin hidden"></i> <span>Submit</span></button>
            </div>
        </div>
    </div>
</div>
<div class="container main-screen commitments">
    <div class="row top-row submitted-msg hidden">
        <div class="col-md-12 alert alert-info table-layout" role="alert">
            <div class="table-row">
                <div class="table-cell table-cell-middle alert-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="table-cell">
                    <span>Thank you for submitting your commitment. The host will analyse it within 48 hours. You will be notified by email when the host takes an action on your commitment.</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row top-row">
        <div class="col-md-7">
            <h2><?php echo $title ?></h2>
        </div>
        <div class="col-md-5 text-right flow-btn">
            <?php
            if ((isset($completed) && $completed['isComplete']) || !isset($completed)) {
                foreach ($workflow->actions as $action) {
                    if (($commitment->proposal_status == 'progress_update' && $commitment->proposal_completion == 100) || $commitment->proposal_status != 'progress_update') {
                        echo '<button class="flow btn ' . $action->class . '" data-toggle="modal" data-target="#comments-modal" data-ooc-target="' . $action->target . '" data-is-note="false" data-comment="' . $action->comment . '" data-datepicker="' . (property_exists($action, 'datepicker') ? $action->datepicker : false) . '" data-email="' . (property_exists($action, 'email') ? $action->email : '') . '">' . $action->label . '</button>';
                    }
                }
            }
            ?>
        </div>
    </div>
    <div class="row contents-top-row">
        <div class="col-md-7 commit-content">
            <div class="data-display commitment-title">
                <span><?php echo $commitment->proposal_title ?></span>
                <span class="label <?php echo $statusObj->{$commitment->proposal_status}->class; ?>"><?php echo $statusObj->{$commitment->proposal_status}->text ?></span>
            </div>
            <?php
            if ($this->auth->get_role() == 'host') { ?>
                <div class="commit-org">
                    <?php echo $organisation->applicant_legal_name . ', ' . $organisation->country . ' - ' . $organisation->type->name ?>
                </div>
            <?php } ?>
            <div class="row commit-icon-summary">
                <div class="col-md-4 text-center">
                    <?php
                    echo '<div><img src="/assets/images/ooc/' . $commitment->proposal_theme->id . '.png" style="height: 50px; width: 50px;" /></div>';
                    echo '<div class="commit-icon-text">' . $commitment->proposal_theme->name . '</div>';
                    ?>
                </div>
                <div class="col-md-4 text-center">
                    <?php
                    echo '<div><i class="far fa-money-bill-alt"></i></div>';
                    echo '<div class="commit-icon-text">$' . number_format($commitment->proposal_budget, 0, '.', ',') . '</div>';
                    ?>
                </div>
                <div class="col-md-4 text-center">
                    <?php
                    echo '<div><i class="far fa-calendar"></i></div>';
                    echo '<div id="commit-year-base-container" class="commit-icon-text"><span id="display-commit-year">' . $commitment->proposal_year . '</span>';
                    if ($this->auth->get_role() == 'host') {
                        echo '<span><i id="edit-commit-year" class="fas fa-pen"></i></span>';
                        echo '</div>'; ?>
                        <div class="form-group with-addon" id="commit-year-container">
                            <div class='input-group date' id='commit-year'>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                <input type='text' class="form-control" name="commit-year"/>
                                <span class="input-group-addon save">
                                    <span class="fas fa-save"></span>
                                </span>
                                <span class="input-group-addon clear">
                                    <span class="fas fa-times"></span>
                                </span>
                            </div>
                            <div class="text-danger year-error-msg">It was not possible update this field</div>
                        </div>

                        <?php
                    } else {
                        echo '</div>';
                    }

                    if (strtotime($commitment->proposal_deadline)) {
                        echo '<div style="font-size: 12px;">DEADLINE</div>';
                        echo '<div>' . date('d/m/Y', strtotime($commitment->proposal_deadline)) . '</div>';
                    }
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <label>Progress</label>
                    <div class="progress">
                        <div class="progress-bar progress-color <?php echo $commitment->proposal_completion == 0 ? 'progress-zero' : '' ?>"
                             role="progressbar" aria-valuenow="<?php echo $commitment->proposal_completion ?>"
                             aria-valuemin="0" aria-valuemax="100"
                             style="width: <?php echo $commitment->proposal_completion ?>%;"><?php echo $commitment->proposal_completion ?>
                            %
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if ($commitment->proposal_abstract != '' && $commitment->proposal_abstract != '0') {
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <label>Abstract</label>
                        <p><?php echo $commitment->proposal_abstract ?></p>
                    </div>
                </div>
                <?php
            }
            if ($commitment->proposal_impact != '' && $commitment->proposal_impact != '0') {
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <label>Impact</label>
                        <p><?php echo $commitment->proposal_impact ?></p>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-6">
                    <span>
                        <?php echo $commitment->proposal_area_active == 0 ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>' ?>
                        <span class="area">Area actively managed</span>
                    </span>
                </div>
                <div class="col-md-6 text-right">
                    <?php
                    if ($commitment->proposal_area > 0) {
                        echo '<span class="area">Protected area surface:</span>';
                        echo '<span>' . number_format($commitment->proposal_area, 0, '.', ',') . ' km<sup>2</sup></span>';
                    }
                    ?>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="map"></div>
                </div>
            </div>
            <?php if ($commitment->proposal_website != '') { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-layout">
                            <div class="table-cell table-cell-middle table-url"><i class="fas fa-globe-americas"></i>
                            </div>
                            <div class="table-cell table-cell-middle"><a class="url"
                                                                         href="<?php echo $commitment->proposal_website ?>"
                                                                         target="_blank"><?php echo $commitment->proposal_website ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if ($commitment->attachments) {
                echo '<div class="row row-attachments">';
                echo '<div class="col-md-12">';
                echo '<div class="table-layout">';
                echo '<div class="table-cell table-url"><i class="fas fa-link"></i></div>';
                echo '<div class="table-cell table-cell-middle">';
                foreach ($commitment->attachments as $attachment) {
                    echo '<div><span>' . $attachment->link_description . ' : </span><a class="url" href="' . $attachment->link_url . '" target="_blank">' . $attachment->link_url . '</a></div>';
                }
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>

            <div class="row row-announcer">
                <div class="col-md-12">
                    <p class="announcer">
                        <span><i class="fas fa-bullhorn" title="Announcer"></i></span>
                        <span><?php echo $commitment->proposal_announcer ?> </span>
                    </p>
                </div>
            </div>
            <div class="row row-language">
                <div class="col-md-12">
                    <p class="announcer-language">
                        <span><i class="fas fa-language" title="Language"></i></span>
                        <span><?php echo $commitment->proposal_language ?> </span>
                    </p>
                </div>
            </div>
            <?php
            if (((isset($completed) && $completed['isComplete']) || !isset($completed)) && $workflow->editable) {
                ?>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <?php
                        if ($workflow->editable && $commitment->proposal_status == 'draft') {
                            ?>
                            <button class="btn btn-default delete">Delete</button>
                        <?php } ?>
                        <button class="btn btn-primary edit">Edit</button>
                    </div>
                </div>
            <?php } ?>
            <div class="row center-block error-msg hidden">
                <div class="col-md-12 alert alert-danger table-layout page-alert" role="alert">
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
        </div>
        <div class="col-md-5 timeline-container">
            <?php
            if ($this->auth->get_role() == 'host') { ?>
                <div class="row">
                    <?php
                    echo '<button class="flow btn btn-default pull-right add-note" data-toggle="modal" data-target="#comments-modal" data-ooc-target="comment" data-is-note="true" data-comment="true" data-datepicker="false" data-email="comment">Add comment</button>';
                    ?>
                </div>
            <?php } ?>

            <ul class="timeline">
                <?php
                foreach ($commitment->history as $item) {
                    if ($this->auth->get_role() == 'host' || ($this->auth->get_role() == 'poc') && $item->is_public) {
                        ?>
                        <li class="timeline-inverted">
                            <div class="timeline-badge <?php echo $item->class ?>">
                                <i class="fas <?php echo $item->icon ?>"></i>
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h4 class="timeline-title"><?php echo $item->detail ?></h4>
                                    <p>
                                        <small class="text-muted">By <?php echo $item->user_firstname . ' ' . $item->user_name ?>
                                            on <?php echo date('d/m/Y', strtotime($item->date)) ?></small></p>
                                </div>
                                <?php if ($item->comment != '') { ?>
                                    <div class="timeline-body">
                                        <p><?php echo $item->comment ?></p>
                                    </div>
                                <?php } ?>
                            </div>
                        </li>
                    <?php }
                }
                ?>
            </ul>
        </div>
        <div class="row timeline-scroller-container">
            <div class="col-md-7"></div>
            <div class="col-md-5 text-center">
                <div class="timeline-scroller">Show more</div>
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
                if ($this->auth->get_role() == 'host') {
                    echo site_url('hosts/policy');
                } else {
                    echo site_url('commitments/policy');
                }
                ?>"
        },
        "cookie": {
            "name": "<?php echo $this->config->item('cookie_prefix') . $this->config->item('cookie_consent') ?>",
            "domain": "<?php echo $this->config->item('cookie_domain') ?>"
        }
    });

    jQuery(document).ready(function () {
        var form = $('form');
        var map = new L.Map('map');
        var locations = <?php echo json_encode($commitment->sites)?>;
        var pageError = $('.page-alert').parent();
        var modalError = $('.modal-alert').parent();
        var actions = $('button.flow');
        var commitYear = new moment(<?php echo $commitment->proposal_year ?>, 'YYYY', true);
        var proposalStatus = "<?php echo $commitment->proposal_status ?>";


        var commitYearPicker = $('#commit-year').datetimepicker({
            format: "YYYY"
        });

        $('#edit-commit-year').click(function () {
            $('#commit-year-container').css('display', 'block');
            $('#commit-year-base-container').css('display', 'none');
            commitYearPicker.data("DateTimePicker").date(commitYear);
        });

        $('#commit-year>span.clear').click(function () {
            $('#commit-year-container').css('display', 'none');
            $('#commit-year-base-container').css('display', 'block');
            $('.year-error-msg').css('display', 'none');
        });

        $('#commit-year>span.save').click(function (e) {
            e.preventDefault();
            $('.year-error-msg').css('display', 'none');
            $('#commit-year').data("DateTimePicker").hide();
            var newYear = $('#commit-year').data("DateTimePicker").date().format('YYYY');
            if (newYear !== commitYear.format('YYYY')) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url('rest/set_commitment_year/' . $commitment->proposal_id) ?>',
                    dataType: 'json',
                    data: {
                        proposal_id: <?php echo $commitment->proposal_id ?>,
                        proposal_year: newYear
                    },
                    success: function (data) {
                        if (data.success) {
                            location.reload();
                            /*$('#commit-year-container').css('display', 'none');
                            $('#commit-year-base-container').css('display', 'block');
                            commitYear = $('#commit-year').data("DateTimePicker").date();
                            $('#display-commit-year').text(newYear);*/
                        }
                    },
                    error: function (data) {
                        $('.year-error-msg').css('display', 'block');
                    }
                });
            }
        });

        form.bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fas fa-check',
                invalid: 'fas fa-times',
                validating: 'fas fa-spinner'
            },
            fields: {
                comment: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide a comment'
                        }
                    }
                },
                schedule_date: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a date until when the commitment is under embargo"
                        },
                        date: {
                            message: 'Please provide a valid date',
                            format: 'DD/MM/YYYY'
                        },
                        callback: {
                            message: 'Please provide a date in the future',
                            callback: function (value, validator) {
                                var m = new moment(value, 'DD/MM/YYYY', true);
                                if (!m.isValid()) {
                                    return false;
                                }
                                return m.isSameOrAfter(moment(), 'day');
                            }
                        }
                    }
                }
            }
        });
        var validator = form.data('bootstrapValidator');
        validator.enableFieldValidators('schedule_date', false);
        validator.enableFieldValidators('comment', false);

        <?php
        if ($this->auth->get_role() == 'poc') {
            $deleteUrl = site_url('commitments/delete_commitment/' . $commitment->proposal_id);
            $ajaxUrl = site_url('commitments/flow/' . $commitment->proposal_id);
            echo 'var deleteUrl = \'' . $deleteUrl . '\';';
        } else {
            $ajaxUrl = site_url('hosts/flow/' . $commitment->proposal_id);
        }

        echo 'var url = \'' . $ajaxUrl . '\';';
        ?>

        var osm = OOC.getOsmLayer();
        map.addLayer(osm);

        var markers = [];
        for (var i = 0; i < locations.length; i++) {
            var coord = {
                lat: locations[i].lat,
                lon: locations[i].lng
            }

            markers.push(L.marker(coord, {draggable: false}));
        }
        var group = L.featureGroup(markers).addTo(map);
        map.fitBounds(group.getBounds(), {
            maxZoom: 10
        });

        $('[data-toggle="popover"]').popover();
        $('[data-toggle="popover"]').click(function (event) {
            event.preventDefault();
        });


        var scheduleDate = $('#schedule_date').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on('dp.change', function (e) {
            validator.revalidateField('schedule_date');
        });

        <?php
        if (isset($commitment->proposal_schedule)) {
            echo '$(\'#comments-modal\').on(\'show.bs.modal\', function (event) {';
            echo 'var date = new moment("' . $commitment->proposal_schedule . '", "YYYY-MM-DD", true);';
            echo '$("#schedule_date").data("DateTimePicker").date(date.format("DD/MM/YYYY"));';
            echo '});';
        }
        ?>

        $('#comments-modal').on('hide.bs.modal', function (evt) {
            validator.resetForm();
            modalError.addClass('hidden');
            $('button .fa-spin').addClass('hidden');
        });

        function deleteCommitment() {
            $.ajax({
                type: 'POST',
                url: deleteUrl,
                success: function (data) {
                    if (data.success) {
                        window.location = data.url;
                    }
                },
                error: function (data) {
                    pageError.removeClass('hidden');
                    var cell = pageError.find('.table-cell').last()
                    var span = cell.children('span');
                    span.html(data.responseJSON.message);
                }
            });
        }

        function submitFlow() {
            var data = form.serializeObject();
            if (data.is_note === 'true') {
                validator.enableFieldValidators('comment', true);
            } else {
                validator.enableFieldValidators('comment', false);
                <?php
                if ($commitment->proposal_status == 'approved' || $commitment->proposal_status == 'scheduled') {
                    echo 'validator.enableFieldValidators(\'schedule_date\', true);';
                }
                ?>
            }

            form.bootstrapValidator('validate');
            if (form.data('bootstrapValidator').isValid() == true) {
                if (data.is_note === 'false') {
                    <?php
                    if ($commitment->proposal_status == 'approved' || $commitment->proposal_status == 'scheduled') {
                        echo 'var date = new moment(data.schedule_date, \'DD/MM/YYYY\', true);';
                        echo 'data.schedule_date = date.format(\'YYYY-MM-DD\');';
                    }
                    ?>
                }
                $.ajax({
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    data: data,
                    success: function (data) {
                        if (data.success) {
                            if (data.displayMsg !== undefined) {
                                OOC.setSubmittedMsg();
                            }
                            window.location = data.url;
                        }
                        $('button .fa-spin').addClass('hidden');
                    },
                    error: function (data) {
                        modalError.removeClass('hidden');
                        var cell = modalError.find('.table-cell').last();
                        var span = cell.children('span');
                        span.html(data.responseJSON.message);
                        $('button .fa-spin').addClass('hidden');
                    }
                });

                return true;
            } else {
                return false;
            }
        }

        $('.btn-do-submit').click(function () {
            var btn = $('button .fa-spin');
            if (btn.hasClass('hidden')) {
                btn.removeClass('hidden');
                var submitted = submitFlow();
                if (!submitted) {
                    btn.addClass('hidden');
                }
            }

        });

        function setTimelineHeight() {
            var targetH = $('.commit-content').height();
            var srcH = $('.timeline-container').height();

            if (srcH > targetH && $(document).width() > 976) {
                $('.timeline-scroller-container').css('display', 'block');
                $('.timeline-container').height(targetH);
            }
        }

        setTimelineHeight();

        $('.timeline-scroller-container').click(function (evt) {
            $('.timeline-scroller-container').css('display', 'none');
            $('.timeline-container').height('100%');
        });

        $('button.btn-primary.edit').click(function () {
            window.location = '<?php echo site_url('/commitments/edit_commitment/' . $commitment->proposal_id) ?>';
        });

        $('button.btn-default.delete').click(function () {
            deleteCommitment();
        });

        for (var i = 0; i < actions.length; i++) {
            var btn = actions[i];

            $(btn).click(function () {
                $('input[name=proposal_status]').val($(this).attr('data-ooc-target'));
                $('input[name=email]').val($(this).attr('data-email'));
                $('input[name=is_note]').val($(this).attr('data-is-note'));

                if ($(this).hasClass('add-note')) {
                    $('#schedule_date').parent().addClass('hidden');
                    $('.modal-body .checkbox').removeClass('hidden');
                    $('.btn-do-submit span').text('Submit and send to POC');
                } else {
                    $('.modal-body .checkbox').addClass('hidden');
                    $('.btn-do-submit span').text('Submit');
                    if (proposalStatus === 'scheduled' || proposalStatus === 'approved') {
                        $('#schedule_date').parent().removeClass('hidden');
                    }
                }
            });
        }

        $('#check-is-public').change(function () {
            var text = 'Submit'
            if (this.checked) {
                text += ' and send to POC';
            }
            $('.btn-do-submit span').text(text);
        });


        $(window).resize(function () {
            $('.timeline-scroller-container').css('display', 'none');
            $('.timeline-container').height('100%');
            setTimelineHeight();
        });

        if (OOC.getSubmittedMsg() === true) {
            $('.submitted-msg').removeClass('hidden');
            OOC.removeSubmittedMsg();

            setTimeout(function () {
                $('.submitted-msg').addClass('hidden');
            }, 10000);
        }
    });
</script>
