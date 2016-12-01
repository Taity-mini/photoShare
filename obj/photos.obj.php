<?php

/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 24/11/2016
 * Time: 14:27
 */
class photos
{
    private $photoID, $userID, $albumID, $filePath, $title, $description, $price;


    //Constructor
    function __construct($photoID = -1)
    {
        $this->photoID = $photoID;
    }

    //Getters

    public function getPhotoID()
    {
        return $this->photoID;
    }

    public function getUserID()
    {
        return $this->userID;
    }

    public function getAlbumID()
    {
        return $this->albumID;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPrice()
    {
        return $this->price;
    }

    //Setters


    public function setPhotoID($photoID)
    {
        $this->photoID = htmlentities($photoID);
    }

    public function setUserID($userID)
    {
        $this->userID = htmlentities($userID);
    }

    public function setAlbumID($albumID)
    {
        $this->albumID = htmlentities($albumID);
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    public function setTitle($title)
    {
        $this->title = htmlentities($title);
    }

    public function setDescription($description)
    {
        $this->description = htmlentities($description);
    }

    public function setPrice($price)
    {
        $this->price = htmlentities($price);
    }


    //Main Methods


    public function getAllDetails($conn)
    {
        $sql = "SELECT * FROM photos WHERE photoID = :photoID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':photoID', $this->getPhotoID(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as $row) {
                $this->setAlbumID($row['albumID']);
                $this->setUserID($row["userID"]);
                $this->setFilePath($row['filePath']);
                $this->setTitle($row['title']);
                $this->setDescription($row['description']);
                $this->setPrice($row['price']);
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
            $sql = "INSERT into photos VALUES (null,:userID, :albumID,  :filePath, :title , :photoDescription, :price)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userID', $this->getUserID(), PDO::PARAM_STR);
            $stmt->bindParam(':albumID', $this->getAlbumID(), PDO::PARAM_STR);
            $stmt->bindParam(':filePath', $this->getFilePath(), PDO::PARAM_STR);
            $stmt->bindParam(':title', $this->getTitle(), PDO::PARAM_STR);
            $stmt->bindParam(':photoDescription', $this->getDescription(), PDO::PARAM_STR);
            $stmt->bindParam(':price', $this->getPrice(), PDO::PARAM_STR);
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

    //Create Photo entry

    public function uploadPhoto()
    {
        $target_dir = "../upload/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
            return false;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 50000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
            return false;
        }
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
            return false;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            return false;
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $this->setFilePath($target_file);
                return true;

                echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
                return false;
            }
        }
    }


    public function getExifData()
    {
        $exif = exif_read_data($this->getFilePath(), 'IFD0');
        echo $exif === false ? "No header data found.<br />\n" : "Image contains headers<br />\n";
        $exif = exif_read_data($this->getFilePath(), 0, true);
        foreach ($exif as $key => $section) {
            foreach ($section as $name => $val) {
                echo "$key.$name: $val<br />\n";
            }
        }
    }


    public function doesExist($conn)
    {
        $sql = "SELECT photoID FROM photos WHERE photoID = :photoID LIMIT 1";

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

    public function listPhotoAlbum($conn)
    {
        $sql = "SELECT * FROM photos where albumID = :albumID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':albumID', $this->getAlbumID(), PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        } catch (PDOException $e) {
            return "Database query failed: " . $e->getMessage();
        }
    }

    public function getLatestPhoto($conn)
    {
        $sql = "SELECT filePath FROM photos WHERE albumID= :albumID ORDER BY photoID  DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':albumID', $this->getAlbumID(), PDO::PARAM_STR);
        try {
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as $row) {
                $this->setFilePath($row['filePath']);
            }
            return true;
        } catch (PDOException $e) {
            return "Database query failed: " . $e->getMessage();
        }
    }

    public function getLatestFivePhotos($conn)
    {
        $sql = "SELECT * FROM photos ORDER BY photoID  DESC LIMIT 5";
        $stmt = $conn->prepare($sql);
        //$stmt->bindParam(':albumID', $this->getAlbumID(), PDO::PARAM_STR);
        try {
            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        } catch (PDOException $e) {
            return "Database query failed: " . $e->getMessage();
        }
    }

}