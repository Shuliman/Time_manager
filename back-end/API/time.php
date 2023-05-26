<?php
namespace API;
use InvalidArgumentException;
use PDO;
use PDOException;
class Time
{
    private $servername;
    private $database;
    private $username;
    private $password;
    private $options;
    public $tableName;
    public $connection;

    /**
     * Constructor for the Time class
     *
     * @param array $config Configuration parameters for connecting to the database
     */

    public function __construct($config)
    {
        // Initialize the database connection parameters
        $this->servername = $config['db']['host'];
        $this->database = $config['db']['dbname'];
        $this->username = $config['db']['username'];
        $this->password = $config['db']['password'];
        $this->options = $config['db']['options'];
        $this->tableName = $config['db']['tableName'];
        try {
            // Create a connection to the database
            $dsn = "mysql:host=$this->servername;dbname=$this->database";
            $this->connection = new PDO($dsn, $this->username, $this->password);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * Retrieves data for the last week
     *
     * @return string JSON representation of the data
     */
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

     /**
     * Retrieves the total project time for the last week.
     *
     * @return mixed Total project time.
     */
    public function getProjectTimeForLastWeek()
    {
        $query = $this->connection->prepare("SELECT SUM(time_on_project)
        FROM $this->tableName
        WHERE day BETWEEN NOW() - INTERVAL 1 WEEK AND NOW();");
        $query->execute();
        return $query->fetchColumn();
    }

    /**
     * Retrieves the total learning time for the last week.
     *
     * @return mixed Total learning time.
     */
    public function getLearningTimeForLastWeek()
    {
        $query = $this->connection->prepare("SELECT SUM(time_on_learning)
        FROM $this->tableName
        WHERE day BETWEEN NOW() - INTERVAL 1 WEEK AND NOW();");
        $query->execute();
        return $query->fetchColumn();
    }

    /**
     * Adds time data to the database.
     *
     * @param float $projectTime Time spent on projects.
     * @param float $learningTime Time spent on learning.
     * @return bool The result of the operation.
     * @throws InvalidArgumentException If the time values are negative or exceed 24 hours.
     */
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
    
    /**
     * Deletes time data from the database for the specified day.
     *
     * @param string $day The day for which to delete the data.
     * @return bool The result of the operation.
     */
    public function deleteTimeData($day)
    {
        $query = "DELETE FROM $this->tableName WHERE day=:day";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':day', $day);
        return $stmt->execute();
    }

    /**
     * Retrieves all data from the database.
     *
     * @return string JSON representation of all data.
     */
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

     /**
     * Retrieves the total project time.
     *
     * @return float Total project time.
     */
    public function getTotalProjectTime()
    {
        $query = $this->connection->query("SELECT SUM(`time_on_project`) FROM $this->tableName");
        
        return  round($query->fetchColumn(), 2);
    }

    /**
     * Retrieves the total learning time.
     *
     * @return float Total learning time.
     */
    public function getTotalLearningTime()
    {
        $query = $this->connection->query("SELECT SUM(`time_on_learning`) FROM $this->tableName");
        
        return  round($query->fetchColumn(), 2);
    }
}
