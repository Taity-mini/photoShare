<?php

/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 29/11/2016
 * Time: 13:55
 */
class comments
{
    private $commentID, $comment, $userID, $photoID;

    function __construct($commentID = -1)
    {
        $this->commentID = $commentID;
    }


    //Getters

    function getCommentID()
    {
        return $this->commentID;
    }

    function getComment()
    {
        return $this->comment;
    }

    function getUserID()
    {
        return $this->userID;
    }

    function getPhotoID()
    {
        return $this->photoID;
    }


    //Setters

    function setCommentID($commentID)
    {
        $this->commentID = $commentID;
    }

    function setComment($comment)
    {
        $this->comment = $comment;
    }

    function setUserID($userID)
    {
        $this->userID = $userID;
    }

    function setPhotoID($photoID)
    {
        $this->photoID = $photoID;
    }


    //Main Methods

    public function create($conn)
    {
        try {
            //SQL Statement
            $sql = "INSERT into comments VALUES (NULL ,:comment, :userID,  :photoID)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':comment', $this->getComment(), PDO::PARAM_STR);
            $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);
            $stmt->bindParam(':photoID', $this->getPhotoID(), PDO::PARAM_STR);
            $stmt->execute();
            //echo "Statement working";
            return true;
        } catch (PDOException $e) {
            //dbClose($conn);
            return "Create photo failed: " . $e->getMessage();
        } catch (Exception $e) {
            //dbClose($conn);
            return "Create photo failed: " . $e->getMessage();
        }
    }


    public function update($conn){
        $sql = "UPDATE comments SET comment = :comment  WHERE commentID = :commentID";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':commentID', $this->getCommentID(), PDO::PARAM_INT);
        $stmt->bindParam(':comment', $this->getComment(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return "Update failed: " . $e->getMessage();
        }
    }

    public function delete($conn)
    {
        $sql = "DELETE FROM comments WHERE commentID = :commentID";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':commentID', $this->getCommentID(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return "Create failed: " . $e->getMessage();
        }
    }

    public function getAllDetails($conn)
    {
        $sql = "SELECT * FROM comments WHERE commentID = :commentID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':commentID', $this->getCommentID(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as $row) {
                $this->setComment($row['comment']);
                $this->setUserID($row["userID"]);
                $this->setPhotoID($row['photoID']);
            }
            return true;
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }

    public function listAllComments($conn)
    {
        $sql = "SELECT * FROM comments where photoID = :photoID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':photoID', $this->getPhotoID(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        } catch (PDOException $e) {
            return "Database query failed: " . $e->getMessage();
        }
    }


    public function doesExist($conn)
    {
        $sql = "SELECT * FROM comments WHERE photoID = :photoID LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':photoID', $this->getPhotoID(), PDO::PARAM_STR);
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