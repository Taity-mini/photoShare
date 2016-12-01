<?php

/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 21/11/2016
 * Time: 13:15
 */
class user_groups
{
    private $groupID, $groupName, $groupDescription, $userID;


    // ***** CONSTRUCTOR *****
    function __construct($userID = "zzz", $groupID = -1)
    {
        $this->userID = $userID;
        $this->groupID = $groupID;
    }


    //Getters

    public function getUserID()
    {
        return $this->userID;
    }

    public function getGroupID()
    {
        return $this->groupID;
    }

    public function getGroupName()
    {
        return $this->groupName;
    }

    public function getGroupDescription()
    {
        return $this->groupDescription;
    }


    //Setters

    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    public function setGroupID($groupID)
    {
        $this->groupID = $groupID;
    }

    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    public function setGroupDescription($groupDescription)
    {
        $this->groupDescription = $groupDescription;
    }

    //Create groups for user

    public function create($conn)
    {
        $sql = "INSERT INTO user_groups VALUES (:userID, :groupID)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);
        $stmt->bindParam(':groupID', $this->getGroupID(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return "Create failed: " . $e->getMessage();
        }
    }

    //Delete groups from user

    public function delete($conn, $userID)
    {
        $sql = "DELETE FROM user_groups WHERE userID = :userID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            $array = array();
            foreach ($results as $row) {
                array_push($array, $row["roleID"]);
            }
            return $array;
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }


    public function getAllDetails($conn)
    {
        try {
            $sql = "SELECT * FROM user_groups ug, groups g WHERE ug.userID = :userID and ug.groupID = g.groupID";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);

            try {
                $stmt->execute();
                $results = $stmt->fetchAll();

                foreach ($results as $row) {
                    $this->setGroupID($row["groupID"]);
                    $this->setGroupDescription($row['groupDescription']);
                    $this->setGroupName($row['groupName']);
                }
                return true;
            } catch (PDOException $e) {
                return "Query failed: " . $e->getMessage();
            }

        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }


    public function getAllGroups($conn)
    {
        $sql = "SELECT g.groupID, g.groupName FROM user_groups m, groups g WHERE m.groupID = g.groupID";
        $stmt = $conn->prepare($sql);
        //$stmt->bindParam(':member', $this->getMember(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            $array = array();
            foreach ($results as $row) {
                $array[$row["groupID"]] = $array[$row["groupName"]];
            }
            return $array;
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }

    //Check permissions

    public function isUserAdministrator($conn, $userID)
    {
        $sql = "SELECT userID FROM users u, user_groups ug, groups g WHERE ug.groupID = g.groupID AND r.id = 1 AND ug.userID = :userID";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }


    public function isUserPhotographer($conn, $userID)
    {
        $sql = "SELECT userID FROM users u, user_groups ug, groups g WHERE ug.groupID = g.groupID AND r.id = 2 AND ug.userID = :userID";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }

    public function isUserShopper($conn, $userID)
    {
        $sql = "SELECT userID FROM users u, user_groups ug, groups g WHERE ug.groupID = g.groupID AND r.id = 3 AND ug.userID = :userID";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }


    //Listing groups
    public function listAllAdministrators($conn)
    {
        $sql = "SELECT u.username, CONCAT(p.firstName, ' ', p.lastName) AS name FROM users u, profile p, user_groups g WHERE g.groupID = 1 AND g.userID = u.userID";
        $stmt = $conn->prepare($sql);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            $array = array();
            foreach ($results as $result) {
                $array[$result["username"]] = $result["name"];
            }

            return $array;
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }

    public function listAllPhotographers($conn)
    {
        $sql = "SELECT u.username, CONCAT(p.firstName, ' ', p.lastName) AS name FROM users u, profile p, user_groups g WHERE g.groupID = 2 AND g.userID = u.userID";
        $stmt = $conn->prepare($sql);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            $array = array();
            foreach ($results as $result) {
                $array[$result["username"]] = $result["name"];
            }

            return $array;
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }


    public function listAllShoppers($conn)
    {
        $sql = "SELECT u.username, CONCAT(p.firstName, ' ', p.lastName) AS name FROM users u, profile p, user_groups g WHERE g.groupID = 3 AND g.userID = u.userID";
        $stmt = $conn->prepare($sql);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            $array = array();
            foreach ($results as $result) {
                $array[$result["username"]] = $result["name"];
            }

            return $array;
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }
}
