<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    results.php
 *
 * Created on Apr 10, 2011
 * Updated on Jun 11, 2011
 *
 * Description: Deze pagina bevat de resultaten functies.
 * 
 * Credits: Spotweb team 
 *
 */

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

/*
 * Function:	UpdateResults
 *
 * Created on Aug 23, 2011
 * Updated on Jun 10, 2011
 *
 * Description: Werk de resultaten bij. Let op, dit is geen spotweb update! 
 *
 * In:	-
 * Out:	Updated snuftemp tabel
 *
 */
function UpdateResults()
{
    // Leeg snuftemp tabel.
    $sql = "TRUNCATE snuftmp";

    ExecuteQuery($sql);

    // Voeg spots id's toe aan tabel snuftemp waar gezochte titel uit snuffel tabel in tabel spots bestaat.   
    $sql = "INSERT INTO snuftmp(messageid, poster, title, tag, category, subcata, subcatb, subcatc, subcatd, subcatz, stamp, reversestamp, filesize, moderated, commentcount, spotrating) ".
           "SELECT messageid, poster, title, tag, category, subcata, subcatb, subcatc, subcatd, subcatz, stamp, reversestamp, filesize, moderated, commentcount, spotrating FROM spots ".
           "WHERE MATCH(title) ".
           "AGAINST((SELECT GROUP_CONCAT(title) FROM snuffel) IN BOOLEAN MODE)";

    ExecuteQuery($sql);
}

?>
