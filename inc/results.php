<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    results.php
 *
 * Created on Apr 10, 2011
 * Updated on Jun 17, 2011
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
    PageHeader(cTitle, "css/snuffel.css");
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
 * Updated on Jun 12, 2011
 *
 * Description: Laat een resultaat rij van de tabel zien.
 *
 * In:  $catkey, $category, $title, $genre, $poster, $date, $nzb
 * Out:	rij
 *
 */
function ShowResultsRow($catkey, $category, $title, $genre, $poster, $date, $nzb)
{
      
    switch ($catkey)
    {
        case 0: if ($catkey !== null) {
                    $class =  " class=\"blue\"";
                }
                else {
                    $class =  " class=\"gray\"";
                }
                break;
            
        case 1: $class =  " class=\"orange\"";
                break;
            
        case 2: $class =  " class=\"green\"";
                break;
            
        case 3: $class =  " class=\"red\"";
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
 * Updated on Jun 11, 2011
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
    $sql = "SELECT t.category, c.name, t.title, g.name, t.poster, t.stamp, t.messageid FROM snuftmp t, snuffel f, snufcat c, snuftag g ".
           "WHERE t.title LIKE CONCAT('%', f.title, '%') ".
           "AND (t.poster = f.poster OR f.poster IS NULL) ".
           "AND (t.category = f.cat OR f.cat IS NULL) ".
           "AND (t.subcata LIKE CONCAT('%', f.subcata, '|%') OR f.subcata IS NULL) ".
           "AND (t.subcatd LIKE CONCAT('%', f.subcatd, '|%') OR f.subcatd IS NULL) ".
           "AND (t.category = c.cat) ".
           "AND (t.subcata = CONCAT(c.tag, '|')) ".
           "AND (t.category = g.cat) ".
           "AND (t.subcata = CONCAT(g.tag, '|') OR t.subcatd LIKE CONCAT('%', g.tag, '|%')) ".
           "$new_spots".
           "GROUP BY t.title ".
           "ORDER BY t.stamp DESC";

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
                $stmt->bind_result($catkey, $category, $title, $genre, $poster, $date, $nzb);
                while($stmt->fetch())
                {
                    ShowResultsRow($catkey, $category, $title, $genre, $poster, $date, $nzb);
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