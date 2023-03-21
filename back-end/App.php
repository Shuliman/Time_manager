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
	$config = require 'config.php';
    $today =  date('Y-m-d');
    class DBManager
    {
        private $servername;
        private $database;
        private $username;
        private $password;
        private $options;
        public $tableName;
        public $connection;

        public function __construct($config)
        {
			$this->servername = $config['db']['host'];
			$this->database = $config['db']['dbname'];
			$this->username = $config['db']['username'];
			$this->password = $config['db']['password'];
			$this->options = $config['db']['options'];
			$this->tableName = $config['db']['tableName'];
            $this->connection = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password, $this->options);
        }

        public function getLastWeekData()
        {
            $query = $this->connection->query("SELECT day, time_on_project, time_on_learning FROM $this->tableName WHERE day 
            BETWEEN DATE_SUB(NOW(), INTERVAL 1 WEEK) AND NOW()");
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }

		public function getTotalProjectTime()
		{
			$query = $this->connection->query("SELECT SUM(`time_on_project`) FROM $this->tableName");
			
			return  round($query->fetchColumn(), 2);
		}
		public function getTotalLearningTime()
		{
			$query = $this->connection->query("SELECT SUM(`time_on_learning`) FROM $this->tableName");
			
			return  round($query->fetchColumn(), 2);
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
    
    $db = new DBManager($config);
    
    $time_on_project_from_last_week = $db->getProjectTimeForLastWeek();
    $time_on_learning_from_last_week = $db->getLearningTimeForLastWeek();
    echo "<p>Today is: $today <br>";
    ?>
    <table>
        <tr>
            <th>Day</th>
            <th>Time on Project</th>
            <th>Time on Learning</th>
        </tr>
        <?php
        $resultSet = $db->getLastWeekData();

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
    $requestedLearnTime = isset($_POST["learning"]) ? (float)$_POST["learning"] : 0; //In this version i don't reduce it, but where we been used separated front/back-end we can't use this patch

    $db->addTimeData($requestedProjectTime, $requestedLearnTime);

    $totalLearningTime = $db->getTotalLearningTime();
	echo "<br>Total project time: " . $totalLearningTime + $config['time']['learning'];

	$totalProjectTime = $db->getTotalProjectTime();
	echo "<br>Total project time: " . $totalProjectTime + $config['time']['project'];

    ?>
    <style>
        td {
            text-align: center;
        }
    </style>
</body>

</html>