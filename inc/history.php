<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    history.php
 *
 * Created on Jul 18, 2011
 * Updated on Jul 23, 2011
 *
 * Description: This page contains the history functions.
 * 
 * Credits: Spotweb team 
 *
 */


/////////////////////////////////////////     Results Main     ////////////////////////////////////////////

/*
 * Function:    CreateHistoryPage
 *
 * Created on Jul 18, 2011
 * Updated on Jul 23, 2011
 *
 * Description: Create the history page.
 *
 * In:  $aFilters
 * Out: History page.
 *
 */
function CreateHistoryPage($aFilters)
{
    PageHeader(cTitle, "css/results.css");
    echo "  <form name=\"".cTitle."\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
     
    ShowPanel(1, $aFilters);
    
    $aInput = GetHistoryInput();
    $aInput = ProcessHistoryInput($aInput, $aFilters);
    ShowHistory($aInput);
 
    // Hidden check and page fields.
    echo "   <input type=\"hidden\" name=\"hidPAGE\" value=\"1\" />\n"; 
    echo "   <input type=\"hidden\" name=\"hidPAGENR\" value=\"".$aInput["PAGENR"]."\" />\n";
    echo "   <input type=\"hidden\" name=\"hidFILTER\" value=\"".$aFilters["FILTER"]."\" />\n";
    echo "   <input type=\"hidden\" name=\"hidFILTERNR\" value=\"".$aFilters["FILTERNR"]."\" />\n";   
    echo "   <input type=\"hidden\" name=\"hidCHECK\" value=\"2\" />\n";
    
    echo "  </form>\n";
    PageFooter();     
}


/////////////////////////////////////////   Get Input Functions   ////////////////////////////////////////

/*
 * Function:    GetHistoryInput
 *
 * Created on Jul 18, 2011
 * Updated on Jul 18, 2011
 *
 * Description: Get user history input.
 *
 * In:  -
 * Out: $aInput
 *
 */
function GetHistoryInput()
{
    $aInput = array("PREV"=>null, "HOME"=>null, "NEXT"=>null, "PAGENR"=>1, "PAGE"=>null, "SQLFILTER"=>null, 
                    "FILTERID"=>-1, "FILTERNR"=>1, "MSGID"=>0);
        
    $aInput["PREV"]   = GetButtonValue("btnPREV");
    $aInput["HOME"]   = GetButtonValue("btnHOME");
    $aInput["NEXT"]   = GetButtonValue("btnNEXT");
    $aInput["PAGE"]   = GetButtonValue("hidPAGE");
    $aInput["PAGENR"] = GetButtonValue("hidPAGENR");
    $aInput["MSGID"]  = GetButtonValue("hidMSGID");
    
    return $aInput;
}


/////////////////////////////////////////   Process Functions    /////////////////////////////////////////

/*
 * Function:	ProcesHistoryInput
 *
 * Created on Jul 18, 2011
 * Updated on Jul 23, 2011
 *
 * Description: Process the history input.
 *
 * In:  $aInput, $aFilters
 * Out:	$aInput
 *
 */
function ProcessHistoryInput($aInput, $aFilters)
{
    // Create filter query condition and determine filter id.
    list($aInput["SQLFILTER"], $aInput["FILTERID"]) = CreateFilter($aFilters["FILTER"]);
    $aInput["FILTERNR"] = $aFilters["FILTERNR"];
    
    if (!$aInput["PAGENR"] || $aInput["PAGE"] != 1) {
        $aInput["PAGENR"] = 1;
    }
    
    if ($aInput["PREV"]) {
        $aInput["PAGENR"] -= 1;
    }
    else if ($aInput["NEXT"]) {
        $aInput["PAGENR"] += 1;
    } 
    
    if ($aInput["HOME"] || $aFilters["RESET"]) {
        $aInput["PAGENR"] = 1;        
    }
    
    return $aInput;
}

/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	ShowHistory
 *
 * Created on Jul 18, 2011
 * Updated on Jul 23, 2011
 *
 * Description: Show table with history.
 *
 * In:	$aInput
 * Out:	History table.
 *
 */
function ShowHistory($aInput)
{
    // Tabel header
    $aHeaders = explode("|", cHeader);
    
    echo "  <div id=\"results_top\">\n";
    echo "  <table class=\"results\">\n";

    // Table header.
    echo "   <thead>\n";
    echo "    <tr>\n";
    echo "     <th class=\"cat\">$aHeaders[0]</th>\n";
    echo "     <th>$aHeaders[1]</th>\n";
    echo "     <th class=\"com\">$aHeaders[2]</th>\n";    
    echo "     <th class=\"gen\">$aHeaders[3]</th>\n";
    echo "     <th class=\"pos\">$aHeaders[4]</th>\n";
    echo "     <th class=\"dat\">$aHeaders[5]</th>\n";
    echo "     <th class=\"hst\"></th>\n";    
    echo "     <th class=\"nzb\">$aHeaders[6]</th>\n";
    echo "    </tr>\n";
    echo "   </thead>\n";

    // Table footer (reserved).
    
    // Table body.
    echo "   <tbody>\n";
    
    // Show the database results in table rows.
    $sql = ShowHistoryRows($aInput);

    echo "   </tbody>\n";    
    echo "  </table>\n";
    
    ShowResultsFooter($sql, $aInput, cItems);
    
    echo "  </div>\n";   
}

/*
 * Function:	ShowHistoryRow
 *
 * Created on Jul 18, 2011
 * Updated on Jul 23, 2011
 *
 * Description: Show history in a table row.
 *
 * In:  $id, $catkey, $category, $title, $genre, $poster, $date, $comment, $history, $aInput
 * Out:	row
 *
 */
function ShowHistoryRow($id, $catkey, $category, $title, $genre, $poster, $date, $comment, $history, $aInput)
{     
    $active = null;
    if ($id == $aInput["MSGID"]) {
        $active = "active ";
    }
    
    $new = null;
    if ($id > cLastMessage) {
        $new = " new";
    }
   
    switch ($catkey)
    {
        case 0: $color = "blue$new";
                break;
            
        case 1: $color = "orange$new";
                break;
            
        case 2: $color = "green$new";
                break;
            
        case 3: $color = "red$new";
                break;
    }
    
    // Convert special HTML characters.
    $title = htmlentities($title);
    
    // Checks NZB download history.
    if ($history) {
        $history = "<img src=\"img/tick.png\" />";
    }
       
    echo "    <tr class=\"$active$color\">\n";
    echo "     <td class=\"cat\">$category</td>\n";
    echo "     <td class=\"title\"><a href=\"spot.php?id=$id&p=1&pn=".$aInput["PAGENR"]."&f=".$aInput["FILTERID"]."&fn=".$aInput["FILTERNR"]."\">$title</a></td>\n";
    echo "     <td class=\"com\">$comment</td>\n";
    echo "     <td class=\"gen\">$genre</td>\n";
    echo "     <td>$poster</td>\n";
    echo "     <td>".time_ago($date, 1)."</td>\n";
    echo "     <td id=\"h$id\">$history</td>\n";
    echo "     <td class=\"nzb\"><a onclick=\"nzb('$id')\" href=\"#$id\">NZB</a></td>\n";
    echo "    </tr>\n";
}


/////////////////////////////////////////   Query Functions   ////////////////////////////////////////////

/*
 * Function:	ShowHistoryRows
 *
 * Created on Jul 18, 2011
 * Updated on Jul 23, 2011
 *
 * Description: Show the history table rows.
 *
 * In:  $aInput
 * Out:	$query
 *
 */
function ShowHistoryRows($aInput)
{        
    //The results query.
    $sql  = "SELECT t.id, t.category, c.name, t.title, g.name, t.poster, t.stamp, t.commentcount, h.id FROM (snuftmp t ".
            "LEFT JOIN snuftag g ON t.category = g.cat AND (t.subcata = CONCAT(g.tag,'|') OR t.subcatd LIKE CONCAT('%',g.tag,'|'))) ".
            "LEFT JOIN snufcat c ON t.category = c.cat AND CONCAT(c.tag,'|') = t.subcata ".
            "RIGHT JOIN snufhst h ON t.id = h.id ";
    $sql .= $aInput["SQLFILTER"];
    
    $query = $sql;
    
    $sql  = AddLimit($sql, $aInput["PAGENR"], cItems);
    
    //Debug
    //echo $sql;
        
    $sfdb = OpenDatabase();
    $stmt = $sfdb->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            // Get number of rows.
            $stmt->store_result();
            $rows = $stmt->num_rows;

            if ($rows != 0)
            {              
                $stmt->bind_result($id, $catkey, $category, $title, $genre, $poster, $date, $comment, $history);
                while($stmt->fetch())
                {                
                    ShowHistoryRow($id, $catkey, $category, $title, $genre, $poster, $date, $comment, $history, $aInput);
                }
            }
            else {
                NoResults(7);
            }
        }
        else
        {
            die('Ececution query failed: '.mysql_error());
        }
        $stmt->close();
    }
    else
    {
        die('Invalid query: '.mysql_error());
    }    

    CloseDatabase($sfdb);  
    
    return $query;
}
?>
