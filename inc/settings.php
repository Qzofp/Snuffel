<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    settings.php
 *
 * Created on Jun 26, 2011
 * Updated on Jul 02, 2011
 *
 * Description: Snuffel's settings page.
 * 
 * Credits: Spotweb team.
 *
 */

// Snuffel's current version.
define("cCurrentVersion", 0.36);

// Put here the Spotweb location.
define("cSPOTWEBFOLDER", "../spotweb");



/////////////////////////////////////////  Pre Spotweb Checks  ///////////////////////////////////////////

// Check if the Spotweb folder exists.
if (file_exists(cSPOTWEBFOLDER)) {
    define("cSPOTWEB", true);
}
else {
    define("cSPOTWEB", false);
}
    
// Check if the Spotweb ownsettings.php page exists and include it.
if (cSPOTWEB)
{
    if (file_exists(cSPOTWEBFOLDER."/ownsettings.php")) 
    {
        include_once(cSPOTWEBFOLDER."/ownsettings.php");
        
        define("cHOST",   $settings['db']['host']);
        define("cDBASE",  $settings['db']['dbname']);
        define("cUSER",   $settings['db']['user']);
        define("cPASS",   $settings['db']['pass']);  
        define("cENGINE", $settings['db']['engine']);
        
        define("cOWNSETTINGS", true);
    }
    else {
        define("cOWNSETTINGS", false);        
    }
}
?>