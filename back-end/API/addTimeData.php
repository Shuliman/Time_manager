<?php
namespace api;

include "time.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$Time = new Time();

$requestedProjectTime = isset($_POST["project"]) ? (float)$_POST["project"] : 0; //verification inputing method, & appropriation if it right
$requestedLearnTime = isset($_POST["learning"]) ? (float)$_POST["learning"] : 0;

$Time->addTimeData($requestedProjectTime, $requestedLearnTime);