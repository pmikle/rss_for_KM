<?
	$action = $_POST['action'];
	$nid = $_POST['nid'];
	
	if (isset($action) and isset($nid)) {
		
		require_once("classes/MainClass.php");
		$MainClass = new MainClass();
		
		$res = $MainClass->GSV("select nid from news where nid = {$nid}");
		
		if ($res and $action = "restart") {
			if ($MainClass->GSV("update news set likes = 0 where nid = {$nid} returning nid")) {
				echo "News id = {$nid}. Likes restart successful.";
			}
		} else {
			echo "News id = {$nid}. Not found.";
		}
	}