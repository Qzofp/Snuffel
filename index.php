<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    index.php
 *
 * Created on Apr 09, 2011
 * Updated on Jun 19, 2011
 *
 * Description: Snuffel's main page.
 * 
 * Credits: Spotweb team.
 *
 */

require_once 'inc/common.php';
require_once 'inc/config.php';
require_once 'inc/panel.php';

// Put here the Spotweb location.
define("cSPOTWEB", "../spotweb");
define("cCurrentVersion", 0.21);

list($check, $process, $page) = GetInput();

if ($check == 2) {
    list($page) = ProcessInput($process, $page);
}    
else {
    list($page, $aChecks) = ConfigureSnuffel($check);
}

switch ($page)
{   
    case 0: require_once "inc/results.php";
            CreateResultsPage(true);  // New results.
            break;

    case 1: require_once "inc/results.php";
            CreateResultsPage(false); // All results.
            break;                    

    case 2: require_once "inc/search.php";
            CreateSearchPage();
            break;
                    
    //Settings
    //case 3: SettingsPage();
    //        break;     

    default: CreateConfigPage($aChecks);
}
?>