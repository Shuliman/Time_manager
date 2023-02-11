<?php
namespace api;
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
        $this->connection = mysqli_connect($this->servername, $this->username, $this->password, $this->database);
        if (!$this->connection) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function getLastWeekData()
    {
        $data = array();
        $query = mysqli_query($this->connection, "SELECT * FROM $this->tableName WHERE day BETWEEN DATE_SUB(NOW(), INTERVAL 1 WEEK) AND NOW();");
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
        return json_encode($data);
    }

    public function getProjectTimeForLastWeek()
    {
        return  mysqli_fetch_row(mysqli_query($this->connection, "SELECT SUM(time_on_project)
        FROM $this->tableName
        WHERE day BETWEEN NOW() - INTERVAL 1 WEEK AND NOW();"))[0];
    }

    public function getLearningTimeForLastWeek()
    {
        return  mysqli_fetch_row(mysqli_query($this->connection, "SELECT SUM(time_on_learning)
        FROM $this->tableName
        WHERE day BETWEEN NOW() - INTERVAL 1 WEEK AND NOW();"))[0];
    }

    public function addTimeData($projectTime, $learningTime)
    {
        $query = "INSERT INTO $this->tableName (day, time_on_project, time_on_learning) 
        VALUES (CURRENT_DATE, $projectTime, $learningTime) 
        ON DUPLICATE KEY UPDATE time_on_project = time_on_project + $projectTime, 
        time_on_learning = time_on_learning + $learningTime";
        return mysqli_query($this->connection, $query);
    }

    public function deleteTimeData($day)
    {
        $query = "DELETE FROM $this->tableName WHERE day='$day'";
        return mysqli_query($this->connection, $query);
    }

    public function getAllData()
    {
        $data = array();
        $query = mysqli_query($this->connection, "SELECT * FROM $this->tableName");
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
        return json_encode($data);
    }
}