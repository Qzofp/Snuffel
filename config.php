<?php
/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    config.php
 *
 * Created on Apr 09, 2011
 * Updated on Jun 13, 2011
 *
 * Description: This page containts the check and configuration functions. 
 * 
 * Credits: Spotweb team.
 *
 */

/////////////////////////////////////////     Config Main     ////////////////////////////////////////////

// Check if spotweb with MySQL exitst.
$aChecks = CheckForSpotweb();
if ($aChecks[0])
{
    // Create the Snuffel tables (first time only).
    CreateSnuffelTables();

    // Fill the snuffel constants from the config database.
    LoadConstants();
}
else {
    // ShowMessage($aChecks)       
    exit();
}

/////////////////////////////////////////   Process Functions    /////////////////////////////////////////

/*
 * Function:	CreateSnuffelTables
 *
 * Created on Jun 13, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Create or update the Snuffel tables.
 *
 * In:	-
 * Out:	Snuffel tables
 *
 */
function CreateSnuffelTables()
{
    // Check if the Snuffel tables exists.
    $version = GetSnuffelVersion();
    if ($version != 0.1) 
    {
        CreateSnufCnf();
        CreateSnuffel();
        CreateSnufTmp();
        CreateSnufCat();
        CreateSnufTag();
    }
}

/*
 * Function:	CheckForSpotweb
 *
 * Created on Aug 14, 2010
 * Updated on Apr 16, 2011
 *
 * Description: Check if Spotweb with MySQL exists.
 *
 * In:	-
 * Out:	$check
 *
 */
function CheckForSpotweb()
{
    // If Spotweb is in another location, please change the value below.
    $spotweb = "../spotweb";

    // Check for: All, Spotweb, Own Settings, MySQL, MySQL connection
    $aChecks = array(false, false, false, false, false);
    
    // Check if the spotweb folder exists.
    if (file_exists($spotweb)) 
    {
        $aChecks[1] = true;
    
        // Check if ownsettings exits.     
        if (file_exists($spotweb."/ownsettings.php")) 
        { 
            include_once($spotweb."/ownsettings.php"); 
            $aChecks[2] = true;
        
            // Check if MySQL is used.
            if ($settings['db']['engine'] == "mysql")
            {
                // Define database constants from ownsettings.
                define("cHOST",  $settings['db']['host']);
                define("cDBASE", $settings['db']['dbname']);
                define("cUSER",  $settings['db']['user']);
                define("cPASS",  $settings['db']['pass']);   

                $aChecks[3] = true;
            
                // Check is a MySQL connection can be made.
                if (ConnectToSpots()) {
                    $aChecks[0] = true;               
                }
            }
        }
    }    
   
    return $aChecks;
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


/////////////////////////////////////////   Query Functions   ////////////////////////////////////////////

/*
 * Function:	GetSnuffelVersion
 *
 * Created on Jun 13, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Return the Snuffel version or false if the Snuffel tables doesn't exist.
 *
 * In:	-
 * Out:	$version
 *
 */
function GetSnuffelVersion()
{
    $db = OpenDatabase();
    
    $version = false;
    $sql = "SELECT value FROM snufcnf ".
           "WHERE name = 'Version'";
  
    if(mysqli_multi_query($db, $sql))
    {
        $result = mysqli_use_result($db);
        if ($result) 
        {
            $row = mysqli_fetch_row($result); 
            $version = $row[0];
            mysqli_free_result($result);
        }
    }
 
    CloseDatabase($db); 
        
    return $version;
}

/*
 * Function:	CreateSnufCnf
 *
 * Created on Jun 13, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Create or update the Snuffel Configuration table.
 *
 * In:	-
 * Out:	snufcnf table
 *
 */
function CreateSnufCnf()
{
    $db = OpenDatabase();

    // If exists drop table.
    $sql = "DROP TABLE IF EXISTS `snufcnf2`";
    
    mysqli_query($db, $sql);
    
    // Create table.
    $sql = "CREATE TABLE IF NOT EXISTS `snufcnf2` ( ".
             "`id` int(11) NOT NULL AUTO_INCREMENT, ".
             "`name` varchar(64) COLLATE utf8_unicode_ci NOT NULL, ".
             "`value` varchar(128) COLLATE utf8_unicode_ci NOT NULL, ".
             "PRIMARY KEY (`id`) ".
           ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";
    
    mysqli_query($db, $sql);
    
    // Fill table.
    $sql = "INSERT INTO `snufcnf2` (`name`, `value`) VALUES ".
           "('Title', 'Snuffel'), ".
           "('Header', 'Cat.|Titel|Genre|Afzender|Datum|NZB|Platform'), ".
           "('MenuText', 'Onderhoud|Laatste update:'), ".
           "('Buttons1', 'Nieuw|Alles|Zoek'), ".
           "('Buttons2', 'Update Snuffel|Verwijder Zoek'), ".
           "('Categories', 'Beeld|Muziek|Spellen|Applicaties'), ".
           "('Days', '14'), ".
           "('TimeValues', 'seconde|seconden|minuut|minuten|uur|uur|dag|dagen|week|weken|maand|maanden|jaar|jaar'), ".
           "('NZBlink', 'http://localhost/spotweb/?page=getnzb&messageid='), ".
           "('Version', '0.1');";
    
    mysqli_query($db, $sql);    
    
    CloseDatabase($db);     
}

/*
 * Function:	CreateSnuffel
 *
 * Created on Jun 13, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Create or update the Snuffel  table.
 *
 * In:	-
 * Out:	snuffel table
 *
 */
function CreateSnuffel()
{
    $db = OpenDatabase();

    // If exists drop table.
    #$sql = "DROP TABLE IF EXISTS `snuffel2`";
    
    #mysqli_query($db, $sql);
    
    // Create table.
    $sql = "CREATE TABLE IF NOT EXISTS `snuffel2` ( ".
             "`id` int(11) NOT NULL AUTO_INCREMENT, ".
             "`poster` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL, ".
             "`title` varchar(128) COLLATE utf8_unicode_ci NOT NULL, ".
             "`cat` int(11) DEFAULT NULL, ".
             "`subcata` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL, ".
             "`subcatd` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL, ".
             "PRIMARY KEY (`id`) ".
           ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
    
    mysqli_query($db, $sql);    

    CloseDatabase($db); 
}

/*
 * Function:	CreateSnufTmp
 *
 * Created on Jun 13, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Create or update the Snuffel temporary (cache) table.
 *
 * In:	-
 * Out:	snuftmp table
 *
 */
function CreateSnufTmp()
{
    $db = OpenDatabase();

    // If exists drop table.
    $sql = "DROP TABLE IF EXISTS `snuftmp2`";
    
    mysqli_query($db, $sql);
    
    // Create table.
    $sql = "CREATE TABLE IF NOT EXISTS `snuftmp2` ( ".
             "`id` int(11) NOT NULL AUTO_INCREMENT, ".
             "`messageid` varchar(128) CHARACTER SET ascii NOT NULL DEFAULT '', ".
             "`poster` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL, ".
             "`title` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL, ".
             "`tag` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL, ".
             "`category` int(11) DEFAULT NULL, ".
             "`subcata` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL, ".
             "`subcatb` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL, ".
             "`subcatc` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL, ".
             "`subcatd` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL, ".
             "`subcatz` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL, ".
             "`stamp` int(10) unsigned DEFAULT NULL, ".
             "`reversestamp` int(11) DEFAULT '0', ".
             "`filesize` bigint(20) unsigned NOT NULL DEFAULT '0', ".
             "`moderated` tinyint(1) DEFAULT NULL, ".
             "`commentcount` int(11) DEFAULT '0', ".
             "`spotrating` int(11) DEFAULT '0', ".
             "PRIMARY KEY (`id`), ".
             "UNIQUE KEY `idx_spots_1` (`messageid`), ".
             "KEY `idx_spots_2` (`stamp`), ".
             "KEY `idx_spots_3` (`reversestamp`), ".
             "KEY `idx_spots_4` (`category`,`subcata`,`subcatb`,`subcatc`,`subcatd`,`subcatz`), ".
             "FULLTEXT KEY `idx_fts_spots_1` (`poster`), ".
             "FULLTEXT KEY `idx_fts_spots_2` (`title`), ".
             "FULLTEXT KEY `idx_fts_spots_3` (`tag`) ".
           ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
    
    mysqli_query($db, $sql);    

    CloseDatabase($db); 
}

/*
 * Function:	CreateSnufCat
 *
 * Created on Jun 13, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Create or update the Snuffel Category table.
 *
 * In:	-
 * Out:	snufcat table
 *
 */
function CreateSnufCat()
{
    $db = OpenDatabase();

    // If exists drop table.
    $sql = "DROP TABLE IF EXISTS `snufcat2`";
    
    mysqli_query($db, $sql);
    
    // Create table.
    $sql = "CREATE TABLE IF NOT EXISTS `snufcat2` ( ".
             "`id` int(11) NOT NULL AUTO_INCREMENT, ".
             "`cat` int(11) NOT NULL, ".
             "`name` varchar(64) COLLATE utf8_unicode_ci NOT NULL, ".
             "`tag` varchar(64) COLLATE utf8_unicode_ci NOT NULL, ".
             "PRIMARY KEY (`id`) ".
           ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
    
    mysqli_query($db, $sql);
    
    // Fill table.
    $sql = "INSERT INTO `snufcat2` (`cat`, `name`, `tag`) VALUES ".
           "(0, 'DivX', 'a0'), ".
           "(0, 'WMV', 'a1'), ".
           "(0, 'MPG', 'a2'), ".
           "(0, 'DVD5', 'a3'), ".
           "(0, 'HD ovg', 'a4'), ".
           "(0, 'ePub', 'a5'), ".
           "(0, 'Blu-ray', 'a6'), ".
           "(0, 'HD-DVD', 'a7'), ".
           "(0, 'WMVHD', 'a8'), ".
           "(0, 'x264HD', 'a9'), ".
           "(0, 'DVD9', 'a10'), ".
           "(1, 'MP3', 'a0'), ".
           "(1, 'WMA', 'a1'), ".
           "(1, 'WAV', 'a2'), ".
           "(1, 'OGG', 'a3'), ".
           "(1, 'EAC', 'a4'), ".
           "(1, 'DTS', 'a5'), ".
           "(1, 'AAC', 'a6'), ".
           "(1, 'APE', 'a7'), ".
           "(1, 'FLAC', 'a8'), ".
           "(2, 'WIN', 'a0'), ".
           "(2, 'MAC', 'a1'), ".
           "(2, 'TUX', 'a2'), ".
           "(2, 'PS', 'a3'), ".
           "(2, 'PS2', 'a4'), ".
           "(2, 'PSP', 'a5'), ".
           "(2, 'XBX', 'a6'), ".
           "(2, '360', 'a7'), ".
           "(2, 'GBA', 'a8'), ".
           "(2, 'GC', 'a9'), ".
           "(2, 'NDS', 'a10'), ".
           "(2, 'Wii', 'a11'), ".
           "(2, 'PS3', 'a12'), ".
           "(2, 'WinPh', 'a13'), ".
           "(2, 'iOS', 'a14'), ".
           "(2, 'Android', 'a15'), ".
           "(2, '3DS', 'a16'), ".
           "(3, 'WIN', 'a0'), ".
           "(3, 'MAC', 'a1'), ".
           "(3, 'TUX', 'a2'), ".
           "(3, 'OS/2', 'a3'), ".
           "(3, 'WinPh', 'a4'), ".
           "(3, 'NAV', 'a5'), ".
           "(3, 'iOS', 'a6'), ".
           "(3, 'Android', 'a7');";
    
    mysqli_query($db, $sql);    
    
    CloseDatabase($db);     
}

/*
 * Function:	CreateSnufTag
 *
 * Created on Jun 13, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Create or update the Snuffel Tag table.
 *
 * In:	-
 * Out:	snuftag table
 *
 */
function CreateSnufTag()
{
    $db = OpenDatabase();

    // If exists drop table.
    $sql = "DROP TABLE IF EXISTS `snuftag2`";
    
    mysqli_query($db, $sql);
    
    // Create table.
    $sql = "CREATE TABLE IF NOT EXISTS `snuftag2` ( ".
             "`id` int(11) NOT NULL AUTO_INCREMENT, ".
             "`cat` int(11) NOT NULL, ".
             "`name` varchar(64) COLLATE utf8_unicode_ci NOT NULL, ".
             "`tag` varchar(64) COLLATE utf8_unicode_ci NOT NULL, ".
             "`hide` tinyint(1) DEFAULT '0', ".
             "PRIMARY KEY (`id`) ".
           ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
    
    mysqli_query($db, $sql);
    
    // Fill table.
    $sql = "INSERT INTO `snuftag2` (`cat`, `name`, `tag`, `hide`) VALUES ".
           "(0, 'Actie', 'd0', 0), ".
           "(0, 'Avontuur', 'd1', 0), ". 
           "(0, 'Animatie', 'd2', 0), ".
           "(0, 'Cabaret', 'd3', 1), ".
           "(0, 'Komedie', 'd4', 0), ".
           "(0, 'Misdaad', 'd5', 1), ".
           "(0, 'Documentaire', 'd6', 0), ".
           "(0, 'Drama', 'd7', 0), ".
           "(0, 'Familie', 'd8', 1), ".
           "(0, 'Fantasie', 'd9', 0), ".
           "(0, 'Filmhuis', 'd10', 1), ".
           "(0, 'Televisie', 'd11', 0), ".
           "(0, 'Horror', 'd12', 0), ".
           "(0, 'Muziek', 'd13', 1), ".
           "(0, 'Musical', 'd14', 1), ".
           "(0, 'Mysterie', 'd15', 1), ".
           "(0, 'Romantiek', 'd16', 1), ".
           "(0, 'Science Fiction', 'd17', 0), ".
           "(0, 'Sport', 'd18', 1), ".
           "(0, 'Korte film', 'd19', 1), ".
           "(0, 'Thriller', 'd20', 0), ".
           "(0, 'Oorlog', 'd21', 1), ".
           "(0, 'Western', 'd22', 0), ".
           "(0, 'Erotiek (hetero)', 'd23', 0), ".
           "(0, 'Erotiek (gay mannen)', 'd24', 1), ".
           "(0, 'Erotiek (gay vrouwen)', 'd25', 1), ".
           "(0, 'Erotiek (bi)', 'd26', 1), ".
           "(0, 'Onbekend', 'd27', 1), ".
           "(0, 'Asian', 'd28', 0), ".
           "(0, 'Anime', 'd29', 0), ".
           "(0, 'Cover', 'd30', 1), ".
           "(0, 'Stripboek', 'd31', 1), ".
           "(0, 'Studie', 'd32', 1), ".
           "(0, 'Zakelijk', 'd33', 1), ".
           "(0, 'Economie', 'd34', 1), ".
           "(0, 'Computer', 'd35', 1), ".
           "(0, 'Hobby', 'd36', 1), ".
           "(0, 'Koken', 'd37', 1), ".
           "(0, 'Knutselen', 'd38', 1), ".
           "(0, 'Handwerk', 'd39', 1), ".
           "(0, 'Gezondheid', 'd40', 1), ".
           "(0, 'Historie', 'd41', 1), ".
           "(0, 'Psychologie', 'd42', 1), ".
           "(0, 'Dagblad', 'd43', 1), ".
           "(0, 'Tijdschrift', 'd44', 1), ".
           "(0, 'Wetenschap', 'd45', 1), ".
           "(0, 'Vrouw', 'd46', 1), ".
           "(0, 'Religie', 'd47', 1), ".
           "(0, 'Roman', 'd48', 1), ".
           "(0, 'Biografie', 'd49', 1), ".
           "(0, 'Detective', 'd50', 1), ".
           "(0, 'Dieren', 'd51', 1), ".
           "(0, 'Humor', 'd52', 1), ".
           "(0, 'Reizen', 'd53', 1), ".
           "(0, 'Waargebeurd', 'd54', 1), ".
           "(0, 'Non-fictie', 'd55', 1), ".
           "(0, 'Politiek', 'd56', 1), ".
           "(0, 'Poezie', 'd57', 1), ".
           "(0, 'Sprookje', 'd58', 1), ".
           "(0, 'Techniek', 'd59', 1), ".
           "(0, 'Kunst', 'd60', 1), ".
           "(0, 'Onbekend', 'd61', 1), ".
           "(0, 'Onbekend', 'd62', 1), ".
           "(0, 'Onbekend', 'd63', 1), ".
           "(0, 'Onbekend', 'd64', 1), ".
           "(0, 'Onbekend', 'd65', 1), ".
           "(0, 'Onbekend', 'd66', 1), ".
           "(0, 'Onbekend', 'd67', 1), ".
           "(0, 'Onbekend', 'd68', 1), ".
           "(0, 'Onbekend', 'd69', 1), ".
           "(0, 'Onbekend', 'd70', 1), ".
           "(0, 'Onbekend', 'd71', 1), ".
           "(0, 'Bi', 'd72', 1), ".
           "(0, 'Lesbo', 'd73', 1), ".
           "(0, 'Homo', 'd74', 1), ".
           "(0, 'Hetero', 'd75', 1), ".
           "(0, 'Amateur', 'd76', 1), ".
           "(0, 'Groep', 'd77', 1), ".
           "(0, 'POV', 'd78', 1), ".
           "(0, 'Solo', 'd79', 1), ".
           "(0, 'Jong', 'd80', 1), ".
           "(0, 'Soft', 'd81', 1), ".
           "(0, 'Fetisj', 'd82', 1), ".
           "(0, 'Oud', 'd83', 1), ".
           "(0, 'Dik', 'd84', 1), ".
           "(0, 'SM', 'd85', 1), ".
           "(0, 'Ruig', 'd86', 1), ".
           "(0, 'Donker', 'd87', 1), ".
           "(0, 'Hentai', 'd88', 1), ".
           "(0, 'Buiten', 'd89', 1), ".
           "(1, 'Blues', 'd0', 0), ".
           "(1, 'Compilatie', 'd1', 0), ".
           "(1, 'Cabaret', 'd2', 0), ".
           "(1, 'Dance', 'd3', 0), ".
           "(1, 'Diversen', 'd4', 0), ".
           "(1, 'Hardcore', 'd5', 0), ".
           "(1, 'Wereld', 'd6', 0), ".
           "(1, 'Jazz', 'd7', 0), ".
           "(1, 'Jeugd', 'd8', 1), ".
           "(1, 'Klassiek', 'd9', 0), ".
           "(1, 'Kleinkunst', 'd10', 1), ".
           "(1, 'Hollands', 'd11', 0), ".
           "(1, 'New Age', 'd12', 0), ".
           "(1, 'Pop', 'd13', 0), ".
           "(1, 'RnB', 'd14', 0), ".
           "(1, 'Hiphop', 'd15', 0), ".
           "(1, 'Reggae', 'd16', 0), ".
           "(1, 'Religieus', 'd17', 1), ".
           "(1, 'Rock', 'd18', 0), ".
           "(1, 'Soundtracks', 'd19', 0), ".
           "(1, 'Onbekend', 'd20', 1), ".
           "(1, 'Hardstyle', 'd21', 1), ".
           "(1, 'Asian', 'd22', 0), ".
           "(1, 'Disco', 'd23', 0), ".
           "(1, 'Classics', 'd24', 0), ".
           "(1, 'Metal', 'd25', 0), ".
           "(1, 'Country', 'd26', 0), ".
           "(1, 'Dubstep', 'd27', 1), ".
           "(1, 'Nederhop', 'd28', 1), ".
           "(1, 'DnB', 'd29', 1), ".
           "(1, 'Electro', 'd30', 1), ".
           "(1, 'Folk', 'd31', 1), ".
           "(1, 'Soul', 'd32', 1), ".
           "(1, 'Trance', 'd33', 1), ".
           "(1, 'Balkan', 'd34', 1), ".
           "(1, 'Techno', 'd35', 1), ".
           "(1, 'Ambient', 'd36', 1), ".
           "(1, 'Latin', 'd37', 1), ".
           "(1, 'Live', 'd38', 1), ".
           "(2, 'Windows', 'a0', 0), ".
           "(2, 'Macintosh', 'a1', 0), ".
           "(2, 'Linux', 'a2', 0), ".
           "(2, 'Playstation', 'a3', 0), ".
           "(2, 'Playstation 2', 'a4', 0), ".
           "(2, 'PSP', 'a5', 0), ".
           "(2, 'Xbox', 'a6', 0), ".
           "(2, 'Xbox 360', 'a7', 0), ".
           "(2, 'Gameboy Advance', 'a8', 0), ".
           "(2, 'Gamecube', 'a9', 0), ".
           "(2, 'Nintendo DS', 'a10', 0), ".
           "(2, 'Nintento Wii', 'a11', 0), ".
           "(2, 'Playstation 3', 'a12', 0), ".
           "(2, 'Windows Phone', 'a13', 0), ".
           "(2, 'iOS', 'a14', 0), ".
           "(2, 'Android', 'a15', 0), ".
           "(2, 'Nintendo 3DS', 'a16', 0), ".
           "(3, 'Windows', 'a0', 0), ".
           "(3, 'Macintosh', 'a1', 0), ".
           "(3, 'Linux', 'a2', 0), ".
           "(3, 'OS/2', 'a3', 0), ".
           "(3, 'Windows Phone', 'a4', 0), ".
           "(3, 'Navigatiesystemen', 'a5', 0), ".
           "(3, 'iOS', 'a6', 0), ".
           "(3, 'Android', 'a7', 0);";
    
    mysqli_query($db, $sql);    
    
    CloseDatabase($db);     
}


/*
 * Function:	ConnectToSpots
 *
 * Created on Jun 13, 2011
 * Updated on Jun 13, 2011
 *
 * Description: Check if a connection to the spotweb database can be made.
 *
 * In:	-
 * Out:	$check
 *
 */
function ConnectToSpots()
{
    $check = false;
    
    // Check if a connection can be made.
    $db = @mysqli_connect(cHOST, cUSER, cPASS, cDBASE);
    if ($db) 
    {   
        // Check if a query can be made.   
        $sql = "SELECT count(*) FROM spotstatelist";

        if (mysqli_query($db, $sql)) {
            $check = true;
        }
    
        mysqli_close($db);
    }
    
    return $check;
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
?>
