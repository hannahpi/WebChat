<?php

require_once 'DebugHelper.php';
require_once '../config/config.php';

class Message {
    private $conn;
    private $table_name = 'Message';
    private $attributes;
    private $debugH;
    private $messageID; //primary key, no reason to change this.
    private $dirty;

    public $userID;
    public $destination;
    public $messageText;
    public $date;


    /**
     * function: interpretItem
     * purpose: converts extracted data from db to an array.
     */
    private function interpretItem($dbRow) {
        $dbMessage = array(
            "MessageID" => $dbRow["MessageID"],
            "UserID" => $dbRow["UserID"],
            "Destination" => $dbRow["Destination"],
            "MessageText" => $dbRow["MessageText"],
            "Date" => $dbRow["Date"]
        );
        return $dbMessage;
    }

    public function __construct($conn, $attributes) {
        $this->attributes = $attributes;
        $this->conn = $conn;
        $this->debugH = new DebugHelper();
        $this->debugH->addObject($this);
        $this->dirty = false;
    }

    public function createNew($userID, $messageText, $destination=$GLOBALS['DEFAULT_DESTINATION'],
        $userLevelID=NULL, $userID=NULL, $creationDate=NULL ) {  //password is generated

        $this->email = $email;
        $this->displayName = $displayName;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        
        $this->userID = $userID;
        $this->creationDate = $creationDate;

        $query = " INSERT INTO User (UserID, Email, DisplayName, FirstName, LastName, Password, "
                ." PassVer, UserLevelID, CreationDate) "
                ." VALUES (:userID, :email, :displayName, :firstName, :lastName, :password, "
                ." NULL, :userLevelID, :creationDate ); ";

        $passGen = chr(random_int(33,126)); //generate random ascii sequence
        for ($i=1; $i<15; $i++) {
            $passGen .= chr(random_int(33,126));
        }

        //send email with confirmation link
		$headers = "From: " . $GLOBALS['AUTO_ADMIN_NAME'] . " " . $GLOBALS['AUTO_ADMIN_EMAIL'];
		$subject = "Confirm your email address";
		$message = "Please confirm your email address at ". $GLOBALS['FQP'] . "/verifyemail.html?confirmNum=$passGen&Email=$email \n"
		         . "If you have problems you may go back to ". $GLOBALS['FQP'] . "/getconfirm.html and try again!";
		mail($email,$subject,$message,$headers);
        $passGen = crypt($passGen);

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":userID", $this->userID, PDO::PARAM_INT);  //this should be NULL
        $stmt->bindValue(":email", $this->email, PDO::PARAM_STR);
        $stmt->bindValue(":displayName", $this->displayName, PDO::PARAM_STR);
        $stmt->bindValue(":firstName", $this->firstName, PDO::PARAM_STR);
        $stmt->bindValue(":lastName", $this->lastName, PDO::PARAM_STR);
        $stmt->bindValue(":password", $passGen, PDO::PARAM_STR);
        $stmt->bindValue(":userLevelID", $this->userLevelID, PDO::PARAM_INT);
        $stmt->bindValue(":creationDate", $this->creationDate, PDO::PARAM_INT);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Create new user failed", "Create User Query failed.");
        if ($stmt->rowCount()==0)
            return;
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return print_r(json_encode($this->interpretItem($row)),true);
    }

?>