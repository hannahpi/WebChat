<?php

require_once '../config/config.php';

class DebugHelper {
    private $trackedObjects;
    private $debugMode;
    private $messages;

    public function addObject($obj) {
        if (!empty($this->trackedObjects)) {
            array_push($this->trackedObjects, $obj);
        } else {
            $this->trackedObjects = array($obj);
        }
    }

    public function addMessage($msg) {
        if (!empty($this->messages)) {
            array_push($this->messages, $msg);
        } else {
            $this->messages = array($msg);
        }
    }

    public function clearMessages($msg) {
        $this->$messages = array();
    }

    public function setTesting($debugMode=false) {
        $this->debugMode = $debugMode;
    }

    public function errormail($userEmail, $adminMessage, $userDieMessage) {
        $headers = "From: ". $GLOBALS['BUG_MAIL_NAME']. " <" . $GLOBALS['BUG_EMAIL'] .">";
        $subject = "Error for $userEmail";
        $errorInfo = print_r($this->trackedObjects, true);
        $adminMessage .= "\nDebug Helper was tracking these objects: \n $errorInfo \n ";
        if (!empty($this->messages)) {
            $adminMessage .= "\nMessages added: " . print_r($this->messages, true);
        }
        mail($GLOBALS['ACTUAL_ADMIN'],$subject,$adminMessage,$headers);
        if ($this->debugMode)
            die("Debug Mode is on.  $adminMessage");
        else
            die("$userDieMessage");
    }

    public function __construct($debugMode=false) {
        $this->debugMode = $debugMode;
    }
}


?>
