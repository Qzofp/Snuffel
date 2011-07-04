<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    panel.php
 *
 * Created on Apr 16, 2011
 * Updated on Jul 04, 2011
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
 * Updated on Jul 03, 2011
 *
 * Description: Get user input.
 *
 * In:  -
 * Out: $aInput
 *
 */
function GetInput()
{  
    $aInput = array("CHECK"=>false, "PAGE"=>-1, "PROCESS"=>-1, "FILTER"=>null, "RESET"=>false);
    
    
    // Get the hidden check spotweb or upgrade snuffel value.
    $aInput["CHECK"] = GetButtonValue("btnCHECK");
    if (!$aInput["CHECK"]) {
        $aInput["CHECK"] = GetButtonValue("hidCHECK");
    }
    
    if ($aInput["CHECK"] == 2)
    {          
        $aInput["PAGE"] = GetButtonValue("btnPAGE");
        if (!$aInput["PAGE"]) {
            $aInput["PAGE"] = GetButtonValue("hidPAGE");
        }

        $aInput["FILTER"] = GetButtonValue("btnFILTER");
        if (!$aInput["FILTER"]) {
            $aInput["FILTER"] = GetButtonValue("hidFILTER");
        }
        else {
            $aInput["RESET"] = true;
        }
        
        $aInput["PROCESS"] = GetButtonValue("btnPROCESS");
    }

    return $aInput;
}


/////////////////////////////////////////   Process Functions    /////////////////////////////////////////

/*
 * Function:    ProcessInput
 *
 * Created on Jun 17, 2011
 * Updated on Jul 03, 2011
 *
 * Description: Process the user input.
 *
 * In:  $aInput
 * Out: $page
 *
 */
function ProcessInput($aInput)
{ 
    LoadConstants();
       
    $aButtons = explode("|", cButtons);
    
    if (strlen($aInput["PAGE"]) > 1) {
         $aInput["PAGE"] = array_search($aInput["PAGE"], $aButtons);
    }
    
    switch($aInput["PROCESS"])
    {
        case $aButtons[6]: UpdateSnuffel();
                           $aInput["FILTER"] = $aButtons[5];
                           $aInput["PAGE"] = 0;
                           break;
            
        case $aButtons[8]: DeleteSearchAll();
                           break;
    }
  
    return $aInput;
}


/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	ShowPanel
 *
 * Created on Apr 16, 2011
 * Updated on Jul 03, 2011
 *
 * Description: Shows the navigation panel.
 *
 * In:  $button, $aFilters
 * Out:	panel
 *
 */
function ShowPanel($button, $aFilters = false)
{
    // Start top panel.
    echo "  <div id=\"panel_top\">\n";
    echo "   <div class=\"panel\">\n";
    echo "   <h4>".cTitle."</h4>\n";

    // Snuffel buttons and menu text.
    $aButtons  = explode("|", cButtons);    
    $aMenuText = explode("|", cMenuText); 
    
    // Show buttons: "Gevonden", "Historie", "Zoek Op" and "Instellingen".
    echo "   <ul class=\"btn_top\">\n";
    for ($i = 0; $i < 3; $i++) {
      if ($i != 1) // Skip "Historie". This is for future implementation. 
      {       
        if ($button == $i) {
            echo "    <li><input type=\"button\" name=\"btnPAGE\" value=\"$aButtons[$i]\"/></li>\n";
        }
        else {
            echo "    <li><input type=\"submit\" name=\"btnPAGE\" value=\"$aButtons[$i]\"/></li>\n";
        }
      }  
    }  
    echo "   </ul>\n";
    echo "   </div>\n";
    echo "  </div>\n";
    // End top panel.

    // Start middle panel.
    echo "  <div id=\"panel_middle\">\n";
    echo "   <div class=\"panel\">\n";
    echo "   <h4>$aMenuText[0]</h4>\n";
    
    // Show filter buttons.
    if ($button == 0)
    {    
        echo "   <ul class=\"btn_top\">\n";
        for ($i = 4; $i < 6; $i++) 
        {
            if ($aButtons[$i] == $aFilters["FILTER"] && $aButtons[4] != $aFilters["FILTER"]) {
                echo "    <li><input type=\"button\" name=\"btnFILTER\" value=\"$aButtons[$i]\"/></li>\n";
            }
            else {
                echo "    <li><input type=\"submit\" name=\"btnFILTER\" value=\"$aButtons[$i]\"/></li>\n";                
            }
        }

        echo "   </ul>\n";
    }
    
    echo "   </div>\n";    
    echo "  </div>\n";
    // End middle panel.
    
    // Start bottom panel.
    echo "  <div id=\"panel_bottom\">\n";
    echo "   <div class=\"panel\">\n";
    
    // Maintenance menu.
    $time = strtotime(UpdateTime());
    echo "   <h4>$aMenuText[1]</h4>\n";
    
    // Last update time or loading...
    echo "   <div class=\"txt_center\">\n";
    echo "    <div id=\"update\">$aMenuText[2] ".time_ago($time, 1)."</div>\n";
    echo "    <div id=\"loading\" style=\"display:none\"><img src=\"img/loading.gif\" /></div>\n";
    echo "   </div>\n";

    // Maintenance buttons.
    echo "   <ul class=\"btn_bottom\">\n";
    
    // Update button.
    echo "    <li onclick=\"toggle('update','loading')\"><input type=\"submit\" name=\"btnPROCESS\" value=\"$aButtons[6]\"/></li>\n";
    
    // Show "Zoek Op" button.
    if ($button == 2) {
        echo "    <li><input type=\"submit\" name=\"btnPROCESS\" value=\"$aButtons[8]\"/></li>\n";
    }

    echo "   </ul>\n";
    echo "   </div>\n";
    echo "  </div>\n";
    // End bottom panel.    
}


/////////////////////////////////////////   Query Functions   ////////////////////////////////////////////

/*
 * Function:	UpdateTime
 *
 * Created on Apr 23, 2011
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
 * Created on Apr 23, 2011
 * Updated on Jul 03, 2011
 *
 * Description: Update Snuffel. Note: This is not a Spotweb update! 
 *
 * In:	-
 * Out:	Updated snuftemp table
 *
 */
function UpdateSnuffel()
{    
    // Add last message to snufcnf table.
    $sql = "UPDATE snufcnf SET value = (SELECT IFNULL(MAX(id), 0) FROM snuftmp) ".
           "WHERE name = 'LastMessage'";
    ExecuteQuery($sql);

    // Empty snuftmp1 table.
    $sql = "TRUNCATE snuftmp";
    ExecuteQuery($sql);
    
    // Copy spots that matches the snuffel title search criteria from the spots table to the snuftmp1 table.
    $sql = "INSERT INTO snuftmp(id, messageid, poster, title, tag, category, subcata, subcatb, subcatc, subcatd, subcatz, stamp, ".
                               "reversestamp, filesize, moderated, commentcount, spotrating) ".
           "SELECT DISTINCT t.id, t.messageid, t.poster, t.title, t.tag, t.category, t.subcata, t.subcatb, t.subcatc, IFNULL(CONCAT(f.subcatd,'|'), ".
                           "t.subcatd), t.subcatz, t.stamp, t.reversestamp, t.filesize, t.moderated, t.commentcount, t.spotrating ".
           "FROM spots t JOIN snuffel f ON t.title LIKE CONCAT('%', f.title, '%') ".
           "AND (t.poster = f.poster OR f.poster IS NULL) ".
           "AND (t.category = f.cat OR f.cat IS NULL) ".
           "AND (t.subcata LIKE CONCAT('%', f.subcata, '|%') OR f.subcata IS NULL) ".
           "AND (t.subcatd LIKE CONCAT('%', f.subcatd, '|%') OR f.subcatd IS NULL) ".
           "WHERE MATCH(t.title) ".
           "AGAINST((SELECT GROUP_CONCAT(title) FROM snuffel) IN BOOLEAN MODE)";
    ExecuteQuery($sql);
}

/*
 * Function:	DeleteSearchAll
 *
 * Created on Jun 06, 2011
 * Updated on Jun 27, 2011
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