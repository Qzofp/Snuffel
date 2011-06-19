<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    config.php
 *
 * Created on Apr 09, 2011
 * Updated on Jun 19, 2011
 *
 * Description: This page containts the check and configuration functions. 
 * 
 * Credits: Spotweb team.
 *
 */

/////////////////////////////////////////     Config Main     ////////////////////////////////////////////

/*
 * Function:	ConfigureSnuffel
 *
 * Created on Jun 16, 2011
 * Updated on Jun 19, 2011
 *
 * Description: Install, Update or Configure Snuffel.
 *
 * In:	$check
 * Out:	$page, $aChecks;
 *
 */
function ConfigureSnuffel($check)
{      
    $aChecks = null;
    
    if (!$check) {
        list($page, $aChecks) = CheckSnuffel();
    }
    else 
    {
        InstallSnuffel();
        sleep(1);
        
        LoadConstants();
        $page = 2;
    }
    
    return array($page, $aChecks);
}


/////////////////////////////////////////   Process Functions    /////////////////////////////////////////

/*
 * Function:	CheckSnuffel
 *
 * Created on Jun 13, 2011
 * Updated on Jun 19, 2011
 *
 * Description: Perform a couple of checks and record what is needed to succesfully run Snuffel.
 *
 * In:	-
 * Out:	$page, $aChecks
 *
 */
function CheckSnuffel()
{
    $page = -1;
    
    $aChecks = array("SPOTWEB"=>false, "OWNSETTINGS"=>false, 
                     "MYSQL"=>false, "CONNECT"=>false, 
                     "SNUFFEL"=>false, "UPGRADE"=>false);
    
    // Check if the Spotweb folder exists.
    if (file_exists(cSPOTWEB)) {
        $aChecks["SPOTWEB"] = true;
    }
    
    // Check if the Spotweb ownsettings.php page exists and include it.
    if ($aChecks["SPOTWEB"])
    {
        if (file_exists(cSPOTWEB."/ownsettings.php")) 
        {
            include_once(cSPOTWEB."/ownsettings.php");
            $aChecks["OWNSETTINGS"] = true;
        }
    }
    
    // Check if Spotweb uses the MySQL database.
    if ($aChecks["OWNSETTINGS"])
    {
        // The following setting comes from Spotwebs ownsettings.php.
        if ($settings['db']['engine'] == "mysql") {
            $aChecks["MYSQL"] = true;
        }        
    }    

    // Check if a connection to the Spotweb database can be succesfully made.
    if ($aChecks["MYSQL"])
    {
         // These settings are from Spotwebs ownsettings.php.
        define("cHOST",  $settings['db']['host']);
        define("cDBASE", $settings['db']['dbname']);
        define("cUSER",  $settings['db']['user']);
        define("cPASS",  $settings['db']['pass']);
        
        if (ConnectToSpots()) {
            $aChecks["CONNECT"] = true;
        }       
    }    
    
    // Check if Snuffel exists.
    if ($aChecks["CONNECT"])
    {
        $version = GetSnuffelVersion();
        if ($version) 
        {
            $aChecks["SNUFFEL"] = true;
            
            if ($version != cCurrentVersion) {
                $aChecks["UPGRADE"] = true;
            }
            else {
                LoadConstants();
                $page = 2;                
            }
        }
    }
    
    return array($page, $aChecks);
}

/*
 * Function:	InstallSnuffel
 *
 * Created on Jun 16, 2011
 * Updated on Jun 17, 2011
 *
 * Description: Install or Upgrade the Snuffel tabels in the Spotweb database.
 *
 * In:	-
 * Out: -
 *
 */
function InstallSnuffel()
{       
    // Get the database settings from Spotwebs ownsettings.php.
    include_once(cSPOTWEB."/ownsettings.php");
    define("cHOST",  $settings['db']['host']);
    define("cDBASE", $settings['db']['dbname']);
    define("cUSER",  $settings['db']['user']);
    define("cPASS",  $settings['db']['pass']);  
    
    include_once("inc/tables.php");
    CreateSnuffelTables();
}

/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	CreateConfigPage
 *
 * Created on Jun 17, 2011
 * Updated on Jun 18, 2011
 *
 * Description: .
 *
 * In:	$aChecks
 * Out: -
 *
 */
function CreateConfigPage($aChecks)
{
    PageHeader("Config", "inc/config.css");
    echo "  <form name=\"Config\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";   
    
    echo "   <h1>Snuffel Config Pagina</h1>\n";
    
    $install = "<input type=\"submit\" name=\"btnCHECK\" value=\"Install\"/>";
    $upgrade = "<input type=\"submit\" name=\"btnCHECK\" value=\"Upgrade\"/>";
    
    switch (true)
    {
        case (!$aChecks["SPOTWEB"])     : $msg = "   De Spotweb folder kan niet gevonden worden.<br/>";
                                          break;
            
        case (!$aChecks["OWNSETTINGS"]) : $msg = "   De Spotweb ownsettings.php file kan niet gevonden worden.<br/>";
                                          break;
                                      
        case (!$aChecks["MYSQL"])       : $msg = "   Spotweb maakt geen gebruik van MySQL.<br/>";
                                          break;
                                      
        case (!$aChecks["CONNECT"])     : $msg = "   Er kan geen verbinding worden gemaakt met de Spotweb database.<br/>";
                                          break;
                                      
        case (!$aChecks["SNUFFEL"])     : $msg = "   De Snuffel tabellen zijn niet ge&iuml;nstalleerd.<br/>\n   $install";
                                          break;
                                      
        case ($aChecks["UPGRADE"])      : $msg = "   Er is een nieuwere versie van Snuffel ge&iuml;nstalleerd.<br/>\n   $upgrade";
                                          break;
    }
    
    echo "$msg\n";
 
    echo "  </form>\n";    
    PageFooter();
}


/////////////////////////////////////////   Query Functions   ////////////////////////////////////////////

/*
 * Function:	ConnectToSpots
 *
 * Created on Jun 13, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Check if a connection to the spotweb database can be made.
 *
 * In:	-
 * Out:	$check
 *
 */
function ConnectToSpots()
{
    $check = false;
    
    // Check if a connection can be made.
    $db = @mysqli_connect(cHOST, cUSER, cPASS, cDBASE);
    if ($db) 
    {   
        // Check if a query can be made.   
        $sql = "SELECT count(*) FROM spotstatelist";

        if (mysqli_query($db, $sql)) {
            $check = true;
        }
    
        mysqli_close($db);
    }
    
    return $check;
}

/*
 * Function:	GetSnuffelVersion
 *
 * Created on Jun 13, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Return the Snuffel version or false if the Snuffel tables doesn't exist.
 *
 * In:	-
 * Out:	$version
 *
 */
function GetSnuffelVersion()
{
    $db = OpenDatabase();
    
    $version = false;
    $sql = "SELECT value FROM snufcnf ".
           "WHERE name = 'Version'";
  
    if(mysqli_multi_query($db, $sql))
    {
        $result = mysqli_use_result($db);
        if ($result) 
        {
            $row = mysqli_fetch_row($result); 
            $version = $row[0];
            mysqli_free_result($result);
        }
    }
 
    CloseDatabase($db); 
        
    return $version;
}
?>
