<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    index.php
 *
 * Created on Apr 09, 2011
 * Updated on Jul 03, 2011
 *
 * Description: Snuffel's main page.
 * 
 * Credits: Spotweb team.
 *
 */

// The Spotweb location is found in the settings.php file.
require_once 'inc/settings.php';
require_once 'inc/common.php';
require_once 'inc/config.php';
require_once 'inc/panel.php';

$aInput = GetInput();

if ($aInput["CHECK"] == 2) {
    $aInput = ProcessInput($aInput);
}    
else {
    list($aInput["PAGE"], $aChecks) = ConfigureSnuffel($aInput["CHECK"]);
}

switch ($aInput["PAGE"])
{   
    // Results
    case 0: require_once "inc/results.php";
            CreateResultsPage($aInput);
            break;

    // History
    //case 1: CreateHistoryPage(); 
    //        break;                    

    // Search
    case 2: require_once "inc/search.php";
            CreateSearchPage();
            break;
                    
    //Settings
    //case 3: CreateSettingsPage();
    //        break;     

    default: CreateConfigPage($aChecks);
}
?>