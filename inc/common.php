<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    common.php
 *
 * Created on Apr 09, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Deze pagina bevat de algemene functies.
 * 
 * Credits: Spotweb team 
 *
 */

/////////////////////////////////////////  Get Input Functions   /////////////////////////////////////////

/*
 * Function:	GetOk
 *
 * Created on Sep 20, 2010
 * Updated on Sep 20, 2010
 *
 * Description: Get Ok.
 *
 * In:	-
 * Out:	$ok (true|false)
 *
 */
function GetOk()
{
    $ok = -1;

    // Get the names (ID) from the $_POST array.
    $aPost = array_keys($_POST);
    foreach ($aPost as $vPost)
    {
	// Find input with an underscore in the name.
	if (strpos($vPost, "_") != null)
        {
            $aNames = explode('_', $vPost);
            if ($aNames[0] == "OK")
            {
                $ok = $aNames[1];
            }
	}
    }

    return $ok;
}


/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	DropDownBox
 *
 * Created on Apr 19, 2010
 * Updated on Dec 06, 2010
 *
 * Description: Show a dropdown box.
 *
 * In:	$name, $form, $aItems, $empty, $selected, $disabled
 * Out:	$box
 *
 */
function DropDownBox($name, $form, $aItems, $empty, $selected, $disable=false)
{
    $box = "\n";
    $disabled = "";

    if ($form) {
        $form = " onchange=$form.submit()";
    }

    if ($disable) {
        $disabled="disabled=\"disabled\"";
    }

    $box.= "      <select name=\"$name\"$form $disabled>\n";

    if ($empty) {
        $box.= "       <option value=\"\"></option>\n";
    }

    if ($aItems)
    {
        foreach ($aItems as $vItem)
        {
        	if ($vItem == $selected) {
                $box.= "       <option selected value=\"$vItem\">$vItem</option>\n";
            }
            else {
                $box.= "       <option value=\"$vItem\">$vItem</option>\n";
            }
        }
    }
    $box.= "      </select>\n     ";

    return $box;
}

/*
 * Function:    time_ago
 *
 * Created on Jun 11, 2011
 * Updated on Jun 11, 2011
 *
 * Description: Change the timestamp to a human readable format.
 * 
 * Credits: http://nl3.php.net/manual/en/function.time.php
 *          http://css-tricks.com/snippets/php/time-ago-function
 *
 * In:	$datefrom, $sqltime
 * Out:	$time
 *
 */
function time_ago($tm,$rcs = 0) {
   $cur_tm = time(); $dif = $cur_tm-$tm;
   
   # seconde|seconden|minuut|minuten|uur|uur|dag|dagen|week|weken|maand|maanden|jaar|jaar
   $aTimes = explode("|", cTimeValues);  
   $pds  = array($aTimes[0], $aTimes[2], $aTimes[4], $aTimes[6], $aTimes[8], $aTimes[10], $aTimes[12]);
   $pds2 = array($aTimes[1], $aTimes[3], $aTimes[5], $aTimes[7], $aTimes[9], $aTimes[11], $aTimes[13]);
   
   $lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
   for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);

   $no = floor($no); if($no <> 1) $pds[$v] = $pds2[$v]; $x=sprintf("%d %s ",$no,$pds[$v]);
   if(($rcs > 0)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= time_ago($_tm, --$rcs);
   return $x;
}


/////////////////////////////////////////   Query Functions   ////////////////////////////////////////////

/*
 * Function:	ExecuteQuery
 *
 * Created on Mar 07, 2010
 * Updated on May 21, 2011
 *
 * Description:  Execute a sql query.
 *
 * In:	$sql
 * Out:	-
 *
 */
function ExecuteQuery($sql)
{
    $db = OpenDatabase();

    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if(!$stmt->execute())
    	{
            die("Ececution of query failed: ".mysql_error());
   	    // Foutpagina maken, doorgeven fout met session variabele.
    	}
    	$stmt->close();
    }
    else
    {
        die("Invalid query: ".mysql_error());
   	// Foutpagina maken, doorgeven fout met session variabele.
    }

    CloseDatabase($db);
}

/*
 * Function:	GetItemsFromDatabase
 *
 * Created on Sep 12, 2010
 * Updated on Apr 23, 2011
 *
 * Description: Get a list of items from the database.
 *
 * In:	$sql
 * Out:	$aItems
 *
 */
function GetItemsFromDatabase($sql)
{
    $aItems = null;

    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $i = 0;
            $stmt->bind_result($name);
            while($stmt->fetch())
            {
                $aItems[$i] = $name;
                $i++;
            }
        }
        else
        {
            die('Ececution query failed: '.mysql_error());
            // Foutpagina maken, doorgeven fout met session variabele.
        }
        $stmt->close();
    }
    else
    {
        die('Invalid query: '.mysql_error());
   	// Foutpagina maken, doorgeven fout met session variabele.
    }

    CloseDatabase($db);

    return $aItems;
}
?>