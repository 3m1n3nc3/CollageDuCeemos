<?php

use CI_Emulator\CI_Emulator\CI_Session; 
use CI_Emulator\CI_Emulator\CI_Security; 
use CI_Emulator\CI_Emulator\CD_input;  

require_once(__DIR__ . '/CI_Common.php');  
require_once(__DIR__ . '/CI_Class.php');  
require_once(__DIR__ . '/CI_Security.php');  
require_once(__DIR__ . '/CD_Class.php');  

$cd_session = new CI_Session;
$cd_security = new CI_Security;
$cd_input = new CD_input; 
