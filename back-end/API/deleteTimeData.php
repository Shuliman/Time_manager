<?php
namespace api;

include "time.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$Time = new Time();
$Time->deleteTimeData($_POST);