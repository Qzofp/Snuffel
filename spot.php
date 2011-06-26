<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    index.php
 *
 * Created on Jun 26, 2011
 * Updated on Jun 26, 2011
 *
 * Description: This is the main page that shows (or processes) the spot information. 
 * 
 * Credits: Spotweb team.
 *
 */

// The Spotweb location is found in the settings.php file.
require_once 'inc/settings.php';
require_once 'inc/common.php';

LoadConstants();

PageHeader(cTitle, "css/spot.css");
echo "  <form name=\"Spot\" action=\"index.php\" method=\"post\">\n";

echo "   <input type=\"submit\" name=\"btnDUMMY\" value=\"SNUFFEL\"/>\n";

// Hidden check and page fields.
echo "   <input type=\"hidden\" name=\"hidPAGE\" value=\"0\" />\n"; 
echo "   <input type=\"hidden\" name=\"hidPAGENR\" value=\"3\" />\n";
echo "   <input type=\"hidden\" name=\"hidCHECK\" value=\"2\" />\n";

echo "  </form>\n";
PageFooter();
?>