<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    panel.php
 *
 * Created on Apr 16, 2011
 * Updated on Jun 18, 2011
 *
 * Description: This page contains the panel functions.
 *
 * Credits: Spotweb team 
 * 
 */

/////////////////////////////////////////   Get Input Functions   ////////////////////////////////////////

/*
 * Function:    GetInput
 *
 * Created on Apr 23, 2011
 * Updated on Jun 18, 2011
 *
 * Description: Get user input.
 *
 * In:  -
 * Out: $check, $process, $page
 *
 */
function GetInput()
{  
    $check   = 0;
    $process = -1;
    $page    = -1;
    
    // Get the hidden check spotweb or upgrade snuffel value.
    $check = GetInputValue("btnCHECK");
    if (!$check) {
        $check = GetInputValue("hidCHECK");
    }
    
    if ($check == 2)
    {
        $process = GetInputValue("btnPROCESS");
               
        $page    = GetInputValue("btnPAGE");
        if (!$page) {
            $page    = GetInputValue("hidPAGE");
        }
    }

    return array($check, $process, $page);
}


/////////////////////////////////////////   Process Functions    /////////////////////////////////////////

/*
 * Function:    ProcessInput
 *
 * Created on Jun 17, 2011
 * Updated on Jun 17, 2011
 *
 * Description: Process the user input.
 *
 * In:  $process, $page
 * Out: $page
 *
 */
function ProcessInput($process, $page)
{
    // Get the database settings from Spotwebs ownsettings.php.
    include_once(cSPOTWEB."/ownsettings.php");    
    define("cHOST",  $settings['db']['host']);
    define("cDBASE", $settings['db']['dbname']);
    define("cUSER",  $settings['db']['user']);
    define("cPASS",  $settings['db']['pass']);
    
    LoadConstants();
    
    $aButtons = explode("|", cButtons);
    
    if (strlen($page) > 1) {
         $page = array_search($page, $aButtons);
    }

   $process = array_search($process, $aButtons);
    
    switch($process)
    {
        case 3: UpdateSnuffel();
                break;
            
        case 4: DeleteSearchAll();
                break;
    }
  
    return array($page);
}


/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	ShowPanel
 *
 * Created on Aug 16, 2011
 * Updated on Jun 18, 2011
 *
 * Description: Shows the navigation panel.
 *
 * In:  $button
 * Out:	panel
 *
 */
function ShowPanel($button)
{
    echo "  <div id=\"panel\">\n";

    // Snuffel menu.
    echo "  <h4>".cTitle."</h4>\n";

    // Snuffel buttons.
    $aButtons = explode("|", cButtons);
    
    // Show the first 3 buttons: Nieuw, Alles and Zoek.
    echo "  <ul>\n";
    for ($i = 0; $i < 3; $i++) {
        if ($button == $i) {
            echo "   <li><input type=\"button\" name=\"btnPAGE\" value=\"$aButtons[$i]\"/></li>\n";
        }
        else {
            echo "   <li><input type=\"submit\" name=\"btnPAGE\" value=\"$aButtons[$i]\"/></li>\n";
        }
    }  
    echo "  </ul>\n";

    // Maintenance menu.
    $aMenuText = explode("|", cMenuText); 
    $time = strtotime(UpdateTime());
    echo "  <h4>$aMenuText[0]</h4>\n";
    echo "  <div class=\"txt_panel\">$aMenuText[1] ".time_ago($time, 1)."</div>\n";

    // Maintenance buttons
    echo "  <ul class=\"buttons2\">\n";
    
    if ($button == 2) {
        echo "   <li><input type=\"submit\" name=\"btnPROCESS\" value=\"$aButtons[4]\"/></li>\n";
    }
    else {
        echo "   <li><input type=\"submit\" name=\"btnPROCESS\" value=\"$aButtons[3]\"/></li>\n";            
    }

    echo "  </ul>\n";

    echo "  </div>\n";
}


/////////////////////////////////////////   Query Functions   ////////////////////////////////////////////

/*
 * Function:	UpdateTime
 *
 * Created on Aug 23, 2011
 * Updated on Jun 10, 2011
 *
 * Description: Haal de tijd op wanneer de tabel snuftemp voor het laatst is gewijzigd.
 *
 * In:	-
 * Out:	$time
 *
 */
function UpdateTime()
{
    $sql = "SELECT UPDATE_TIME ".
           "FROM information_schema.tables ".
           "WHERE TABLE_SCHEMA = 'spotweb' AND TABLE_NAME = 'snuftmp'";

    $aItems = GetItemsFromDatabase($sql);
    $time   = $aItems[0];

    return $time;
}

/*
 * Function:	UpdateSnuffel
 *
 * Created on Aug 23, 2011
 * Updated on Jun 10, 2011
 *
 * Description: Update Snuffel. Note: This is not a Spotweb update! 
 *
 * In:	-
 * Out:	Updated snuftemp table
 *
 */
function UpdateSnuffel()
{
    // Leeg snuftemp tabel.
    $sql = "TRUNCATE snuftmp";

    ExecuteQuery($sql);

    // Voeg spots id's toe aan tabel snuftemp waar gezochte titel uit snuffel tabel in tabel spots bestaat.   
    $sql = "INSERT INTO snuftmp(messageid, poster, title, tag, category, subcata, subcatb, subcatc, subcatd, subcatz, stamp, reversestamp, filesize, moderated, commentcount, spotrating) ".
           "SELECT messageid, poster, title, tag, category, subcata, subcatb, subcatc, subcatd, subcatz, stamp, reversestamp, filesize, moderated, commentcount, spotrating FROM spots ".
           "WHERE MATCH(title) ".
           "AGAINST((SELECT GROUP_CONCAT(title) FROM snuffel) IN BOOLEAN MODE)";

    ExecuteQuery($sql);
}  

/*
 * Function:	DeleteSearchAll
 *
 * Created on Jun 06, 2011
 * Updated on Jun 06, 2011
 *
 * Description: Deletes all the search records from the snuftemp and snuffel table.
 *
 * In:  
 * Out:	
 *
 */
function DeleteSearchAll()
{
    $sql = "TRUNCATE snuftmp";
    ExecuteQuery($sql);   
    
    $sql = "TRUNCATE snuffel";
    ExecuteQuery($sql);
}
?>