<?php

namespace api;

include_once(__DIR__ . "/API/time.php");

const ROUTE = "/Time_manager/back-end/index.php"; 

$Time = new Time();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === ROUTE . '/time') {
    header("Content-Type: application/json; charset=UTF-8");
    echo $Time->getAllData();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === ROUTE . '/time/last_week') {
    header("Content-Type: application/json; charset=UTF-8");
    echo $Time->getLastWeekData();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === ROUTE . '/time/last_week/project') {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    echo $Time->getProjectTimeForLastWeek();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === ROUTE . '/time/last_week/learning') {
    header("Content-Type: application/json; charset=UTF-8");
    echo $Time->getLearningTimeForLastWeek();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === ROUTE . '/time') {
    $projectTime = $_POST['projectTime'];
    $learningTime = $_POST['learningTime'];
    header("Content-Type: application/json; charset=UTF-8");
    echo $Time->addTimeData($projectTime, $learningTime);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && preg_match('/^\/time\/(\d{4}-\d{2}-\d{2})$/', $_SERVER['REQUEST_URI'], $matches)) {
    $day = $matches[1];
    header("Content-Type: application/json; charset=UTF-8");
    echo $Time->deleteTimeData($day);
}
