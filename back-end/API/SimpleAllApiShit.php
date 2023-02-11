<?php

use api\Time;

$time = new Time();

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

if ($request[0] == 'time') {
    switch ($method) {
        case 'GET':
            if (isset($request[1])) {
                switch ($request[1]) {
                    case 'lastweek':
                        echo $time->getLastWeekData();
                        break;
                    case 'project':
                        echo $time->getProjectTimeForLastWeek();
                        break;
                    case 'learning':
                        echo $time->getLearningTimeForLastWeek();
                        break;
                    case 'all':
                        echo $time->getAllData();
                        break;
                }
            } else {
                echo 'Invalid request';
            }
            break;
        case 'POST':
            $projectTime = $_POST['project_time'];
            $learningTime = $_POST['learning_time'];
            echo $time->addTimeData($projectTime, $learningTime);
            break;
        case 'DELETE':
            $day = $_GET['day'];
            echo $time->deleteTimeData($day);
            break;
    }
} else {
    echo 'Invalid request';
}
