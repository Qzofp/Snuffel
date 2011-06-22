<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    common.php
 *
 * Created on Apr 09, 2011
 * Updated on Jun 18, 2011
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


/*
 * Function:	GetInputValue
 *
 * Created on Jun 17, 2011
 * Updated on Jun 18, 2011
 *
 * Description: Get input value from a button.
 *
 * In:	$name
 * Out:	$value
 *
 */
function GetInputValue($name)
{
    $value = null;
    
    if (isset($_POST[$name]) && !empty($_POST[$name]))
    {
        $value = $_POST[$name];
    }  
    
    return $value;
}


/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	Header
 *
 * Created on Aug 14, 2010
 * Updated on Apr 16, 2011
 *
 * Description: Returns a page header.
 *
 * In:	$title, $css
 * Out:	header
 *
 */
function PageHeader($title, $css)
{
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
    echo "   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"> \n";

    echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";

    echo " <head> \n";
    echo "  <title>$title</title> \n";
    echo "  <meta http-equiv=\"content-type\" content=\"text/html;charset=ISO-8859-1\" />\n";
    echo "  <link href=\"$css\" rel=\"stylesheet\" type=\"text/css\" />\n";
    echo " </head>\n";

    echo " <body>\n";
    echo "  <div id=\"main\">\n";
}

/*
 * Function:	Footer
 *
 * Created on Aug 14, 2010
 * Updated on Apr 16, 2010
 *
 * Description: Returns a page footer.
 *
 * In:	-
 * Out:	footer
 *
 */
function PageFooter()
{
    // HTML end
    echo "  </div>\n"; // Close div main.
    echo " </body>\n";
    echo "</html>";
}

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
 * Function:	OpenDatabase
 *
 * Created on Aug 22, 2008
 * Updated on Nov 29, 2009
 *
 * Description: Open the database.
 *
 * In:	-
 * Out:	$db
 *
 */
function OpenDatabase()
{
    // Make a connection to the database.
    $db = mysqli_connect(cHOST, cUSER, cPASS, cDBASE);
    if (!$db) {
        die('Could not connect: '.mysqli_error());
    }

    // Select the database.
    $db_selected = mysqli_select_db($db, cDBASE);
    if (!$db_selected) {
        die ('Can\'t use '.cDBASE.' : '.mysqli_error());
    }

    return $db;
}

/*
 * Function:	CloseDatabase
 *
 * Created on Aug 22, 2008
 * Updated on Nov 29, 2009
 *
 * Description: Close the database.
 *
 * In:	$db
 * Out:	-
 *
 */
function CloseDatabase($db)
{
    mysqli_close($db);
}

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

/*
 * Function:	LoadConstants
 *
 * Created on Jun 13, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Load the Snuffel constants from the snufcnf table.
 *
 * In:	-
 * Out:	Snuffel constants
 *
 */
function LoadConstants()
{
    $db = OpenDatabase();
    
    $sql = "SELECT name, value FROM snufcnf";

    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($name, $value);
            while($stmt->fetch())
            {
                define("c$name", "$value");
                // cVersion
                // c......
            }
        }
        else {
            die('Ececution query failed: '.mysqli_error());
        }
        $stmt->close();
    }
    else {
        die('Invalid query: '.mysqli_error());
    }

    CloseDatabase($db);    
}

/*
 * Function:	AddLimit
 *
 * Created on Okt 03, 2008
 * Updated on Jun 23, 2010
 *
 * Description: Determine limit and add it to the SQL query.
 *
 * In:	$sql, $n
 * Out:	$sql
 *
 */
function AddLimit($sql, $n)
{
    $limit = " LIMIT ";
    $limit.= $n * cItems;
    $limit.= ", ";
    $limit.= cItems;

    $sql.= $limit;

    return $sql;
}

/*
 * Function:	CountRows
 *
 * Created on Dec 20, 2009
 * Updated on Jun 22, 2011
 *
 * Description: Count the number of rows from a sql query.
 *
 * In:	$sql
 * Out:	$rows
 *
 */
function CountRows($sql)
{
    $db = OpenDatabase();

    $result = $db->query($sql);
    if ($result)
    {
        // Determine number of rows result set.
    	$rows = $result->num_rows;

    	// Close result set.
    	$result->close();
    }
    else {
        die('Ececution query failed: '.$db->error);
    }
    
    CloseDatabase($db);
    
    return $rows;
}
?>