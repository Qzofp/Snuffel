<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    results.php
 *
 * Created on Apr 10, 2011
 * Updated on Jun 23, 2011
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
 * Updated on Jun 23, 2011
 *
 * Description: Create the results page.
 *
 * In:  $new
 * Out: -
 *
 */
function CreateResultsPage($new)
{
    PageHeader(cTitle, "css/results.css");
    echo "  <form name=\"".cTitle."\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
    
    if ($new) {
        $page = 0;
    }
    else {
        $page = 1;
    }
    
    ShowPanel($page);  
    
    $aInput = GetResultsInput();
    $aInput = ProcessResultsInput($aInput, $page);
    ShowResults($new, $aInput);
 
    // Hidden check and page fields.
    echo "   <input type=\"hidden\" name=\"hidPAGE\" value=\"$page\" />\n"; 
    echo "   <input type=\"hidden\" name=\"hidPAGENR\" value=\"".$aInput["PAGENR"]."\" />\n";
    echo "   <input type=\"hidden\" name=\"hidCHECK\" value=\"2\" />\n";
    
    echo "  </form>\n";
    PageFooter(); 
}


/////////////////////////////////////////   Get Input Functions   ////////////////////////////////////////

/*
 * Function:    GetResultsInput
 *
 * Created on Jun 22, 2011
 * Updated on Jun 22, 2011
 *
 * Description: Get user results input.
 *
 * In:  -
 * Out: $aInput
 *
 */
function GetResultsInput()
{
    $aInput = array("PREV"=>null, "HOME"=>null, "NEXT"=>null, "PAGENR"=>0, "PAGE"=>null);
    
    $aInput["PREV"]   = GetInputValue("btnPREV");
    $aInput["HOME"]   = GetInputValue("btnHOME");    
    $aInput["NEXT"]   = GetInputValue("btnNEXT");
    $aInput["PAGENR"] = GetInputValue("hidPAGENR");
    $aInput["PAGE"]   = GetInputValue("hidPAGE");  
    
    return $aInput;
}


/////////////////////////////////////////   Process Functions    /////////////////////////////////////////

/*
 * Function:	ProcesResultsInput
 *
 * Created on Jun 22, 2011
 * Updated on Jun 22, 2011
 *
 * Description: Process the results input.
 *
 * In:  $aInput, $page
 * Out:	$aInput
 *
 */
function ProcessResultsInput($aInput, $page)
{
    if ($aInput["PREV"]) {
        $aInput["PAGENR"] -= 1;
    }
    else if ($aInput["NEXT"]) {
        $aInput["PAGENR"] += 1;
    } 
    
    if ($aInput["PAGE"] != $page || $aInput["HOME"]) {
        $aInput["PAGENR"] = 0;        
    }

    return $aInput;
}

/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	ShowResults
 *
 * Created on Apr 10, 2011
 * Updated on Jun 23, 2011
 *
 * Description: Laat de zoekresultaten zien.
 *
 * In:	$new, $aInput
 * Out:	Tabel met zoekresultaten
 *
 */
function ShowResults($new, $aInput)
{
    // Show newest or all spots.
    $new_spots = "";
    if ($new) 
    {
        $days = time() - cDays * 86400;
        $new_spots = "WHERE t.stamp > $days";
        $sort = "";
    }
    else {
        $sort = " t.title,";
    }

    // Tabel header
    $aHeaders = explode("|", cHeader);
    
    echo "  <div id=\"results\">\n";
    echo "  <table>\n";

    // Table header.
    echo "   <thead>\n";
    echo "    <tr>\n";
    echo "     <th class=\"cat\">$aHeaders[0]</th>\n";
    echo "     <th>$aHeaders[1]</th>\n";
    echo "     <th>$aHeaders[2]</th>\n";
    echo "     <th>$aHeaders[3]</th>\n";
    echo "     <th>$aHeaders[4]</th>\n";
    echo "     <th>$aHeaders[5]</th>\n";    
    echo "    </tr>\n";
    echo "   </thead>\n";

    // Table footer.
    ShowResultsFooter($new_spots, $aInput);
    
    // Table body.
    echo "   <tbody>\n";
    
    // Show the database results in table rows.
    ShowResultsRows($new_spots, $aInput["PAGENR"], $sort);

    echo "   </tbody>\n";    
    echo "  </table>\n";
    echo "  </div>\n";
}

/*
 * Function:	ShowResultsFooter
 *
 * Created on Jun 22, 2011
 * Updated on Jun 22, 2011
 *
 * Description: Shows the results table footer.
 *
 * In:  $aInput
 * Out:	Table footer
 *
 */
function ShowResultsFooter($new_spots, $aInput)
{
    // Count the number of rows from thhe results query.
    //$new_spots = str_replace("AND", "WHERE", $new_spots);
    $sql  = "SELECT * FROM snuftmp2 t $new_spots";
    $rows = CountRows($sql);
    $max  = ceil($rows/cItems);

    // The previous and next buttons. The page number is put in the hidden field: "hidPAGENR". 
    if ($max > 1) 
    {
       $n = $aInput["PAGENR"];
        switch($n)
        {
            case 0:       $prev = "     <td class=\"btn\"></td>\n";
                          $next = "     <td class=\"btn\"><input type=\"submit\" name=\"btnNEXT\" value=\"&gt;&gt;\"/></td>\n"; 
                          break;
        
            case $max-1:  $prev = "     <td class=\"btn\"><input type=\"submit\" name=\"btnPREV\" value=\"&lt;&lt;\"/></td>\n";                     
                          $next = "     <td class=\"btn\"></td>\n";
                          break;
                                            
            default:      $prev = "     <td class=\"btn\"><input type=\"submit\" name=\"btnPREV\" value=\"&lt;&lt;\"/></td>\n";
                          $next = "     <td class=\"btn\"><input type=\"submit\" name=\"btnNEXT\" value=\"&gt;&gt;\"/></td>\n"; 
        }

        $home = "<input type=\"submit\" name=\"btnHOME\" value=\"1\"/>";
        
        $n += 1;
        if ($n < 10) {
            $n = "00$n";
        }
        else if ($n < 100) {
            $n = "0$n";
        }
        
        // Show the footer.
        echo "   <tfoot>\n";
        echo "    <tr>\n";
        echo "     <td colspan=\"6\"></td>\n";
        echo "    </tr>\n";    
        echo "    <tr>\n";
        echo       $prev;
        echo "     <td colspan=\"4\" class=\"bar\">$home<span>$n</span></td>\n";
        echo       $next;
        echo "    </tr>\n";    
        echo "   </tfoot>\n";
    }    
}


/*
 * Function:	ShowResultsRow
 *
 * Created on Jun 11, 2011
 * Updated on Jun 19, 2011
 *
 * Description: Laat een resultaat rij van de tabel zien.
 *
 * In:  $action, $catkey, $category, $title, $genre, $poster, $date, $nzb
 * Out:	rij
 *
 */
function ShowResultsRow($action, $catkey, $category, $title, $genre, $poster, $date, $nzb)
{
    $class = null;    
    if ($action) {
        $action   = " $action";
    }
    
    switch ($catkey)
    {
        case 0: if ($catkey !== null) {
                    $class =  " class=\"blue$action\"";
                }
                else {
                    $class =  " class=\"gray$action\"";
                }
                break;
            
        case 1: $class =  " class=\"orange$action\"";
                break;
            
        case 2: $class =  " class=\"green$action\"";
                break;
            
        case 3: $class =  " class=\"red$action\"";
                break;
    }
       
    echo "    <tr$class>\n";
    echo "     <td class=\"cat\">$category</td>\n";
    echo "     <td>$title</td>\n";
    echo "     <td class=\"gen\">$genre</td>\n";
    echo "     <td>$poster</td>\n";
    echo "     <td>".time_ago($date, 1)."</td>\n";  
    echo "     <td class=\"nzb\">".CreateNZBLink($nzb)."</td>\n";
    echo "    </tr>\n";
}

/*
 * Function:	CreateNZBLink
 *
 * Created on Jun 12, 2011
 * Updated on Jun 12, 2011
 *
 * Description: Laat een resultaat rij van de tabel zien.
 *
 * In:  $nzb
 * Out:	$nzblink
 *
 */
function CreateNZBLink($nzb)
{
    $nzblink = "<a href=\"".cNZBlink.$nzb."\">NZB</a>";
    
    return $nzblink;
}


/////////////////////////////////////////   Query Functions   ////////////////////////////////////////////

/*
 * Function:	ShowResultsRows
 *
 * Created on Jun 11, 2011
 * Updated on Jun 23, 2011
 *
 * Description: Show the results table rows.
 *
 * In:  $new_spots, $pagenr, $sort
 * Out:	Results rows
 *
 */
function ShowResultsRows($new_spots, $pagenr, $sort)
{        
    //The results query.
    $sql = "SELECT t.category, c.name, t.title, g.name, t.poster, t.stamp, t.messageid FROM (snuftmp2 t ".
           "LEFT JOIN snuftag g ON t.category = g.cat AND (t.subcata = CONCAT(g.tag,'|') OR t.subcatd LIKE CONCAT('%',g.tag,'|'))) ".
           "LEFT JOIN snufcat c ON t.category = c.cat AND CONCAT(c.tag,'|') = t.subcata ".
           "$new_spots " .
           "ORDER BY$sort t.stamp DESC";    
    $sql = AddLimit($sql, $pagenr);
    
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
                $delta = time() - strtotime(UpdateTime());
                $last  = cLastUpdate + $delta;
                
                $stmt->bind_result($catkey, $category, $title, $genre, $poster, $date, $nzb);
                while($stmt->fetch())
                {
                    $newrow = null;
                    if ($date > $last) {
                        $newrow = "new";
                    }
                    
                    // Convert special HTML characters.
                    $title = htmlentities($title);
                    
                    ShowResultsRow($newrow, $catkey, $category, $title, $genre, $poster, $date, $nzb);
                }
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

    CloseDatabase($sfdb);    
}
?>