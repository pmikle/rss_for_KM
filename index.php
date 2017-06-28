<?
	
	header("Content-Type: text/html; charset=utf-8");
	
	require_once("classes/MainClass.php");
	
	$MainClass = new MainClass();
	
	require_once("tpl/main.html");