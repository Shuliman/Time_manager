<?php
namespace api;
use InvalidArgumentException;
use PDO;
use PDOException;
class Time
{
    private $servername = "localhost";
    private $database = "time_manager";
    private $username = "root";
    private $password = "";
    public $tableName = "drey_copy";
    public $connection;

    public function __construct()
    {
        try {
            $dsn = "mysql:host=$this->servername;dbname=$this->database";
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getLastWeekData()
    {
        $data = array();
        $query = $this->connection->prepare("SELECT * FROM $this->tableName WHERE day BETWEEN DATE_SUB(NOW(), INTERVAL 1 WEEK) AND NOW();");
        $query->execute();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return json_encode($data);
    }

    public function getProjectTimeForLastWeek()
    {
        $query = $this->connection->prepare("SELECT SUM(time_on_project)
        FROM $this->tableName
        WHERE day BETWEEN NOW() - INTERVAL 1 WEEK AND NOW();");
        $query->execute();
        return $query->fetchColumn();
    }

    public function getLearningTimeForLastWeek()
    {
        $query = $this->connection->prepare("SELECT SUM(time_on_learning)
        FROM $this->tableName
        WHERE day BETWEEN NOW() - INTERVAL 1 WEEK AND NOW();");
        $query->execute();
        return $query->fetchColumn();
    }

    public function addTimeData($projectTime, $learningTime)
    {
        if ($projectTime < 0 || $learningTime < 0) {
            throw new InvalidArgumentException("Time values cannot be negative.");
        }
        if ($projectTime >= 24 || $learningTime >= 24) {
            throw new InvalidArgumentException("Time values cannot be over 24.");
        }
        if ($projectTime === null) {
            $projectTime = 0;
        }
        if ($learningTime === null) {
            $learningTime = 0;
        }
        $query = "INSERT INTO $this->tableName (day, time_on_project, time_on_learning)
                  VALUES (CURRENT_DATE, :projectTime, :learningTime)
                  ON DUPLICATE KEY UPDATE time_on_project = time_on_project + :projectTime,
                                          time_on_learning = time_on_learning + :learningTime";
    
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':projectTime', $projectTime);
        $stmt->bindParam(':learningTime', $learningTime);
    
        return $stmt->execute();
    }
    

    public function deleteTimeData($day)
    {
        $query = "DELETE FROM $this->tableName WHERE day=:day";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':day', $day);
        return $stmt->execute();
    }

    public function getAllData()
    {
        $data = array();
        $query = $this->connection->prepare("SELECT * FROM $this->tableName");
        $query->execute();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return json_encode($data);
    }
}
