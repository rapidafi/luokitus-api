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

$col_arr = array("koodi","nimi","nimi_sv","nimi_en","alkupvm","loppupvm","koulutusaste2002koodi","koulutusaste2002nimi","koulutusaste2002nimi_sv","koulutusaste2002nimi_en","koulutusala2002koodi","koulutusala2002nimi","koulutusala2002nimi_sv","koulutusala2002nimi_en","opintoala2002koodi","opintoala2002nimi","opintoala2002nimi_sv","opintoala2002nimi_en","koulutusaste1995koodi","koulutusaste1995nimi","koulutusaste1995nimi_sv","koulutusaste1995nimi_en","koulutusala1995koodi","koulutusala1995nimi","koulutusala1995nimi_sv","koulutusala1995nimi_en","opintoala1995koodi","opintoala1995nimi","opintoala1995nimi_sv","opintoala1995nimi_en","tutkintokoodi","tutkintonimi","tutkintonimi_sv","tutkintonimi_en","tutkintotyyppikoodi","tutkintotyyppinimi","tutkintotyyppinimi_sv","tutkintotyyppinimi_en","koulutustyyppikoodi","koulutustyyppinimi","koulutustyyppinimi_sv","koulutustyyppinimi_en","isced2011koulutusastekoodi","isced2011koulutusastenimi","isced2011koulutusastenimi_sv","isced2011koulutusastenimi_en","isced2011koulutusastetaso1koodi","isced2011koulutusastetaso1nimi","isced2011koulutusastetaso1nimi_sv","isced2011koulutusastetaso1nimi_en","isced2011koulutusastetaso2koodi","isced2011koulutusastetaso2nimi","isced2011koulutusastetaso2nimi_sv","isced2011koulutusastetaso2nimi_en","isced2011koulutusalataso1koodi","isced2011koulutusalataso1nimi","isced2011koulutusalataso1nimi_sv","isced2011koulutusalataso1nimi_en","isced2011koulutusalataso2koodi","isced2011koulutusalataso2nimi","isced2011koulutusalataso2nimi_sv","isced2011koulutusalataso2nimi_en","isced2011koulutusalataso3koodi","isced2011koulutusalataso3nimi","isced2011koulutusalataso3nimi_sv","isced2011koulutusalataso3nimi_en","okmohjauksenalakoodi","okmohjauksenalanimi","okmohjauksenalanimi_sv","okmohjauksenalanimi_en");

if ($type == "csv") {
    foreach ($col_arr as $col) {
        echo $col.";";
    }
    echo "\n";
}

// tsv?
//json:
$return_arr = array();
$redarr = $redis->lrange("koulutus",0,-1);
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
