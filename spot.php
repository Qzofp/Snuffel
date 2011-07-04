<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    index.php
 *
 * Created on Jun 26, 2011
 * Updated on Jul 04, 2011
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

// GetSpotInput
$id     = GetLinkValue('id');

// Get the spot message id.
$sql = "SELECT messageid FROM snuftmp ".
       "WHERE id = $id";
list($msg) = GetItemsFromDatabase($sql);

// Download nzb file or show spot details.
$pagenr = GetLinkValue('s');
if (!$pagenr)
{
    $pagenr = GetLinkValue('n');
    $nzb = cSPOTWEBFOLDER."/?page=getnzb&messageid=$msg";
    
    // Download nzb file.
    header("Location: $nzb");
}
else 
{   
    $aButtons = explode("|", cButtons);

    // Get filterid and determine filter.
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
    $filternr = GetLinkValue('p');
    
    $spot = cSPOTWEBFOLDER."/?page=getspot&amp;messageid=$msg";

    PageHeader(cTitle, "css/spot.css");

    // Show spot.
    echo "  <iframe src=\"$spot\"></iframe>\n";
    
    // Return to Snuffel button.
    echo "  <form name=\"Spot\" action=\"index.php\" method=\"post\">\n";
    echo "   <input type=\"submit\" name=\"btnDUMMY\" value=\"".cTitle."\"/>\n";
    
    // Hidden check and page fields.
    echo "   <input type=\"hidden\" name=\"hidPAGE\" value=\"0\" />\n"; 
    echo "   <input type=\"hidden\" name=\"hidPAGENR\" value=\"$pagenr\" />\n";
    echo "   <input type=\"hidden\" name=\"hidFILTER\" value=\"$filter\" />\n";
    echo "   <input type=\"hidden\" name=\"hidFILTERNR\" value=\"$filternr\" />\n";
    echo "   <input type=\"hidden\" name=\"hidCHECK\" value=\"2\" />\n";

    echo "  </form>\n";
    PageFooter(false);
}
?>