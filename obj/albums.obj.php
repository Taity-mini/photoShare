<?php

/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 24/11/2016
 * Time: 14:27
 */
class albums
{
    private $albumID, $userID, $albumName, $albumDescription;

    //Constructor
    function __construct($albumID = -1)
    {
        $this->albumID = $albumID;
    }

    //Getters


    public function getAlbumID()
    {
        return $this->albumID;
    }


    public function getUserID()
    {
        return $this->userID;
    }

    public function getAlbumName()
    {
        return $this->albumName;
    }

    public function getAlbumDescription()
    {
        return $this->albumDescription;
    }

    //Setters


    public function setAlbumID($albumID)
    {
        $this->albumID = htmlentities($albumID);
    }

    public function setUserID($userID)
    {
        $this->userID = htmlentities($userID);
    }

    public function setAlbumName($albumName)
    {
        $this->albumName =htmlentities($albumName);
    }

    public function setAlbumDescription($albumDescription)
    {
        $this->albumDescription = htmlentities($albumDescription);
    }



    //Main Methods


    public function getAllDetails($conn){
        $sql = "SELECT * FROM albums WHERE albumID = :albumID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':albumID', $this->getAlbumID(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as $row) {
                $this->setAlbumName($row['albumName']);
                $this->setAlbumDescription($row["albumDescription"]);
                $this->setUserID($row["userID"]);
            }
            return true;
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }



    public function create($conn)
    {
        try {
            //SQL Statement
            $sql = "INSERT into albums VALUES (null,:userID, :albumName, :albumDescription)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);
            $stmt->bindParam(':albumName', $this->getAlbumName(), PDO::PARAM_STR);
            $stmt->bindParam(':albumDescription', $this->getAlbumDescription(), PDO::PARAM_STR);
            $stmt->execute();
            echo "Statement working";
            return true;
        } catch (PDOException $e) {
            //dbClose($conn);
            return "Create album failed: " . $e->getMessage();
        } catch (Exception $e) {
            //dbClose($conn);
            return "Create album failed: " . $e->getMessage();
        }
    }

    public function update($conn)
    {
        $sql = "UPDATE albums SET albumName = :albumName, albumDescription = :albumDescription WHERE albumID = :albumID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':albumID', $this->getAlbumID(), PDO::PARAM_STR);
        $stmt->bindParam(':albumName', $this->getAlbumName(), PDO::PARAM_STR);
        $stmt->bindParam(':albumDescription', $this->getAlbumDescription(), PDO::PARAM_STR);
        try {
            $stmt->execute();
            dbClose($conn);
            return true;
        } catch (PDOException $e) {
            dbClose($conn);
            return "Update failed: " . $e->getMessage();
        } catch (Exception $e) {
            dbClose($conn);
            return "Update failed: " . $e->getMessage();
        }
    }


//List all albums in the database and from user from optional parameter
    public function listAllAlbums($conn, $userID = null){
        $sql = "SELECT * FROM albums a";

        if (!is_null($userID)) {
            $sql .= " WHERE a.userID = :userID";
        }

        $stmt = $conn->prepare($sql);

        if (!is_null($userID)) {
            $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
        }

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        } catch (PDOException $e) {
            return "Database query failed: " . $e->getMessage();
        }
    }

    public function listAllAlbumSelect($conn, $userID = null){
        $sql = "SELECT albumID, albumName FROM albums";

        if (!is_null($userID)) {
            $sql .= " WHERE userID = :userID";
        }

        $stmt = $conn->prepare($sql);

        if (!is_null($userID)) {
            $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
        }

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            $array = array();
            foreach ($results as $result) {
                $array[$result["albumID"]] = $result["albumName"];
            }
            return $array;

        } catch (PDOException $e) {
            return "Database query failed: " . $e->getMessage();
        }
    }


    public function doesExist($conn){
        $sql = "SELECT albumID FROM albums WHERE albumID = :albumID LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':albumID', $this->getAlbumID(), PDO::PARAM_STR);
        try {
            $stmt->execute();
            $results = $stmt->fetchAll();
            if (count($results) > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }

}

