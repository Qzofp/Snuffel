<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    index.php
 *
 * Created on Jun 26, 2011
 * Updated on Jul 11, 2011
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

// GetSpotInput
$id = GetLinkValue('id');

// Get the spot message id.
$sql = "SELECT messageid FROM snuftmp ".
       "WHERE id = $id";
list($msg) = GetItemsFromDatabase($sql);

// Get filter id and determine the filter.
$filterid = GetLinkValue('f');
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
    
// Get the filter page number.
$filternr = GetLinkValue('fp');

// Download nzb file or show spot details.
$pagenr = GetLinkValue('s');
if (!$pagenr)
{
    $pagenr = GetLinkValue('n');
    $spot = "http://".$_SERVER['SERVER_NAME']."/spotweb/?page=getnzb&messageid=$msg";
    
    // Add message id to the history table.
    $sql = "REPLACE INTO snufhst (id) VALUES ($id)";
    ExecuteQuery($sql);
    
    $button = null;
    $auto_submit = "  <script>setTimeout('window.document.Spot.submit()',1000);</script>\n";
}
else 
{     
    $spot = cSPOTWEBFOLDER."/?page=getspot&amp;messageid=$msg";
    
    $button =  "   <input type=\"submit\" name=\"btnDUMMY\" value=\"".cTitle."\"/>\n";
    $auto_submit = null;    
}

// The Spot page.
PageHeader(cTitle, "css/spot.css");

// Show or download spot.
echo "  <iframe id=\"check\" src=\"$spot\"></iframe>\n";
    
echo "  <form name=\"Spot\" action=\"index.php\" method=\"post\">\n";

// Show Snuffel button.
echo $button;

// Hidden check and page fields.
echo "   <input type=\"hidden\" name=\"hidPAGE\" value=\"0\" />\n"; 
echo "   <input type=\"hidden\" name=\"hidPAGENR\" value=\"$pagenr\" />\n";
echo "   <input type=\"hidden\" name=\"hidFILTER\" value=\"$filter\" />\n";
echo "   <input type=\"hidden\" name=\"hidFILTERNR\" value=\"$filternr\" />\n";
echo "   <input type=\"hidden\" name=\"hidMSGID\" value=\"$id\" />\n";
echo "   <input type=\"hidden\" name=\"hidCHECK\" value=\"2\" />\n";
echo "  </form>\n";

// Auto submit form.
echo $auto_submit;

PageFooter(false);
?>