<?php
if ($_GET) {
  $type = $_GET['type'];
} else {
  $type = "json"; // oletus
}

if ($type=="json") {
  header('Content-Type: application/json; charset=utf-8');
} elseif ($type=="tsv") {
  //header('Content-Type: text/tab-separated-values; charset=utf-8');
  header('Content-Type: text/plain; charset=iso-8859-1');
} else {
  //header('Content-Type: text/csv; charset=utf-8');
  header('Content-Type: text/plain; charset=iso-8859-1');
}

header("Access-Control-Allow-Origin: *");

require "predis/autoload.php";
Predis\Autoloader::register();

try {
  $redis = new Predis\Client(array(
    "scheme" => "tcp",
    "host" => "localhost",
    "port" => 6379
  ));
}
catch (Exception $e) {
  die($e->getMessage());
}

$col_arr = array("koodi","nimi","nimi_sv","nimi_en","alkupvm","loppupvm","maakuntakoodi","maakuntanimi","maakuntanimi_sv","maakuntanimi_en","avikoodi","avinimi","avinimi_sv","avinimi_en","elykoodi","elynimi","elynimi_sv","elynimi_en","kielisuhdekoodi","kielisuhdenimi","kielisuhdenimi_sv","kielisuhdenimi_en","seutukuntakoodi","seutukuntanimi","seutukuntanimi_sv","seutukuntanimi_en","laanikoodi","laaninimi","laaninimi_sv","laaninimi_en","kuntaryhmakoodi","kuntaryhmanimi","kuntaryhmanimi_sv","kuntaryhmanimi_en");
if ($type == "csv") {
  foreach ($col_arr as $col) {
    echo $col.";";
  }
  echo "\n";
}

// tsv?
//json:
$return_arr = array();
$redarr = $redis->lrange("kunta",0,-1);
foreach ($redarr as $row) {
  if ($type == "csv" || $type == "tsv") {
    $obj = json_decode($row);
    foreach ($col_arr as $col) {
      echo "\"".utf8_decode($obj->$col)."\"";
      if ($type == "csv") {
        echo ";";
      } elseif ($type == "tsv") {
        echo "\t";
      }
    }
    echo "\n";
  }
  if ($type == "json") {
    array_push($return_arr,json_decode($row));
  }
}
if ($type == "json") {
  echo json_encode($return_arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
?>
