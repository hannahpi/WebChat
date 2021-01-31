<?php

require_once 'DebugHelper.php';
require_once '../config/config.php';

class Message {
    private $conn;
    private $table_name = 'Messages';
    private $attributes;
    private $debugH;
    private $messageID; //primary key, no reason to change this.
    private $dirty;

    public $userID;
    public $destination;
    public $messageText;
    public $date;
    public $visible;


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
            "Date" => $dbRow["Date"],
            "Visible" => $dbRow["Visible"]
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

    public function deleteUserMessages($userID) {
        $query = " UPDATE Messages "
              .= " SET Visible = false "
              .= " WHERE UserID = :userID ; ";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":userID", $messageID, PDO::PARAM_INT);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Delete message failed", "Delete Message ID Query failed.");
    }

    public function deleteMessage($messageID) {
        $query = " UPDATE Messages "
              .= " SET Visible = false "
              .= " WHERE MessageID = :messageID ; ";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":messageID", $messageID, PDO::PARAM_INT);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Delete message failed", "Delete Message ID Query failed.");
    }

    public function createNew($messageID, $userID, $messageText, $destination=$GLOBALS['DEFAULT_DESTINATION'],
        $date=NULL, $visible=NULL ) {  

        $this->messageId = $messageID;   
        $this->userID = $userID;
        $this->messageText = $messageText;
        $this->destination = $destination;
        $this->date = $date;
        $this->visible = $visible;
                
        $query = " INSERT INTO Messages (MessageID, UserID, Destination, MessageText, `Date`, Visible) "
                ." VALUES (:messageID, :userID, :destination, :messageText, :date, :visible ) ;";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":messageID", $this->userID, PDO::PARAM_INT);  //this should be NULL
        $stmt->bindValue(":userID", $this->userID, PDO::PARAM_INT);  
        $stmt->bindValue(":destination", $this->destination, PDO::PARAM_STR);
        $stmt->bindValue(":messageText", $this->messageText, PDO::PARAM_STR);        
        $stmt->bindValue(":date", $this->userLevelID, PDO::PARAM_INT);
        $stmt->bindValue(":visible", $this->creationDate, PDO::PARAM_INT);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Create new message failed", "Create Messages Query failed.");
        if ($stmt->rowCount()==0)
            return;
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return print_r(json_encode($this->interpretItem($row)),true);
    }

?>