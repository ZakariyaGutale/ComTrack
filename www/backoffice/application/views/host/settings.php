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

    $host = false;
    $title = false;
    foreach ($publish as $item){
        if ($item->config_key == 'app_host' && $item->config_value != ''){
            $host = true;
        } elseif ($item->config_key == 'app_title' && $item->config_value != ''){
            $title = true;
        }
    }
?>
<div class="container main-screen settings">
    <div id="type-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Organisation type</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form role="form">
                                <div class="form-group">
                                    <label for="name">ID</label>
                                    <input type="text" class="form-control" name="id">
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group">
                                    <label for="name">Label</label>
                                    <input type="text" class="form-control" name="name">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-save-type">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div id="theme-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Commitment themes</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form role="form" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="id">ID</label>
                                    <input type="text" class="form-control" name="id">
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group">
                                    <label for="name">Label</label>
                                    <input type="text" class="form-control" name="name">
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group">
                                    <label>Icon</label>
                                    <span class="control-fileupload">
                                        <label for="icon"></label>
                                        <input type="file" class="form-control" name="icon" id="icon">
                                    </span>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row center-block error-msg hidden">
                        <div class="col-md-12 alert alert-danger table-layout" role="alert">
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
                    <button type="submit" class="btn btn-primary btn-save-theme">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2>Settings</h2>
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#host" data-toggle="tab">Host</a></li>
                <li role="presentation"><a href="#types" data-toggle="tab">Organisation types</a></li>
                <li role="presentation"><a href="#themes" data-toggle="tab">Commitment themes</a></li>
                <li role="presentation"><a href="#viewer" data-toggle="tab">Map viewer</a></li>
                <li role="presentation"><a href="#publish" data-toggle="tab">Publishing scheduler</a></li>
            </ul>
        </div>
    </div>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="host">
            <div class="row">
                <div class="col-md-12">
                    <form role="form" class="host-form">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-success">Update</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group combo">
                                    <label for="host_flag">Hosting Country</label>
                                    <select id="country-combo" class="form-control" name="host_flag"></select>
                                </div>
                                <div class="help-block with-errors"></div>
                                <input type="hidden" class="form-control" name="host_country">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="host_conf_site">Official Conference Website URL</label>
                                    <input type="text" class="form-control" name="host_conf_site">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <!-- CAMPAIGN -->
                        <div class="row camp-row">
                            <div class="col-md-12">
                                <div class="form-group camp-active">
                                    <label for="label_host_camp_active">Campaign active</label>
                                    <div class="material-switch switch">
                                        <input id="host_camp_active" name="host_camp_active" type="checkbox" />
                                        <label for="host_camp_active" class="label-primary"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="campaign-container" class="hidden">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="radio-inline <?php echo $is_new_campaign ? 'hidden' : ''?>">
                                            <input type="radio"
                                                   name="camp_action"
                                                   value="update" <?php echo $is_new_campaign ? '': 'checked' ?>> Update existing campaign
                                        </label>
                                        <label class="radio-inline <?php echo $is_new_campaign ? 'radio-pad-adjust' : ''?>">
                                            <input type="radio" name="camp_action" value="create" <?php echo $is_new_campaign ? 'checked': '' ?> > Create new campaign
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group with-addon">
                                        <label for="host_conf_start">Conference start date</label>
                                        <div class="input-group date" id="conf-start-date">
                                            <input type='text' class="form-control" name="host_conf_start"/>
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group with-addon">
                                        <label for="host_conf_end">Conference end date</label>
                                        <div class="input-group date" id="conf-end-date">
                                            <input type='text' class="form-control" name="host_conf_end"/>
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group with-addon">
                                        <label for="host_camp_start">Campaign start date</label>
                                        <div class="input-group date" id="camp-start-date">
                                            <input type='text' class="form-control" name="host_camp_start" <?php echo $is_new_campaign ? '' : 'disabled' ?>/>
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group with-addon">
                                        <label for="host_camp_end">Campaign end date</label>
                                        <div class="input-group date" id="camp-end-date">
                                            <input type='text' class="form-control" name="host_camp_end"/>
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="host_camp_new_target">Target number of new commitments</label>
                                        <input type="number" class="form-control" name="host_camp_new_target" min="1" step="1" />
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="host_camp_new_target">Target number of updated commitments</label>
                                        <input type="number" class="form-control" name="host_camp_update_target" min="1" step="1" />
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="types">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-success add-type" data-toggle="modal" data-target="#type-modal">Add type</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Label</th>
                                <th class="text-center"># Used</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($types as $item) { ?>
                                <tr>
                                    <td><?php echo $item->id ?></td>
                                    <td><?php echo $item->name ?></td>
                                    <td class="text-center"><?php echo $item->count ?></td>
                                    <td>
                                        <button class="btn btn-default icon edit-type" title="Edit" data-toggle="modal" data-target="#type-modal" data-id="<?php echo $item->id ?>" data-label="<?php echo $item->name ?>"><i class="fas fa-pencil-alt"></i></button>
                                        <?php if ($item->count == 0){ ?>
                                            <button class="btn btn-default icon delete-type" title="Delete" data-id="<?php echo $item->id ?>"><i class="fas fa-trash-alt"></i></button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row center-block error-msg hidden">
            <div class="col-md-12 alert alert-danger table-layout dynamic-alert" role="alert">
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
        <div role="tabpanel" class="tab-pane fade" id="themes">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-success add-theme" data-toggle="modal" data-target="#theme-modal">Add theme</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Icon</th>
                            <th>Label</th>
                            <th class="text-center"># Used</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($themes as $item) { ?>
                            <tr>
                                <td><?php echo $item->id ?></td>
                                <td><img src="<?php echo site_url('/assets/images/ooc/'.$item->id.'.png' )?>" style="height: 20px; width: 20px;" /></td>
                                <td><?php echo $item->name ?></td>
                                <td class="text-center"><?php echo $item->count ?></td>
                                <td>
                                    <button class="btn btn-default icon edit-theme" title="Edit" data-toggle="modal" data-target="#theme-modal" data-id="<?php echo $item->id ?>" data-label="<?php echo $item->name ?>"><i class="fas fa-pencil-alt"></i></button>
                                    <?php if ($item->count == 0){ ?>
                                        <button class="btn btn-default icon delete-theme" title="Delete" data-id="<?php echo $item->id ?>"><i class="fas fa-trash-alt"></i></button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="viewer">
            <form role="form" class="map-viewer-form">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-success">Update</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="app_title">Title</label>
                            <input type="text" class="form-control" name="app_title">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="app_description">Description</label>
                            <textarea class="form-control" rows="6" name="app_description"></textarea>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="app_host">URL</label>
                            <input type="text" class="form-control" name="app_host">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group combo">
                            <label for="app_theme">Theme</label>
                            <select id="app-theme-combo" class="form-control" name="app_theme">
                                <option value="">Default</option>
                                <option value="grey">Grey</option>
                                <option value="green">Green</option>
                                <option value="orange">Orange</option>
                                <option value="jazzberry">Jazzberry</option>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group combo">
                            <label for="app_mode">Mode</label>
                            <select id="app-mode-combo" class="form-control" name="app_mode">
                                <option value="7">All</option>
                                <option value="1">Map only</option>
                                <option value="2">List only</option>
                                <option value="4">Stats only</option>
                                <option value="3">All but stats</option>
                                <option value="5">All but list</option>
                                <option value="6">All but map</option>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="app_disclaimer">Disclaimer</label>
                            <div class="btn-toolbar" data-role="editor-toolbar" data-target="#editor-disclaimer">
                                <div class="btn-group">
                                    <a type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="Font Size"><i class="fas fa-text-height"></i>&nbsp;<b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a data-edit="fontSize 5"><font size="5">Huge</font></a></li>
                                        <li><a data-edit="fontSize 3"><font size="3">Normal</font></a></li>
                                        <li><a data-edit="fontSize 1"><font size="1">Small</font></a></li>
                                    </ul>
                                </div>
                                <div class="btn-group">
                                    <a class="btn btn-default" data-edit="bold" title="Bold (Ctrl+B)"><i class="fas fa-bold"></i></a>
                                    <a class="btn btn-default" data-edit="italic" title="Italic (Ctrl+I)"><i class="fas fa-italic"></i></a>
                                    <a class="btn btn-default" data-edit="underline" title="Underline (Ctrl+U)"><i class="fas fa-underline"></i></a>
                                </div>
                                <div class="btn-group">
                                    <a class="btn btn-default" data-edit="insertunorderedlist" title="Bullet list"><i class="fas fa-list-ul"></i></a>
                                    <a class="btn btn-default" data-edit="insertorderedlist" title="Number list"><i class="fas fa-list-ol"></i></a>
                                    <a class="btn btn-default" data-edit="outdent" title="Reduce indent (Shift+Tab)"><i class="fas fa-indent fa-flip-horizontal"></i></a>
                                    <a class="btn btn-default" data-edit="indent" title="Indent (Tab)"><i class="fas fa-indent"></i></a>
                                </div>
                                <div class="btn-group">
                                    <a class="btn btn-default" data-edit="justifyleft" title="Align Left (Ctrl+L)"><i class="fas fa-align-left"></i></a>
                                    <a class="btn btn-default" data-edit="justifycenter" title="Center (Ctrl+E)"><i class="fas fa-align-center"></i></a>
                                    <a class="btn btn-default" data-edit="justifyright" title="Align Right (Ctrl+R)"><i class="fas fa-align-right"></i></a>
                                    <a class="btn btn-default" data-edit="justifyfull" title="Justify (Ctrl+J)"><i class="fas fa-align-justify"></i></a>
                                </div>
                                <div class="btn-group">
                                    <a class="btn btn-default" data-edit="undo" title="Undo (Ctrl+Z)"><i class="fas fa-undo"></i></a>
                                    <a class="btn btn-default" data-edit="redo" title="Redo (Ctrl+Y)"><i class="fas fa-redo"></i></a>
                                </div>
                            </div>
                            <div id="editor-disclaimer"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="publish">
            <div class="row center-block error-msg <?php echo $host && $title ? 'hidden': '' ?>">
                <div class="col-md-12 alert alert-danger table-layout static-alert" role="alert">
                    <div class="table-row">
                        <div class="table-cell table-cell-middle alert-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="table-cell">
                            <span>You cannot set your scheduler settings until you have defined both the map viewer title and URL.</span>
                        </div>
                    </div>
                </div>
            </div>
            <form role="form" class="scheduler-form">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-success <?php echo $host && $title ? '' : 'hidden' ?>">Update</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="label_scheduler_active">Scheduler active</label>
                            <div class="material-switch switch">
                                <input id="scheduler_active" name="label_scheduler_active" type="checkbox" />
                                <label for="scheduler_active" class="label-primary"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 scheduler-row hidden">
                        <div class="form-group combo scheduler-selector-combo selector-combo">
                            <div class="group-container">
                                <span class="custom">Every</span>
                                <select id="scheduler-selector" class="form-control" name="scheduler-selector">
                                    <option value="year">Year</option>
                                    <option value="month">Month</option>
                                    <option value="week">Week</option>
                                    <option value="day">Day</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group combo scheduler-selector-combo weekday-combo hidden">
                            <div class="group-container">
                                <span class="custom">on</span>
                                <select id="scheduler-weekday" class="form-control" name="scheduler-weekday">
                                    <option value="0">Sunday</option>
                                    <option value="1">Monday</option>
                                    <option value="2">Tuesday</option>
                                    <option value="3">Wednesday</option>
                                    <option value="4">Thursday</option>
                                    <option value="5">Friday</option>
                                    <option value="6">Saturday</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group combo scheduler-selector-combo day-combo">
                            <div class="group-container">
                                <span class="custom">on the</span>
                                <select id="scheduler-day" class="form-control" name="scheduler-day">
                                    <option value="1">1st</option>
                                    <option value="2">2nd</option>
                                    <option value="3">3rd</option>
                                    <option value="4">4th</option>
                                    <option value="5">5th</option>
                                    <option value="6">6th</option>
                                    <option value="7">7th</option>
                                    <option value="8">8th</option>
                                    <option value="9">9th</option>
                                    <option value="10">10th</option>
                                    <option value="11">11th</option>
                                    <option value="12">12th</option>
                                    <option value="13">13th</option>
                                    <option value="14">14th</option>
                                    <option value="15">15th</option>
                                    <option value="16">16th</option>
                                    <option value="17">17th</option>
                                    <option value="18">18th</option>
                                    <option value="19">19th</option>
                                    <option value="20">20th</option>
                                    <option value="21">21st</option>
                                    <option value="22">22nd</option>
                                    <option value="23">23rd</option>
                                    <option value="24">24th</option>
                                    <option value="25">25th</option>
                                    <option value="26">26th</option>
                                    <option value="27">27th</option>
                                    <option value="28">28th</option>
                                    <option value="29">29th</option>
                                    <option value="30">30th</option>
                                    <option value="31">31st</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group combo scheduler-selector-combo month-combo">
                            <div class="group-container">
                                <span class="custom">of</span>
                                <select id="scheduler-month" class="form-control" name="scheduler-month">
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group combo scheduler-selector-combo hour-combo">
                            <div class="group-container">
                                <span class="custom">at</span>
                                <select id="scheduler-hour" class="form-control" name="scheduler-hour">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group combo scheduler-selector-combo minute-combo">
                            <div class="group-container">
                                <span class="custom">:</span>
                                <select id="scheduler-minute" class="form-control" name="scheduler-minute">
                                    <option value="0">0</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                    <option value="25">25</option>
                                    <option value="30">30</option>
                                    <option value="35">35</option>
                                    <option value="40">40</option>
                                    <option value="45">45</option>
                                    <option value="50">50</option>
                                    <option value="55">55</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row cron hidden">
                <div class="col-md-12">
                    <div class="alert alert-warning" role="alert">Date and time should be defined in UTC (0). Make sure that you are making the proper conversion.</div>
                </div>
            </div>
            <!--<div class="row cron hidden">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Cron Job Expression:</label>
                        <span></span>
                    </div>
                </div>
            </div>-->
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
            "href": "<?php echo site_url('hosts/policy') ?>"
        },
        "cookie": {
            "name": "<?php echo $this->config->item('cookie_prefix').$this->config->item('cookie_consent') ?>",
            "domain": "<?php echo $this->config->item('cookie_domain') ?>"
        }
    });

    jQuery(document).ready(function(){
        var hostForm = $('#host form');
        var typeForm = $('#type-modal form');
        var themeForm = $('#theme-modal form');
        var themeError = $('#theme-modal .alert-danger').parent();
        var viewerForm =  $('#viewer form');
        var viewerModel = <?php echo json_encode($publish) ?>;
        var isNewCampaign = !!<?php echo $is_new_campaign ? 1 : 0 ?>;
        var schedulerForm = $('#publish form');
        var dynAlert = $('.dynamic-alert');
        var staticAlert = $('.static-alert');



        /* HOST SETTINGS */
        hostForm.bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fas fa-check',
                invalid: 'fas fa-times',
                validating: 'fas fa-spinner'
            },
            fields: {
                host_conf_site: {
                    validators: {
                        uri: {
                            message: 'Please provide a valid URL for the map viewer'
                        }
                    }
                },
                host_conf_start: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a date for the start of the conference"
                        },
                        date: {
                            message: 'Please provide a valid date',
                            format: 'DD/MM/YYYY'
                        },
                        callback: {
                            message: 'Please provide a date before the end date of the conference',
                            callback: function(value, validator) {
                                var start = new moment(value, 'DD/MM/YYYY', true);
                                var end = new moment($('#conf-end-date input').val(), 'DD/MM/YYYY', true);
                                if (!start.isValid()) {
                                    return false;
                                }
                                if (end.isValid()){
                                    return start.isBefore(end);
                                }

                                return true;
                            }
                        }
                    }
                },
                host_conf_end: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a date for the end of the conference"
                        },
                        date: {
                            message: 'Please provide a valid date',
                            format: 'DD/MM/YYYY'
                        },
                        callback: {
                            message: 'Please provide a date after the start date of the conference',
                            callback: function(value, validator) {
                                var end = new moment(value, 'DD/MM/YYYY', true);
                                var start = new moment($('#conf-start-date input').val(), 'DD/MM/YYYY', true);
                                if (!end.isValid()) {
                                    return false;
                                }
                                if (start.isValid()){
                                    return end.isAfter(start);
                                }

                                return true;
                            }
                        }
                    }
                },
                host_camp_start: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a date for the start of the campaign"
                        },
                        date: {
                            message: 'Please provide a valid date',
                            format: 'DD/MM/YYYY'
                        },
                        callback: {
                            callback: function(value, validator) {
                                var msg = 'Please provide a date before the end date of the campaign';
                                var isValid = true;
                                var start = new moment(value, 'DD/MM/YYYY', true);
                                var end = new moment($('#camp-end-date input').val(), 'DD/MM/YYYY', true);
                                var today = moment().startOf('day');
                                var startConf = new moment($('#conf-start-date input').val(), 'DD/MM/YYYY', true);

                                if (!start.isValid()) {
                                    validations.push(false);
                                    messages.push('Please provide valid date');
                                }

                                var validations = [];
                                var messages = [];

                                if (end.isValid()){
                                    validations.push(start.isBefore(end));
                                    messages.push(msg);
                                }

                                if (startConf.isValid()){
                                    validations.push(start.isBefore(startConf));
                                    messages.push('Please provide a date before the start date of the conference');
                                }

                                if (isNewCampaign && start.isSameOrBefore(today)){
                                    validations.push(false);
                                    messages.push('Please provide a date after today');
                                }

                                for (var i = 0; i < validations.length; i++){
                                    if (!validations[i]){
                                        isValid = validations[i];
                                        msg = messages[i];
                                        break;
                                    }
                                }

                                return {
                                    valid: isValid,
                                    message: msg
                                };
                            }
                        }
                    }
                },
                host_camp_end: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a date for the end of the campaign"
                        },
                        date: {
                            message: 'Please provide a valid date',
                            format: 'DD/MM/YYYY'
                        },
                        callback: {
                            //message: 'Please provide a date after the start date of the conference',
                            callback: function(value, validator) {
                                var msg = 'Please provide a date after the start date of the campaign';
                                var isValid = true;
                                var end = new moment(value, 'DD/MM/YYYY', true);
                                var start = new moment($('#camp-start-date input').val(), 'DD/MM/YYYY', true);
                                var endConf = new moment($('#conf-end-date input').val(), 'DD/MM/YYYY', true);
                                if (!end.isValid()) {
                                    return false;
                                }

                                var validations = [];
                                var messages = [];

                                if (start.isValid()){
                                    validations.push(end.isAfter(start));
                                    messages.push(msg);
                                }

                                if (endConf.isValid()){
                                    validations.push(end.isSameOrAfter(endConf));
                                    messages.push('Please provide a date equal to or after the end date of the conference');
                                }

                                for (var i = 0; i < validations.length; i++){
                                    if (!validations[i]){
                                        isValid = validations[i];
                                        msg = messages[i];
                                        break;
                                    }
                                }

                                return {
                                    valid: isValid,
                                    message: msg
                                };

                            }
                        }
                    }
                },
                host_camp_new_target: {
                    validators: {
                        notEmpty: {
                            message: "Please a target number for new commitments"
                        }
                    }
                },
                host_camp_update_target: {
                    validators: {
                        notEmpty: {
                            message: "Please a target number for new commitments"
                        }
                    }
                }
            }
        });
        var hostValidator = hostForm.data('bootstrapValidator');
        hostValidator.enableFieldValidators('host_camp_start', false);
        hostValidator.enableFieldValidators('host_camp_end', false);

        var countryCombo = $('#country-combo').select2({
            theme: 'bootstrap',
            tags: false,
            delay: 250,
            templateResult: function(data){
                if (!data.id){
                    return data.text;
                }

                var $el = $('<div class="host-flag flag-icon-' + data.id.toLowerCase() + '"></div><span>' + data.text + '</span>');
                return $el;
            },
            ajax: {
                type: 'POST',
                url: '<?php echo site_url('rest/countries') ?>',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        term: params.term,
                        page: params.page || 1
                    };

                    return query;
                },
                processResults: function (data, params) {
                    return {
                        results: data.items,
                        pagination: data.pagination
                    }
                },
                cache: true
            }
        });

        countryCombo.on('select2:select', function (evt) {
            var data = evt.params.data;
            $('input[name=host_country]').val(data.text);
        });

        $('#host_camp_active').change(function() {
            if(this.checked) {
                $('#campaign-container').removeClass('hidden');
            } else {
                $('#campaign-container').addClass('hidden');
            }
        });

        function clearCampForm(){
            hostValidator.enableFieldValidators('host_camp_start', true);
            $('#conf-start-date').data('DateTimePicker').clear();
            $('#conf-end-date').data('DateTimePicker').clear();
            $('#camp-start-date').data('DateTimePicker').clear();
            $('#camp-start-date input').prop('disabled', false);
            $('#camp-end-date').data('DateTimePicker').clear();
            $('input[name=host_camp_new_target').val('');
            $('input[name=host_camp_update_target').val('');
            hostValidator.resetForm();
        }

        function updateCampForm(){
            $('#camp-start-date input').prop('disabled', true);
            for (var i = 0; i < viewerModel.length; i++){
                var row = viewerModel[i];
                switch (row.config_key){
                    case 'host_conf_start':
                    case 'host_conf_end':
                    case 'host_camp_start':
                    case 'host_camp_end':
                        if (!isNewCampaign){
                            var date = new moment(row.config_value, 'DD/MM/YYYY', true);
                            $(dateFieldMappers[row.config_key]).data("DateTimePicker").date(date);
                        }
                        break;
                    case 'host_camp_new_target':
                    case 'host_camp_update_target':
                        $('input[name=' + row.config_key +']').val(row.config_value);
                        break;
                }
            }
            hostValidator.enableFieldValidators('host_camp_start', false);
            hostValidator.resetForm();

        };

        $('input[type=radio][name=camp_action]').change(function(){
            hostValidator.resetForm();
            if (this.value === 'update'){
                isNewCampaign = false;
                updateCampForm();
            } else {
                isNewCampaign = true;
                clearCampForm();
            }
        });

        var confStartDate = $('#conf-start-date').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on('dp.change', function (e) {
            hostValidator.revalidateField('host_conf_start');
            if ($('#conf-end-date input').val() !== '' && !hostValidator.isValidField('host_conf_end')){
                hostValidator.revalidateField('host_conf_end');
            }
            if ($('#camp-start-date input').val() !== ''){
                hostValidator.revalidateField('host_camp_start');
            }
        });

        var confEndDate = $('#conf-end-date').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on('dp.change', function (e) {
            hostValidator.revalidateField('host_conf_end');
            if ($('#conf-start-date input').val() !== '' && !hostValidator.isValidField('host_conf_start')){
                hostValidator.revalidateField('host_conf_start');
            }
            if ($('#camp-end-date input').val() !== ''){
                hostValidator.revalidateField('host_camp_end');
            }
        });

        var campStartDate = $('#camp-start-date').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on('dp.change', function (e) {
            hostValidator.revalidateField('host_camp_start');
            if ($('#camp-end-date input').val() !== '' && !hostValidator.isValidField('host_camp_end')){
                hostValidator.revalidateField('host_camp_end');
            }
        });

        var campEndDate = $('#camp-end-date').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on('dp.change', function (e) {
            hostValidator.revalidateField('host_camp_end');
            if ($('#camp-start-date input').val() !== '' && !hostValidator.isValidField('host_camp_start')){
                hostValidator.revalidateField('host_camp_start');
            }
        });

        function updateFlagAndLink(data){
            var html = '<span class="flag-container flag-icon-' + data.host_flag.toLowerCase() + '"></span>';
            if (data.host_conf_site != ''){
                html = '<a href="' + data.host_conf_site + '" target="_blank" title="Visit Our Ocean Conference official website">' + html + '</span>';
            }

            $('.header-flag').empty();
            $('.header-flag').append(html);
        }

        function updateRadios(){
            var updateRadio = $('.radio-inline').first();
            if (updateRadio.length > 0){
                updateRadio.removeClass('hidden');
                updateRadio.children('input').click();
                $('.radio-pad-adjust').removeClass('radio-pad-adjust');
            }

        }

        function submitHostForm(){
            hostForm.bootstrapValidator('validate');
            if (hostForm.data('bootstrapValidator').isValid() == true){
                var data = hostForm.serializeObject();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url('hosts/set_host_config'); ?>',
                    dataType: 'json',
                    data: data,
                    success: function(resp){
                        dynAlert.parent().removeClass('hidden');
                        dynAlert.removeClass('alert-danger')
                        dynAlert.addClass('alert-success');
                        var cell = $('.dynamic-alert .table-cell').last()
                        var span = cell.children('span');
                        span.html(resp.message);
                        hideAlert();
                        updateFlagAndLink(data);
                        isNewCampaign = resp.is_new_campaign;
                        viewerModel = resp.configs;
                        updateRadios();
                    },
                    error: function(resp){
                        dynAlert.parent().removeClass('hidden');
                        dynAlert.removeClass('alert-success');
                        dynAlert.addClass('alert-danger');
                        var cell = $('.dynamic-alert .table-cell').last()
                        var span = cell.children('span');
                        span.html(resp.responseJSON.message);
                        hideAlert();
                    }
                })
            }
        }


        $('.host-form button').click(function(e){
            e.preventDefault();
            submitHostForm();
        });


        /* ORGANISATION TYPES */
        typeForm.bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fas fa-check',
                invalid: 'fas fa-times',
                validating: 'fas fa-spinner'
            },
            fields: {
                id: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a valid ID for your type"
                        },
                        callback: {
                            message: 'This ID is already in use. Please provide a different ID for your type.',
                        }
                    }
                },
                name: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide a valid name for your type'
                        }
                    }
                }
            }
        });
        var typeValidator = typeForm.data('bootstrapValidator');

        $('#type-modal').on('hide.bs.modal', function (e) {
            typeValidator.resetForm();
        });

        var editTypeBtns = $('button.edit-type');
        var deleteTypeBtns = $('button.delete-type');

        $('button.add-type').click(function (e) {
            $('#type-modal input[name=id]').parent('.form-group').removeClass('hidden');
            $('#type-modal input[name=id]').val('');
            $('#type-modal input[name=name]').val('');
        });

        $('#type-modal input[name="id"').change(function(){
            checkTypeId($( this ).val());
        });

        for (var i = 0; i < editTypeBtns.length; i++){
            var btn = editTypeBtns[i];

            $(btn).click(function(){
                $('#type-modal input[name=id]').parent('.form-group').addClass('hidden');
                $('#type-modal input[name=id]').val($(this).attr('data-id'));
                $('#type-modal input[name=name]').val($(this).attr('data-label'));
            });
        }

        for (var i = 0; i < deleteTypeBtns.length; i++) {
            var btn = deleteTypeBtns[i];

            $(btn).click(function(){
                deleteType($(this).attr('data-id'));
            });
        }

        function checkTypeId(id) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('hosts/get_type')?>/' + id,
                success: function (data) {
                    if (data.success) {
                        typeValidator.updateStatus('id', 'VALID', 'callback');
                    }
                },
                error: function (data) {
                    typeValidator.updateStatus('id', 'INVALID', 'callback');
                }
            });
        }

        function deleteType(id){
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('hosts/delete_type') ?>/' + id,
                success: function (data) {
                    if (data.success) {
                        OOC.setSettingsToLocalStorage('types');
                        window.location = data.url;
                    }
                }
            });
        }

        function submitType(){
            typeForm.bootstrapValidator('validate');
            if (typeForm.data('bootstrapValidator').isValid() == true){
                var data = typeForm.serializeObject();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url('hosts/set_type') ?>/' + data.id,
                    dataType: 'json',
                    data: data,
                    success: function (data) {
                        if (data.success) {
                            OOC.setSettingsToLocalStorage('types');
                            window.location = data.url;
                        }
                    }
                });
            }
        }

        OOC.registerEnterKey(typeForm, submitType);
        $('.btn-save-type').click(function(){
            submitType();
        });

        /* COMMITMENT THEMES*/
        themeForm.bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fas fa-check',
                invalid: 'fas fa-times',
                validating: 'fas fa-spinner'
            },
            fields: {
                id: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a valid ID for your type"
                        },
                        callback: {
                            message: 'This ID is already in use. Please provide a different ID for your theme.',
                        }
                    }
                },
                name: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide a valid name for your theme'
                        }
                    }
                },
                icon: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide an icon for your theme'
                        }
                    }
                }
            }
        });
        var themeValidator = themeForm.data('bootstrapValidator');

        $('#theme-modal').on('hide.bs.modal', function (e) {
            themeValidator.resetForm();
            themeError.addClass('hidden');
            $('input[type=file]').val('');
            $('input[type=file]').prev('label').text('');
        });

        var editThemeBtns = $('button.edit-theme');
        var deleteThemeBtns = $('button.delete-theme');

        $('input[type=file]').change(function(){
            var t = $(this).val();
            var labelText = t.substr(12, t.length);
            $(this).prev('label').text(labelText);
        })

        $('button.add-theme').click(function (e) {
            themeValidator.enableFieldValidators('icon', true);
            $('#theme-modal input[name=id]').parent('.form-group').removeClass('hidden');
            $('#theme-modal input[name=id]').val('');
            $('#theme-modal input[name=name]').val('');
        });

        $('#theme-modal [name="id"').change(function(){
            checkThemeId($( this ).val());
        });

        for (var i = 0; i < editThemeBtns.length; i++){
            var btn = editThemeBtns[i];

            $(btn).click(function(){
                themeValidator.enableFieldValidators('icon', false);
                $('#theme-modal input[name=id]').parent('.form-group').addClass('hidden');
                $('#theme-modal input[name=id]').val($(this).attr('data-id'));
                $('#theme-modal input[name=name]').val($(this).attr('data-label'));
            });
        }

        for (var i = 0; i < deleteThemeBtns.length; i++) {
            var btn = deleteThemeBtns[i];

            $(btn).click(function(){
                deleteTheme($(this).attr('data-id'));
            });
        }

        function checkThemeId(id) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('hosts/get_theme') ?>/' + id,
                success: function (data) {
                    if (data.success) {
                        themeValidator.updateStatus('id', 'VALID', 'callback');
                    }
                },
                error: function (data) {
                    themeValidator.updateStatus('id', 'INVALID', 'callback');
                }
            });
        }

        function deleteTheme(id){
            OOC.setSettingsToLocalStorage('themes');
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('hosts/delete_theme') ?>/' + id,
                success: function (data) {
                    if (data.success) {
                        OOC.setSettingsToLocalStorage('themes');
                        window.location = data.url + '?tab=themes';
                    }
                }
            });
        }

        function submitTheme(){
            themeError.addClass('hidden');
            themeForm.bootstrapValidator('validate');
            if (themeForm.data('bootstrapValidator').isValid() == true){
                var serData = themeForm.serializeObject();
                var data = new FormData(themeForm[0]);
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url('hosts/set_theme') ?>/' + serData.id,
                    data: data,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function (data) {
                        if (data.success) {
                            OOC.setSettingsToLocalStorage('themes');
                            window.location = data.url + '?tab=themes';
                        }
                    },
                    error: function (data) {
                        themeError.removeClass('hidden');
                        var cell = $('#theme-modal .alert-danger .table-cell').last()
                        var span = cell.children('span');
                        span.html(data.responseJSON.message);
                    }
                });
            }
        }

        OOC.registerEnterKey(themeForm, submitTheme);
        $('.btn-save-theme').click(function(){
            submitTheme();
        });

        /* MAP VIEWER */
        viewerForm.bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fas fa-check',
                invalid: 'fas fa-times',
                validating: 'fas fa-spinner'
            },
            fields: {
                app_title: {
                    validators: {
                        notEmpty: {
                            message: "Please provide a valid title for the map viewer"
                        }
                    }
                },
                app_host: {
                    validators: {
                        notEmpty: {
                            message: 'Please provide a valid URL for the map viewer'
                        },
                        uri: {
                            message: 'Please provide a valid URL for the map viewer'
                        }
                    }
                },
            }
        });
        var viewerValidator = themeForm.data('bootstrapValidator');

        var themeCombo = $('#app-theme-combo').select2({
            theme: 'bootstrap'
        });

        var modeCombo = $('#app-mode-combo').select2({
            theme: 'bootstrap'
        });

        $('#editor-disclaimer').wysiwyg();

        function submitViewer(){
            viewerForm.bootstrapValidator('validate');
            if (viewerForm.data('bootstrapValidator').isValid() == true){
                var data = viewerForm.serializeObject();
                data.app_disclaimer = $('#editor-disclaimer').html();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url('hosts/set_viewer_config'); ?>',
                    dataType: 'json',
                    data: data,
                    success: function (data) {
                        dynAlert.parent().removeClass('hidden');
                        dynAlert.removeClass('alert-danger')
                        dynAlert.addClass('alert-success');
                        var cell = $('.dynamic-alert .table-cell').last()
                        var span = cell.children('span');
                        span.html(data.message);
                        hideAlert();
                        if (data.app_title !== '' && data.app_host !== ''){
                            staticAlert.parent().addClass('hidden');
                            $('.scheduler-form button').removeClass('hidden');
                        }
                    },
                    error: function (data) {
                        dynAlert.parent().removeClass('hidden');
                        dynAlert.removeClass('alert-success');
                        dynAlert.addClass('alert-danger');
                        var cell = $('.dynamic-alert .table-cell').last()
                        var span = cell.children('span');
                        span.html(data.responseJSON.message);
                        hideAlert();
                    }
                });
            }
        }

        $('.map-viewer-form button').click(function(e){
            e.preventDefault();
            submitViewer();
        });

        /* SCHEDULER  */
        var cron = {
            selector: 'year',
            weekday: 0,
            day: 1,
            month: 1,
            hour: 0,
            minute: 0
        };

        $('#scheduler_active').change(function() {
            if(this.checked) {
                $('.scheduler-row').removeClass('hidden');
                $('.cron').removeClass('hidden');
            } else {
                $('.scheduler-row').addClass('hidden');
                $('.cron').addClass('hidden');
            }
        });

        function toggleSchedulerCombos(visibility){
            var comboSel = ['weekday-combo', 'day-combo', 'month-combo'];
            for (var i = 0; i < comboSel.length; i++) {
                if (visibility[i] === true){
                    $('.' + comboSel[i]).removeClass('hidden');
                } else {
                    $('.' + comboSel[i]).addClass('hidden');
                }
            }
        }

        var selectorCombo = $('#scheduler-selector').select2({
            theme: 'bootstrap'
        });

        selectorCombo.on('select2:select', function (e) {
            var item = e.params.data.id;
            cron.selector = item;
            getCronExpression(true);
            switchSelector(item);
        });

        function switchSelector(selector){
            switch (selector) {
                case 'year':
                    toggleSchedulerCombos([false, true, true]);
                    break;
                case 'month':
                    toggleSchedulerCombos([false, true, false]);
                    break;
                case 'week':
                    toggleSchedulerCombos([true, false, false]);
                    break;
                case 'day':
                    toggleSchedulerCombos([false, false, false]);
                    break;
            }
        }

        var weekdayCombo = $('#scheduler-weekday').select2({
            theme: 'bootstrap'
        });

        weekdayCombo.on('select2:select', function (e) {
            var item = e.params.data.id;
            cron.weekday = item;
            // getCronExpression(true);
        });

        var dayCombo = $('#scheduler-day').select2({
            theme: 'bootstrap'
        });

        dayCombo.on('select2:select', function (e) {
            var item = e.params.data.id;
            cron.day = item;
            // getCronExpression(true);
        });

        var monthCombo = $('#scheduler-month').select2({
            theme: 'bootstrap'
        });

        monthCombo.on('select2:select', function (e) {
            var item = e.params.data.id;
            cron.month = item;
            // getCronExpression(true);
        });

        var hourCombo = $('#scheduler-hour').select2({
            theme: 'bootstrap'
        });

        hourCombo.on('select2:select', function (e) {
            var item = e.params.data.id;
            cron.hour = item;
            // getCronExpression(true);
        });

        var minuteCombo = $('#scheduler-minute').select2({
            theme: 'bootstrap'
        });

        minuteCombo.on('select2:select', function (e) {
            var item = e.params.data.id;
            cron.minute = item;
            // getCronExpression(true);
        });

        function getCronExpression(visible){
            var expression = ['*', '*', '*', '*', '*'];

            expression[0] = cron.minute;
            expression[1] = cron.hour;
            switch (cron.selector) {
                case 'year':
                    expression[2] = cron.day;
                    expression[3] = cron.month;
                    break;
                case 'month':
                    expression[2] = cron.day;
                    break;
                case 'week':
                    expression[4] = cron.weekday;
                    break;
            }

            if (visible){
                $('.cron span').text(expression.join(' '));
            } else {
                return expression;
            }

        }

        function getSchedulerSerializedObject(){
            var data = schedulerForm.serializeObject();
            var scheduler = {
                'scheduler-day': data['scheduler-day'],
                'scheduler-month': data['scheduler-month'],
                'scheduler-hour': data['scheduler-hour'],
                'scheduler-minute': data['scheduler-minute'],
                'scheduler-weekday': data['scheduler-weekday'],
                'scheduler-selector': data['scheduler-selector'],
                'scheduler-expression': getCronExpression().join(' ')
            };

            var final = {
                'scheduler_active': $('[name="label_scheduler_active"]:checked').length,
                'scheduler_expression': scheduler
            };

            return final;
        }

        function submitScheduler(){
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('hosts/set_scheduler'); ?>',
                dataType: 'json',
                data: getSchedulerSerializedObject(),
                success: function (data) {
                    dynAlert.parent().removeClass('hidden');
                    dynAlert.removeClass('alert-danger')
                    dynAlert.addClass('alert-success');
                    var cell = $('.dynamic-alert .table-cell').last()
                    var span = cell.children('span');
                    span.html(data.message);
                    hideAlert();
                },
                error: function (data) {
                    dynAlert.parent().removeClass('hidden');
                    dynAlert.removeClass('alert-success');
                    dynAlert.addClass('alert-danger');
                    var cell = $('.dynamic-alert .table-cell').last()
                    var span = cell.children('span');
                    span.html(data.responseJSON.message);
                    hideAlert();
                }
            });
        }

        $('.scheduler-form button').click(function(e){
            e.preventDefault();
            submitScheduler();
        });

        function findCountryNameFromModel(startIdx){
            for (var i = startIdx; i < viewerModel.length; i++){
                var row = viewerModel[i];
                if (row.config_key === 'host_country'){
                    return row.config_value;
                }
            }
        }

        var dateFieldMappers = {
            host_conf_start: '#conf-start-date',
            host_conf_end: '#conf-end-date',
            host_camp_start: '#camp-start-date',
            host_camp_end: '#camp-end-date'
        };

        for (var i = 0; i < viewerModel.length; i++) {
            var row = viewerModel[i];
            var countryConfigIdx = -1;
            switch (row.config_key){
                case 'host_flag':
                    if (countryConfigIdx === -1) {
                        countryConfigIdx = i
                    }
                    var newOption = new Option(findCountryNameFromModel(countryConfigIdx), row.config_value, true, true);
                    countryCombo.append(newOption).trigger('change');
                    break;
                case 'host_conf_site':
                case 'host_country':
                    countryConfigIdx = i;
                    $('input[name=' + row.config_key +']').val(row.config_value);
                    break;
                case 'host_camp_active':
                    $('#host_camp_active').prop('checked', parseInt(row.config_value) === 0 ? false : true);
                    if (parseInt(row.config_value) === 1){
                        $('#campaign-container').removeClass('hidden');
                    }
                    break;
                case 'host_conf_start':
                case 'host_conf_end':
                case 'host_camp_start':
                case 'host_camp_end':
                    if (!isNewCampaign){
                        var date = new moment(row.config_value, 'DD/MM/YYYY', true);
                        $(dateFieldMappers[row.config_key]).data("DateTimePicker").date(date);
                    }
                    break;
                case 'app_title':
                case 'app_host':
                case 'host_camp_new_target':
                case 'host_camp_update_target':
                    $('input[name=' + row.config_key +']').val(row.config_value);
                    break;
                case 'app_description':
                    $('textarea[name=' + row.config_key +']').text(row.config_value);
                    break;
                case 'app_theme':
                    themeCombo.val(row.config_value).trigger('change');
                    break;
                case 'app_mode':
                    modeCombo.val(row.config_value).trigger('change');
                    break;
                case 'app_disclaimer':
                    $('#editor-disclaimer').html(row.config_value);
                    break;
                case 'scheduler_active':
                    $('#scheduler_active').prop('checked', parseInt(row.config_value) === 0 ? false : true);
                    if (parseInt(row.config_value) === 1){
                        $('.scheduler-row').removeClass('hidden');
                        $('.cron').removeClass('hidden');
                    }
                    break;
                case 'scheduler_expression':
                    var data = JSON.parse(row.config_value)
                    selectorCombo.val(data['scheduler-selector']).trigger("change");
                    weekdayCombo.val(data['scheduler-weekday']).trigger("change");
                    dayCombo.val(data['scheduler-day']).trigger("change");
                    monthCombo.val(data['scheduler-month']).trigger("change");
                    hourCombo.val(data['scheduler-hour']).trigger("change");
                    minuteCombo.val(data['scheduler-minute']).trigger("change");
                    cron = {
                        selector: data['scheduler-selector'],
                        weekday: data['scheduler-weekday'],
                        day: data['scheduler-day'],
                        month: data['scheduler-month'],
                        hour: data['scheduler-hour'],
                        minute: data['scheduler-minute']
                    };
                    switchSelector(data['scheduler-selector']);
                    getCronExpression(true);
                    break;
            }
        }

        function hideAlert(){
            setTimeout(function(){
                dynAlert.parent().addClass('hidden');
            }, 5000);
        }

        function showTab(tabId){
            $('.nav-tabs a[href="#' + tabId + '"]').tab('show');
            window.history.replaceState({}, document.title, "settings");
        }

        function gup() {
            var params = {};
            if (location.search) {
                var parts = location.search.substring(1).split('&');

                for (var i = 0; i < parts.length; i++) {
                    var nv = parts[i].split('=');
                    if (!nv[0]) continue;
                    params[nv[0]] = nv[1] || true;
                }
            }

            return params;
        }

        var urlParams = gup();
        if (urlParams !== {} && urlParams.tab !== undefined){
            showTab(urlParams.tab);
        }

        getCronExpression(true);

        hostValidator.enableFieldValidators('host_camp_start', true);
        hostValidator.enableFieldValidators('host_camp_end', true);

        function setActiveTabOnLoad(){
            var activeTab = OOC.getSettingsFromLocalStorage();
            if (activeTab !== null){
                $('.nav-tabs a[href="#' + activeTab + '"]').tab('show');
                OOC.removeFromLocalStorage('ooc-settings');
            }

        }

        setActiveTabOnLoad();
    });
</script>
