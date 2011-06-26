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

PageHeader(cTitle, "css/results.css");

PageFooter(); 
?>
