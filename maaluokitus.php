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

$col_arr = array("nimi","maanosatnimi","maanosatkoodi","maanosat2koodi","alkupvm","maanosat2nimi","maanosat3koodi","maanosat3nimi_sv","loppupvm","iso2koodi","koodi","maanosat2nimi_sv","maanosatnimi_en","maanosat3nimi","maanosat3nimi_en","nimi_en","nimi_sv","maanosat2nimi_en","maanosatnimi_sv");
if ($type == "csv") {
  foreach ($col_arr as $col) {
    echo $col.";";
  }
  echo "\n";
}

// tsv?
//json:
$return_arr = array();
$redarr = $redis->lrange("maatjavaltiot2",0,-1);
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
