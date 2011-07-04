<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    results.php
 *
 * Created on Apr 10, 2011
 * Updated on Jul 04, 2011
 *
 * Description: This page contains the results functions.
 * 
 * Credits: Spotweb team 
 *
 */


/////////////////////////////////////////     Results Main     ////////////////////////////////////////////

/*
 * Function:    CreateResultsPage
 *
 * Created on Jun 18, 2011
 * Updated on Jul 03, 2011
 *
 * Description: Create the results page.
 *
 * In:  $aFilters
 * Out: Results page.
 *
 */
function CreateResultsPage($aFilters)
{
    PageHeader(cTitle, "css/results.css");
    echo "  <form name=\"".cTitle."\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
     
    ShowPanel(0, $aFilters);
    
    $aInput = GetResultsInput();
    $aInput = ProcessResultsInput($aInput, $aFilters);
    ShowResults($aInput);
 
    // Hidden check and page fields.
    echo "   <input type=\"hidden\" name=\"hidPAGE\" value=\"0\" />\n"; 
    echo "   <input type=\"hidden\" name=\"hidPAGENR\" value=\"".$aInput["PAGENR"]."\" />\n";
    echo "   <input type=\"hidden\" name=\"hidFILTER\" value=\"".$aFilters["FILTER"]."\" />\n";
    echo "   <input type=\"hidden\" name=\"hidCHECK\" value=\"2\" />\n";
    
    echo "  </form>\n";
    PageFooter(); 
}


/////////////////////////////////////////   Get Input Functions   ////////////////////////////////////////

/*
 * Function:    GetResultsInput
 *
 * Created on Jun 22, 2011
 * Updated on Jul 04, 2011
 *
 * Description: Get user results input.
 *
 * In:  $filter
 * Out: $aInput
 *
 */
function GetResultsInput()
{
    $aInput = array("PREV"=>null, "HOME"=>null, "NEXT"=>null, "PAGENR"=>1, "PAGE"=>null, "SQLFILTER"=>null, "FILTERID"=>-1);
        
    $aInput["PREV"]   = GetButtonValue("btnPREV");
    $aInput["HOME"]   = GetButtonValue("btnHOME");
    $aInput["NEXT"]   = GetButtonValue("btnNEXT");
    $aInput["PAGE"]   = GetButtonValue("hidPAGE");
    $aInput["PAGENR"] = GetButtonValue("hidPAGENR");
    
    return $aInput;
}


/////////////////////////////////////////   Process Functions    /////////////////////////////////////////

/*
 * Function:	ProcesResultsInput
 *
 * Created on Jun 22, 2011
 * Updated on Jul 04 , 2011
 *
 * Description: Process the results input.
 *
 * In:  $aInput, $aFilters
 * Out:	$aInput
 *
 */
function ProcessResultsInput($aInput, $aFilters)
{
    // Create filter query condition and determine filter id.
    list($aInput["SQLFILTER"], $aInput["FILTERID"]) = CreateFilter($aFilters["FILTER"]);
    
    if (!$aInput["PAGENR"] || $aInput["PAGE"] != 0) {
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
 * Function:	ShowResults
 *
 * Created on Apr 10, 2011
 * Updated on Jul 03, 2011
 *
 * Description: Show the search results.
 *
 * In:	$aInput
 * Out:	Table with the search results.
 *
 */
function ShowResults($aInput)
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
    echo "     <th class=\"nzb\">$aHeaders[6]</th>\n";
    echo "    </tr>\n";
    echo "   </thead>\n";

    // Table footer (reserved).
    
    // Table body.
    echo "   <tbody>\n";
    
    // Show the database results in table rows.
    $sql = ShowResultsRows($aInput);

    echo "   </tbody>\n";    
    echo "  </table>\n";
    
    ShowResultsFooter($sql, $aInput, cItems);
    
    echo "  </div>\n";   
}

/*
 * Function:	ShowResultsRow
 *
 * Created on Jun 11, 2011
 * Updated on Jul 04, 2011
 *
 * Description: Show the results in a table row.
 *
 * In:  $id, $catkey, $category, $title, $genre, $poster, $date, $comment, $pagenr, $filterid
 * Out:	row
 *
 */
function ShowResultsRow($id, $catkey, $category, $title, $genre, $poster, $date, $comment, $pagenr, $filterid)
{
    $class = null;     
    
    $new = null;
    if ($id > cLastMessage) {
        $new = " new";
    }
   
    switch ($catkey)
    {
        case 0: if ($catkey !== null) {
                    $class =  " class=\"blue$new\"";
                }
                else {
                    $class =  " class=\"gray$new\"";
                }
                break;
            
        case 1: $class =  " class=\"orange$new\"";
                break;
            
        case 2: $class =  " class=\"green$new\"";
                break;
            
        case 3: $class =  " class=\"red$new\"";
                break;
    }
    
    // Convert special HTML characters.
    $title = htmlentities($title);
       
    echo "    <tr$class>\n";
    echo "     <td class=\"cat\">$category</td>\n";
    echo "     <td><a href=\"spot.php?id=$id&s=$pagenr&f=$filterid\">$title</a></td>\n";
    echo "     <td class=\"com\">$comment</td>\n";
    echo "     <td class=\"gen\">$genre</td>\n";
    echo "     <td>$poster</td>\n";
    echo "     <td>".time_ago($date, 1)."</td>\n";  
    echo "     <td class=\"nzb\"><a href=\"spot.php?id=$id&n=$pagenr\">NZB</a></td>\n";
    echo "    </tr>\n";
}

/*
 * Function:	NoResults
 *
 * Created on Jul 04, 2011
 * Updated on Jul 04, 2011
 *
 * Description: Laat een resultaat rij van de tabel zien.
 *
 * In:  -
 * Out:	no results message
 *
 */
function NoResults()
{
    echo "    <tr class=\"no_results\">\n";
    echo "     <td colspan=\"7\">".cNoResults."</td>\n";
    echo "    </tr>\n";
}


/////////////////////////////////////////   Query Functions   ////////////////////////////////////////////

/*
 * Function:	ShowResultsRows
 *
 * Created on Jun 11, 2011
 * Updated on Jul 04, 2011
 *
 * Description: Show the results table rows.
 *
 * In:  $aInput
 * Out:	$query
 *
 */
function ShowResultsRows($aInput)
{        
    //The results query.
    $sql  = "SELECT t.id, t.category, c.name, t.title, g.name, t.poster, t.stamp, t.commentcount FROM (snuftmp t ".
            "LEFT JOIN snuftag g ON t.category = g.cat AND (t.subcata = CONCAT(g.tag,'|') OR t.subcatd LIKE CONCAT('%',g.tag,'|'))) ".
            "LEFT JOIN snufcat c ON t.category = c.cat AND CONCAT(c.tag,'|') = t.subcata ";
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
                $stmt->bind_result($id, $catkey, $category, $title, $genre, $poster, $date, $comment);
                while($stmt->fetch())
                {                   
                    ShowResultsRow($id, $catkey, $category, $title, $genre, $poster, $date, $comment, $aInput["PAGENR"], $aInput["FILTERID"]);
                }
            }
            else {
                NoResults();
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

/*
 * Function:	CreateFilter
 *
 * Created on Jul 02, 2011
 * Updated on Jul 04, 2011
 *
 * Description: Create filter condition which is added to the final query.
 *
 * In:	$filter
 * Out:	$sql, id
 *
 */
function CreateFilter($filter)
{
    // Reset id.
    $id  = -1;
    
    $sql = "ORDER BY t.title, t.stamp DESC";
    $check = false;
    
    $aButtons = explode("|", cButtons);
      
     // No "Reset"
    if ($filter != $aButtons[4])
    {   
        $check = true;
        
        // "Nieuw"
        if ($filter == $aButtons[5]) 
        {  
            // "Nieuw" id.
            $id = 0;
            
            // Get last message id.
            $sql  = "SELECT value FROM snufcnf WHERE name = 'LastMessage'";
            list($last) = GetItemsFromDatabase($sql);
            
            $sql = "WHERE t.id > $last ORDER BY t.stamp DESC";
        }
        else if ($filter)
        {
            // Determine filter id.
            $sql = "SELECT id FROM snuffel ".
                   "WHERE title = '$filter'";
            list($id) = GetItemsFromDatabase($sql);
            
            $sql = "WHERE MATCH(t.title) AGAINST ('$filter' IN BOOLEAN MODE) ORDER BY t.stamp DESC";
        } 
    }
    
    return array($sql, $id);
}
?>