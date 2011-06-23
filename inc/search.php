<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    search.php
 *
 * Created on May 07, 2011
 * Updated on Jun 23, 2011
 *
 * Description: This page contains the search functions.
 * 
 * Credits: Spotweb team 
 *
 */

/////////////////////////////////////////     Search Main     ////////////////////////////////////////////

/*
 * Function:    CreateSearchPage
 *
 * Created on Jun 18, 2011
 * Updated on Jun 18, 2011
 *
 * Description: Greate the search page.
 *
 * In:  -
 * Out: -
 *
 */
function CreateSearchPage()
{
    //LoadConstants();
        
    PageHeader(cTitle, "css/search.css");
    echo "  <form name=\"".cTitle."\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
    
    ShowPanel(2);
    
    $aSearchInput = GetSearchInput();
    $aSearchInput = ProcessSearchInput($aSearchInput);
    ShowSearch($aSearchInput);
    ShowSearchHiddenFields($aSearchInput);
 
    // Hidden check and page fields.
    echo "   <input type=\"hidden\" name=\"hidPAGE\" value=\"2\" />\n";    
    echo "   <input type=\"hidden\" name=\"hidCHECK\" value=\"2\" />\n";
    
    echo "  </form>\n";
    PageFooter();    
}


/////////////////////////////////////////   Get Input Functions   ////////////////////////////////////////

/*
 * Function:    GetSearchInput
 *
 * Created on May 15, 2011
 * Updated on May 23, 2011
 *
 * Description: Get user search input.
 *
 * In:  -
 * Out: $aInput
 *
 */
function GetSearchInput()
{
    // Initial values for gategory, title, genre and poster.
    $aInput = array(null, null, null, null, null, null, null, null);

    // Get the Ok button.
    $aInput[0] = GetOk();  
    
    // Get gategory value.
    $name = "lstCategories";
    if (isset($_POST[$name]) && !empty($_POST[$name])) {
        $aInput[1] = $_POST[$name];
    }  

    // Get hidden title.
    $name = "hidTITLE";
    if (isset($_POST[$name]) && !empty($_POST[$name]))
    {
        $aInput[2] = $_POST[$name];
    }

    // Get title value.
    $name = "txtTITLE";
    if (isset($_POST[$name]) && !empty($_POST[$name])) {
        $aInput[2] = $_POST[$name];
    }    
    
    // Get genre value.
    $name = "lstGenre";
    if (isset($_POST[$name]) && !empty($_POST[$name])) {
        $aInput[3] = $_POST[$name];
    }      
    
    // Get hidden poster.
    $name = "hidPOSTER";
    if (isset($_POST[$name]) && !empty($_POST[$name]))
    {
        $aInput[4] = $_POST[$name];
    }

    // Get poster value.
    $name = "txtPOSTER";
    if (isset($_POST[$name]) && !empty($_POST[$name])) {
        $aInput[4] = $_POST[$name];
    }      
    
    // Get mode value: Add, Edit or Delete.
    list($aInput[5], $aInput[6], $aInput[7]) = GetSearchMode();
    
    return $aInput;
}

/*
 * Function:    GetSearchMode
 *
 * Created on May 21, 2011
 * Updated on May 29, 2011
 *
 * Description: Get user search mode input: ADD_0, EDIT_ID or DEL__ID.
 *
 * In:  -
 * Out: $mode, $key, $check
 *
 */
function GetSearchMode()
{
    $mode  = "ADD";
    $key   = 0;
    $check = false;

    // Get the hidden mode value.
    if (isset($_POST['hidMODE']) && !empty($_POST['hidMODE']))
    {
        $aNames = explode('_', $_POST['hidMODE']);
        $mode = $aNames[0];
        $key  = $aNames[1];
    }

    // Get the names (MODE) from the $_POST array.
    $aPost = array_keys($_POST);
    foreach ($aPost as $vPost)
    {
	// Find input with an underscore in the name.
	if (strpos($vPost, "_") != null)
        {
            $aNames = explode('_', $vPost);
            if ($aNames[0] == "EDIT" || $aNames[0] == "DEL")
            {
                $mode  = $aNames[0];
                $key   = $aNames[1];
                $check = true;
            }
	}
    }
    
    return array($mode, $key, $check);
}


/////////////////////////////////////////   Process Functions    /////////////////////////////////////////

/*
 * Function:	ProcesSearchInput
 *
 * Created on May 16, 2011
 * Updated on May 28, 2011
 *
 * Description: Process the search input.
 *
 * In:  $aInput
 * Out:	$aInput
 *
 */
function ProcessSearchInput($aInput)
{
    $mode = $aInput[5];
        
    switch ($mode)
    {
        case "ADD"  :   $aInput = AddSearch($aInput);                       
                        break;
                    
        case "EDIT" :   $aInput = EditSearch($aInput);
                        break;    
                    
        case "DEL"  :   $aInput = DelSearch($aInput);
                        break;
    }
    
    return $aInput;
}


/////////////////////////////////////////   Display Functions    /////////////////////////////////////////

/*
 * Function:	ShowSearch
 *
 * Created on May 07, 2011
 * Updated on Jun 12, 2011
 *
 * Description: Laat de zoek pagina zien.
 *
 * In:  $aInput
 * Out:	Tabel met zoek items.
 *
 */
function ShowSearch($aInput)
{
    // Tabel header
    $aHeaders = explode("|", cHeader);
    
    echo "  <div id=\"search\">\n";
    echo "  <table>\n"; //debug border

    // Table header.
    echo "   <thead>\n";
    echo "    <tr>\n";
    echo "     <th class=\"msg\"></th>\n";
    echo "     <th class=\"but\"></th>\n";
    echo "     <th class=\"cat\">$aHeaders[0]</th>\n";
    echo "     <th>$aHeaders[1]</th>\n";
    echo "     <th class=\"gen\">$aHeaders[2] / $aHeaders[6]</th>\n";
    echo "     <th>$aHeaders[3]</th>\n";
    echo "    </tr>\n";
    echo "   </thead>\n";

    // Table footer.
    // Gereserveerd.

    // Table body.
    echo "   <tbody>\n";
    // Toevoegen zoek item (rij).
    ShowSearchAddRow($aInput);
    
    // Laat overige rijen zien.
    ShowSearchRows($aInput);
    
    echo "   </tbody>\n";
    echo "  </table>\n";
    
    echo "  </div>\n";
}

/*
 * Function:	ShowSearchAddRow
 *
 * Created on May 07, 2011
 * Updated on Jun 20, 2011
 *
 * Description: Laat de invoerrij zien.
 *
 * In:  $aInput
 * Out:	Invoerrij
 *
 */
function ShowSearchAddRow($aInput)
{
    $inCategory = $aInput[1];
    $inTitle    = $aInput[2];
    $inGenre    = $aInput[3];
    $inPoster   = $aInput[4];
    $mode       = $aInput[5];
    
    $key        = -1;

    // Ok buttons (submit|cancel or add).
    if ($mode == "ADD") 
    {
        $ok = "<input type=\"image\" src=\"img/tick.png\" name=\"OK_1\"/><input type=\"image\" src=\"img/slash.png\" name=\"OK_0\"/>";
        $disabled = "";
        $aItems   = explode("|", cCategories);     
        $active   = false;  
        $message = "Add";
        $action  = null;
    }
    else 
    {
        $ok = "<img src=\"img/empty.png\"/>";
        $disabled = "disabled";
        $aItems   = array("empty");
        $active   = true;
        $inTitle  = null;
        $inPoster = null;
        $message = null;
        $action = "hov";
    }
    
    // Categorie dropbox
    $category = DropDownBox("lstCategories", cTitle, $aItems, true, $inCategory, $active);

    // Titel veld
    $title = "<input type=\"text\" size=\"40\" maxlength=\"100\" name=\"txtTITLE\" value=\"$inTitle\" $disabled/>";

    // Genre veld
    if ($inCategory && $mode == "ADD")     // Controleer of category bestaat.
    {
        $key = array_search($inCategory, $aItems);
        
        $sql = "SELECT name FROM snuftag ".
               "WHERE cat = $key AND hide = 0 ".
               "ORDER BY name";
        
        $aItems = GetItemsFromDatabase($sql);  
        $active = false;
    }
    else 
    {
        $aItems = array("empty");
        $active = true;
    }   
    
    $genre = DropDownBox("lstGenre", cTitle, $aItems, true, $inGenre, $active);
    
    // Poster veld
    $poster = "<input type=\"text\" size=\"40\" maxlength=\"100\" name=\"txtPOSTER\" value=\"$inPoster\" $disabled/>";

    ShowSearchRow($action, $message, $ok, $key, $category, $title, $genre, $poster);
}

/*
 * Function:	ShowSearchEditRow
 *
 * Created on May 28, 2011
 * Updated on Jun 20, 2011
 *
 * Description: Laat de wijzigrij zien.
 *
 * In:  $aInput, $inCategory, $inTitle, $inGenre, $inPoster
 * Out:	Wijzigrij
 *
 */
function ShowSearchEditRow($aInput, $inCategory, $inTitle, $inGenre, $inPoster)
{      
    $inCategory2 = $aInput[1];
    $inGenre2    = $aInput[3];
    $check       = $aInput[7];
   
    // Fill items array with the categories.
    $aCatItems = explode("|", cCategories);  
    
    // Check if the edit button is pushed, otherwise use the database values. 
    if (!$check) 
    {
        $inCategory = array_search($inCategory2, $aCatItems);
        $inGenre    = $inGenre2;
    }    
    
    $key = $inCategory;
    
    // Controleer of category bestaat.
    if (strlen($inCategory))     
    {     
        $catitem = $aCatItems[$inCategory];
        $sql = "SELECT name FROM snuftag ".
               "WHERE cat = $inCategory AND hide = 0 ".
               "ORDER BY name";
        
        $aGenItems = GetItemsFromDatabase($sql);  
        $active    = false;
    }
    else 
    {
        $catitem   = null; 
        $aGenItems = array("empty");
        $active    = true;
        $key       = null;
    }   
 
    // The Submit and Cancel buttons.
    $buttons = "<input type=\"image\" src=\"img/tick.png\" name=\"OK_1\"/><input type=\"image\" src=\"img/slash.png\" name=\"OK_0\"/>";
    
    // Categorie dropbox    
    $category = DropDownBox("lstCategories", cTitle, $aCatItems, true, $catitem);
 
    // Titel veld
    $title = "<input type=\"text\" size=\"40\" maxlength=\"100\" name=\"txtTITLE\" value=\"$inTitle\"/>";    
    
    // Genre veld
    $genre = DropDownBox("lstGenre", cTitle, $aGenItems, true, $inGenre, $active);
    
    // Poster veld
    $poster = "<input type=\"text\" size=\"40\" maxlength=\"100\" name=\"txtPOSTER\" value=\"$inPoster\"/>";

    ShowSearchRow(null, "Edit", $buttons, $key, $category, $title, $genre, $poster);
}

/*
 * Function:	ShowSearchHiddenFields
 *
 * Created on May 16, 2011
 * Updated on Jun 18, 2011
 *
 * Description: Laat de hidden fields titel en poster zien.
 *
 * In:  $aInput
 * Out:	Hidden fields
 *
 */
function ShowSearchHiddenFields($aInput)
{
    // Title veld
    echo "   <input type=\"hidden\" name=\"hidTITLE\" value=\"$aInput[2]\" />\n";
    
    // Poster veld
    echo "   <input type=\"hidden\" name=\"hidPOSTER\" value=\"$aInput[4]\" />\n";    

    // Mode veld
    if ($aInput[5]) {
        $aInput[5] .= '_';
    }
    
    echo "   <input type=\"hidden\" name=\"hidMODE\" value=\"$aInput[5]$aInput[6]\" />\n";
}

/*
 * Function:	ShowSearchRow
 *
 * Created on May 23, 2011
 * Updated on Jun 20, 2011
 *
 * Description: Laat een rij van de zoekwaardes tabel zien.
 *
 * In:  $action, $message, $buttons, $catkey, $category, $title, $genre, $poster
 * Out:	rij
 *
 */
function ShowSearchRow($action, $message, $buttons, $catkey, $category, $title, $genre, $poster)
{
    $class = null; 
    
    if ($action) {
        $action .= " ";
    }
    
    switch ($catkey)
    {
        case 0: if ($catkey !== null) {
                    $class =  " class=\"".$action."blue\"";
                }
                else {
                    $class =  " class=\"".$action."gray\"";
                }
                break;
            
        case 1: $class =  " class=\"".$action."orange\"";
                break;
            
        case 2: $class =  " class=\"".$action."green\"";
                break;
            
        case 3: $class =  " class=\"".$action."red\"";
                break;
            
        default: $class =  " class=\"".$action."gray\"";
    }
       
    echo "    <tr$class>\n";
    echo "     <td class=\"msg\">$message</td>\n";    
    echo "     <td class=\"but\">$buttons</td>\n";
    echo "     <td>$category</td>\n";
    echo "     <td>$title</td>\n";
    echo "     <td class=\"gen\">$genre</td>\n";
    echo "     <td>$poster</td>\n";
    echo "    </tr>\n";    
}

/*
 * Function:	ShowSearchDeleteRow
 *
 * Created on May 23, 2011
 * Updated on Jun 12, 2011
 *
 * Description: Laat een rij van de zoekwaardes tabel zien, die verwijderd gaat worden..
 *
 * In:  $catkey, $category, $title, $genre, $poster
 * Out:	rij
 *
 */
function ShowSearchDeleteRow($catkey, $category, $title, $genre, $poster)
{
    // The Submit and Cancel buttons.
    $buttons = "<input type=\"image\" src=\"img/tick.png\" name=\"OK_1\"/><input type=\"image\" src=\"img/slash.png\" name=\"OK_0\"/>";
    
    ShowSearchRow("del", "Delete", $buttons, $catkey, $category, $title, $genre, $poster);
}


/////////////////////////////////////////   Query Functions   ////////////////////////////////////////////

/*
 * Function:	ShowSearchRows
 *
 * Created on May 16, 2011
 * Updated on Jun 23, 2011
 *
 * Description: Show the search rows.
 *
 * In:  $aInput
 * Out:	Zoekrijen
 *
 */
function ShowSearchRows($aInput)
{
    $mode = $aInput[5];
    $key  = $aInput[6];
    
    $aCatItems = explode("|", cCategories);  
    
    //Query the Snuffel search items.
    $sql = "SELECT f.id, f.cat, f.title, t.name, f.subcata, f.subcatd, f.poster FROM snuffel f ".
           "LEFT JOIN snuftag t ".
           "ON f.cat = t.cat AND (f.subcata = t.tag OR f.subcatd = t.tag) ".
           "ORDER BY f.title";

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
                $stmt->bind_result($id, $catkey, $title, $genre, $subcata, $subcatd, $poster);
                while($stmt->fetch())
                {
                    $category = "";
                    if ($catkey !== null) {
                        $category = $aCatItems[$catkey];
                    }

                    // Edit and delete buttons.
                    if ($id == $key) 
                    {                       
                        switch ($mode)
                        {
                            case "ADD"  : $buttons = "<input type=\"image\" src=\"img/edit.png\" name=\"EDIT_$id\"/><input type=\"image\" src=\"img/del.png\" name=\"DEL_$id\"/>";
                                          ShowSearchRow("hov add", null, $buttons, $catkey, $category, $title, $genre, $poster);
                                          break;
                                
                            case "EDIT" : ShowSearchEditRow($aInput, $catkey, $title, $genre, $poster);
                                          break;
                                      
                            case "DEL"  : ShowSearchDeleteRow($catkey, $category, $title, $genre, $poster);
                                          break;
                        } 
                    }
                    else 
                    {
                        $buttons = "<input type=\"image\" src=\"img/edit.png\" name=\"EDIT_$id\"/><input type=\"image\" src=\"img/del.png\" name=\"DEL_$id\"/>";   
                        ShowSearchRow("hov", null, $buttons, $catkey, $category, $title, $genre, $poster);
                    }
                }
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
}

/*
 * Function:	AddSearch
 *
 * Created on May 21, 2011
 * Updated on Jun 18, 2011
 *
 * Description: Voeg zoekwaarde toe.
 *
 * In:  $aInput
 * Out:	$aInput
 *
 */
function AddSearch($aInput)
{
    $ok = $aInput[0];  

    if ($ok == 1)
    {        
        $key = null;
        list($check, $category, $title, $poster, $subcata, $subcatd) = CheckAndFixInput($aInput);

        // Add search values to the database.
        $sql = "INSERT INTO snuffel (cat, title, poster, subcata, subcatd) ".
               "VALUES ($category, $title, $poster, $subcata, $subcatd)";
    
        if ($check) 
        {
            ExecuteQuery($sql);
        
            $sql    = "SELECT MAX(id) FROM snuffel";
            $aItems = GetItemsFromDatabase($sql);
            $key = $aItems[0];
        }
        
        $aInput = array(null, null, null, null, null, "ADD", $key, null);     
    }
    
    if ($ok == 0)
    {
        $aInput = array(null, null, null, null, null, "ADD", null, null);        
    }
        
    return $aInput;
}

/*
 * Function:	EditSearch
 *
 * Created on May 28, 2011
 * Updated on May 29, 2011
 *
 * Description: Wijig zoekwaardes.
 *
 * In:  $aInput
 * Out:	$aInput
 *
 */
function EditSearch($aInput)
{
    $ok  = $aInput[0];
    $key = $aInput[6];

    if ($ok == 1)
    {
        list($check, $cat, $title, $poster, $subcata, $subcatd) = CheckAndFixInput($aInput);

        // Update search values in the database.
        $sql = "UPDATE snuffel ".
               "SET cat = $cat, title = $title, poster = $poster,  subcata = $subcata, subcatd = $subcatd ".
               "WHERE id = $key";
        
        if ($check) {
            ExecuteQuery($sql);
        }
        
        $aInput = array(null, null, null, null, null, "ADD", $key, null);         
    }
    
    if ($ok == 0)
    {
        $aInput = array(null, null, null, null, null, "ADD", null, null);
    }
        
    return $aInput;
}

/*
 * Function:	DelSearch
 *
 * Created on May 23, 2011
 * Updated on May 29, 2011
 *
 * Description: Verwijder zoekwaarde.
 *
 * In:  $aInput
 * Out:	$aInput
 *
 */
function DelSearch($aInput)
{
    $ok   = $aInput[0];
    $key  = $aInput[6];
    
    if ($ok == 1)
    {
        $sql = "DELETE FROM snuffel WHERE id = $key";
        ExecuteQuery($sql);    
    }
    
    if ($ok != -1)
    {    
        $aInput = array(null, null, null, null, null, "ADD", null, null);  
    }
    
    return $aInput;    
}

/*
 * Function:	CheckAndFixInput
 *
 * Created on May 28, 2011
 * Updated on Jun 10, 2011
 *
 * Description: Controleer en repareer input.
 *
 * In:  $aInput
 * Out:	$check, $category, $title, $poster, $subcata, $subcatd
 *
 */
function CheckAndFixInput($aInput)
{
    $check  = false;
    $cat    = $aInput[1];
    $title  = $aInput[2];
    $genre  = $aInput[3];
    $poster = $aInput[4];
    
    $subcata = "NULL";
    $subcatd = "NULL";   
   
    $aCategories = explode("|", cCategories);
    $cat = array_search($cat, $aCategories);
    if ($cat === false) {
        $cat = "NULL";
    }
    else 
    {    
        // Get the correct subcategory.
        $sql = "SELECT tag ".
               "FROM snuftag ".
               "WHERE cat = '$cat' AND name = '$genre'";
           
        $aItems = GetItemsFromDatabase($sql);  
        if ($aItems)
        {
            if ($cat < 2) {       
                $subcatd = "'$aItems[0]'";
            }
            else {
                $subcata = "'$aItems[0]'";
            }
        }
    }
    
    if ($title) {
        $title = "'$title'";
        $check = true;
    }
    else {
        $title = "NULL";
    }    
    
    if ($poster) {
        $poster = "'$poster'";
    }
    else {
        $poster = "NULL";
    }
    
    return array($check, $cat, $title, $poster, $subcata, $subcatd);
}
?>
