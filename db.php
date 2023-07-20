<?php

class Database {
    private $server;
    private $username;
    private $password;
    private $database;
    private $connection;

    public function __construct($server, $username, $password, $database) {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    public function connect() {
        $this->connection = new PDO("mysql:host=$this->server;dbname=$this->database", $this->username, $this->password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function execute($sql){
        try {
            $stmt = $this->connection->prepare( $sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $rows;
        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
        }
    }
    public function getChannelsLogged() {
        try {
            $stmt = $this->connection->prepare("SELECT DISTINCT(channel) FROM history");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                echo $row['channel'] . "<br>";
            }
        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
        }
    }

    public function getHistory($channel) {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM history WHERE channel = :channel");
            $stmt->bindParam(':channel', $channel);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                echo "ID: " . $row['id'] . ", chatmsg: " . $row['chatmsg'] . "<br>";
            }
        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
        }
    }
    public function getCount($channel) {
        try {
            $stmt = $this->connection->prepare("SELECT count(*) FROM history WHERE channel = :channel");
            $stmt->bindParam(':channel', $channel);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                echo "ID: " . $row['id'] . ", chatmsg: " . $row['chatmsg'] . "<br>";
            }
        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
        }
    }
    public function getEntryCount() {
        try {
            $stmt = $this->connection->prepare("SELECT count(*) as total FROM history");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $rows;
        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
        }
    }
    public function addChannel($channel) {
        try {
            $stmt = $this->connection->prepare("INSERT INTO config (channel) VALUES (:channel)");
            $stmt->bindParam(':channel', $channel);
            $stmt->execute();
            flog('ADDED CHANNEL '.$channel);
            return "Channel added successfully.";
        } catch (PDOException $e) {
            flog("ADD CHANNEL FAIL ". $e->getMessage());
            return "Query failed: " . $e->getMessage();
        }
    }
    public function addJoin($username, $channel) {
        try {
            $stmt = $this->connection->prepare("INSERT INTO visits (username,channel) VALUES (:username, :channel)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':channel', $channel);
            $stmt->execute();
            flog('ADDED JOIN '.$channel);
            return "Channel added successfully.";
        } catch (PDOException $e) {
            flog("ADD CHANNEL FAIL ". $e->getMessage());
            return "Query failed: " . $e->getMessage();
        }
    }

    public function addChat($author, $channel,$message,$emotes) {
        try {
            $stmt = $this->connection->prepare("INSERT INTO history (author, channel, message, emotes) VALUES (:author,:channel,:message,:emotes)");
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':channel', $channel);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':emotes', $emotes);
            $stmt->execute();
            return "added successfully.";
        } catch (PDOException $e) {
            flog("ADD CHAT FAIL ". $e->getMessage());
            return "Query failed: " . $e->getMessage();
        }
    }
    public function removeChannel($channel) {
        try {
            $stmt = $this->connection->prepare("DELETE FROM config WHERE channel = :channel");
            $stmt->bindParam(':channel', $channel);
            $stmt->execute();
            // flog('TERMINATING IRC CONNECTION');
            flog("REMOVED CHANNEL ".$channel);
            return "Channel removed successfully.";
        } catch (PDOException $e) {
            flog('REMOVE CHANNEL FAIL '.$e->getMessage());
            return "Query failed: " . $e->getMessage();
        }
    }
    

    public function getChannels() {
        try {
            $stmt = $this->connection->prepare("SELECT channel FROM config");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rows;
        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
        }
    }
    
    public function getConfig() {
        try {
            $stmt = $this->connection->prepare("SELECT channel FROM config");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $rows;
        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
        }
    }
}




$server = 'db';
$username = 'root';
$password = '@PASSword123';
$database = 'Twitch';
$db = new Database($server, $username, $password, $database);
$db->connect();
?>