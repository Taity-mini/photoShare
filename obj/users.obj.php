<?php

//Users object class used to represent users of the website

class users
{
    private $userID, $username, $email, $firstName, $lastName, $bio, $website;

    //Constructor
    function __construct($userID = -1)
    {
        $this->userID = htmlentities($userID);
    }

    //Getters

    public function getUserID()
    {
        return $this->userID;
    }

    public function getUserIDFromUsername($conn)
    {
        $sql = "SELECT userID FROM users WHERE username = :username LIMIT 1";
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

    public function getUserNameFromUserID($conn)
    {
        $sql = "SELECT username FROM users WHERE userID = :userID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as $row) {
                $this->username = $row["username"];
            }
            return true;
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function getWebsite()
    {
        return $this->website;
    }


    //Setters

    public function setUserID($userID)
    {
        $this->userID = htmlentities($userID);
    }

    public function setUsername($username)
    {
        $this->username = htmlentities($username);
    }

    public function setEmail($email)
    {
        $this->email = htmlentities($email);
    }

    public function setFirstName($firstName)
    {
        $this->firstName = htmlentities($firstName);
    }

    public function setLastName($lastName)
    {
        $this->lastName = htmlentities($lastName);
    }

    public function setBio($bio)
    {
        $this->bio = htmlentities($bio);
    }

    public function setWebsite($website)
    {
        $this->website = htmlentities($website);
    }


    //get all details

    public function getAllDetails($conn)
    {
        $sql = "SELECT * FROM profiles WHERE userID = :userID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);

        $sql2 = "SELECT username FROM users WHERE userID = :userID";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as $row) {
                $this->setEmail($row['email']);
                $this->setFirstName($row["firstName"]);
                $this->setLastName($row["lastName"]);
                $this->setBio($row["bio"]);
                $this->setWebsite($row["website"]);
            }

            $this->getUserNameFromUserID($conn);
            return true;
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }

    public function getTotalCount($conn)
    {
        $sql = "SELECT COUNT(*) FROM users";
        $stmt = $conn->prepare($sql);
        try {
            $stmt->execute();
            $results = $stmt->fetch();
            $count = $results[0];
            return $count;
        } catch (PDOException $e) {
            return "Database query failed: " . $e->getMessage();
        }
    }


    //Create user and profile

    public function create($conn, $password, $group)
    {
        if ($this->createUser($conn, $password, $group) && $this->createProfile($conn)) {
            return true;
        } else {
            return false;
        }
    }

    public function createUser($conn, $password, $group)
    {
        try {
            //Lets use the php bcrypt function on the password
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $approved = "";
            //switch case for approval:
            switch ($group) {
                case 1:
                    $approved = true;
                    break;
                case 2:
                    $approved = false;
                    break;
                case 3:
                    $approved = true;
                    break;

                default:
                    $approved = true;
                    break;
            }

            //SQL Statement

            $sql = "INSERT into users VALUES (null,:username, :password, :group, :approve)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $this->getUsername(), PDO::PARAM_STR);
            $stmt->bindParam(':password', $hash, PDO::PARAM_STR);
            $stmt->bindParam(':group', $group, PDO::PARAM_STR);
            $stmt->bindParam(':approve', $approved, PDO::PARAM_STR);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            dbClose($conn);
            return "Create user failed: " . $e->getMessage();
        } catch (Exception $e) {
            dbClose($conn);
            return "Create user failed: " . $e->getMessage();
        }
    }

    public function createProfile($conn)
    {
        $this->getUserIDFromUsername($conn);
        try {
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
            return "Create profile failed: " . $e->getMessage();
        } catch (Exception $e) {
            dbClose($conn);
            return "create profile failed: " . $e->getMessage();
        }

    }

    public function updateProfile($conn)
    {
        try {
            $sql = "UPDATE profiles SET email = :email, firstName = :firstName, lastName = :lastName, bio = :bio, website = :website WHERE userID = :userID";

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
            return "update profile failed: " . $e->getMessage();
        } catch (Exception $e) {
            dbClose($conn);
            return "update profile failed: " . $e->getMessage();
        }
    }

    //List all users

    public function listAllUsers($conn, $name = null)
    {
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

    public function getLatestFiveUsers($conn)
    {
        $sql = "SELECT * FROM users ORDER BY userID  DESC LIMIT 5";
        $stmt = $conn->prepare($sql);
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
        $sql = "SELECT userID FROM users u WHERE u.approved = 1  AND u.userID = :userID";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);

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

    public function isBanned($conn)
    {
        $sql = "SELECT userID FROM users u WHERE u.banned = 1  AND u.userID = :userID";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);

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

    public function approveUser($conn)
    {
        if (!$this->isApproved($conn)) {
            $sql = "UPDATE users SET approved = 1 WHERE userID = :userID";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);
            try {
                $stmt->execute();
                dbClose($conn);
                return true;
            } catch (PDOException $e) {
                dbClose($conn);
                return "Approval failed: " . $e->getMessage();
            } catch (Exception $e) {
                dbClose($conn);
                return "Approval failed: " . $e->getMessage();
            }
        }
    }

    public function banningToggleUser($conn, $userID)
    {
        //Check if the user is banned
        //Not currently banned? Then let's ban them from the site
        if (!$this->isBanned($conn)) {
            $sql = "UPDATE users SET banned = 1 WHERE userID = :userID";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);
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

        } //Otherwise we can unban them from the site
        else if ($this->isBanned($conn)) {
            $sql = "UPDATE users SET banned = 0 WHERE userID = :userID";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);
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
    }


    //Checking boolean methods
    //Does the user exist
    public function doesExist($conn)
    {
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

    //No user with the same username
    public function doesUserNameExist($conn)
    {
        $sql = "SELECT username FROM users WHERE username = :username LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $this->getUsername(), PDO::PARAM_STR);
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

    //Validation


    //Login Function
    public function Login($userID, $password, $conn)
    {
        try {
            $sql = "SELECT userID, password from users WHERE userID =:userID";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userID', htmlentities($userID), PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll();
            $hash = $results[0]['password'];

            if (isset($results)) {
                if (password_verify($password, $hash)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Query failed: " . $e->getMessage();
        }
    }

}
