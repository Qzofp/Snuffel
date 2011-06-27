<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    index.php
 *
 * Created on Jun 26, 2011
 * Updated on Jun 27, 2011
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
$pagenr = GetLinkValue('n');

// Get the spot message id.
$sql = "SELECT messageid FROM snuftmp ".
       "WHERE id = $id";
list($msg) = GetItemsFromDatabase($sql);
$spot = cSPOTWEBFOLDER."/?page=getspot&amp;messageid=$msg";

PageHeader(cTitle, "css/spot.css");
echo "  <form name=\"Spot\" action=\"index.php\" method=\"post\">\n";

// Show Spot.
echo "   <iframe src=\"$spot\" width=\"100%\" height=\"80%\"></iframe>\n";

echo "   <input type=\"submit\" name=\"btnDUMMY\" value=\"SNUFFEL\"/>\n";

// Hidden check and page fields.
echo "   <input type=\"hidden\" name=\"hidPAGE\" value=\"0\" />\n"; 
echo "   <input type=\"hidden\" name=\"hidPAGENR\" value=\"$pagenr\" />\n";
echo "   <input type=\"hidden\" name=\"hidCHECK\" value=\"2\" />\n";

echo "  </form>\n";
PageFooter(false);


/////////////////////////////////////////   Query Functions   ////////////////////////////////////////////


?>