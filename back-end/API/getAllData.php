<?php
namespace api;

include "time.php";

ini_set('display_errors', 1);
error_reporting(E_ALL); 

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$Time = new Time();
echo $Time->getAllData();