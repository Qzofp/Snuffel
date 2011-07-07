<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    search.php
 *
 * Created on May 07, 2011
 * Updated on Jul 07, 2011
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
 * Updated on Jul 02, 2011
 *
 * Description: Greate the search page.
 *
 * In:  -
 * Out: -
 *
 */
function CreateSearchPage()
{       
    PageHeader(cTitle, "css/search.css");
    echo "  <form name=\"".cTitle."\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
    
    ShowPanel(2);
    
    $aInput = GetSearchInput();
    $aInput = ProcessSearchInput($aInput);
    ShowSearch($aInput);
    ShowSearchHiddenFields($aInput);
 
    // Hidden check and page fields.
    echo "   <input type=\"hidden\" name=\"hidPAGE\" value=\"2\" />\n";  
    echo "   <input type=\"hidden\" name=\"hidPAGENR\" value=\"".$aInput["PAGENR"]."\" />\n";    
    echo "   <input type=\"hidden\" name=\"hidCHECK\" value=\"2\" />\n";
    
    echo "  </form>\n";
    PageFooter();    
}


/////////////////////////////////////////   Get Input Functions   ////////////////////////////////////////

/*
 * Function:    GetSearchInput
 *
 * Created on May 15, 2011
 * Updated on Jul 01, 2011
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
    $aInput = array("OK"=>null, "CAT"=>null, "TITLE"=>null, "GENRE"=>null, "POSTER"=>null, "MODE"=>null, "ID"=>0, 
                    "CHECK"=>false, "PREV"=>null, "HOME"=>null, "NEXT"=>null, "PAGENR"=>1, "PAGE"=>null);
    
    $aInput["OK"]     = GetOk();
    $aInput["CAT"]    = GetButtonValue("lstCATEGORIES");
    $aInput["GENRE"]  = GetButtonValue("lstGENRE");
    
    $aInput["TITLE"]  = GetButtonValue("txtTITLE"); 
    if (!$aInput["TITLE"]) {
        $aInput["TITLE"]  = GetButtonValue("hidTITLE");    
    }
    
    $aInput["POSTER"]  = GetButtonValue("txtPOSTER"); 
    if (!$aInput["POSTER"]) {
        $aInput["POSTER"]  = GetButtonValue("hidPOSTER");    
    }    
    
    list($aInput["MODE"], $aInput["ID"], $aInput["CHECK"]) = GetSearchMode();
    
    $aInput["PREV"]   = GetButtonValue("btnPREV");
    $aInput["HOME"]   = GetButtonValue("btnHOME");   
    $aInput["NEXT"]   = GetButtonValue("btnNEXT");    
    $aInput["PAGE"]   = GetButtonValue("hidPAGE");
    $aInput["PAGENR"] = GetButtonValue("hidPAGENR");

    
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
 * Updated on Jul 02, 2011
 *
 * Description: Process the search input.
 *
 * In:  $aInput
 * Out:	$aInput
 *
 */
function ProcessSearchInput($aInput)
{      
    if (!$aInput["PAGENR"] || $aInput["PAGE"] != 2) {
        $aInput["PAGENR"] = 1;
    }
    
    if ($aInput["PREV"]) {
        $aInput["PAGENR"] -= 1;
    }
    else if ($aInput["NEXT"]) {
        $aInput["PAGENR"] += 1;
    } 
    
    if ($aInput["HOME"]) {
        $aInput["PAGENR"] = 1;        
    }
    
    switch ($aInput["MODE"])
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
 * Updated on Jul 07, 2011
 *
 * Description: Show the search page.
 *
 * In:  $aInput
 * Out:	Table with search items.
 *
 */
function ShowSearch($aInput)
{
    // Table header
    $aHeaders = explode("|", cHeader);
    
    echo "  <div id=\"search_top\">\n";
    echo "  <table class=\"search\">\n";

    // Table header.
    echo "   <thead>\n";
    echo "    <tr>\n";
    echo "     <th class=\"msg\"></th>\n";
    echo "     <th class=\"but\"></th>\n";
    echo "     <th class=\"cat\">$aHeaders[0]</th>\n";
    echo "     <th>$aHeaders[1]</th>\n";
    echo "     <th class=\"gen\">$aHeaders[3] / $aHeaders[7]</th>\n";
    echo "     <th>$aHeaders[4]</th>\n";
    echo "    </tr>\n";
    echo "   </thead>\n";

    // Table footer.
    // Gereserveerd.

    // Table body.
    echo "   <tbody>\n";
    // The first row is the add row.
    ShowSearchAddRow($aInput);
    
    // The rest of the rows.
    ShowSearchRows($aInput);
    
    echo "   </tbody>\n";
    echo "  </table>\n";
    
    $sql = "SELECT * FROM snuffel";
    ShowResultsFooter($sql, $aInput, cItems);
    
    echo "  </div>\n";
}

/*
 * Function:	ShowSearchAddRow
 *
 * Created on May 07, 2011
 * Updated on Jul 07, 2011
 *
 * Description: Show the add input field. 
 *
 * In:  $aInput
 * Out:	input field
 *
 */
function ShowSearchAddRow($aInput)
{   
    $key = -1;   

    // Ok buttons (submit|cancel or add).
    if ($aInput["MODE"] == "ADD")
    {
        $ok = "<input type=\"image\" src=\"img/tick.png\" name=\"OK_1\"/><input type=\"image\" src=\"img/slash.png\" name=\"OK_0\"/>";
        $disabled = "";
        $aItems   = explode("|", cCategories);     
        $active   = false;  
        $message = "<input type=\"submit\" name=\"OK_1\" value=\"Add\"/>";
        $action  = null;
    }
    else 
    {
        $ok = "<img src=\"img/empty.png\"/>";
        $disabled         = " disabled";
        $aItems           = array("empty");
        $active           = true;
        $aInput["TITLE"]  = null;
        $aInput["POSTER"] = null;
        $message          = null;
        $action           = "hov";
    }
    
    // Category dropbox
    $category = DropDownBox("lstCATEGORIES", cTitle, $aItems, true, $aInput["CAT"], $active);

    // Title field. Show messsage if Ok button if pushed and the title field is empty. This field is mandatory.
    if ($aInput["OK"] == 1 && !$aInput["TITLE"]) {
        $title = "<input class=\"warning\" type=\"text\" maxlength=\"100\" name=\"txtTITLE\" value=\"".cWarning."\" onfocus=\"checkclear(this);\" />";
    }
    else {
        $title = "<input type=\"text\" maxlength=\"100\" name=\"txtTITLE\" value=\"".$aInput["TITLE"]."\"$disabled />";
    }    

    // Genre field
    if ($aInput["CAT"] && $aInput["MODE"] == "ADD")     // Check if category exists.
    {
        $key = array_search($aInput["CAT"], $aItems);
        
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
    
    $genre = DropDownBox("lstGENRE", cTitle, $aItems, true, $aInput["GENRE"], $active);
    
    // Poster field
    $poster = "<input type=\"text\" size=\"40\" maxlength=\"100\" name=\"txtPOSTER\" value=\"".$aInput["POSTER"]."\"$disabled />";

    ShowSearchRow($action, $message, $ok, $key, $category, $title, $genre, $poster);
}

/*
 * Function:	ShowSearchEditRow
 *
 * Created on May 28, 2011
 * Updated on Jun 29, 2011
 *
 * Description: Show the edit row.
 *
 * In:  $aInput, $catnr, $title, $genre, $poster
 * Out:	Edit row
 *
 */
function ShowSearchEditRow($aInput, $catnr, $title, $genre, $poster)
{   
    // Fill items array with the categories.
    $aCategories = explode("|", cCategories);  
    
    // Check if the edit button is pushed, otherwise use the database values. 
    if (!$aInput["CHECK"]) 
    {
        $catnr   = array_search($aInput["CAT"], $aCategories);
        $genre = $aInput["GENRE"];
    }    
       
    // Controleer of category bestaat.
    if (strlen($catnr)) 
    {     
        $category = $aCategories[$catnr];
        $sql = "SELECT name FROM snuftag ".
               "WHERE cat = $catnr AND hide = 0 ".
               "ORDER BY name";
        
        $aGenres = GetItemsFromDatabase($sql);  
        $active    = false;
    }
    else 
    {
        $category = null; 
        $aGenres  = array("empty");
        $active   = true;
        $catnr    = null;
    }   
 
    // The Message submit button.
    $message = "<input type=\"submit\" name=\"OK_1\" value=\"Edit\"/>";
    
    // The Submit and Cancel buttons.
    $buttons = "<input type=\"image\" src=\"img/tick.png\" name=\"OK_1\"/><input type=\"image\" src=\"img/slash.png\" name=\"OK_0\"/>";
    
    // Categorie dropbox    
    $category = DropDownBox("lstCATEGORIES", cTitle, $aCategories, true, $category);
 
    // Titel veld
    $title = "<input type=\"text\" size=\"40\" maxlength=\"100\" name=\"txtTITLE\" value=\"$title\"/>";    
    
    // Genre veld
    $genre = DropDownBox("lstGENRE", cTitle, $aGenres, true, $genre, $active);
    
    // Poster veld
    $poster = "<input type=\"text\" size=\"40\" maxlength=\"100\" name=\"txtPOSTER\" value=\"$poster\"/>";

    ShowSearchRow(null, $message, $buttons, $catnr, $category, $title, $genre, $poster);
}

/*
 * Function:	ShowSearchHiddenFields
 *
 * Created on May 16, 2011
 * Updated on Jun 29, 2011
 *
 * Description: Laat de hidden fields titel en poster zien.
 *
 * In:  $aInput
 * Out:	Hidden fields
 *
 */
function ShowSearchHiddenFields($aInput)
{
    // Title field
    echo "   <input type=\"hidden\" name=\"hidTITLE\" value=\"".$aInput["TITLE"]."\" />\n";
    
    // Poster field
    echo "   <input type=\"hidden\" name=\"hidPOSTER\" value=\"".$aInput["POSTER"]."\" />\n";    

    // Mode field
    if ($aInput["MODE"]) {
        $aInput["MODE"] .= '_';
    }
    
    echo "   <input type=\"hidden\" name=\"hidMODE\" value=\"".$aInput["MODE"].$aInput["ID"]."\" />\n";
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
    echo "     <td class=\"btn\">$buttons</td>\n";
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
 * Updated on Jun 23, 2011
 *
 * Description: Show a row which is going to be removed.
 *
 * In:  $catkey, $category, $title, $genre, $poster
 * Out:	rij
 *
 */
function ShowSearchDeleteRow($catkey, $category, $title, $genre, $poster)
{
    // The Message submit button.
    $message = "<input type=\"submit\" name=\"OK_1\" value=\"Delete\"/>"; 
    
    // The Submit and Cancel buttons.
    $buttons = "<input type=\"image\" src=\"img/tick.png\" name=\"OK_1\"/><input type=\"image\" src=\"img/slash.png\" name=\"OK_0\"/>";
    
    ShowSearchRow("del", $message, $buttons, $catkey, $category, $title, $genre, $poster);
}


/////////////////////////////////////////   Query Functions   ////////////////////////////////////////////

/*
 * Function:	ShowSearchRows
 *
 * Created on May 16, 2011
 * Updated on Jul 07, 2011
 *
 * Description: Show the search rows.
 *
 * In:  $aInput
 * Out:	Zoekrijen
 *
 */
function ShowSearchRows($aInput)
{    
    $aCategories = explode("|", cCategories);
       
    //Query the Snuffel search items.
    $sql = "SELECT f.id, f.cat, f.title, t.name, f.subcata, f.subcatd, f.poster FROM snuffel f ".
           "LEFT JOIN snuftag t ".
           "ON f.cat = t.cat AND (f.subcata = t.tag OR f.subcatd = t.tag) ".
           "ORDER BY f.title";
    $sql = AddLimit($sql, $aInput['PAGENR'], cItems);

    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            // Get number of rows.
            $stmt->store_result();
            $rows = $stmt->num_rows;

            if ($rows != 0)
            {                
                $stmt->bind_result($id, $catnr, $title, $genre, $subcata, $subcatd, $poster);
                while($stmt->fetch())
                {
                    $category = "";
                    if ($catnr !== null) {
                        $category = $aCategories[$catnr];
                    }

                    // Edit and delete buttons.
                    if ($id == $aInput["ID"]) 
                    {                       
                        switch ($aInput["MODE"])
                        {
                            case "ADD"  : $buttons = "<input type=\"image\" src=\"img/edit.png\" name=\"EDIT_$id\"/><input type=\"image\" src=\"img/del.png\" name=\"DEL_$id\"/>";
                                          ShowSearchRow("hov add", null, $buttons, $catnr, $category, $title, $genre, $poster);
                                          break;
                                
                            case "EDIT" : ShowSearchEditRow($aInput, $catnr, $title, $genre, $poster);
                                          break;
                                      
                            case "DEL"  : ShowSearchDeleteRow($catnr, $category, $title, $genre, $poster);
                                          break;
                        } 
                    }
                    else 
                    {
                        $buttons = "<input type=\"image\" src=\"img/edit.png\" name=\"EDIT_$id\"/><input type=\"image\" src=\"img/del.png\" name=\"DEL_$id\"/>";   
                        ShowSearchRow("hov", null, $buttons, $catnr, $category, $title, $genre, $poster);
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

    CloseDatabase($db); 
}

/*
 * Function:	AddSearch
 *
 * Created on May 21, 2011
 * Updated on Jun 29, 2011
 *
 * Description: Voeg zoekwaarde toe.
 *
 * In:  $aInput
 * Out:	$aInput
 *
 */
function AddSearch($aInput)
{
    if ($aInput["OK"] == 1)
    {        
        $id = null;
        list($check, $catnr, $title, $poster, $subcata, $subcatd) = CheckAndFixInput($aInput);

        // Add search values to the database.
        $sql = "INSERT INTO snuffel (cat, title, poster, subcata, subcatd) ".
               "VALUES ($catnr, $title, $poster, $subcata, $subcatd)";
        
        if ($check) 
        {   
            ExecuteQuery($sql);
        
            $sql      = "SELECT MAX(id) FROM snuffel";
            list($id) = GetItemsFromDatabase($sql);
            
            // Reset values.
            $aInput["CAT"]    = null;
            $aInput["TITLE"]  = null;
            $aInput["GENRE"]  = null;
            $aInput["POSTER"] = null;
        }
        
        $aInput["OK"]   = !$check;
        $aInput["MODE"] = "ADD";
        $aInput["ID"]   = $id;
    }
    
    if ($aInput["OK"] == 0)
    {
        // Reset values.
        $aInput["CAT"]    = null;
        $aInput["TITLE"]  = null;
        $aInput["GENRE"]  = null;
        $aInput["POSTER"] = null;         
        
        $aInput["MODE"] = "ADD";
    }
        
    return $aInput;
}

/*
 * Function:	EditSearch
 *
 * Created on May 28, 2011
 * Updated on Jun 29, 2011
 *
 * Description: Edit search values.
 *
 * In:  $aInput
 * Out:	$aInput
 *
 */
function EditSearch($aInput)
{
    if ($aInput["OK"] == 1)
    {
        list($check, $catnr, $title, $poster, $subcata, $subcatd) = CheckAndFixInput($aInput);

        // Update search values in the database.
        $sql = "UPDATE snuffel ".
               "SET cat = $catnr, title = $title, poster = $poster,  subcata = $subcata, subcatd = $subcatd ".
               "WHERE id = ".$aInput["ID"];
        
        if ($check) {
            ExecuteQuery($sql);
        }
        
        // Reset values.
        $aInput["CAT"]    = null;
        $aInput["TITLE"]  = null;
        $aInput["GENRE"]  = null;
        $aInput["POSTER"] = null;
        
        $aInput["OK"]   = -1;
        $aInput["MODE"] = "ADD";
        $aInput["ID"]   = $aInput["ID"];      
        
    }
    
    if ($aInput["OK"] == 0)
    {   
        // Reset values.
        $aInput["CAT"]    = null;
        $aInput["TITLE"]  = null;
        $aInput["GENRE"]  = null;
        $aInput["POSTER"] = null;
        
        $aInput["MODE"] = "ADD";
        $aInput["ID"]   = 0;
    }
        
    return $aInput;
}

/*
 * Function:	DelSearch
 *
 * Created on May 23, 2011
 * Updated on Jun 29, 2011
 *
 * Description: Verwijder zoekwaarde.
 *
 * In:  $aInput
 * Out:	$aInput
 *
 */
function DelSearch($aInput)
{    
    if ($aInput["OK"] == 1)
    {
        $sql = "DELETE FROM snuffel WHERE id = ".$aInput["ID"];
        ExecuteQuery($sql);    
    }
    
    if ($aInput["OK"]!= -1)
    {        
        // Reset values.
        $aInput["CAT"]    = null;
        $aInput["TITLE"]  = null;
        $aInput["GENRE"]  = null;
        $aInput["POSTER"] = null;
        
        $aInput["OK"]   = -1;        
        $aInput["MODE"] = "ADD";
        $aInput["ID"]   = 0;        
    }
    
    return $aInput;    
}

/*
 * Function:	CheckAndFixInput
 *
 * Created on May 28, 2011
 * Updated on Jul 04, 2011
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
    
    $subcata = "NULL";
    $subcatd = "NULL";   
   
    $aCategories = explode("|", cCategories);
    $catnr = array_search($aInput["CAT"], $aCategories);
    if ($catnr === false) {
        $catnr = "NULL";
    }
    else 
    {    
        // Get the correct subcategory.
        $sql = "SELECT tag ".
               "FROM snuftag ".
               "WHERE cat = '$catnr' AND name = '".$aInput["GENRE"]."'";
        list($tag) = GetItemsFromDatabase($sql);
        
        if ($tag)
        {
            if ($catnr < 2) {     
                $subcatd = "'$tag'";
            }
            else {
                $subcata = "'$tag'";
            }
        }
    }
    
    if ($aInput["TITLE"] && $aInput["TITLE"] != cWarning) 
    {
        $aInput["TITLE"] = "'".$aInput["TITLE"]."'";
        $check = true;
    }
    else {
        $aInput["TITLE"] = null;
    }    
    
    if ($aInput["POSTER"]) {
        $aInput["POSTER"] = "'".$aInput["POSTER"]."'";
    }
    else {
        $aInput["POSTER"] = "NULL";
    }
    
    return array($check, $catnr, $aInput["TITLE"], $aInput["POSTER"], $subcata, $subcatd);
}
?>
