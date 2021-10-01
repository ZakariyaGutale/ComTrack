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

class PreSystem {
    private $dotenv;

    public function __construct(){
        $this->loadDotEnv();
        $this->setupEnvConstraints();
        $this->setLogLevel();
        $this->setSession();
    }

    private function loadDotEnv(){
        require_once(dirname(__FILE__, 3).'/vendor/autoload.php');
        $this->dotenv = Dotenv\Dotenv::create(dirname(__FILE__, 3), 'config.env');
        $this->dotenv->load();
    }

    private function setupEnvConstraints(){
        $this->dotenv->required('BO_APP_ENV')->allowedValues(['development', 'production']);
        $this->dotenv->required(['BO_APP_COOKIE_DOMAIN', 'BO_APP_TIMEZONE', 'BO_APP_UPLOADS',
                                 'BO_APP_REPORTS'])->notEmpty();
        $this->dotenv->required(['BO_DB_HOST', 'BO_DB_NAME', 'BO_DB_USER', 'BO_DB_PASSWORD'])->notEmpty();
    }

    private function setLogLevel(){
        $level = 0;
        if (getenv('BO_APP_ENV') == 'development'){
            $level = 4;
        }
        putenv("BO_APP_LOGLEVEL=$level");
    }

    private function setSession(){
        $this->setSessionName();
        $this->setSessionExpiration();
        $this->setCokiePrefix();
    }

    private function setSessionName(){
        if (getenv('BO_APP_COOKIES_NAME') == false){
            putenv("BO_APP_COOKIES_NAME=ooc_session");
        }
    }

    private function setSessionExpiration(){
        if (getenv('BO_APP_SESSION_EXPIRATION') == false){
            $exp = 7200;
            if (getenv('BO_APP_ENV') == 'development'){
                $exp = 0;
            }
            putenv("BO_APP_SESSION_EXPIRATION=$exp");
        }
    }

    private function setCokiePrefix(){
        if (getenv('BO_APP_COOKIE_PREFIX') == false){
            putenv("BO_APP_COOKIE_PREFIX=ooc");
        }
    }
}
