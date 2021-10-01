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
    $countries = $this->config->item('countries');
?>

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h3><h3><a class="commit-title" href="?p=<?php echo $row->applicant_id ?>" target="_blank"><?php echo $row->applicant_legal_name ?></a></h3></h3>
            <p class="org-type"><?php $row->type->name ?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="table-layout">
                        <div class="table-cell"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="table-cell">
                            <span><?php echo $row->applicant_street ?></span>
                            <br>
                            <span><?php echo $row->applicant_post_code.' '.$row->applicant_city ?></span>
                            <br>
                            <span><?php echo $countries[$row->applicant_country_code] ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($row->applicant_web_page && $row->applicant_web_page != '') {?>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="table-layout">
                        <div class="table-cell"><i class="fas fa-globe-americas"></i></div>
                        <div class="table-cell table-url"><a class="url" href="<?php echo $row->applicant_web_page ?>" target="_blank"><?php echo $row->applicant_web_page ?></a></div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
            <div class="row">
                <?php
                    $counter = 0;
                    foreach($row->pocs as $item) {
                        if ($item->user_accept_pub == 1){
                            if ($counter > 0 && $counter % 2 == 0) {
                                echo '</div>';
                                echo '<div class="row">';
                            }
                            $counter += 1;
                ?>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div class="thumbnail text-center">
                        <div class="caption">
                            <p class="user-name"><?php echo $item->user_firstname.' '.$item->user_name ?></p>
                            <p><?php echo $item->user_function ?></p>
                            <p><i class="fas fa-phone"></i><span class="phone-span"><?php echo $item->user_phone ?></span></p>
                            <p><i class="fas fa-envelope"></i><a href="mailto:<?php echo $item->user_email ?>"><?php echo $item->user_email ?></a></p>
                        </div>
                    </div>
                </div>
                <?php }} ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h4>Commitments</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php foreach ($proposals as $project) { ?>
            <div class="media">
                <div class="media-left">
                    <img class="media-object" width="35" height="35" src="src/images/ooc/<?php echo $project->proposal_theme ?>.png" title="<?php echo $project->name ?>">
                </div>
                <div class="media-body">
                    <h5 class="media-heading"><?php echo '<a href="#" class="project-link" data-id="'.$project->proposal_id.'">'.$project->proposal_title.'</a>' ?></h5>
                    <p><?php
                        $text = $project->proposal_abstract;
                        if (strlen($text) > 250){
                            $text = substr($project->proposal_abstract, 0 ,250).' ...';
                        }
                        echo  $text;
                    ?></p>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
