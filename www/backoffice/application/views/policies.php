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

    $this->load->view('elements/header');
    $this->load->view('elements/navigation');
    echo $isHost;
?>
<div class="container main-screen">
    <div class="row center-block policy-container">
        <div class="col-md-12">
            <h1>Cookie Policy</h1>
        </div>
    </div>
    <div class="row center-block policy-container">
        <div class="col-md-12">
            <p>Thank you for visiting <a href="<?php echo $isHost ? site_url('hosts') : site_url('commitments') ?>">Our Ocean Conference BackOffice</a>. This cookies notice apply only to this website.</p>
            <p>By using this website, you are consenting to our use of cookies, if you do not agree to the cookies policy, hereby specified, you should not use this site.</p>
        </div>
    </div>
    <div class="row center-block policy-container">
        <div class="col-md-12">
            <h3>What are cookies?</h3>
            <p>Cookies are small text files that are placed on your computer by the websites you visit. They are widely used in order to make websites work, or work more efficiently,
                as well as to provide information to the owners of the site.</p>
        </div>
    </div>
    <div class="row center-block policy-container">
        <div class="col-md-12">
            <h3>What are cookies used for?</h3>
            <p>Cookies can be used for many different purposes. <a href="<?php echo $isHost ? site_url('hosts') : site_url('commitments') ?>">Our Ocean Conference BackOffice</a> makes use of cookies for the following purposes:</p>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Categories of Use</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Authentication</td>
                        <td>If you are signed in to <a href="<?php echo $isHost ? site_url('hosts') : site_url('commitments') ?>">Our Ocean Conference BackOffice</a>, cookies are used to show you the right information and personalize your experience.</td>
                    </tr>
                    <tr>
                        <td>Security</td>
                        <td>We use cookies to enable and support security features, such as data access and functionality rights.</td>
                    </tr>
                    <tr>
                        <td>Preferences</td>
                        <td>We use cookies to store search preferences in order to optimize your experience within the site.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row center-block policy-container">
        <hr>
    </div>
    <div class="row center-block policy-container">
        <div class="col-md-12">
            <h1>Data Privacy Policy</h1>
        </div>
    </div>
    <div class="row center-block policy-container">
        <div class="col-md-12">
                <h3>Introduction</h3>
                <p>This privacy statement explains the reason for the processing of your personal data, the way we collect, handle and ensure protection of all personal data provided,
                    how that information is used and what rights you have in relation to your personal data. It also specifies the contact details of the responsible Data Controller
                    with whom you may exercise your rights.</p>
        </div>
    </div>
    <div class="row center-block policy-container">
        <div class="col-md-12">
            <h3>Why and how do we process your personal data?</h3>
            <p>The <a href="<?php echo $isHost ? site_url('hosts') : site_url('commitments') ?>">Our Ocean Conference BackOffice</a> website collects some minimal private data about users:  name, email address, physical address and phone number.
                We use this information in order to remain in contact with you, to send you email confirmations or reminders.  We may publish this personal data on the
                <a href="<?php echo $isHost ? site_url('hosts') : site_url('commitments') ?>">Our Ocean Conference BackOffice</a> website, but only if you have given explicit authorization (checkbox of consent in the user profile screen).</p>
        </div>
    </div>
    <div class="row center-block policy-container">
        <div class="col-md-12">
            <h3>How long do we keep your personal data?</h3>
            <p><a href="<?php echo $isHost ? site_url('hosts') : site_url('commitments') ?>">Our Ocean Conference BackOffice</a> website will keep this information in the database as long as you do not personally request to be removed. As a user,
                you can connect at any point of time to the BackOffice platform and ask to be removed as a user.  In this situation, the user profile is marked as ‘deleted’
                and we will only keep your name in the record (this is necessary because the system tracks changes to commitments, and remembers who made a modification.</p>
        </div>
    </div>
    <div class="row center-block policy-container">
        <div class="col-md-12">
            <h3>How do we protect and safeguard your personal data?</h3>
            <p>All personal data is stored in a database hosted on Amazon AWS services.  The servers are hosted in the United States.  We take reasonable precautions to
                prevent hacking. For example, the usernames and password are encrypted, to prevent anyone from being able to steal the list of passwords.</p>
        </div>
    </div>
    <div class="row center-block policy-container">
        <div class="col-md-12">
            <h3>Who has access to your personal data and to whom is it disclosed?</h3>
            <p>Access to your personal data is provided to the Our Ocean Conference Organizers staff responsible for preparing the next Our Ocean Conference and to
                authorized staff according to the “need to know” principle. Such staff abide by statutory, and when required, additional confidentiality agreements.</p>
            <p>We will not share your personal data  with any third-party, except for the very rare cases where the host country decides to commission a research
                study, in which case we may communicate your personal information the the selected research group so they can contact you on our behalf.</p>
            <p>We will never sell your personal data to marketers or other commercial websites.  The information we collect will not be given to any other
                third party, except to the extent and for the purpose we may be required to do so by law.</p>
        </div>
    </div>
    <div class="row center-block policy-container">
        <div class="col-md-12">
            <h3>What are your rights and how can you exercise them?</h3>
            <p>You may have heard about the General Data Protection Regulation (“GDPR”) in Europe. GDPR gives people under its protection certain rights with respect
                to their personal information collected by us on the Site. Accordingly, we recognize and will comply with GDPR and those rights, except as limited by
                applicable law. The rights under GDPR include:</p>
            <ul>
                <li><b>Right of Access</b>. This includes your right to access the personal information we gather about you, and your right to obtain information about the
                    sharing, storage, security and processing of that information.</li>
                <li><b>Right to Correction</b>. This is your right to request correction of your personal information.</li>
                <li><b>Right to Erasure</b>. This is your right to request, subject to certain limitations under applicable law, that your personal information be erased
                    from our possession (also known as the “Right to be forgotten”). However, if applicable law requires us to comply with your request to delete your
                    information, fulfillment of your request may prevent you from using Basecamp services and may result in closing your account.</li>
                <li><b>Right to Complain</b>. You have the right to make a complaint regarding our handling of your personal information with the appropriate supervisory authority.</li>
                <li><b>Right to Restrict Processing</b>. This is your right to request restriction of how and why your personal information is used or processed.</li>
                <li><b>Right to Object</b>. This is your right, in certain situations, to object to how or why your personal information is processed.</li>
                <li><b>Right to Portability</b>. This is your right to receive the personal information we have about you and the right to transmit it to another party.</li>
                <li><b>Right to not be subject to Automated Decision-Making</b>. This is your right to object and prevent any decision that could have a legal, or similarly significant,
                    effect on you from being made solely based on automated processes. This right is limited, however, if the decision is necessary for performance of any contract
                    between you and us, is allowed by applicable European law, or is based on your explicit consent.</li>
            </ul>
        </div>
    </div>
    <div class="row center-block policy-container">
        <div class="col-md-12">
            <h3>Contact information</h3>
            <p>If you would like to exercise your rights under GDPR, or if you have comments, questions or concerns, or if you would like to submit a complaint regarding the collection
                and use of your personal data, please feel free to contact the Organizing team at <a href = "mailto:OurOceanConference@gmail.com">OurOceanConference@gmail.com</a>.</p>
        </div>
    </div>
</div>
<?php
    $this->load->view('elements/footer');
?>











