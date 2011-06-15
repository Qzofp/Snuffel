<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    index.php
 *
 * Created on Apr 09, 2011
 * Updated on Jun 11, 2011
 *
 * Description: Snuffel's openingspagina.
 * 
 * Credits: Spotweb team.
 *
 */

require_once 'config.php';
require_once 'inc/common.php';
require_once 'inc/panel.php';
require_once 'inc/results.php';
require_once 'inc/search.php';

$aPanelInput = GetPanelInput();

// Definieer buttons.
$aButtons1 = explode("|", cButtons1);
$aButtons2 = explode("|", cButtons2);

switch ($aPanelInput[1]) 
{
    # Update Snuffel
    case $aButtons2[0] : UpdateResults();
                         # Zet button op Nieuw.
                         $aPanelInput[0] = $aButtons1[0];
                         break;
    # Verwijder Alles
    case $aButtons2[1] : DelSearchAll();
                         break;   
}

PageHeader(cTitle, "css/snuffel.css");

echo "  <form name=\"".cTitle."\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
ShowPanel($aPanelInput[0]);

switch ($aPanelInput[0])
{   
    # Nieuw
    case $aButtons1[0]: ShowResults(true);
                        break;
    # Alles
    case $aButtons1[1]: ShowResults(false);
                        break;                    
    # Zoek
    case $aButtons1[2]: $aSearchInput = GetSearchInput();
                        $aSearchInput = ProcesSearchInput($aSearchInput);
                        ShowSearch($aSearchInput);
                        ShowSearchHiddenFields($aSearchInput);                                               
                        break;        
}

echo "   <input type=\"hidden\" name=\"hidPANEL1\" value=\"$aPanelInput[0]\" />\n";
echo "  </form>\n";

PageFooter();
?>