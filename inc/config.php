<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    config.php
 *
 * Created on Apr 09, 2011
 * Updated on Jun 21, 2011
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
 * Updated on Jun 21, 2011
 *
 * Description: Create the config page which shows the results of the Snuffel checks.
 *
 * In:	$aChecks
 * Out: -
 *
 */
function CreateConfigPage($aChecks)
{
    PageHeader("Config", "css/config.css");
    echo "  <form name=\"Config\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";   
    echo "   <div id=\"gabbo\">\n";
    
    switch (true)
    {
        case (!$aChecks["SPOTWEB"])     : $msg = ShowCheckSpotweb();
                                          $check = false;
                                          break;
            
        case (!$aChecks["OWNSETTINGS"]) : $msg = ShowCheckSettings();
                                          $check = false;            
                                          break;
                                      
        case (!$aChecks["MYSQL"])       : $msg = ShowCheckMySQL();
                                          $check = false;  
                                          break;
                                      
        case (!$aChecks["CONNECT"])     : $msg = ShowCheckConnect();
                                          $check = false; 
                                          break;
                                      
        case (!$aChecks["SNUFFEL"])     : $msg = ShowCheckSnuffel();
                                          $check = true;
                                          break;
                                      
        case ($aChecks["UPGRADE"])      : $msg = ShowCheckUpgrade();
                                          $check = true;
                                          break;
    }
    
    echo "$msg"; 
    
    echo "   </div>\n";
    echo "   <input type=\"hidden\" name=\"hidCHECK\" value=\"$check\" />\n";
    echo "  </form>\n";    
    PageFooter();
}

/*
 * Function:	ShowCheckSpotweb
 *
 * Created on Jun 21, 2011
 * Updated on Jun 21, 2011
 *
 * Description: Show the check spotweb folder message.
 *
 * In:	-
 * Out: $msg
 *
 */
function ShowCheckSpotweb()
{   
    $msg  = "    <div id=\"check\">\n";          
    $msg .= "     <img src=\"img/fail.png\"/>\n";
    $msg .= "     Spotweb niet gevonden!<br/>\n";  
    $msg .= "    </div>\n";           

    $msg .= "    <div id=\"button\">\n";      
    $msg .= "     <input type=\"submit\" name=\"btnDUMMY\" value=\"Opnieuw\"/>\n";
    $msg .= "    </div>\n";    
    
    $msg .= "    <div id=\"help\">\n";      
    $msg .= "     Installeer Spotweb: <a href=\"https://github.com/spotweb/spotweb/wiki\" ".
                 "target=\"_new\">https://github.com/spotweb/spotweb/wiki</a>\n";
    $msg .= "    </div>\n"; 
    
    $msg .= "    <div id=\"tip\">\n";
    $msg .= "     Snuffel gaat ervan uit dat Spotweb naast Snuffel is ge&iuml;nstalleerd, bijvoorbeeld:\n";
    $msg .= "     <ul>\n";
    $msg .= "      <li>http://localhost/Snuffel</li>\n";
    $msg .= "      <li>http://localhost/spotweb</li>\n";  
    $msg .= "     </ul>\n";    
    $msg .= "    </div>\n";      
    
    return $msg;
}

/*
 * Function:	ShowCheckSettings
 *
 * Created on Jun 21, 2011
 * Updated on Jun 21, 2011
 *
 * Description: Show the check ownsetting message.
 *
 * In:	-
 * Out: $msg
 *
 */
function ShowCheckSettings()
{   
    $msg  = "    <div id=\"check\">\n";          
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Spotweb gevonden.<br/>\n";     
    $msg .= "     <img src=\"img/fail.png\"/>\n";
    $msg .= "     Ownsettings niet gevonden!<br/>\n";  
    $msg .= "    </div>\n";           

    $msg .= "    <div id=\"button\">\n";      
    $msg .= "     <input type=\"submit\" name=\"btnDUMMY\" value=\"Opnieuw\"/>\n";
    $msg .= "    </div>\n";    
    
    $msg .= "    <div id=\"help\">\n";      
    $msg .= "     Gebruik Spotweb's ownsettings.php: <a href=\"https://github.com/spotweb/spotweb/wiki\" ".
                 "target=\"_new\">https://github.com/spotweb/spotweb/wiki</a>\n";
    $msg .= "    </div>\n"; 
    
    $msg .= "    <div id=\"tip\">\n";
    $msg .= "     Snuffel haalt de Spotweb database gegevens uit de ownsettings.php file. ".
                 "Plaats deze in de Spotweb folder (bv. http://localhost/spotweb).\n";
    $msg .= "    </div>\n";
    
    return $msg;
}

/*
 * Function:	ShowCheckMySQL
 *
 * Created on Jun 21, 2011
 * Updated on Jun 21, 2011
 *
 * Description: Show the check MySQL message.
 *
 * In:	-
 * Out: $msg
 *
 */
function ShowCheckMySQL()
{   
    $msg  = "    <div id=\"check\">\n";          
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Spotweb gevonden.<br/>\n";     
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Ownsettings gevonden.<br/>\n";  
    $msg .= "     <img src=\"img/fail.png\"/>\n";
    $msg .= "     Spotweb zonder MySQL!<br/>\n";     
    $msg .= "    </div>\n";           

    $msg .= "    <div id=\"button\">\n";      
    $msg .= "     <input type=\"submit\" name=\"btnDUMMY\" value=\"Opnieuw\"/>\n";
    $msg .= "    </div>\n";    
    
    $msg .= "    <div id=\"help\">\n";      
    $msg .= "     Gebruik Spotweb met MySQL: <a href=\"http://gathering.tweakers.net/forum/list_messages/1448575\" ".
                 "target=\"_new\">http://gathering.tweakers.net/forum/list_messages/1448575</a>\n";
    $msg .= "    </div>\n"; 
    
    $msg .= "    <div id=\"tip\">\n";
    $msg .= "     Snuffel gebruikt de MySQL database van Spotweb. Helaas geen ondersteuning of ".
                 "toekomstige ondersteuning voor de andere databases.\n";
    $msg .= "    </div>\n";
    
    return $msg;
}

/*
 * Function:	ShowCheckConnect
 *
 * Created on Jun 21, 2011
 * Updated on Jun 21, 2011
 *
 * Description: Show the check connection message.
 *
 * In:	-
 * Out: $msg
 *
 */
function ShowCheckConnect()
{   
    $msg  = "    <div id=\"check\">\n";          
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Spotweb gevonden.<br/>\n";     
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Ownsettings gevonden.<br/>\n";  
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Spotweb met MySQL.<br/>\n";
    $msg .= "     <img src=\"img/fail.png\"/>\n";
    $msg .= "     Verbindingsprobleem!<br/>\n";     
    $msg .= "    </div>\n";           

    $msg .= "    <div id=\"button\">\n";      
    $msg .= "     <input type=\"submit\" name=\"btnDUMMY\" value=\"Opnieuw\"/>\n";
    $msg .= "    </div>\n";    
    
    $msg .= "    <div id=\"help\">\n";      
    $msg .= "     Controleer de MySQL database instellingen.";
    $msg .= "    </div>\n"; 
    
    $msg .= "    <div id=\"tip\">\n";
    $msg .= "     De database instellingen staan in de ownsettings.php file van Spotweb ".
                 "(bv. in http://localhost/spotweb). Controleer ook of Spotweb goed werkt.\n";
    $msg .= "    </div>\n";
    
    return $msg;
}

/*
 * Function:	ShowCheckSnuffel
 *
 * Created on Jun 21, 2011
 * Updated on Jun 21, 2011
 *
 * Description: Show the check snuffel message.
 *
 * In:	-
 * Out: $msg
 *
 */
function ShowCheckSnuffel()
{   
    $msg  = "    <div id=\"check\">\n";          
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Spotweb gevonden.<br/>\n";     
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Ownsettings gevonden.<br/>\n";  
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Spotweb met MySQL.<br/>\n";
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Verbinding in orde.<br/>\n";
    $msg .= "     <img src=\"img/fail.png\"/>\n";
    $msg .= "     Geen Snuffel tabellen!<br/>\n";
    $msg .= "    </div>\n";           

    $msg .= "    <div id=\"button\">\n";      
    $msg .= "     <input type=\"submit\" name=\"btnDUMMY\" value=\"Installeer\"/>\n";
    $msg .= "    </div>\n";    
    
    $msg .= "    <div id=\"help\">\n";      
    $msg .= "     Installeer de Snuffel tabellen.";
    $msg .= "    </div>\n"; 
    
    $msg .= "    <div id=\"tip\">\n";
    $msg .= "     Snuffel gebruikt een aantal MySQL tabellen. Deze komen in de Spotweb ".
                 "database en zijn te herkennen aan de naam beginnend met snuf.\n";
    $msg .= "    </div>\n";
    
    return $msg;
}

/*
 * Function:	ShowCheckUpgrade
 *
 * Created on Jun 21, 2011
 * Updated on Jun 21, 2011
 *
 * Description: Show the check upgrade message.
 *
 * In:	-
 * Out: $msg
 *
 */
function ShowCheckUpgrade()
{   
    $msg  = "    <div id=\"check\">\n";          
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Spotweb gevonden.<br/>\n";     
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Ownsettings gevonden.<br/>\n";  
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Spotweb met MySQL.<br/>\n";
    $msg .= "     <img src=\"img/fine.png\"/>\n";
    $msg .= "     Verbinding in orde.<br/>\n";
    $msg .= "     <img src=\"img/fail.png\"/>\n";
    $msg .= "     Oude Snuffel tabellen!<br/>\n";
    $msg .= "    </div>\n";           

    $msg .= "    <div id=\"button\">\n";      
    $msg .= "     <input type=\"submit\" name=\"btnDUMMY\" value=\"Upgrade\"/>\n";
    $msg .= "    </div>\n";    
    
    $msg .= "    <div id=\"help\">\n";      
    $msg .= "     Upgrade de Snuffel tabellen.";
    $msg .= "    </div>\n"; 
    
    $msg .= "    <div id=\"tip\">\n";
    $msg .= "     Er is een nieuwe versie van Snuffel ge&iuml;nstalleerd. De ".
                 "Snuffel tabellen moeten nu worden bijgewerkt.\n";
    $msg .= "    </div>\n";
    
    return $msg;
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
