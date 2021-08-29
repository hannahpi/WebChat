<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once '../Classes/DebugHelper.php';
require_once '../Classes/User.php';
require_once '../Classes/Session.php';
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

function login_user($conn, $attributes, $request) {    
    $user = new User($conn, $attributes);
    $userInfo = json_decode(strip_tags($request),true);

    if (($userInfo["Email"]) && ($userInfo["Password"])) {
        $user = $user->login_user($userInfo);
        $session = new Session($conn, $attributes);
        $session->create_new($user->userID);
        $_SESSION['SessionObject'] = $session;     
    }

    if (isUserMe($myUser, $user) || $user->isUserAdmin()) {
        if (isset($userInfo["Nickname"])) 
            $user->setNickname($userInfo["Nickname"]);
        if (isset($userInfo["FirstName"])) 
            $user->setFirstName($userInfo["FirstName"]);
        if (isset($userInfo["LastName"])) 
            $user->setLastName($userInfo["LastName"]);
        if (isset($userInfo["Password"])) 
            $user->setPassword($userInfo["Password"]);
    }
    //if admin
    if ($user->isUserAdmin()) {
        if (isset($userInfo["CreationDate"]))
            $user->setCreationDate($userInfo["CreationDate"]);
        if (isset($userInfo["UserLevelID"]))
            $user->setUserLevelID($userInfo["UserLevelID"]);    
    }
    $user->updateDB();
    print_r($request);
}

function logout_user($conn, $attributes, $request) {
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



switch ($method) {
    case 'DELETE':
        logout_user($conn, $attributes, $request);
        break;
    case 'POST':
        login_user($conn, $attributes, $request);
        break;
    default:
        print_r(json_encode(array("message" => "Invalid method received")));
        $debugH->errormail($myUser->email, "API Call to user", "Invalid API call");
}

 ?>
