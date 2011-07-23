<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    index.php
 *
 * Created on Jun 26, 2011
 * Updated on Jul 18, 2011
 *
 * Description: This is the main page that shows (or processes) the spot information. 
 * 
 * Credits: Spotweb team.
 *
 */

/////////////////////////////////////////      Spot Main      ////////////////////////////////////////////

// The Spotweb location is found in the settings.php file.
require_once 'inc/settings.php';
require_once 'inc/common.php';

LoadConstants();
$aButtons = explode("|", cButtons);

// Get id, page, page number, filter id and filter nr.
$id       = GetLinkValue('id');
$page     = GetLinkValue('p');
$pagenr   = GetLinkValue('pn');
$filterid = GetLinkValue('f');
$filternr = GetLinkValue('fn');

// Get the spot message id.
$sql = "SELECT messageid FROM snuftmp ".
       "WHERE id = $id";
list($msg) = GetItemsFromDatabase($sql);

// Determine filter.
if ($filterid == -1) {
    $filter = $aButtons[4]; // "Reset" filter;
}
else if ($filterid == 0){
    $filter = $aButtons[5]; // "Nieuw" filter;
}
else
{
    $sql = "SELECT title FROM snuffel ".
           "WHERE id = '$filterid'";
    list($filter) = GetItemsFromDatabase($sql);
}

$spot = cSPOTWEBFOLDER."/?page=getspot&amp;messageid=$msg";

PageHeader(cTitle, "css/spot.css");

// Show spot.
echo " <iframe src=\"$spot\"></iframe>\n";
    
// Return to Snuffel button.
echo " <form name=\"Spot\" action=\"index.php\" method=\"post\">\n";
echo " <input type=\"submit\" name=\"btnDUMMY\" value=\"".cTitle."\"/>\n";
    
// Hidden check and page fields.
echo " <input type=\"hidden\" name=\"hidPAGE\" value=\"$page\" />\n";
echo " <input type=\"hidden\" name=\"hidPAGENR\" value=\"$pagenr\" />\n";
echo " <input type=\"hidden\" name=\"hidFILTER\" value=\"$filter\" />\n";
echo " <input type=\"hidden\" name=\"hidFILTERNR\" value=\"$filternr\" />\n";
echo " <input type=\"hidden\" name=\"hidMSGID\" value=\"$id\" />\n";
echo " <input type=\"hidden\" name=\"hidCHECK\" value=\"2\" />\n";

echo " </form>\n";

PageFooter(false);
?>