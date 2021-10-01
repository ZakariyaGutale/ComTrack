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
?>
<div class="container">
    <div class="row top-row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h3><a class="commit-title" href="?p=<?php echo $row->proposal_id ?>" target="_blank"><?php echo htmlspecialchars($row->proposal_title) ?></a></h3>
        </div>
    </div>

    <div class="row text-center">
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div><img src="/src/images/ooc/<?php echo $row->id ?>.png" style="height: 40px; width: 40px;" /></div>
            <div class="icon-text"><?php echo $row->name ?></div>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div><i class="far fa-money-bill-alt"></i></div>
            <div class="icon-text"><?php echo number_format($row->proposal_budget, 0, '.', ',') ?> $</div>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div><i class="far fa-calendar"></i></div>
            <div class="icon-text"><?php echo $row->proposal_year ?></div>
            <?php if (strtotime($row->proposal_deadline)){ ?>
                <div style="font-size: 12px;">DEADLINE</div>
                <div><?php echo date('d/m/Y', strtotime($row->proposal_deadline)) ?></div>
            <?php } ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <label>Progress</label>
            <div class="progress">
                <div class="progress-bar progress-color <?php echo $row->proposal_completion == 0 ? 'progress-zero' : '' ?>" role="progressbar" aria-valuenow="<?php echo $row->proposal_completion ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $row->proposal_completion ?>%;"><?php echo $row->proposal_completion ?>%</div>
            </div>
        </div>
    </div>

    <?php if ($row->proposal_abstract) { ?>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <label>Abstract</label>
            <p><?php echo str_replace('_x000D_','<br>',$row->proposal_abstract) ?></p>
        </div>
    </div>
    <?php } ?>

    <?php if ($row->proposal_impact) { ?>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <label>Impact</label>
            <p><?php echo str_replace('_x000D_','<br>',$row->proposal_impact) ?></p>
        </div>
    </div>
    <?php } ?>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <span>
                    <?php echo $row->proposal_area_active == 0 ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>' ?>
                    <span class="area">Area actively managed</span>
                </span>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?php if ($row->proposal_area > 0) { ?>
                <span class="area">Protected area surface:</span>
                <span><?php echo number_format($row->proposal_area, 0, '.', ',') ?> km<sup>2</sup></span>
            <?php } ?>
        </div>
    </div>

    <?php if(isset($beneficiaries) && count($beneficiaries) > 0) {
        $countries = $this->config->item('countries');
        foreach($beneficiaries as $beneficiary) {
    ?>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="table-layout">
                    <div class="table-cell"><i class="fas fa-building" title="Organisation"></i></div>
                    <div class="table-cell">
                        <a href="#" class="beneficiary-link" data-id="<?php echo $beneficiary->applicant_id ?>" title="<?php echo $beneficiary->applicant_legal_name ?>"><?php echo $beneficiary->applicant_legal_name ?></a> - <?php echo $countries[$beneficiary->applicant_country_code] ?></li>
                    </div>
                </div>
            </div>
        </div>
    <?php }} ?>

    <?php if ($row->proposal_website) { ?>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="table-layout">
                    <div class="table-cell"><i class="fas fa-globe-americas"></i></div>
                    <div class="table-cell table-url"><a class="url" href="<?php echo $row->proposal_website ?>" target="_blank"><?php echo $row->proposal_website ?></a></div>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if ($row->attachments) { ?>
        <div class="row row-attachments">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="table-layout">
                    <div class="table-cell"><i class="fas fa-link"></i></div>
                    <div class="table-cell table-cell-middle">
                        <?php
                        foreach ($row->attachments as $attachment) {
                            echo '<div><span>'.$attachment->link_description.' : </span><a class="url" href="'.$attachment->link_url.'" target="_blank">'.$attachment->link_url.'</a></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="row">
        <?php if ($row->proposal_announcer) { ?>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <div class="table-layout">
                    <div class="table-cell"><i class="fas fa-bullhorn" title="Announcer"></i></div>
                    <div class="table-cell"><?php echo $row->proposal_announcer ?></div>
                </div>
            </div>
        <?php } ?>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="table-layout">
                <div class="table-cell"><i class="fas fa-language" title="Language"></i></div>
                <div class="table-cell"><?php echo $row->proposal_language ?></div>
            </div>
        </div>
    </div>
</div>
