<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>time manager</title>
</head>

<body>
    <h1>DEV</h1>
    <?php
    $today =  date('Y-m-d');
    $time_on_project = [];
    $time_on_learning = [];
    class DBManager
    {
        private $servername = "localhost";
        private $database = "time_manager";
        private $username = "root";
        private $password = "";
        private $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];
        public $tableName = "drey_copy";
        public $connection;

        public function __construct()
        {
            $this->connection = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password, $this->options);
        }

        public function getLastWeekData()
        {
            $query = $this->connection->query("SELECT * FROM $this->tableName WHERE day 
            BETWEEN DATE_SUB(NOW(), INTERVAL 1 WEEK) AND NOW()");
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getProjectTime()
        {
            $query = $this->connection->query("SELECT `time_on_project` FROM $this->tableName");
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getProjectTimeForLastWeek()
        {
            $query = $this->connection->query("SELECT SUM(time_on_project) FROM $this->tableName WHERE day 
            BETWEEN NOW() - INTERVAL 1 WEEK AND NOW()");
            return $query->fetchColumn();
        }

        public function getLearningTimeForLastWeek()
        {
            $query = $this->connection->query("SELECT SUM(time_on_learning) FROM $this->tableName WHERE day 
            BETWEEN NOW() - INTERVAL 1 WEEK AND NOW()");
            return $query->fetchColumn();
        }

        public function addTimeData($projectTime, $learningTime)
        {
            $query = $this->connection->prepare("INSERT INTO $this->tableName (day, time_on_project, time_on_learning) 
            VALUES (CURRENT_DATE, :projectTime, :learningTime) 
            ON DUPLICATE KEY UPDATE time_on_project = time_on_project + :projectTime, 
            time_on_learning = time_on_learning + :learningTime");

            $query->bindParam(':projectTime', $projectTime);
            $query->bindParam(':learningTime', $learningTime);
            return $query->execute();
        }
    }
    $db = new DBManager();
    $queryLastWeek = $db->getLastWeekData();
    $time_on_project = $db->getProjectTime();
    $time_on_project_from_last_week = $db->getProjectTimeForLastWeek();
    $time_on_learning_from_last_week = $db->getLearningTimeForLastWeek();
    $check_query = $db->connection->query("SELECT * FROM $db->tableName WHERE day=$today");
    echo "<p>Today is: $today <br>";
    ?>
    <table>
        <tr>
            <th>Day</th>
            <th>Time on Project</th>
            <th>Time on Learning</th>
        </tr>
        <?php
        $stmt = $db->connection->prepare("SELECT * FROM $db->tableName WHERE day BETWEEN DATE_SUB(NOW(), INTERVAL 1 WEEK) AND NOW()");
        $stmt->execute();
        $resultSet = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultSet as $row) {
            echo '<tr>';
            echo '<td>' . $row['day'] . '</td>';
            echo '<td>' . $row['time_on_project'] . '</td>';
            echo '<td>' . $row['time_on_learning'] . '</td>';
            echo '</tr>';
        }
        ?>
    </table>
    <?php
    echo '<br> PR time from last week: ' . $time_on_project_from_last_week . '<br>';
    echo '<br> LR time from last week: ' . $time_on_learning_from_last_week . '<br>';
    ?>
    <form action="App.php" method="post" id="timeAdding">
        <p>How much time have you spent on the project today? : <input type="float" name="project" />
        <p>How much time have you spent on learning today? : <input type="float" name="learning" /></p>
        <input type="submit" value="Submit">
    </form>
    <?php
    $requestedProjectTime = isset($_POST["project"]) ? (float)$_POST["project"] : 0; //verification inputing method, & appropriation if it right
    $requestedLearnTime = isset($_POST["learning"]) ? (float)$_POST["learning"] : 0;

    $db->addTimeData($requestedProjectTime, $requestedLearnTime);

    $time_on_learning_all = 514; // changed with user
    foreach ($time_on_learning as $key => $value) {
        global $time_on_learning_all;
        $time_on_learning_all += $key;
    }
    echo '<br> Time on learn all: ' . ' ' .  $time_on_learning_all;

    $time_on_project_all = 510; // changed with user
    foreach ($time_on_project as $key => $value) {
        global $time_on_project_all;
        $time_on_project_all += $key;
    }
    echo '<br> Time on project all: ' . ' ' . $time_on_project_all;

    ?>
    <style>
        td {
            text-align: center;
        }
    </style>
</body>

</html>