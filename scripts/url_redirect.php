<?php
include "../include/config.php";
include "../include/Urlshortner.php";
 
// How are you getting your short code?
 
// from framework or front controller using a URL format like
// http://.example.com/r/X4c
// $code = $uri_data[1];
 
// from the query string using a URL format like
// http://example.com/r?c=X4c where this file is index.php in the
// directory http_root/r/index.php
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