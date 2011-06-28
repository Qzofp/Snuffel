<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    panel.php
 *
 * Created on Apr 16, 2011
 * Updated on Jun 27, 2011
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
 * Updated on Jun 26, 2011
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
    $check = GetButtonValue("btnCHECK");
    if (!$check) {
        $check = GetButtonValue("hidCHECK");
    }
    
    if ($check == 2)
    {
        $process = GetButtonValue("btnPROCESS");
               
        $page    = GetButtonValue("btnPAGE");
        if (!$page) {
            $page    = GetButtonValue("hidPAGE");
        }
    }

    return array($check, $process, $page);
}


/////////////////////////////////////////   Process Functions    /////////////////////////////////////////

/*
 * Function:    ProcessInput
 *
 * Created on Jun 17, 2011
 * Updated on Jun 28, 2011
 *
 * Description: Process the user input.
 *
 * In:  $process, $page
 * Out: $page
 *
 */
function ProcessInput($process, $page)
{ 
    LoadConstants();
    
    $aButtons = explode("|", cButtons);
    
    if (strlen($page) > 1) {
         $page = array_search($page, $aButtons);
    }

   $process = array_search($process, $aButtons);
    
    switch($process)
    {
        case 4: UpdateSnuffel();
                $page = 0;
                break;
            
        case 6: DeleteSearchAll();
                break;
    }
  
    return array($page);
}


/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	ShowPanel
 *
 * Created on Aug 16, 2011
 * Updated on Jun 28, 2011
 *
 * Description: Shows the navigation panel.
 *
 * In:  $button
 * Out:	panel
 *
 */
function ShowPanel($button)
{
    // Start top panel.
    echo "  <div id=\"panel_top\">\n";
    echo "   <div class=\"panel\">\n";
    echo "   <h4>".cTitle."</h4>\n";

    // Snuffel buttons.
    $aButtons = explode("|", cButtons);
    
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
    echo "   <h4>Dummy</h4>\n";
    echo "   </div>\n";    
    echo "  </div>\n";
    // End middle panel.
    
    // Start bottom panel.
    echo "  <div id=\"panel_bottom\">\n";
    echo "   <div class=\"panel\">\n";
    
    // Maintenance menu.
    $aMenuText = explode("|", cMenuText); 
    $time = strtotime(UpdateTime());
    echo "   <h4>$aMenuText[0]</h4>\n";
    echo "   <div class=\"txt_center\">$aMenuText[1] ".time_ago($time, 1)."</div>\n";

    // Maintenance buttons
    echo "   <ul class=\"btn_bottom\">\n";
    
    echo "    <li><input type=\"submit\" name=\"btnPROCESS\" value=\"$aButtons[4]\"/></li>\n";  
    if ($button == 2) {
        echo "    <li><input type=\"submit\" name=\"btnPROCESS\" value=\"$aButtons[6]\"/></li>\n";
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
 * Updated on Jun 27, 2011
 *
 * Description: Update Snuffel. Note: This is not a Spotweb update! 
 *
 * In:	-
 * Out:	Updated snuftemp table
 *
 */
function UpdateSnuffel()
{
    $db = OpenDatabase();
    // Add last update time to snufcnf table.
    $last =  strtotime(UpdateTime());
    $sql = "UPDATE snufcnf SET value = '$last' ".
           "WHERE name = 'LastUpdate'";        
    mysqli_query($db, $sql);

    // Empty snuftmp1 table.
    $sql = "TRUNCATE snuftmp";
    mysqli_query($db, $sql);
    
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
           "AGAINST((SELECT GROUP_CONCAT(title) FROM snuffel) IN BOOLEAN MODE) ".
           "ORDER BY t.title";
    mysqli_query($db, $sql);
    
    CloseDatabase($db);
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