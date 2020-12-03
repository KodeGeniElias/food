<?php


//DETTE ER 3: personalized - ranked low to high in terms of salt

// //save new column
// $ncolumn = 0;

// //kolonnen med kalorier er linje 17 i filen
// if(lcal !=false){
//     $ncolumnm = 15;
// }

// if(hcal !=false){
//     $ncolumnm = 15;
// }



// //kolonnen med fett er linje 16 i filen
// if(lfat !=false){
//     $ncolumnm = 14;
// }

// if(hfat !=false){
//     $ncolumnm = 14;
// }



//echo $_SERVER["DOCUMENT_ROOT"];
//console.log("hallo");

set_include_path(dirname(__FILE__));
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace( 'Zend_' );
ini_set('display_errors', 1);
error_reporting(E_ALL);

/*$frontendOptions = array(
    'lifetime' => 3000, // Lebensdauer des Caches 2 Stunden
    'automatic_serialization' => true
);

$backendOptions = array(
    'cache_dir' => $_SERVER['DOCUMENT_ROOT'].'/food/tmp/' // Verzeichnis, in welches die Cache Dateien kommen
);

// Ein Zend_Cache_Core Objekt erzeugen
$cache = Zend_Cache::factory('Core','File',$frontendOptions,$backendOptions);
*/
session_start();

if (isset($_GET['selection'])) {
    $selection=$_GET['selection'];
} else {
    if (isset($_SESSION['selection'])) {
        $selection=$_SESSION['selection'];
        $_SESSION['selection']=$selection;
    } else {
        $selection="hobo";
    };
}

switch ($selection) {
    case "lcal":
        $num = 1;
        break;
    case "hcal":
        $num = 2;
        break;
    case "lfat":
        $num = 3;
        break;
    case "hfat":
        $num = 4;
        break;
    case "hobo":
        $num = 5;
}

function sortBySodium($a, $b) {     //Sorts by sodium - low to high
    return $a['sodium'] - $b['sodium'];
}


//LEGG TIL EN IF-SETNING SOM SJEKKER OM DE VIL SORTERE BY LOW TO HIGH ELLER HIGH TO LOW BASERT PÅ SELECTION NÅR DET FUNKER
function sortBySelection($c, $d) {  //Sorts by users selection - low to high
    if ($_SESSION['selection'] == "hcal" || $_SESSION['selection'] == "hfat")  {
        return $d['fsa'] - $c['fsa'];
    }
    else {
        return $c['fsa'] - $d['fsa'];
    } 
}






function deleteDir($dirPath) {
    if (!is_dir($dirPath)) {
        //    throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}


$index_file = $_SERVER["DOCUMENT_ROOT"].'/Food/index3';
//$index_file = $_SERVER["DOCUMENT_ROOT"].'/d828113e/Food/index3';
//console.log($_SERVER["DOCUMENT_ROOT"]);

$index = null;

deleteDir($index_file); //for debug purposes leave that line in

if (!file_exists($index_file)) {
    $index = Zend_Search_Lucene::create($index_file);

    $data = null;

    $file = fopen($_SERVER['DOCUMENT_ROOT'].'/Food/visual_data.csv', 'r');
    //$file = fopen($_SERVER['DOCUMENT_ROOT'].'/d828113e/Food/visual_data.csv', 'r');

    while (($line = fgetcsv($file,0, "\t")) !== FALSE) {
        //$line is an array of the csv elements
        //  array_push($_SESSION['data'],$line);

        $dat = array();
        for ($i=1; $i < count($line);$i++) {
            array_push($dat,$line[$i]);
        }
        $data[$line[0]] = $dat;
    }
    fclose($file);

    setlocale(LC_ALL, 'en_GB');


    if ($data != null) {

        foreach ($data as $key => $value) {

            $r_title = $value[0];
            $r_image = $value[11];
           // echo $r_image;
           // echo strlen($r_image)."<br>";
           // echo substr($r_image,strripos($r_image,"/"));

           //$r_image = "imageselection/{$r_image}.jpg")
          // $r_image = "images/thumbnail.".substr($r_image,strripos($r_image,"/")+1);   //als je afbeeldingen toevoegt moet je die denk ik in de thumbnail map gooien
            $r_image = "images/".substr($r_image,strripos($r_image,"/")+1);   //TEST REGEL - VERWIJDEREN

            //HER KOMMER IF-SETNINGEN SOM SKAL BASERES PÅ OM DE VELGER HCAL/LCAL/LFAT/HFAT OSV
            switch ($num) {
                case 1:
                    $r_fsa = $value[15]; //CAL
                    break;
                case 2:
                    $r_fsa = $value[15]; //CAL
                    break;
                case 3:
                    $r_fsa = $value[14]; //FAT
                    break;
                case 4:
                    $r_fsa = $value[14]; //FAT
                    break;
                case 5:
                    $r_fsa = $value[4];
                    break;
            }

            $r_sodium = $value[4]; //DETTE ER VARIABELEN FOR Å SORTERE PÅ SALT

              //add a column of FSA scores to the csv document (keep it in tab-separated format to be sure)
                                    //don't know why but the value 6 does not work. The value 2 does work...
            //echo $key." ".$r_title."<br>";

            /*
            $document = new Zend_Search_Lucene_Document();
            $document->addField(Zend_Search_Lucene_Field::Text('title',  iconv("UTF-8", "ASCII//TRANSLIT", $r_title)));
            $document->addField(Zend_Search_Lucene_Field::Text('dir', iconv("UTF-8", "ASCII//TRANSLIT", $r_dir)));
            $document->addField(Zend_Search_Lucene_Field::Text('ing', iconv("UTF-8", "ASCII//TRANSLIT",  $r_ing)));
          // $document->addField(Zend_Search_Lucene_Field::Text('ID', iconv("UTF-8", "ASCII//TRANSLIT", "hel")));
            $document->addField(Zend_Search_Lucene_Field::Text('img', iconv("UTF-8", "ASCII//TRANSLIT", $r_image)));
            $document->addField(Zend_Search_Lucene_Field::Text('fsa', iconv("UTF-8", "ASCII//TRANSLIT", $r_fsa))); //This line is new; you could also do this with the WHO score
            */
            $document = new Zend_Search_Lucene_Document();
            $document->addField(Zend_Search_Lucene_Field::Text('title', $r_title));
          // $document->addField(Zend_Search_Lucene_Field::Text('ID', iconv("UTF-8", "ASCII//TRANSLIT", "hel")));
            $document->addField(Zend_Search_Lucene_Field::Text('img', $r_image));
            $document->addField(Zend_Search_Lucene_Field::Text('fsa', $r_fsa)); //This line is new; you could also do this with the WHO score
            $document->addField(Zend_Search_Lucene_Field::Text('sodium', $r_sodium));

            $index->addDocument($document);
         //iconv converts --> maybe there is a better php function
        }

    }

} else {
    $index = Zend_Search_Lucene::open($index_file);
}




$query = (!empty($_GET['q'])) ? strtolower($_GET['q']) : null;

if (!isset($query)) {
    die('Invalid query.');
}

$status = true;

$databaseUsers = null;

$queryString = urldecode($query);
// echo $queryString;
//$queryString = "chicken";

try {
    $query_ = Zend_Search_Lucene_Search_QueryParser::parse($queryString."*"); //without the star it would be exact  (the . is an append) --> you could bugfix this if you want search queries to be exactly fitting
} catch (Zend_Search_Lucene_Search_QueryParserException $e) {
    echo "Abfrage Syntax Fehler: " . $e->getMessage() . "\n";
}

// $term  = new Zend_Search_Lucene_Index_Term($queryString."*", 'title');
// $query = new Zend_Search_Lucene_Search_Query_Wildcard($term);
$hits  = $index->find($query_);
$counter = 0;
foreach ($hits as $hit) {
    $counter++;
    $databaseUsers[] = array(
        'recipe' => $hit->title,
        'image' => $hit->img, //The comma is new
        'fsa' => $hit->fsa,  //This line is new
        'sodium' => $hit->sodium
    );
    // echo $hit->score;
    // echo $hit->title;
    // echo $hit->ID;
    // $array[] = $hit->title;


    //if ($counter == 10)     //LIMITS THE NUMBER OF SEARCH RESULTS - if you comment this out, you get all the results.
       // break;

}


//usort($databaseUsers[8], 'sortBySodium'); //BARE SORTER DE 8 FØRSTE, FINN EN MÅTE FOR DET (BRUK SPLIT)
//foreach ($databaseUsers


//print_r($databaseUsers);

//BELOW HERE (163-170) Christoph told me YOU CAN RE-RANK THE SEARCH RESULTS  --> re-sort the databaseUsers array by FSA
//However i did some googling and am going to write a function that might order it all




/*$resultUsers = [];
foreach ($databaseUsers as $key => $oneUser) {
    if (strpos(strtolower($oneUser["recipe"]), $query) !== false ||
        strpos(str_replace('-', '', strtolower($oneUser["recipe"])), $query) !== false ||
        strpos(strtolower($oneUser["id"]), $query) !== false) {
            $resultUsers[] = $oneUser;
        }
}*/


usort($databaseUsers, 'sortBySelection');
// Means no result were found
if (empty($databaseUsers) ) {
    $status = false;
}
$splitRecipes = array_slice($databaseUsers, 0, 8);
usort($splitRecipes, 'sortBySodium');

header('Content-Type: application/json');

echo json_encode(array(
    "status" => $status,
    "error"  => null,
    "data"   => array(
        "recipes"      => $splitRecipes//,
        // "project"   => $resultProjects
    )
));









?>
