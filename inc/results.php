<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    results.php
 *
 * Created on Apr 10, 2011
 * Updated on Jun 25, 2011
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
 * Updated on Jun 25, 2011
 *
 * Description: Create the results page.
 *
 * In:  -
 * Out: Results page.
 *
 */
function CreateResultsPage()
{
    PageHeader(cTitle, "css/results.css");
    echo "  <form name=\"".cTitle."\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
     
    ShowPanel(0);
    
    $aInput = GetResultsInput();
    $aInput = ProcessResultsInput($aInput);
    ShowResults($aInput);
 
    // Hidden check and page fields.
    echo "   <input type=\"hidden\" name=\"hidPAGE\" value=\"0\" />\n"; 
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
 * Updated on Jun 25, 2011
 *
 * Description: Process the results input.
 *
 * In:  $aInput
 * Out:	$aInput
 *
 */
function ProcessResultsInput($aInput)
{
    if ($aInput["PREV"]) {
        $aInput["PAGENR"] -= 1;
    }
    else if ($aInput["NEXT"]) {
        $aInput["PAGENR"] += 1;
    } 
    
    if ($aInput["HOME"]) {
        $aInput["PAGENR"] = 0;        
    }

    return $aInput;
}

/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	ShowResults
 *
 * Created on Apr 10, 2011
 * Updated on Jun 25, 2011
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
    echo "     <th>$aHeaders[2]</th>\n";
    echo "     <th>$aHeaders[3]</th>\n";
    echo "     <th>$aHeaders[4]</th>\n";
    echo "     <th>$aHeaders[5]</th>\n";    
    echo "    </tr>\n";
    echo "   </thead>\n";

    // Table footer (reserved).
    
    // Table body.
    echo "   <tbody>\n";
    
    // Show the database results in table rows.
    ShowResultsRows($aInput["PAGENR"]);

    echo "   </tbody>\n";    
    echo "  </table>\n";
    
    ShowResultsFooter($aInput);    
    echo "  </div>\n";   
}

/*
* Function: ShowResultsFooter
*
* Created on Jun 22, 2011
* Updated on Jun 25, 2011
*
* Description: Shows the results footer with navigation bar.
*
* In: $aInput
* Out: Results footer
*
*/
function ShowResultsFooter($aInput)
{
    $sql = "SELECT * FROM snuftmp2";
    $rows = CountRows($sql);
    $max = ceil($rows/cItems);

    // The previous and next buttons. The page number is put in the hidden field: "hidPAGENR".
    if ($max > 1)
    {
       $n = $aInput["PAGENR"];
        switch($n)
        {
            case 0     : $prev = "    <input class=\"left\" type=\"button\" name=\"\" value=\"\"/>\n";
                         $next = "    <input class=\"right\" type=\"submit\" name=\"btnNEXT\" value=\"&gt;&gt;\"/>\n";
                         break;
        
            case $max-1: $prev = "    <input class=\"left\" type=\"submit\" name=\"btnPREV\" value=\"&lt;&lt;\"/>\n";
                         $next = "    <input class=\"right\" type=\"button\" name=\"\" value=\"\"/>\n";
                         break;
                                            
            default:     $prev = "    <input class=\"left\" type=\"submit\" name=\"btnPREV\" value=\"&lt;&lt;\"/>\n";
                         $next = "    <input class=\"right\" type=\"submit\" name=\"btnNEXT\" value=\"&gt;&gt;\"/>\n";
        }

        $home = "    <input class=\"home\" type=\"submit\" name=\"btnHOME\" value=\"1\"/>\n";
               
        // Show footer / navigation bar.
        echo "  <div class=\"results\">\n";
        echo "   <div class=\"bar\">\n";
        echo      $prev; 
        echo      $home;
        echo "    <span>".($n+1)."</span>\n";
        echo      $next;
        echo "   </div>\n";        
        echo "  </div>\n";
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
 * Updated on Jun 25, 2011
 *
 * Description: Show the results table rows.
 *
 * In:  $pagenr
 * Out:	Results rows
 *
 */
function ShowResultsRows($pagenr)
{        
    //The results query.
    $sql = "SELECT t.category, c.name, t.title, g.name, t.poster, t.stamp, t.messageid FROM (snuftmp2 t ".
           "LEFT JOIN snuftag g ON t.category = g.cat AND (t.subcata = CONCAT(g.tag,'|') OR t.subcatd LIKE CONCAT('%',g.tag,'|'))) ".
           "LEFT JOIN snufcat c ON t.category = c.cat AND CONCAT(c.tag,'|') = t.subcata ".
           "ORDER BY t.stamp DESC";  
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