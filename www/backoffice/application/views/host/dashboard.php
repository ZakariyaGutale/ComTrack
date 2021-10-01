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

$workflow = array_keys($this->config->item('workflow'));

?>
<div id="merge-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Merge Organisations</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        Do you wish to merge:
                        <div class="radio-container"></div>
                    </div>
                </div>
                <hr>
                <div class="row org-details"></div>
                <div class="row center-block">
                    <div class="col-md-12">
                        <div class="alert alert-warning table-layout">
                            <div class="table-row">
                                <div class="table-cell table-cell-middle alert-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="table-cell">
                                    <span>Be aware that after the merge, the organisation <span class="org-name-alert"></span> will be deleted from the system.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-do-submit"><i class="fas fa-spinner fa-spin hidden"></i> Submit</button>
            </div>
        </div>
    </div>
</div>

<div class="container main-screen dashboard">
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <?php
                    if ($overview_configs->active){
                        echo '<li role="presentation" class="active"><a href="#overview" data-toggle="tab">Overview</a></li>';
                        echo '<li role="presentation"><a href="#commitments" data-toggle="tab">Manage commitments';
                    } else {
                        echo '<li role="presentation" class="active"><a href="#commitments" data-toggle="tab">Manage commitments';
                    }

                    if ($commit_count > 0){
                        echo '<span class="badge commits-badge" title="'.$commit_count.' commitment'.($commit_count > 1 ? 's' : '').' pending approval">'.$commit_count.'</span>';
                    }
                ?>
                </a></li>
                <li role="presentation"><a href="#users" data-toggle="tab">Manage users
                    <?php
                        if ($user_count > 0){
                            echo '<span class="badge users-badge" title="'.$user_count.' user'.($user_count > 1 ? 's' : '').' pending approval">'.$user_count.'</span>';
                        }
                    ?>
                </a></li>
                <li role="presentation"><a href="#organisations" data-toggle="tab">Manage organisations
                    <?php
                        if ($org_count > 0){
                            echo '<span class="badge orgs-badge" title="'.$org_count.' incomplete organisation profile'.($org_count > 1 ? 's': '').'">'.$org_count.'</span>';
                        }
                    ?>
                </a></li>
            </ul>
        </div>
    </div>
    <div class="tab-content">
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
        <div class="row center-block success-msg hidden">
            <div class="col-md-12 alert alert-success table-layout dynamic-alert" role="alert">
                <div class="table-row">
                    <div class="table-cell table-cell-middle alert-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="table-cell">
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- OVERVIEW -->
        <?php
            if ($overview_configs->active) {
        ?>
        <div role="tabpanel" class="tab-pane fade in active" id="overview">
            <div class="row">
                <div class="col-md-12">
                    <div class="line-container">
                        <div class="campaign-label"></div>
                        <div class="line"></div>
                    </div>
                </div>
            </div>
            <hr class="overview-top-separator">
            <div class="row updates-container first-container-child">
                <div class="col-md-4">
                    <div class="overview-margin-bottom-small">
                        <div class="card-title grey">Updated commitments</div>
                        <div class="card-number blue"><?php echo $overview->updated + $overview->closed ?></div>
                        <div class="card-text black card-text-200"><b><?php echo $overview->update_total_p?>%</b>&nbsp;of target</div>
                    </div>
                    <div id="first-gauge" class="overview-margin-bottom-big"></div>
                    <div>
                        <div class="card-title black">update target</div>
                        <div class="card-number green"><?php echo $overview->update_target ?></div>
                        <div class="card-text black card-text-200">out of <b><?php echo $overview->existing ?></b> updatable commitments</div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="panel panel-default panel-top">
                        <div class="panel-body">
                            <div class="row row-vcenter">
                                <div class="col-md-6 card-align-left col-vcenter">
                                    <div class="card-margin-left">
                                        <div class="card-inner-section card-inner-center">
                                            <div class="card-small-title">Updated</div>
                                            <div>
                                                <span class="card-small-number blue"><?php echo $overview->updated_p ?>%</span>
                                                <span>of the total updatable commitments</span>
                                            </div>
                                            <div>
                                                <span class="card-small-number black"><?php echo $overview->updated ?></span>
                                                <span>commitments</span>
                                            </div>
                                        </div>
                                        <div class="card-inner-section card-inner-center card-margin-top">
                                            <div class="card-small-title">Closed</div>
                                            <div>
                                                <span class="card-small-number grey"><?php echo $overview->closed_p ?>%</span>
                                                <span>of the total updatable commitments</span>
                                            </div>
                                            <div>
                                                <span class="card-small-number black"><?php echo $overview->closed ?></span>
                                                <span>commitments</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-vcenter">
                                    <div id="first-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="overview-top-separator">
            <div class="row updates-container">
                <div class="col-md-4">
                    <div class="overview-margin-bottom-small">
                        <div class="card-title grey">new commitments</div>
                        <div class="card-number blue"><?php echo $overview->new ?></div>
                        <div class="card-text black card-text-200"><b><?php echo $overview->new_p?>%</b>&nbsp;of target</div>
                    </div>
                    <div id="second-gauge" class="overview-margin-bottom-big"></div>
                    <div>
                        <div class="card-title black">target</div>
                        <div class="card-number green"><?php echo $overview->new_target ?></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="panel panel-default panel-middle">
                        <div class="panel-body">
                            <div class="row row-vcenter">
                                <div class="col-md-6 card-align-left col-vcenter">
                                    <div class="card-margin-left">
                                        <div class="card-inner-section card-inner-center">
                                            <div class="card-small-title">Draft</div>
                                            <div>
                                                <span class="card-small-number blue"><?php echo $overview->draft_p ?>%</span>
                                                <span>of new commitments</span>
                                            </div>
                                            <div>
                                                <span class="card-small-number black"><?php echo $overview->draft ?></span>
                                                <span>commitments</span>
                                            </div>
                                        </div>
                                        <div class="card-inner-section card-inner-center card-margin-top">
                                            <div class="card-small-title">Approved</div>
                                            <div>
                                                <span class="card-small-number green"><?php echo $overview->approved_p ?>%</span>
                                                <span>of new commitments</span>
                                            </div>
                                            <div>
                                                <span class="card-small-number black"><?php echo $overview->approved ?></span>
                                                <span>commitments</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-vcenter">
                                    <div id="second-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- BUDGET -->
            <div class="row updates-container row-vcenter">
                <div class="col-md-4 vcenter">
                    <div class="overview-margin-bottom-small budget-total">
                        <div class="card-title grey">budget raised</div>
                        <div class="card-number blue">$<?php echo number_format($overview->budget_stats->total, 0, '.', ',') ?></div>
                        <div class="card-text black card-text-200">from <b><?php echo $overview->new?></b>&nbsp;new commitments</div>
                    </div>
                </div>
                <div class="col-md-8">
                    <?php if ($overview->budget_stats->total > 0 ) {?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row row-vcenter">
                                <div class="col-md-6">
                                    <div id="first-budget-chart"></div>
                                </div>
                                <div class="col-md-6">
                                    <div id="second-budget-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php }

            if ($overview_configs->active) {
                echo '<div role="tabpanel" class="tab-pane fade" id="commitments">';
            } else {
                echo '<div role="tabpanel" class="tab-pane fade in active" id="commitments">';
            }
        ?>
        <!-- COMMITMENTS-->
            <div class="row row-button-group">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-success action-publish">
                        <i class="fas fa-spinner fa-spin hidden"></i> Publish Now
                    </button>
                    <button type="button" class="btn btn-default btn-download-commits" title="Export commitments to file"><i class="fas fa-file-download"></i></button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <form class="commitments-form">
                        <div class="form-group side-menu-adjust">
                            <div class="input-group">
                                <input type="text" class="form-control" id="free-text-search" name="q" placeholder="Search..."/>
                                <span class="input-group-addon side-menu-search"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Area of action</label>
                            <?php foreach ($themes as $theme) {
                                echo '<div class="checkbox checkbox-theme">';
                                echo '<input type="checkbox" id="check-theme'.$theme->id.'" name="area" value="'.$theme->id.'" checked>';
                                echo '<label class="check-label" for="check-theme'.$theme->id.'"><span class="action-theme"><img src="/assets/images/ooc/'.$theme->id.'.png"></span>'.$theme->name.'</label>';
                                echo '</div>';
                            } ?>
                        </div>
                        <div class="from-group combo-group">
                            <label for="year">Year</label>
                            <select id="year-combo" class="form-control" name="year">
                                <option value="all">All</option>
                                <?php foreach ($years as $year){
                                    echo '<option value="'.$year->year.'">'.$year->year.'</option>';
                                }?>
                            </select>
                        </div>
                        <div class="from-group combo-group">
                            <label for="organisation">Organisation</label>
                            <select id="org-combo" class="form-control" name="organisation">
                                <option value="all">All</option>
                            </select>
                        </div>
                        <div class="from-group combo-group">
                            <label for="completion">Progress</label>
                            <select id="completion-combo" class="form-control" name="completion">
                                <option value="all">All</option>
                                <?php foreach (array_keys($completions) as $completion){
                                    echo '<option value="'.$completion.'">'.$completions[$completion].'</option>';
                                }?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <?php foreach ($status as $item) {
                                $isChecked = 'checked';
                                echo '<div class="checkbox">';
                                if ($commit_count > 0 && $item != 'submitted'){
                                    $isChecked = '';
                                }
                                echo '<input type="checkbox" id="check-commit'.$item.'" name="status" value="'.$item.'" '.$isChecked.'>';
                                echo '<label class="check-label" for="check-commit'.$item.'">'. (strpos($item, '_') ? ucfirst(str_replace('_', ' ', $item)) : ucfirst($item)).'</label>';
                                echo '</div>';
                            } ?>
                        </div>
                    </form>
                </div>
                <!--DATA TABLE-->
                <div class="col-md-9">
                    <table id="commits-table" class="table table-striped table-hover wrap" style="width: 100%;">
                        <thead class="table-header">
                        <tr>
                            <th>Area</th>
                            <th>Title</th>
                            <th>Year</th>
                            <th>Organisation</th>
                            <th>Progress</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- USERS -->
        <div role="tabpanel" class="tab-pane fade" id="users">
            <div class="row row-button-group">
                <div class="col-md-12 text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-spinner fa-spin hidden"></i> Action <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="javascript:void(0)" class="action-approve">Approve</a></li>
                            <li><a href="javascript:void(0)" class="action-reject">Reject</a></li>
                            <li><a href="javascript:void(0)" class="action-delete">Delete</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="javascript:void(0)" class="action-add-host">Add to Hosts</a></li>
                            <li><a href="javascript:void(0)" class="action-remove-host">Remove from Hosts</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-default btn-download-users" title="Export users to file"><i class="fas fa-file-download"></i></button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <form class="users-form">
                        <div class="form-group side-menu-adjust">
                            <div class="input-group">
                                <input type="text" class="form-control" id="free-user-search" name="q" placeholder="Search..."/>
                                <span class="input-group-addon side-user-search"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                        <div class="from-group combo-group">
                            <label for="organisation">Organisation</label>
                            <select id="user-org-combo" class="form-control" name="organisation">
                                <option value="all">All</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <div class="checkbox">
                                <input type="checkbox" id="check-user-approved" name="status" value="A" <?php echo $user_count == 0 ? 'checked': '' ?>>
                                <label class="check-label" for="check-user-approved">Approved</label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" id="check-user-pending" name="status" value="P" <?php echo $user_count > 0 ? 'checked': '' ?>>
                                <label class="check-label" for="check-user-pending">Pending approval</label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" id="check-user-inactive" name="status" value="I" >
                                <label class="check-label" for="check-user-inactive">Rejected</label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" id="check-user-deleted" name="status" value="D" >
                                <label class="check-label" for="check-user-deleted">Deleted</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <div class="checkbox">
                                <input type="checkbox" id="check-user-poc" name="role" value="poc" checked>
                                <label class="check-label" for="check-user-poc">POC</label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" id="check-user-host" name="role" value="host" checked>
                                <label class="check-label" for="check-user-host">Host</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-9">
                    <table id="users-table" class="table table-striped table-hover wrap" style="width: 100%;">
                        <thead class="table-header">
                        <tr>
                            <th>
                                <label class="cb-table">
                                    <input type="checkbox" name="users_select_all" value="all">
                                    <i class="far fa-square unchecked"></i>
                                    <i class="far fa-check-square checked"></i>
                                </label>
                            </th>
                            <th>Name</th>
                            <th>Organisation</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="organisations">
            <div class="row row-button-group">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-success btn-merge" data-toggle="modal" data-target="#merge-modal" title="Merge Organisations" disabled="true"><i class="fas fa-compress-arrows-alt"></i>Merge</button>
                    <button type="button" class="btn btn-default btn-download-orgs" title="Export organisations to file"><i class="fas fa-file-download"></i></button>
                </div>

            </div>
            <div class="row">
                <div class="col-md-3">
                    <form class="org-form">
                        <div class="form-group side-menu-adjust">
                            <div class="input-group">
                                <input type="text" class="form-control" id="free-org-search" name="q" placeholder="Search..."/>
                                <span class="input-group-addon side-org-search"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <!--<div class="checkbox">
                                <input type="checkbox" id="check-org-unset" name="type" value="" checked>
                                <label class="check-label" for="check-org-unset">Not defined</label>
                            </div>-->
                            <?php foreach ($org_types as $type) {
                                echo '<div class="checkbox">';
                                echo '<input type="checkbox" id="check-org-'.$type->id.'" name="type" value="'.$type->id.'" checked>';
                                echo '<label class="check-label" for="check-org-'.$type->id.'">'.ucfirst($type->name).'</label>';
                                echo '</div>';
                            }

                            if ($org_count > 0){
                                echo '<div class="checkbox">';
                                echo '<input type="checkbox" id="check-org-undefined" name="type" value="undefined" checked>';
                                echo '<label class="check-label" for="check-org-undefined">Undefined</label>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </form>
                </div>
                <div class="col-md-9">
                    <table id="org-table" class="table table-striped table-hover wrap" style="width: 100%;">
                        <thead class="table-header">
                        <tr>
                            <!--<th><div class="cb-table"><input type="checkbox" class="cb-table" name="orgs_select_all" value="all"></div></th>-->
                            <th></th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Country</th>
                            <th>City</th>
                            <th class="table-col-min-width-commit"># Commits</th>
                            <th class="table-col-min-width"># Users</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
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
            "href": "<?php echo site_url('hosts/policy') ?>"
        },
        "cookie": {
            "name": "<?php echo $this->config->item('cookie_prefix').$this->config->item('cookie_consent') ?>",
            "domain": "<?php echo $this->config->item('cookie_domain') ?>"
        }
    });

    jQuery(document).ready(function(){
        var isLoading = true;
        var alert = $('.error-msg');
        var successAlert = $('.success-msg');
        var commitForm = $('.commitments-form');
        var userForm = $('.users-form');
        var orgForm = $('.org-form');
        var srcDates = [];
        var overviewData;
        <?php
            if ($this->auth->get_role() == 'poc' ){
                $url = site_url('commitments/view');
            } else {
                $url = site_url('/hosts/view');
            }
            echo 'var url = \''.$url.'\';';

            if ($overview_configs->active){
                echo 'srcDates= ["'.$overview_configs->host_camp_start.'", "'.$overview_configs->host_conf_start.'", "'.$overview_configs->host_conf_end.'", "'.$overview_configs->host_camp_end.'"];';
            }

            if (isset($overview) && $overview != null){
                echo 'overviewData = '.json_encode($overview).';';
            }
        ?>

        var colors = {
            selColors: ['#e6194B', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#42d4f4', '#f032e6', '#bfef45', '#fabebe',
                '#469990', '#e6beff', '#9A6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#000075', '#a9a9a9'],
            theme: {
                cc: '#005cc8',
                mp: '#ff4c02',
                mpa: '#fccd02',
                ms: '#f7931e',
                sbe: '#2f9eea',
                sf: '#77bc1f'
            }
        };


        /* OVERVIEW */
        function calculateTimeline(srcDates){
           var srcLabels = ['Campaign starts', 'Conference starts', 'Conference ends', 'Campaign ends'];
           var srcClass = ['start-end', 'conference', 'conference', 'start-end'];

           var obj = {
               dates: [],
               diffs: [],
               labels: [],
               class: [],
               today: moment().startOf('day'),
               intFactor: 0.2,
               lastInt: 0,
               addedInt: 0,
               daysLeft: null,
               msgLabel: null,
               timelineCovered: 0
           };

           var startDate = new moment(srcDates[0], 'DD/MM/YYYY', true);
           var endDate = new moment(srcDates[srcDates.length - 1], 'DD/MM/YYYY', true);
           obj.lastInt = moment.duration(endDate.diff(startDate)).asDays();

           var idx = -1;
           for (var i = 0; i < srcDates.length; i++){
               var current = new moment(srcDates[i], 'DD/MM/YYYY', true);
               obj.dates.push(current);
               obj.labels.push(srcLabels[i]);
               obj.class.push(srcClass[i]);
               idx += 1;
               if (i > 0){
                   var diff = moment.duration(current.diff(obj.dates[idx - 1])).asDays();
                   obj.diffs.push(diff);
                   if (diff !== 0 && diff / obj.lastInt < 0.1){
                       obj.addedInt += obj.intFactor;
                   }
               }
           }

           var diffTodayToEnd = obj.dates[obj.dates.length - 1].diff(obj.today);
           var daysLeft = moment.duration(diffTodayToEnd).asDays();
           if (daysLeft >= 0){
               obj.daysLeft = Math.ceil(daysLeft);
               if (obj.today.isBefore(obj.dates[0])){
                   obj.msgLabel = 'The campaign has not started yet!';
               }
           } else {
               obj.msgLabel = 'The campaign has finished!';
           }


           if (obj.today.isSameOrAfter(obj.dates[0])){
               var diffTodayToStart = obj.today.diff(obj.dates[0]);
               var passedDays = moment.duration(diffTodayToStart).asDays();
               obj.timelineCovered = passedDays * 100 / obj.lastInt;
           }



           return obj;
        }


        function getJoinedLabel(labels){
           var l1 = labels[0].split(' ');
           var l2 = labels[1].split(' ');
           var label = l1 [0] + ' ' + l2[0] + ' ' + l1[1];
           return label;
        }

        function makeCircles(){
           var maxInt = timeline.lastInt * (1 + timeline.addedInt);
           var intFactor = timeline.lastInt * timeline.intFactor;
           var currentInt;
           for (var i = 0; i < timeline.dates.length; i++){
               var currentInt;
               if (i == 0){
                   currentInt = 0;
               } else {
                   if (timeline.diffs[i - 1] !== 0){
                       if (timeline.diffs[i - 1] / timeline.lastInt < 0.1){
                           currentInt += intFactor;
                       } else {
                           currentInt += timeline.diffs[i - 1];
                       }
                   }
               }
               var relativeInt = currentInt / maxInt;
               if (i === timeline.dates.length - 1){
                   relativeInt = 1;
               }

               if (i > 0 && timeline.diffs[i - 1] === 0){
                   var circle = $('#circle-' + (i-1));
                   var bottomLabel = circle.children('.timeline-label').not('.timeline-top,.today');
                   if (bottomLabel.length > 0){
                       var label = getJoinedLabel([timeline.labels[i-1], timeline.labels[i]]);
                       bottomLabel.text(label);
                   }
               } else {
                   var cls = 'circle';
                   if (timeline.dates[i].isBefore(timeline.today)){
                       cls += ' past';
                   }
                   var circle = '<div class="' + cls + '" id="circle-'+ i + '" style="left: ' + relativeInt * 100 + '%;">';
                   circle += '<div class="timeline-label ' + timeline.class[i] + '">' + timeline.labels[i] + '</div>';
                   circle += '<div class="timeline-label timeline-top ' + timeline.class[i] + '-popup hidden">' + timeline.dates[i].format('MMMM Do, YYYY') + '</div>';
                   circle += '</div>';
                   $('.line').append(circle);
               }
           }
        }

        function updateCircleWithToday(pos){
           var lineAdded = false;
           var circle = $('#circle-' + pos);

           //When we merge dates we have one less position
           if (circle.length === 0){
               circle = $('#circle-' + (pos - 1));
           }

           circle.addClass('active today');
           var label = circle.find('.timeline-top');
           label.removeClass('timeline-top ' + timeline.class[pos] + '-popup hidden');
           label.addClass('today');
           var today = '<span class="today-label">Today</span>';
           today += '<div class="days-left">' + timeline.daysLeft +'</div>';
           today += '<div class="days-left-label">days left</div>';
           label.html(today);

           if (i != 0){
               var width = parseFloat($('.circle.today')[0].style.left);
               var blueLine = '<div class="line-past" style="width: ' + width + '%"></div>';
               $('.line').prepend(blueLine);
               lineAdded = true;
           }

           return lineAdded;
        }


        function addTodayCircle(){
           if (!timeline.today.isBefore(timeline.dates[0]) || !timeline.today.isAfter(timeline.dates[timeline.dates.length - 1])){
               if (timeline.today.isSame(timeline.dates[0])){
                   lineAdded =  updateCircleWithToday(0);
               } else if (timeline.today.isSame(timeline.dates[timeline.dates.length - 1])){
                   lineAdded = updateCircleWithToday(timeline.dates.length - 1);
               } else {
                   var lineAdded = false;
                   for (var i = 0; i < timeline.dates.length - 1; i++){
                       if (i > 0 && timeline.today.isSame(timeline.dates[i])){
                           lineAdded = updateCircleWithToday(i);
                           break;
                       } else if (timeline.today.isAfter(timeline.dates[i]) && timeline.today.isBefore(timeline.dates[i+1])){
                           var prevLeft = parseFloat($('#circle-' + i)[0].style.left);
                           var afterLeft = parseFloat($('#circle-' + (i + 1))[0].style.left);
                           var bigDiff = moment.duration(timeline.dates[i + 1].diff(timeline.dates[i])).asDays();
                           var todayDiff = moment.duration(timeline.today.diff(timeline.dates[i])).asDays();
                           var leftDiff = afterLeft - prevLeft;
                           var newLeft = prevLeft + (todayDiff * leftDiff / bigDiff);

                           var circle = '<div class="circle active today" id="circle-today" style="left: ' + newLeft + '%;">';
                           circle += '<div class="timeline-label today">';
                           circle += '<span class="today-label">Today</span>';
                           circle += '<div class="days-left">' + timeline.daysLeft +'</div>';
                           circle += '<div class="days-left-label">days left</div>';
                           circle += '</div>';
                           circle += '</div>';
                           $('.line').append(circle);
                           var blueLine = '<div class="line-past" style="width: ' + newLeft + '%"></div>';
                           $('.line').prepend(blueLine);
                           lineAdded = true;
                           break;
                       }
                   }
                   if (!lineAdded && (timeline.msgLabel === null || timeline.msgLabel.indexOf('finished') !== -1)){
                       var blueLine = '<div class="line-past" style="width: 100%"></div>';
                       $('.line').prepend(blueLine);
                   }
               }

           }
        }

        function updateCampaignLabel(){
           if (timeline.msgLabel !== null){
               $('.line-container').css('margin-top', '20px');
               $('.campaign-label').css('display', 'block');
               $('.campaign-label').text(timeline.msgLabel);
               $('.overview-top-separator').css('margin-top', '50px');
           }
        }

        var makeTooltip = function(d, $$, config, defaultTitleFormat, defaultValueFormat, color, useFormat, useTitle, labels, popupString){
           var valueFormat = config.tooltip_format_value || defaultValueFormat,
               nameFormat = config.tooltip_format_name || function (name) { return name; },
               text, i, title, value, name, bgcolor;

           if (useTitle){
               var titleFormat = config.tooltip_format_title || defaultTitleFormat;
           }

           for (i = 0; i < d.length; i++) {
               if (! (d[i] && (d[i].value || d[i].value === 0))) { continue; }

               if (d[i] && d[i].id === 'total'){ continue;}


               if (useTitle && ! text) {
                   title = titleFormat ? titleFormat(d[i].x) : d[i].x;
                   if (labels !== undefined && labels !== null){
                       title = labels[d[i].x];
                   }

                   text = "<table class='" + $$.CLASS.tooltip + "'>" + (title || title === 0 ? "<tr><th colspan='2'>" + title + "</th></tr>" : "");
               }

               name = nameFormat(d[i].name);
               if (useFormat){
                   value = valueFormat(d[i].value, d[i].ratio, d[i].id, d[i].index);
               } else {
                   value = d[i].value;
               }

               bgcolor = $$.levelColor ? $$.levelColor(d[i].value) : color(d[i].id);

               if (useTitle){
                   text += "<tr class='" + $$.CLASS.tooltipName + "-" + d[i].id + "'>";
                   text += "<td class='custom-name'><span style='background-color:" + bgcolor + "'></span>" + name + "</td>";
                   text += "<td class='value'>" + value + "</td>";
               } else {
                   text = "<table class='" + $$.CLASS.tooltip + "'>"
                   text += "<tr class='" + $$.CLASS.tooltipName + "-" + d[i].id + "'>";

                   if (value === 100 && popupString !== null & popupString !== undefined && popupString.indexOf('target') !== -1) {
                       text += "<td class='value'>Target achieved</td>";
                   } else {
                       text += "<td class='value'>" + value;
                       if (value === 1 && popupString !== null & popupString !== undefined && popupString.indexOf('commitment') !== -1){
                           text += " commitment</td>";
                       } else if (popupString !== undefined && popupString !== null) {
                           text += popupString + "</td>";
                       } else {
                           text += "</td>";
                       }
                   }
               }
               text += "</tr>";
           }
           return text + "</table>";
        }

        function prepareDataForThemeChart(){
           var data = {
               'draft': ['Draft'],
               'approved': ['Approved'],
               'total': ['total'],
               'labels': []
           };

           for (var key in overviewData.new_stats.data){
               data.draft.push(overviewData.new_stats.data[key].draft);
               data.approved.push(overviewData.new_stats.data[key].approved);
               data.total.push(overviewData.new_stats.data[key].total);
               data.labels.push(overviewData.new_stats.labels[key]);
           }

           return data;
        }

        function createImgLegend(){
            var srcUrl = '<?php echo site_url('assets/images/ooc')?>/';
            var counter = 0;
            var x = null;
            var y = null;
            var yShifts = [];
            d3.selectAll("#second-chart .c3-axis-x .tick")
                .each(function(d){
                    var coords = this.getScreenCTM()
                    yShifts.push(coords.f);
                });

            for (var key in overviewData.new_stats.labels){
                var imgUrl = srcUrl + key + '.png';

                d3.select("#second-chart .c3-axis-x")
                    .append("image")
                    .attr("xlink:href", imgUrl)
                    .attr("width", "18px")
                    .attr("height", "18px")
                    .attr("x", function(d) {
                        if (x === null){
                            var bbox = this.parentNode.getBBox();
                            x = bbox.x - 10;
                        }
                        return x;
                    })
                    .attr("y", function() {
                        if (y === null) {
                            y = 13;
                        } else {
                            var delta = yShifts[counter] - yShifts[counter-1];
                            y += delta;
                        }
                        return y;
                    });
                counter += 1;
            }
        }

        function getGaugeBreakpointsAndColors(){
            var config = {
                colors: ['#60B044'],
                values: []
            }
            if (timeline.timelineCovered >= 35){
                config.colors = ['#FF0000', '#F97600', '#F6C600', '#60B044'];
                config.values = [50 , 70, 90, 100]
            }

            return config;
        }

        function getBudgetForTypesData(){
            var data = [];
            var type_keys = Object.keys(overviewData.budget_stats.budget_type);
            for (var i = 0; i < type_keys.length ; i++){
                if (overviewData.budget_stats.budget_type[type_keys[i]]["budget"] > 0) {
                    data.push([ overviewData.budget_stats.types[type_keys[i]], overviewData.budget_stats.budget_type[type_keys[i]]["budget"]]);
                }
            }
            return data;
        }

        function getBudgetForThemesDataAndColors(){
            var data = [];
            var colorPattern = [];
            var theme_keys = Object.keys(overviewData.budget_stats.budget_theme);
            colorIdx = 0;
            for (var i = 0; i < theme_keys.length ; i++){
                if (overviewData.budget_stats.budget_theme[theme_keys[i]]["budget"] > 0) {
                    if (colors.theme.hasOwnProperty(theme_keys[i].toLowerCase())){
                        colorPattern.push(colors.theme[theme_keys[i].toLowerCase()]);
                    } else {
                        colorPattern.push(colors.selColors[colorIdx]);
                        colorIdx += 1;
                    }
                    data.push([ overviewData.budget_stats.themes[theme_keys[i]], overviewData.budget_stats.budget_theme[theme_keys[i]]["budget"]]);
                }
            }
            return [data, colorPattern];
        }

        var makeDonutTooltip = function(d, $$, baseData, searchObj){
           var text, i, value, commits;
           for (i = 0; i < d.length; i++) {
               if (! (d[i] && (d[i].value || d[i].value === 0))) { continue; }

               if (d[i] && d[i].id === 'total'){ continue;}

               value = d[i].value;
               value = '$' + value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");

               var searchValues = Object.values(searchObj);
               var idx = searchValues.indexOf(d[i].id);
               var searchKey = Object.keys(searchObj)[idx];

               text = "<table class='" + $$.CLASS.tooltip + "'>"
               text += "<tr>";
               text += "<td class='value'><b>" + value + '</b> (' + (Math.round((d[i].value * 100 / overviewData.budget_stats.total)* 10) / 10) + '%)';
               if (searchKey !== undefined && searchKey !== null){
                   commits = baseData[searchKey].commits;
                   text += "<br />";
                   text += "<b>" + commits + "</b> commitment" + (commits == 1 ? '': 's') + ' (' + (Math.round((commits * 100 / overviewData.new) * 10) / 10) + '%)';
                   text += "</td>";
               }

               text += "</tr>";
           }
           return text + "</table>";
        }

        //Start by drawing the timeline
        if (srcDates.length > 0){
           var timeline = calculateTimeline(srcDates);
           makeCircles();
           addTodayCircle();
           updateCampaignLabel();

           $(".circle").mouseenter(function() {
               $(this).addClass('hover');
               $(this).find('.timeline-top').removeClass('hidden');
           });

           $(".circle").mouseleave(function() {
               $(this).removeClass('hover');
               $(this).find('.timeline-top').addClass('hidden');
           });
        }


        //Draw the dasboard
        if (overviewData !== undefined){
           var gaugeConfig = getGaugeBreakpointsAndColors()
           var firstGaugeValue = (parseInt(overviewData.updated) + parseInt(overviewData.closed)) * 100 / parseInt(overviewData.update_target);
           firstGaugeValue = firstGaugeValue > 100 ? 100 : firstGaugeValue;
           var firstGauge = c3.generate({
               bindto: '#first-gauge',
               data: {
                   columns: [
                       ['data', firstGaugeValue]
                   ],
                   type: 'gauge'
               },
               legend: {
                   show: false
               },
               color: {
                   pattern: gaugeConfig.colors,
                   threshold: {
                       values: gaugeConfig.values
                   }
               },
               tooltip: {
                   contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
                       var $$ = this, config = $$.config;

                       return makeTooltip(d, $$, config, defaultTitleFormat, defaultValueFormat, color, false, false, null, '% of target');
                   }
               },
               size: {
                   height: 120
               }
           });

           var firstChart = c3.generate({
               bindto: '#first-chart',
               data: {
                   columns: [
                       ['Updated', overviewData.updated],
                       ['Closed', overviewData.closed],
                       ['Untouched', (overviewData.existing - overviewData.updated - overviewData.closed)]
                   ],
                   type : 'pie',
                   colors: {
                       'Updated': '#1b80e5',
                       'Closed': '#979797',
                       'Untouched': '#7ed321'
                   }
               },
               size: {
                   height: 250
               },
               tooltip: {
                   contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
                       var $$ = this, config = $$.config;

                       return makeTooltip(d, $$, config, defaultTitleFormat, defaultValueFormat, color, false, false, null, ' commitments');
                   }
               }
           });

           var secondGaugeValue = parseFloat(overviewData.new_p);
           secondGaugeValue = secondGaugeValue > 100 ? 100 : secondGaugeValue;
           var secondGauge = c3.generate({
               bindto: '#second-gauge',
               data: {
                   columns: [
                       ['data', secondGaugeValue]
                   ],
                   type: 'gauge'
               },
               legend: {
                   show: false
               },
               color: {
                   pattern: gaugeConfig.colors,
                   threshold: {
                       values: gaugeConfig.values
                   }
               },
               tooltip: {
                   contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
                       var $$ = this, config = $$.config;

                       return makeTooltip(d, $$, config, defaultTitleFormat, defaultValueFormat, color, false, false, null, '% of target');
                   }
               },
               size: {
                   height: 120
               }
           });

           var barChartData = prepareDataForThemeChart();
           var secondChart = c3.generate({
               bindto: '#second-chart',
               data: {
                   columns: [
                       barChartData.draft,
                       barChartData.approved,
                       barChartData.total
                   ],
                   type: 'bar',
                   groups: [
                       ['Draft', 'Approved']
                   ],
                   types: {
                       total: 'line'
                   },
                   labels: {
                       format: {
                           total: d3.format(''),
                       }
                   }
               },
               bar: {
                   width: 20,
               },
               legend: {
                   show: true,
                   hide: ['total']
               },
               axis: {
                   rotated: true,
                   x: {
                       type: 'category',
                       show: true
                   },
                   y: {
                       show: false
                   }
               },
               color: {
                   pattern: ['#1b80e5', '#7ed321']
               },
               tooltip: {
                   contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
                       var $$ = this, config = $$.config;
                       return makeTooltip(d, $$, config, defaultTitleFormat, defaultValueFormat, color, true, true, barChartData.labels);
                   }
               }
           });
           createImgLegend();

           if (parseInt(overviewData.budget_stats.total) > 0){
               var firstBudgetChart = c3.generate({
                   bindto: '#first-budget-chart',
                   data: {
                       columns: getBudgetForTypesData(),
                       type : 'donut',
                   },
                   tooltip: {
                       contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
                           var $$ = this, config = $$.config;
                           return makeDonutTooltip(d, $$, overviewData.budget_stats.budget_type, overviewData.budget_stats.types);
                       }
                   },
                   size: {
                       height: 300
                   },
                   legend: {
                       item: { onclick: function () {} }
                   }
               });

               var budgetThemeData = getBudgetForThemesDataAndColors()
               var secondBudgetChart = c3.generate({
                   bindto: '#second-budget-chart',
                   data: {
                       columns: budgetThemeData[0],
                       type : 'donut',
                   },
                   color: {
                       pattern: budgetThemeData[1]
                   },
                   tooltip: {
                       contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
                           var $$ = this, config = $$.config;
                           return makeDonutTooltip(d, $$, overviewData.budget_stats.budget_theme, overviewData.budget_stats.themes);
                       }
                   },
                   size: {
                       height: 300
                   },
                   legend: {
                       item: { onclick: function () {} }
                   }
               });
           }
        }



        /* COMMITMENTS */
        var table = $('#commits-table').DataTable({
            ordering: false,
            searching: false,
            lengthChange: false,
            info: false,
            responsive: {
                details: false
            },
            serverSide: true,
            pageLength: 25,
            pagingType: 'simple_numbers',
            stateSave: true,
            ajax: {
                url: '<?php echo site_url('rest/commitments') ?>',
                type: 'POST',
                dataSrc: 'items',
                data: function(d){
                    Object.assign(d, serializeForm());
                    return d;
                }
            },
            preDrawCallback: function (settings) {
                var api = new $.fn.dataTable.Api(settings);
                var pagination = $(this)
                    .closest('.dataTables_wrapper')
                    .find('.dataTables_paginate');
                pagination.toggle(api.page.info().pages > 1);
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
            },
            language: {
                emptyTable: 'No data to display',
                paginate: {
                    next: '<i class="fas fa-angle-double-right"></i>',
                    previous: '<i class="fas fa-angle-double-left"></i>'
                }
            },
            columns: [{
                data: 'proposal_theme',
                render: function(data, type){
                    var url = '<?php echo site_url('assets/images/ooc')?>/' + data.id + '.png';
                    return '<img src="'+ url +'" style="height: 20px; width: 20px;" title="'+ data.name +'" />'
                }
            },{
                data: 'proposal_title',
                render: function (data, type, row){
                    var text = '';
                    if (row.proposal_status === 'submitted' || row.proposal_status === 'approved' || (row.proposal_status === 'progress_update' && parseInt(row.proposal_completion) === 100)){
                        text += '<i class="fas fa-exclamation-triangle" title="Action required"></i> ';
                    }
                    text += data;

                    return text;
                }
            },{
                data: 'proposal_year'
            },{
                data: 'applicant_legal_name'
            },{
                data: 'proposal_completion',
                render: function (data, type) {
                    return data + '%';
                }
            }, {
                data: 'proposal_status',
                render: function(data, type){
                    if (data.indexOf('_') != -1){
                        data = data.replace('_', ' ');
                    }
                    return OOC.capitalizeAddress(data);
                }
            }]
        });

        $('#commits-table tbody').on( 'click', 'td', function(){
            var idx = parseInt($(this).index());
            if (idx !== 0){
                var data = table.row( this.parentNode ).data();
                if (data.hasOwnProperty('proposal_id') && data.proposal_id !== ''){
                    window.location = url + '/' + data.proposal_id;
                }
            }
        });

        OOC.registerEnterKey($('#free-text-search'), table.ajax.reload);
        OOC.registerEscKey($('#free-text-search'), function(){
            $('#free-text-search').val('');
            table.ajax.reload();
        });
        $('.side-menu-search').click(function () {
            table.ajax.reload();
        });

        var yearCombo = $('#year-combo').select2({
            theme:"bootstrap"
        });

        yearCombo.on('select2:select', function(e){
            table.ajax.reload();
        });

        var orgCombo = $('#org-combo').select2({
            theme: 'bootstrap',
            tags: false,
            delay: 250,
            ajax: {
                type: 'POST',
                url: '<?php echo site_url('rest/organisation') ?>',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        term: params.term,
                        page: params.page || 1
                    };

                    return query;
                },
                processResults: function (data, params) {
                    var page = params.page || 1;

                    if (page === 1){
                        data.items.unshift({id: 'all', text: 'All'});
                    }

                    return {
                        results: data.items,
                        pagination: data.pagination
                    }
                },
                cache: true
            }
        });

        orgCombo.on('select2:select', function (e) {
            table.ajax.reload();
        });

        var compCombo = $('#completion-combo').select2({
            theme:"bootstrap"
        });

        compCombo.on('select2:select', function(e){
            table.ajax.reload();
        });

        var cb = $('.commitments-form input[type=checkbox]');
        for (var i = 0; i < cb.length; i++){
            $(cb[i]).click(function(){
                table.ajax.data = serializeForm();
                table.ajax.reload();
            });
        }

        function getCbState(id){
            var cbs = $('[id*=' + id + ']');
            var data = [];
            for (var i = 0; i < cbs.length ; i++) {
                data.push({
                    id: '#' + cbs[i].id,
                    checked: cbs[i].checked
                });
            }

            return data;
        }

        function getComboState(id){
            var combo = $(id).select2('data')[0];
            var data = {
                id: id,
                value: combo.id,
                text: combo.text
            };

            return data;
        }

        function serializeForm(){
            var values = commitForm.serializeArray();
            var postValues = {
                q: '',
                area: [],
                year: '',
                completion: '',
                status: [],
                organisation: ''
            };

            values.forEach(function (item) {
                if (item.name === 'area' || item.name == 'status'){
                    postValues[item.name].push(item.value);
                } else {
                    postValues[item.name] = item.value;
                }
            });

            if (!isLoading){
                var copy = Object.assign({}, postValues);
                copy.area = getCbState('check-theme');
                copy.status = getCbState('check-commit');
                copy.organisation = getComboState('#org-combo');
                copy.completion = getComboState('#completion-combo');
                copy.year = getComboState('#year-combo');

                OOC.setToLocalSorage(copy, '#commitments');
            }


            return postValues;
        }

        function publish(){
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('hosts/publish')?>',
                success: function (data) {
                    if (data.success) {
                        successAlert.removeClass('hidden');
                        var cell = $('.success-msg .alert-success .table-cell').last();
                        var span = cell.children('span');
                        span.html(data.message);
                        hideAlert('success');
                        table.ajax.reload();
                        processUserBadges(data);
                    }
                    $('button .fa-spin').addClass('hidden');
                },
                error: function (data) {
                    alert.removeClass('hidden');
                    var cell = $('.error-msg .alert-danger .table-cell').last();
                    var span = cell.children('span');
                    span.html(data.responseJSON.message);
                    hideAlert('error');
                    processUserBadges(data.responseJSON);
                    $('button .fa-spin').addClass('hidden');
                }
            });
        }

        $('.action-publish').click(function (e) {
            e.preventDefault();
            var btn = $('button .fa-spin');
            if (btn.hasClass('hidden')){
                btn.removeClass('hidden');
                publish();
            }
        });

        function exportCSV(type){
            var formData, url, fileName;
            switch (type) {
                case 'commits':
                    formData = Object.assign({}, serializeForm());
                    url = '<?php echo site_url('hosts/exportCommitsCSV')?>';
                    fileName = 'commitments.csv';
                    break;
                case 'users':
                    formData = Object.assign({}, serializeUserForm());
                    url = '<?php echo site_url('hosts/exportUsersCSV')?>';
                    fileName = 'users.csv';
                    break;
                case 'orgs':
                    formData = Object.assign({}, serializeOrgForm());
                    url = '<?php echo site_url('hosts/exportOrgsCSV')?>';
                    fileName = 'organisations.csv';
                    break;
            }


            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                success: function (resp) {
                    if (resp.success) {
                        successAlert.removeClass('hidden');
                        var cell = $('.success-msg .alert-success .table-cell').last();
                        var span = cell.children('span');
                        span.html(resp.message);
                        hideAlert('success');

                        var downloadLink = document.createElement("a");
                        var blob = new Blob(["\ufeff", resp.data]);
                        var url = URL.createObjectURL(blob);
                        downloadLink.href = url;
                        downloadLink.download = fileName;
                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                    }
                },
                error: function (data) {
                    alert.removeClass('hidden');
                    var cell = $('.error-msg .alert-danger .table-cell').last();
                    var span = cell.children('span');
                    span.html(data.responseJSON.message);
                    hideAlert('error');
                }
            });
        }

        $('.btn-download-commits').click(function (e) {
            e.preventDefault();
            exportCSV('commits');
        });

        /* USER */
        var usersTable = $('#users-table').DataTable({
            ordering: false,
            searching: false,
            lengthChange: false,
            info: false,
            responsive: {
                details: false
            },
            serverSide: true,
            pageLength: 25,
            pagingType: 'simple_numbers',
            stateSave: true,
            ajax: {
                url: '<?php echo site_url('rest/users')?>',
                type: 'POST',
                dataSrc: 'items',
                data: function(d){
                    Object.assign(d, serializeUserForm());
                    return d;
                }
            },
            preDrawCallback: function (settings) {
                var api = new $.fn.dataTable.Api(settings);
                var pagination = $(this)
                    .closest('.dataTables_wrapper')
                    .find('.dataTables_paginate');
                pagination.toggle(api.page.info().pages > 1);
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
            },
            language: {
                emptyTable: 'No data to display',
                paginate: {
                    next: '<i class="fas fa-angle-double-right"></i>',
                    previous: '<i class="fas fa-angle-double-left"></i>'
                }
            },
            columnDefs: [{
                targets: 0,
                className: 'text-center'
            }],
            columns: [{
                data: 'user_id',
                render: function (data, type){
                    // return '<div class="cb-table"><input type="checkbox" name="user_row[]" value="' + data +'"></div>';
                    return '<label class="cb-table"><input type="checkbox" name="user_row[]" value="' + data +'">'
                           + '<i class="far fa-square unchecked"></i>'
                           + '<i class="far fa-check-square checked"></i></label>';
                }
            },{
                data: 'user_firstname',
                render: function(data, type, row){
                    var text = '<div>';
                    if (row.user_status === 'P'){
                        text += '<i class="fas fa-exclamation-triangle" title="Action required"></i> ';
                    }

                    text += row.user_firstname + ' ' + row.user_name;
                    /*if (row.user_role === 'host'){
                        text += '<span class="fa-stack fa-2x" title="Host">';
                        text += '<i class="fas fa-user-circle fa-stack-1x"></i>';
                        text += '<i class="fas fa-cog fa-stack-1x"></i>';
                        text += '</span>';
                    }*/

                    text += '</div>';
                    return text;
                }
            },{
                data: 'organisation',
                render: function(data, type){
                    if (data === null){
                        data = {};
                        data.applicant_legal_name = ''
                    }
                    return data.applicant_legal_name;
                }
            },{
                data: 'user_email'
            },{
                data: 'user_status',
                render: function (data, type) {
                    switch (data) {
                        case 'A':
                            return 'Approved';
                        case 'P':
                            return 'Pending approval';
                        case 'I':
                            return 'Rejected';
                        default:
                            return 'Deleted';
                    }
                }
            }]
        });

        $('#users-table tbody').on( 'click', 'td', function(evt){
            var idx = parseInt($(this).index());
            if (idx !== 0){
                var data = usersTable.row( this.parentNode ).data();
                if (data.hasOwnProperty('user_id') && data.user_id !== ''){
                    window.location = '<?php echo site_url('hosts/view_user_profile') ?>/' + data.user_id;
                }
            }
        });

        var userOrgCombo = $('#user-org-combo').select2({
            theme: 'bootstrap',
            tags: false,
            delay: 250,
            ajax: {
                type: 'POST',
                url: '<?php echo site_url('rest/organisation') ?>',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        term: params.term,
                        page: params.page || 1
                    };

                    return query;
                },
                processResults: function (data, params) {
                    var page = params.page || 1;

                    if (page === 1){
                        data.items.unshift({id: 'all', text: 'All'});
                    }

                    return {
                        results: data.items,
                        pagination: data.pagination
                    }
                },
                cache: true
            }
        });

        userOrgCombo.on('select2:select', function (e) {
            usersTable.ajax.reload();
        });

        var userCb = $('.users-form input[type=checkbox]');
        for (var i = 0; i < userCb.length; i++){
            $(userCb[i]).click(function(){
                usersTable.ajax.data = serializeForm();
                usersTable.ajax.reload();
            });
        }

        /* TABLE TH CHECKBOX*/
        $('input[name="users_select_all"]').on('click', function(){
            var rows = usersTable.rows().nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        /* TABLE TD CHECKBOX*/
        $('#users-table tbody').on('change', 'input[type="checkbox"]', function(){
            var rows = usersTable.rows().nodes();
            var checked = $('input[type="checkbox"]:checked', rows).length;
            var status= false;
            if (rows.length === checked){
                status = true;
            }

            $('input[name="users_select_all"]').prop('checked', status);
        });


        OOC.registerEnterKey($('#free-user-search'), usersTable.ajax.reload);
        OOC.registerEscKey($('#free-user-search'), function(){
            $('#free-user-search').val('');
            usersTable.ajax.reload();
        });
        $('.side-user-search').click(function () {
            usersTable.ajax.reload();
        });

        function approveRejectUsers(ids, action){
            var url;
            switch (action) {
                case 'approve':
                    url = '<?php echo site_url('hosts/activate_users') ?>';
                    break;
                case 'reject':
                    url = '<?php echo site_url('hosts/deactivate_users') ?>';
                    break;
                case 'delete':
                    url = '<?php echo site_url('hosts/delete_users') ?>';
                    break;
                case 'add':
                    url = '<?php echo site_url('hosts/add_host') ?>';
                    break;
                case 'remove':
                    url = '<?php echo site_url('hosts/remove_host') ?>';
                    break;
                default:
                    url = '<?php echo site_url('hosts/activate_users') ?>';
                    break;
            }

            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: {ids: ids},
                success: function (data) {
                    if (data.success) {
                        usersTable.ajax.reload();
                        processUserBadges(data);
                    }

                    $('button .fa-spin').addClass('hidden');
                },
                error: function (data) {
                    alert.removeClass('hidden');
                    var cell = $('.error-msg .alert-danger .table-cell').last();
                    var span = cell.children('span');
                    span.html(data.responseJSON.message);
                    hideAlert('error');
                    processUserBadges(data.responseJSON);
                    $('button .fa-spin').addClass('hidden');
                }
            });
        }

        function hideAlert(type){
            setTimeout(function(){
                if (type === 'success'){
                    successAlert.addClass('hidden');
                } else {
                    alert.addClass('hidden');
                }
            }, 5000);
        }

        function processUserBadges(response){
            if (response.pending_users === 0){
                $('.users-badge').remove();
            } else {
                $('.users-badge').text(response.pending_users);
            }
        }

        function executeUserAction(action){
            var ids = [];
            usersTable.$('input[type="checkbox"]').each(function(){
                if (this.checked){
                    ids.push(this.value);
                }
            });

            if (ids.length > 0){
                approveRejectUsers(ids, action);
                $('input[name="users_select_all"]').prop('checked', false);
            } else {
                $('button .fa-spin').addClass('hidden');
            }
        }

        $('.action-approve').click(function (e) {
            e.preventDefault();
            var btn = $('button .fa-spin');
            if (btn.hasClass('hidden')){
                btn.removeClass('hidden');
                executeUserAction('approve');
            }
        });

        $('.action-reject').click(function (e) {
            e.preventDefault();
            var btn = $('button .fa-spin');
            if (btn.hasClass('hidden')){
                btn.removeClass('hidden');
                executeUserAction('reject');
            }
        });

        $('.action-delete').click(function (e) {
            e.preventDefault();
            var btn = $('button .fa-spin');
            if (btn.hasClass('hidden')){
                btn.removeClass('hidden');
                executeUserAction('delete');
            }
        });

        $('.action-add-host').click(function (e) {
            e.preventDefault();
            var btn = $('button .fa-spin');
            if (btn.hasClass('hidden')){
                btn.removeClass('hidden');
                executeUserAction('add');
            }
        });

        $('.action-remove-host').click(function (e) {
            e.preventDefault();
            var btn = $('button .fa-spin');
            if (btn.hasClass('hidden')){
                btn.removeClass('hidden');
                executeUserAction('remove');
            }
        });


        $('.btn-download-users').click(function (e) {
            e.preventDefault();
            exportCSV('users');
        });

        function serializeUserForm(){
            var values = userForm.serializeArray();
            var postValues = {
                q: '',
                status: [],
                role: [],
                organisation: ''
            };

            values.forEach(function (item) {
                if (item.name === 'status' || item.name === 'role'){
                    postValues[item.name].push(item.value);
                } else {
                    postValues[item.name] = item.value;
                }
            });

            if (!isLoading) {
                var copy = Object.assign({}, postValues);
                copy.organisation = getComboState('#user-org-combo');
                copy.status = getCbState('check-user');

                OOC.setToLocalSorage(copy, '#users');
            }

            return postValues;
        }

        /* ORGANISATION */
        var mergeBtn = $('.btn-merge');
        var orgTable = $('#org-table').DataTable({
            ordering: false,
            searching: false,
            lengthChange: false,
            info: false,
            responsive: {
                details: false
            },
            serverSide: true,
            pageLength: 25,
            pagingType: 'simple_numbers',
            stateSave: true,
            ajax: {
                url: '<?php echo site_url('rest/organisations') ?>',
                type: 'POST',
                dataSrc: 'items',
                data: function(d){
                    Object.assign(d, serializeOrgForm());
                    return d;
                }
            },
            preDrawCallback: function (settings) {
                var api = new $.fn.dataTable.Api(settings);
                var pagination = $(this)
                    .closest('.dataTables_wrapper')
                    .find('.dataTables_paginate');
                pagination.toggle(api.page.info().pages > 1);
                unregisterDeleteBtns();
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                mergeBtn.attr('disabled', true);
                registerDeleteBtns()
            },
            language: {
                emptyTable: 'No data to display',
                paginate: {
                    next: '<i class="fas fa-angle-double-right"></i>',
                    previous: '<i class="fas fa-angle-double-left"></i>'
                }
            },
            columnDefs: [{
                targets: 5,
                className: 'text-center'
            }, {
                targets: 6,
                className: 'text-center'
            }],
            columns: [{
               data: 'applicant_id',
               render: function(data, type){
                   // return '<div class="cb-table"><input type="checkbox" name="org_row[]" value="' + data +'"></div>';
                   return '<label class="cb-table"><input type="checkbox" name="org_row[]" value="' + data +'">'
                       + '<i class="far fa-square unchecked"></i>'
                       + '<i class="far fa-check-square checked"></i></label>';
               }
            },{
                data: 'applicant_legal_name',
                render: function(data, type, row){
                    var text = '';
                    if (!row.complete) {
                        text += '<i class="fas fa-exclamation" title="Incomplete profile"></i> ';
                    }
                    text += data;

                    return text;
                }
            },{
                data: 'type',
                render: {
                    _: 'name'
                }
            },{
                data: 'applicant_country_code'
            },{
                data: 'applicant_city'
            },{
                data: 'commit_count'
            },{
                data: 'user_count'
            },{
                data: 'applicant_id',
                render: function(data, type, row){
                    var html = '';
                    if (row.commit_count === 0 && row.user_count === 0){
                        html += '<button class="btn btn-default delete-type" title="Delete" data-id="' + data + '"><i class="fas fa-trash-alt"></i></button>'
                    }

                    return html;
                }
            }]
        });

        function registerDeleteBtns(){
            var delBtns = $('button.delete-type');
            for (var i = 0; i < delBtns.length; i++){
                $(delBtns[i]).click(function(evt){
                    evt.preventDefault();
                    deleteOrganisation($(this).attr('data-id'));
                });
            }
        }

        function unregisterDeleteBtns(){
            var delBtns = $('button.delete-type');
            for (var i = 0; i < delBtns.length; i++){
                $(delBtns[i]).unbind('click');
            }
        }

        function deleteOrganisation(id){
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('rest/delete_organisation') ?>',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function (data) {
                    if (data.success) {
                        orgTable.ajax.reload();
                    }
                },
                error: function (data) {
                    alert.removeClass('hidden');
                    var cell = $('.error-msg .alert-danger .table-cell').last();
                    var span = cell.children('span');
                    span.html('There was an error deleting the selected otganisation.');
                    hideAlert('error');
                }
            });

            console.log(id);
        }


        $('#org-table tbody').on( 'click', 'td', function(evt){
            var idx = parseInt($(this).index());
            if (idx !== 0 && idx !== 7){
                var data = orgTable.row( this.parentNode ).data();
                if (data.hasOwnProperty('applicant_id') && data.applicant_id !== ''){
                    window.location = '<?php echo site_url('hosts/view_organisation_profile') ?>/' + data.applicant_id;
                }
            }
        });

        var cbOrg = $('.org-form input[type=checkbox]');
        for (var i = 0; i < cbOrg.length; i++){
            $(cbOrg[i]).click(function(){
                orgTable.ajax.data = serializeOrgForm();
                orgTable.ajax.reload();
            });
        }

        /* TABLE TD CHECKBOX*/
        var mergeObj = {
            orgs: [],
            fromId: null,
            toId: null,
            direction: 'ltr'
        };

        function resetMergeObj(){
            mergeObj = {
                orgs: [],
                fromId: null,
                toId: null,
                direction: 'ltr'
            };
        }

        $('#org-table tbody').on('change', 'input[type="checkbox"]', function(){
            var rows = orgTable.rows().nodes();
            var checked = $('input[type="checkbox"]:checked', rows).length;

            if (checked > 2){
                $(this).prop('checked', false);
                return;
            }

            if (checked === 2 && (mergeBtn.attr('disabled') === 'disabled' || mergeBtn.attr('disabled') === undefined)){
                mergeBtn.attr('disabled', false);
            } else {
                if (mergeBtn.attr('disabled') === undefined) {
                    mergeBtn.attr('disabled', true);
                }
            }
        });

        function buildRadioHtml(from, to, direction){
            var elem = '<div class="radio"><label><input type="radio" name="direction" value="' + direction + '"';
            if (direction === 'ltr') {
                elem += ' checked';
            }

            elem += '>';
            return  elem + from.applicant_legal_name + ' <b>into</b> ' + to.applicant_legal_name + '</label></div>';
        }

        function buildOrgSummary(org){
            var creation = new moment(org.applicant_created);
            var user = 'users';
            var commit = 'commitments'
            if (parseInt(org.user_count) === 1){
                user = 'user';
            }

            if (parseInt(org.commit_count) === 1){
                commit = 'commitment';
            }

            return  '<div class="col-md-5 text-center">'
                    + '<div class="org-name">' + org.applicant_legal_name + '</div>'
                    + '<div><i class="fas fa-user"></i>' + org.user_count + ' ' + user + '</div>'
                    + '<div class="org-detail-margin"><i class="fas fa-clipboard-list"></i>' + org.commit_count + ' ' + commit +'</div>'
                    + '<div class="org-detail-margin">Created on ' + creation.format('DD-MM-YYYY') + '</div>'
                    + '</div>';

        }

        function registerEventsForRadios(){
            $('input[name="direction"]').change(function(e){
                var value = $(this).val();
                var toSet = 'fa-arrow-alt-circle-right';
                var toRem = 'fa-arrow-alt-circle-left';
                var fromId = mergeObj.orgs[0].applicant_id;
                var toId = mergeObj.orgs[1].applicant_id;
                var name = mergeObj.orgs[0].applicant_legal_name;
                if (value !== 'ltr'){
                    toSet = 'fa-arrow-alt-circle-left';
                    toRem = 'fa-arrow-alt-circle-right';
                    fromId = mergeObj.orgs[1].applicant_id;
                    toId = mergeObj.orgs[0].applicant_id;
                    name = mergeObj.orgs[1].applicant_legal_name;
                }
                $('.direction-arrow').removeClass(toRem);
                $('.direction-arrow').addClass(toSet);
                $('.org-name-alert').text(name);
                mergeObj.fromId = fromId;
                mergeObj.toId = toId;
                mergeObj.direction = value;
            });
        }

        $('#merge-modal').on('shown.bs.modal', function (e) {
            var data = orgTable.data().toArray();

            var orgs = []
            orgTable.$('input[type="checkbox"]').each(function(){
                if (this.checked){
                    var id = parseInt(this.value);
                    var org = data.find(function(org){
                        if (parseInt(org.applicant_id) === id){
                            orgs.push(org);
                        }
                    });
                }
            });

            orgs.sort(function(a,b){
                return a.applicant_id < b.applicant_id ? 1 : -1;
            });

            mergeObj.orgs = orgs;
            mergeObj.fromId = orgs[0].applicant_id;
            mergeObj.toId = orgs[1].applicant_id;
            $('.radio-container').append(buildRadioHtml(orgs[0], orgs[1], 'ltr'));
            $('.radio-container').append(buildRadioHtml(orgs[1], orgs[0], 'rtl'));
            $('.org-details').append(buildOrgSummary(orgs[0]));
            $('.org-details').append('<div class="col-md-2 text-center"><i class="far fa-arrow-alt-circle-right direction-arrow"></i></div>');
            $('.org-details').append(buildOrgSummary(orgs[1]));
            $('.org-name-alert').text(orgs[0].applicant_legal_name);

            registerEventsForRadios()
        });

        $('#merge-modal').on('hidden.bs.modal', function (e) {
            $('.radio-container').empty();
            $('.org-details').empty();
            $('.org-name-alert').empty();
            resetMergeObj();
        });

        function processOrgBadges(response){
            if (response.pending_orgs === 0){
                $('.orgs-badge').remove();
            } else {
                $('.orgs-badge').text(response.pending_users);
            }
        }

        function doMergeOrgs(){
            var url = '<?php echo site_url('hosts/merge_organisations') ?>';

            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: {
                    from: mergeObj.fromId,
                    to: mergeObj.toId
                },
                success: function (data) {
                    if (data.success) {
                        orgTable.ajax.reload();
                        processOrgBadges(data);
                    }

                    $('button .fa-spin').addClass('hidden');
                    $('#merge-modal').modal('hide');
                },
                error: function (data) {
                    $('#merge-modal').modal('hide');
                    alert.removeClass('hidden');
                    var cell = $('.error-msg .alert-danger .table-cell').last();
                    var span = cell.children('span');
                    span.html(data.responseJSON.message);
                    hideAlert('error');
                    processOrgBadges(data.responseJSON);
                    $('button .fa-spin').addClass('hidden');
                }
            });
        }

        $('.btn-do-submit').click(function (e) {
            e.preventDefault();
            var btn = $('button .fa-spin');
            if (btn.hasClass('hidden')){
                btn.removeClass('hidden');
                doMergeOrgs();
            }
        });

        $('.btn-download-orgs').click(function (e) {
            e.preventDefault();
            exportCSV('orgs');
        });

        function serializeOrgForm(){
            var values = orgForm.serializeArray();
            var postValues = {
                q: '',
                type: []
            };

            values.forEach(function (item) {
                if (item.name === 'type'){
                    postValues[item.name].push(item.value);
                } else {
                    postValues[item.name] = item.value;
                }

            });

            if (!isLoading) {
                var copy = Object.assign({}, postValues);
                copy.type = getCbState('check-org');

                OOC.setToLocalSorage(copy, '#organisations');
            }

            return postValues;
        }

        OOC.registerEnterKey($('#free-org-search'), orgTable.ajax.reload);
        OOC.registerEscKey($('#free-org-search'), function(){
            $('#free-org-search').val('');
            orgTable.ajax.reload();
        });

        $('.side-org-search').click(function () {
            orgTable.ajax.reload();
        });

        var typeCombo = $('#type-org-combo').select2({
            theme: 'bootstrap'
        });

        typeCombo.on('select2:select', function (e) {
            orgTable.ajax.reload();
        });

        function setStatus(){
            var storage = OOC.getFromLocalStorage();
            if (storage !== null && typeof(storage) === 'object' && Object.keys(storage).length > 0){
                OOC.cleanDataTablesStorage(storage.tab.replace('#', ''));
                $('.nav-tabs a[href="' + storage.tab + '"]').tab('show');
                var table = $(storage.table).DataTable();
                for (var key in storage) {
                    if (key === 'cb'){
                        for (var i = 0; i < storage.cb.length; i++) {
                            $(storage.cb[i]['id']).prop('checked', storage.cb[i]['checked']);
                        }
                    } else if (key === 'combo'){
                        for (var i = 0; i < storage.combo.length; i++) {
                            if ($(storage.combo[i].id).find('option[value="' + storage.combo[i].value + '"]').length > 0){
                                $(storage.combo[i].id).val(storage.combo[i].value).trigger('change');
                            } else {
                                var newOption = new Option(storage.combo[i].text, storage.combo[i].value, true, true);
                                $(storage.combo[i].id).append(newOption).trigger('change');
                            }
                        }
                    } else if (key === 'text') {
                        $(storage[key].id).val(storage[key].value);
                    }
                }

                setTimeout(function(){table.ajax.reload(null, false)}, 10);
            }
            isLoading = false;
        }

        setStatus();

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var tab = $(e.target).attr("href"); // activated tab
            if (tab === '#users'){
                serializeUserForm();
            } else if (tab === '#commitments'){
                serializeForm();
            } else if (tab === '#organisations') {
                serializeOrgForm();
            } else {
                //TODO refactor by extracting amethod
                OOC.setToLocalSorage({}, '#overview');
            }
        });
    });
</script>
