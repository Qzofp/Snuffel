<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    panel.php
 *
 * Created on Apr 16, 2011
 * Updated on Jun 10, 2011
 *
 * Description: Deze pagina bevat de navigatie functies..
 *
 * Credits: Spotweb team 
 * 
 */

/////////////////////////////////////////   Get Input Functions   ////////////////////////////////////////

/*
 * Function:    GetPanelInput
 *
 * Created on Apr 23, 2011
 * Updated on Apr 25, 2011
 *
 * Description: Get user panel input.
 *
 * In:  -
 * Out: $aInput
 *
 */
function GetPanelInput()
{
    // Initial values.
    $aButtons1 = explode("|", cButtons1);

    // Start met Nieuw
    $aInput = array($aButtons1[0], null);

    // Get hidden button value.
    $name = "hidPANEL1";
    if (isset($_POST[$name]) && !empty($_POST[$name]))
    {
        $aInput[0] = $_POST[$name];
    }

    // Get setting button value.
    $name = "btnPANEL1";
    if (isset($_POST[$name]) && !empty($_POST[$name])) {
        $aInput[0] = $_POST[$name];
    }  

    // Get update button value.
    $name = "btnPANEL2";
    if (isset($_POST[$name]) && !empty($_POST[$name])) {
        $aInput[1] = $_POST[$name];
    }

    return $aInput;
}


/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	ShowPanel
 *
 * Created on Aug 16, 2011
 * Updated on Jun 06, 2011
 *
 * Description: Laat het navigatie paneel zien.
 *
 * In:  $button
 * Out:	panel
 *
 */
function ShowPanel($button)
{
    echo "  <div id=\"panel\">\n";

    # Snuffel menu.
    echo "  <h4>".cTitle."</h4>\n";

    # Snuffel buttons.
    $aButtons1 = explode("|", cButtons1);
    echo "  <ul>\n";
    foreach ($aButtons1 as $vButton1) {
        if ($vButton1 == $button) {
            echo "   <li><input type=\"button\" name=\"btnPANEL1\" value=\"$vButton1\"/></li>\n";
        }
        else {
            echo "   <li><input type=\"submit\" name=\"btnPANEL1\" value=\"$vButton1\"/></li>\n";
        }
    }  
    echo "  </ul>\n";

    # Onderhoud menu.
    $aMenuText = explode("|", cMenuText); 
    
    $time = strtotime(UpdateTime());
    
    echo "  <h4>$aMenuText[0]</h4>\n";
    echo "  <div class=\"txt_panel\">$aMenuText[1] ".time_ago($time, 1)."</div>\n";

    # Onderhoud buttons.
    $aButtons2 = explode("|", cButtons2);
    echo "  <ul class=\"buttons2\">\n";
    
    # Als Zoek is gekozen dan toon Verwijder Alles.
    if ($aButtons1[2] == $button) {
        echo "   <li><input type=\"submit\" name=\"btnPANEL2\" value=\"$aButtons2[1]\"/></li>\n";
    }
    else {
        echo "   <li><input type=\"submit\" name=\"btnPANEL2\" value=\"$aButtons2[0]\"/></li>\n";            
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

?>
