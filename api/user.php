<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once '../config/config.php';
require_once '../Classes/DebugHelper.php';
require_once '../Classes/User.php';
require_once '../Classes/Database.php';

$database = new Database();
$conn = $database->getConnection();
$attributes = $database->getAttributes();

$myUser = new User($conn, $attributes);
if (isset($_SESSION['Email'])) {
    $myUser->get($_SESSION['Email'], false);
}
$method = $_SERVER['REQUEST_METHOD'];
$request = file_get_contents('php://input');
$debugH = new DebugHelper(true);
$debugH->addObject($method);
$debugH->addObject($request);
$debugH->addObject($myUser);
$debugH->setTesting(true);  //TODO: please remove

function isUserMe($myUser, $otherUser) {
    return ($myUser->email == $otherUser->email) ;
}

function update_user($conn, $attributes, $request) {    
    $user = new User($conn, $attributes);
    $callingUser = new User($conn, $attributes);
    if (isset($_SESSION['Email'])) {
        $callingUser->get($_SESSION['Email'], false);
    }
    $userInfo = json_decode(strip_tags($request),true);

    if ($userInfo["UserID"]) {
        $user->getByID($userInfo["UserID"]);
        $user->setEmail($userInfo["Email"]);
    } else if ($userInfo["Email"]) {
        $user->get($userInfo["Email"],false);
    }

    //if Calling User is self or calling user is an admin and target user is not an admin, perform the update
    if (isUserMe($callingUser, $user) || ($callingUser->isUserAdmin() && !$user->isUserAdmin())) {
        if (isset($userInfo["Nickname"])) 
            $user->setNickname($userInfo["Nickname"]);
        if (isset($userInfo["FirstName"])) 
            $user->setFirstName($userInfo["FirstName"]);
        if (isset($userInfo["LastName"])) 
            $user->setLastName($userInfo["LastName"]);
        if (isset($userInfo["Password"])) 
            $user->setPassword($userInfo["Password"]);
        if (isset($userInfo["CreationDate"]))
            $user->setCreationDate($userInfo["CreationDate"]);
        
        //we should be updating the user level.
        if (isset($userInfo["UserLevelID"])) {
            $user->setUserLevelID($userInfo["UserLevelID"]); 
        } else {
            //if the user level is lower than minimum for registered/verified user-change it.
            if ($user["UserLevelID"] < $GLOBALS['MIN_USER_LEVEL_LISTED']) {
                $user->setUserLevelID($GLOBALS['MIN_USER_LEVEL_LISTED']);
            }
        } 
    }
    
    $user->updateDB();
    print_r($user);
}

function add_user($conn, $attributes, $request) {
    $user = new User($conn, $attributes);    
    
    $userInfo = json_decode(strip_tags($request),true);
    $nickname = NULL;
    $firstName = NULL;
    $lastName = NULL;
    $email = NULL;
    if (isset($userInfo["Email"]))
        $email = $userInfo["Email"];
    if (isset($userInfo["Nickname"]))
        $nickname = $userInfo["Nickname"];
    if (isset($userInfo["FirstName"]))
        $firstName = $userInfo["FirstName"];
    if (isset($userInfo["LastName"]))
        $lastName = $userInfo["LastName"];
    $user->createNew($email, $nickname, $lastName, $firstName);
    
    print_r(json_encode($user));
}

function get_user($conn, $attributes, $request) {
    $user = new User($conn, $attributes);
    switch (strtolower($request[0])) {
        case "email":
            $rEmail = strip_tags($request[1]);
            break;
        case "getall":
            print_r($user->getAllJson());
            return;
        default:
            $rID = strip_tags($request[0]);
            break;
    }
    if (isset($rID)) {
        echo $user->getByID($rID, true);
    } else if (isset($rEmail)) {
        echo $user->get($rEmail);
    }
    echo "request:";
    print_r($request);
}

switch ($method) {
    case 'PUT':
        update_user($conn, $attributes, $request);
        break;
    case 'POST':
        add_user($conn, $attributes, $request);
        break;
    case 'GET': 
        $request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
        get_user($conn, $attributes, $request);
        break;
    default:
        print_r(json_encode(array("message" => "Invalid method received")));
        $debugH->errormail($myUser->email, "API Call to user", "Invalid API call");
}

 ?>
