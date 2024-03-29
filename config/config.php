<?php
// Replace with your actual admin email (yeah just put your email here)
$GLOBALS['ACTUAL_ADMIN'] = "someEmail@domain.com"; //TODO: EDIT ME!

// host for database connection in relation to web host
$GLOBALS['DB_HOSTNAME'] = 'localhost';

// default destination for chat messages
$GLOBALS['DEFAULT_DESTINATION'] = '#chatRoom';

// default name decorator (prefix for names in chat)
$GLOBALS['NAME_DECORATOR'] = '';

// default chat room decorator (prefix for rooms in chat)
$GLOBALS['ROOM_DECORATOR'] = '';

// default PDO Attributes
$GLOBALS['PDO_ATTRIBS'] = array(PDO::ATTR_CURSOR=> PDO::CURSOR_FWDONLY);

// max_width, default value.
$GLOBALS['max_width'] = 1024;

// DB_USER_BASE - the base name for all database users and databases
//$GLOBALS['DB_USER_BASE'] = 'userBase';
$GLOBALS['DB_USER_BASE'] = '';   //if this doesn't apply to you, leave this blank 

// DB_USERNAME - username for this database
$GLOBALS['DB_USERNAME'] = 'webChatter'; //TODO: EDIT ME!

// DB_FULLUSER - Full database name (shouldn't change this unless your provider uses a different username scheme.
if (empty($GLOBALS['DB_USER_BASE'])) {
	$GLOBALS['DB_FULLUSER'] = $GLOBALS['DB_USERNAME'];	
} else {
	$GLOBALS['DB_FULLUSER'] = $GLOBALS['DB_USER_BASE'] . "_" . $GLOBALS['DB_USERNAME'];
}

// DB_PASSWORD - Password for the database.
$GLOBALS['DB_PASSWORD'] = ''; //TODO: EDIT ME!

// DB_SEL_NAME - Name of the database
$GLOBALS['DB_SEL_NAME'] = 'webChat';

// DB_NAME - Data Base Name, you should not change this unless you have a different naming convention.
if (empty($GLOBALS['DB_USER_BASE'])) {
	$GLOBALS['DB_NAME'] = $GLOBALS['DB_SEL_NAME'];
} else {
	$GLOBALS['DB_NAME'] = $GLOBALS['DB_USER_BASE'] . "_" . $GLOBALS['DB_SEL_NAME'];
}
// BUG_MAIL_NAME - bug email name
$GLOBALS['BUG_MAIL_NAME'] = 'DreamersNet Chat'; //TODO: EDIT ME!

// BUG_EMAIL - where bug emails should be sent from
$GLOBALS['BUG_EMAIL'] = 'bugs@domain.com'; //TODO: EDIT ME!

// CSS page
$GLOBALS['CSS'] = 'chat.css';

// AUTO_ADMIN_NAME - The Name that should show when automatic emails are generated
$GLOBALS['AUTO_ADMIN_NAME'] = 'AutoAdmin DOMAIN'; //TODO: EDIT ME!

// AUTO_ADMIN_EMAIL - The automatically generated emails from admin
$GLOBALS['AUTO_ADMIN_EMAIL'] = 'no-reply@domain.com'; //TODO: EDIT ME!

// BASE_FILE_UPLOAD_PATH - local file path for uploads
$GLOBALS['BASE_FILE_UPLOAD_PATH'] = '/home/chat/uploads'; //TODO: EDIT ME!

// BASE_USER_PATH - base path for user folder, should end with /
$GLOBALS['BASE_USER_PATH'] = 'users/';

//FQP - Fully Qualified Path
//Warning: This should not end with a / and it should start with https://
$GLOBALS['FQP'] = 'https://chatter.someplace.com'; //TODO: EDIT ME!

//Minimum User Level after registering.
$GLOBALS['MIN_USER_LEVEL_LISTED'] = 1;

//User Level Admin
$GLOBALS['USER_LEVEL_ADMIN'] = 100;


function errormail($email, $message, $errorInfo, $diemsg) {
	//send email with confirmation link
	$headers = "From: ". $GLOBALS['BUG_MAIL_NAME']. " <" . $GLOBALS['BUG_EMAIL'] .">";
	$subject = "Error for $email";
	$message .= "Additional information: $errorInfo \n "
	           ." no session variables here. \n   ";
	mail($GLOBALS['ACTUAL_ADMIN'],$subject,$message,$headers);
	echo '<link rel="stylesheet" type="text/css" href="'. $GLOBALS['CSS'] . '" />';
	die("$diemsg");
}

function connect_chatter() {
	$hostname = "localhost";
	$username = $GLOBALS['DB_FULLUSER'];
	$password = $GLOBALS['DB_PASSWORD'];
	$db = $GLOBALS['DB_NAME'];
	try {
		$dbh = new PDO("mysql:host=$hostname;dbname=$db", $username, $password);
		return $dbh;
  } catch(PDOException $e) {
    errormail($email, $e->getMessage(), "No info", $e->getMessage());
  }
}



?>
