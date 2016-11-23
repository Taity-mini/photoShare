<?php

//Users object class used to represent users of the website

class users
{
    private $userID, $username, $email, $firstName, $lastName, $bio, $website;


    //Constructor
    function __construct($username = "cat"){
        $this->username = $username;
    }


    //Getters

    public function getUserID(){
        return $this->userID;
    }

    public function getUserIDFromUsername($conn){
        $sql = "SELECT userID FROM users WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $this->getUsername(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as $row) {
                $this->userID = $row["userID"];
            }
            return true;
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }

    public function getUsername(){
        return $this->username;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getFirstName(){
        return $this->firstName;
    }

    public function getLastName(){
        return $this->lastName;
    }

    public function getBio(){
        return $this->bio;
    }

    public function getWebsite(){
        return $this->website;
    }


    //Setters

    public function setUserID($userID){
        $this->userID = $userID;
    }

    public function setUsername($username){
        $this->username = $username;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function setFirstName($firstName){
        $this->firstName = $firstName;
    }

    public function setLastName($lastName){
        $this->lastName = $lastName;
    }

    public function setBio($bio){
        $this->bio = $bio;
    }

    public function setWebsite($website){
        $this->website = $website;
    }


    //get all details


    public function getAllDetails($conn) {
        $sql = "SELECT * FROM profiles WHERE userID = :userID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userID', $this->userID, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as $row) {
                $this->setUsername($row["username"]);
                $this->setSASANumber($row["sasaNumber"]);
                $this->setStatus($row["status"]);
                $this->setFirstName($row["firstName"]);
                $this->setMiddleName($row["middleName"]);
                $this->setLastName($row["lastName"]);
                $this->setGender($row["gender"]);
                $this->setDOB($row["dob"]);
                $this->setAddress1($row["address1"]);
                $this->setAddress2($row["address2"]);
                $this->setCity($row["city"]);
            }
            return true;
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }


    //Create user and profile

    public function create($conn, $password){
        if($this->createUser($conn,$password) && $this->createProfile($conn)) {
            return true;
        }
        else{
            return false;
        }
    }

    private function createUser($conn, $password){
        try {
            //Lets use the php bcrypt function on the password
            $hash = password_hash($password, PASSWORD_DEFAULT);

            //SQL Statement

            $sql = "INSERT into users VALUES (:username, password, false)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $this->getUsername(), PDO::PARAM_STR);
            $stmt->bindParam(':password', $hash, PDO::PARAM_STR);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            dbClose($conn);
            return "Create failed: " . $e->getMessage();
        } catch (Exception $e) {
            dbClose($conn);
            return "create failed: " . $e->getMessage();
        }
    }

    private function createProfile($conn)
    {
        $this->getUserIDFromUsername($conn);
        try
        {
            $sql = "INSERT INTO profiles VALUES (:userID, :email, :firstName, :lastName, :bio, :website)";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->getEmail(), PDO::PARAM_INT);
            $stmt->bindParam(':firstName', $this->getFirstName(), PDO::PARAM_STR);
            $stmt->bindParam(':lastName', $this->getLastName(), PDO::PARAM_INT);
            $stmt->bindValue(':bio', $this->getBio(), PDO::PARAM_INT);
            $stmt->bindParam(':website', $this->getWebsite(), PDO::PARAM_STR);
            var_dump($stmt);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            dbClose($conn);
            return "Create failed: " . $e->getMessage();
        }
        catch (Exception $e) {
            dbClose($conn);
            return "create failed: " . $e->getMessage();
        }

    }

    //List all users

    public function listAllUsers($conn, $name = null) {
        $sql = "SELECT * FROM profiles p";
        if (!is_null($name)) {
            $sql .= " WHERE p.firstName = :name OR p.lastName = :name";
        }

        $stmt = $conn->prepare($sql);

        if (!is_null($name)) {
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        }

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        } catch (PDOException $e) {
            return "Database query failed: " . $e->getMessage();
        }
    }

    //Banning and approval Methods

    public function isApproved($conn)
    {


    }

    public function isBanned($conn)
    {

    }

    public function approveUser($conn, $userID)
    {

    }

    public function banningUser($conn, $userID)
    {

    }



    //Checking boolean methods
    //Does the user exist
    public function doesExist($conn) {
        $sql = "SELECT userID FROM users WHERE userID = :userID LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);
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
