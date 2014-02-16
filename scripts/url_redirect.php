<?php
include "../include/config.php";
include "../include/Urlshortner.php";
 
$shorturl = $_GET["su"];
 
try 
{
    $pdo = new PDO("mysql:host=$host;dbname=$db",$user,$pass);
}
catch (PDOException $e) 
{
    trigger_error("Error: Failed to establish connection to database.");
    exit;
}
 
$shortUrl = new UrlShortner($pdo);

try 
{
    $url = $shortUrl->shorturlToUrl($shorturl);
    header("Location: " . $url);
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