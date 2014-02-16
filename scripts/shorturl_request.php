<?php
include "../include/config.php";
include "../include/urlshortner.php";
 
try 
{
    $pdo = new PDO("mysql:host=$host;dbname=$db",$user,$pass);
}
catch (PDOException $e) 
{
    trigger_error("Error: Failed to establish connection to database.");
    exit;
}
 
$UrlShortner = new UrlShortner($pdo);

try 
{
    $shorturl = $UrlShortner->urlToShortUrl($_POST["url"]);
    printf('<p><strong>Short URL:</strong> <a href="%s">%1$s</a></p>',
        SHORTURL_PREFIX . $shorturl);
	session_start();
	$_SESSION['shorturl']=$shorturl;
	header("location: ../views/index.php");
	
    exit;
}
catch (Exception $e) 
{
	session_start();
	$_SESSION['error']=$e->getMessage();
	header("location: ../views/index.php");
	exit;
}
?>