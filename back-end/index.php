<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>time manager</title>
</head>

<body>
    <?php
    $today =  date('Y-m-d');
    $time_on_project = [];
    $time_on_learning = [];
    class DBConnection
    {
        private $servername = "localhost";
        private $database = "time_manager";
        private $username = "root";
        private $password = "";
        public $tableName = "drey_copy";

        public function __construct()
        {
            $this->connection = mysqli_connect($this->servername, $this->username, $this->password, $this->database);
            if (!$this->connection) {
                die("Connection failed: " . mysqli_connect_error());
            }
            echo "Connected successfully <br>";
        }

        public function getLastWeekData()
        {
            return  mysqli_query($this->connection, "SELECT * FROM $this->tableName WHERE day BETWEEN DATE_SUB(NOW(), INTERVAL 1 WEEK) AND NOW();");
        }

        public function getProjectTime()
        {
            return  mysqli_query($this->connection, "SELECT `time_on_project` FROM $this->tableName");
        }

        public function getProjectTimeForLastWeek()
        {
            return  mysqli_fetch_row(mysqli_query($this->connection, "SELECT SUM(time_on_project)
        FROM $this->tableName
        WHERE day BETWEEN CURRENT_DATE - INTERVAL 7 DAY AND CURRENT_DATE;"));
        }

        public function getLearningTimeForLastWeek()
        {
            return  mysqli_fetch_row(mysqli_query($this->connection, "SELECT SUM(time_on_learning)
        FROM $this->tableName
        WHERE day BETWEEN CURRENT_DATE - INTERVAL 7 DAY AND CURRENT_DATE;"));
        }
    }
    $db = new DBConnection();
    $queryLastWeek = $db->getLastWeekData();
    $time_on_project = $db->getProjectTime();
    $time_on_project_from_last_week = $db->getProjectTimeForLastWeek()[0];
    $time_on_learning_from_last_week = $db->getLearningTimeForLastWeek()[0];
    $check_query = mysqli_query($db->connection, "SELECT * FROM $db->tableName WHERE day='$today'");
    if (mysqli_num_rows($check_query) == 0) {
        mysqli_query($db->connection, "INSERT INTO $db->tableName (day, time_on_project, time_on_learning) 
        VALUES ('$today', 0, 0) ");
    }  // cheap patch, don't do that

    echo "<p>Today is: $today <br>";
    ?>
    <table>
        <tr>
            <th>Day</th>
            <th>Time on Project</th>
            <th>Time on Learning</th>
        </tr>
        <?php

        while ($dataArray = mysqli_fetch_assoc($queryLastWeek)) {
            echo '<tr>';
            echo '<td>' . $dataArray['day'] . '</td>';
            echo '<td>' . $dataArray['time_on_project'] . '</td>';
            echo '<td>' . $dataArray['time_on_learning'] . '</td>';
            echo '</tr>';
        }
        ?>
    </table>
    <?php
    echo '<br> PR time from last week: ' . $time_on_project_from_last_week . '<br>';
    echo '<br> LR time from last week: ' . $time_on_learning_from_last_week . '<br>';
    ?>
    <form action="index.php" method="post" id="timeAdding">
        <p>How much time have you spent on the project today? : <input type="float" name="project" />
        <p>How much time have you spent on learning today? : <input type="float" name="learning" /></p>
        <input type="submit" value="Submit">
    </form>
    <?php
    $requestedProjectTime = isset($_POST["project"]) ? (float)$_POST["project"] : 0; //verification inputing method, & appropriation if it right
    $requestedLearnTime = isset($_POST["learning"]) ? (float)$_POST["learning"] : 0;

    echo "<br>requestedLearnTime: $requestedLearnTime<br>" . "<br>requestedProjectTime: $requestedProjectTime<br>";

    function  add_time(string $time_to, float $time)
    {
        global $db;
        switch ($time_to) {
            case 'project':
                $query = "INSERT INTO $db->tableName (day, time_on_project)
            VALUES (CURDATE(), $time)
            ON DUPLICATE KEY UPDATE
            time_on_project = time_on_project + $time;";
                mysqli_query($db->connection, $query);
                break;
            case 'learning':
                $query = "INSERT INTO $db->tableName (day, time_on_learning)
            VALUES (CURDATE(), $time)
            ON DUPLICATE KEY UPDATE
            time_on_learning = time_on_learning + $time;";
                mysqli_query($db->connection, $query);
                break;
        }
    }


    add_time('learning', $requestedLearnTime);
    add_time('project', $requestedProjectTime);

    /*
TO FUCKING DO!!!
foreach ($time_on_project as $key => $value) {
    echo $key . '<br>';
}



echo 'time on project';
foreach ($time_on_project as $value) {
    echo "<br>$value<br>";
}
echo 'time on lerning';
foreach ($time_on_learning as $value) {
    echo "<br>$value<br>";
}*/



    $time_on_learning_all = 514;
    foreach ($time_on_learning as $key => $value) {
        global $time_on_learning_all;
        $time_on_learning_all += $key;
    }
    echo '<br> Time on learn all: ' . ' ' .  $time_on_learning_all;

    $time_on_project_all = 510;
    foreach ($time_on_project as $key => $value) {
        global $time_on_project_all;
        $time_on_project_all += $key;
    }
    echo '<br> Time on project all: ' . ' ' . $time_on_project_all;

    mysqli_close($db->connection);
    ?>
    <style>
        td {
            text-align: center;
        }
    </style>
</body>

</html>