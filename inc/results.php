<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    results.php
 *
 * Created on Apr 10, 2011
 * Updated on Jun 19, 2011
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
 * Updated on Jun 18, 2011
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
        $button = 0;
    }
    else {
        $button = 1;
    }
    
    ShowPanel($button);
    
    ShowResults($new);
 
    // Hidden check and page fields.
    echo "   <input type=\"hidden\" name=\"hidPAGE\" value=\"$button\" />\n";    
    echo "   <input type=\"hidden\" name=\"hidCHECK\" value=\"2\" />\n";
    
    echo "  </form>\n";
    PageFooter(); 
}


/////////////////////////////////////////   Get Input Functions   ////////////////////////////////////////



/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	ShowResults
 *
 * Created on Apr 10, 2011
 * Updated on Jun 11, 2011
 *
 * Description: Laat de zoekresultaten zien.
 *
 * In:	$new
 * Out:	Tabel met zoekresultaten
 *
 */
function ShowResults($new)
{
    // Tabel header
    $aHeaders = explode("|", cHeader);
    
    echo "  <div id=\"results\">\n";
    echo "  <table>\n"; //debug border

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
    // Gereserveerd.

    // Table body.
    echo "   <tbody>\n";
    
    // Laat resultaat rijen zien.
    ShowResultsRows($new);

    echo "   </tbody>\n";    
    echo "  </table>\n";
    echo "  </div>\n";
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
 * Updated on Jun 21, 2011
 *
 * Description: Laat de resultaatrijen zien.
 *
 * In:  $new
 * Out:	Resultaatrijen
 *
 */
function ShowResultsRows($new)
{
    // Bepaal de nieuwste spots.
    $new_spots = "";
    if ($new) 
    {
        $days = time() - cDays * 86400;
        $new_spots = "AND t.stamp > $days ";
    }
    
    //Geef snuffel resultaten weer  
    $sql = "SELECT t.category, (SELECT name FROM snufcat WHERE CONCAT(tag,'|') = t.subcata AND cat = t.category) AS name, ".
                  "t.title, g.name, t.poster, t.stamp, t.messageid ".
           "FROM snuftmp2 t, snuftag g ".
           "WHERE t.category = g.cat AND (t.subcata = CONCAT(g.tag,'|') OR t.subcatd LIKE CONCAT('%',g.tag,'|')) ".
           "$new_spots".
           "ORDER BY t.stamp DESC ";
           //"LIMIT 0, 100";
    
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