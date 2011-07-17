<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    index.php
 *
 * Created on Jul 15, 2011
 * Updated on Jul 16, 2011
 *
 * Description: This is the main page that processes the nzb information.
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
$id = GetLinkValue('id');

// Get the spot message id.
$sql = "SELECT messageid FROM snuftmp ".
       "WHERE id = $id";
list($msg) = GetItemsFromDatabase($sql);

//$nzb = "http://".$_SERVER['SERVER_NAME']."/spotweb/?page=getnzb&messageid=$msg";
$nzb = cSPOTWEBFOLDER."/?page=getnzb&messageid=$msg";

header("Location: $nzb");
?>
