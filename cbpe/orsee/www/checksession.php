<?php

error_reporting( E_ALL );
ini_set( 'display_errors', 1);

if(!isset($_GET['action'])){
	die('Param is missing. Look at the <a href="https://gist.github.com/2995743">source</a>.');
}

switch($_GET['action']) {
	case "start":
		if(session_start() && $_SESSION['test']='test123'){
			echo 'Test session probably started successfully. Go <a href="/orsee/checksession.php?action=check">check</a>!<br />';
		}else{
			echo 'Starting a test session seems to have failed. Go <a href="/orsee/checksession.php?action=check">check</a>!<br />';
		}
		echo 'And some additional information:</br>';
		echo 'session.save_path : ' . ini_get('session.save_path') . '</br>';
		echo 'session.cookie_path : ' . ini_get('session.cookie_path') . '</br>';
		echo 'session.name : ' . ini_get('session.name') . '</br>'; 
		break;
	case "delete":
		$_SESSION = array();
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly'] );
		}
		session_destroy();
		echo "Test session deleted.";
		break;
	case "check":
		session_start();
		if(isset($_SESSION['test']) and  $_SESSION['test'] == 'test123'){
			echo "Sessions seem to work. :-)";
		}else{
			echo "Sessions don't seem to work. :-(";
		}
		break;
	default: die("Hey, I told you to look at the source, idiot. :P");
}