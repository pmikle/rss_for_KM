<?
	header("Content-Type: text/html; charset=utf-8");
	
	$val_start = $_GET['val_start'];
	$val_end = $_GET['val_end'];
	$rss_channel = $_GET['rss_channel'];
	$nid = $_GET['nid'];
	$update = $_GET['update'];
	
	require_once("classes/MainClass.php");
	
	$MainClass = new MainClass();
	
	if ($val_start or $val_end or $rss_channel) {
		
		$str = " where 1=1";
		
		if ($rss_channel) {
			$rss_channel = $MainClass->security_query($rss_channel);
			$str .= " and c.chid = {$rss_channel}";
		}
		
		if ($val_start and $val_end) {
			$val_start = date_parse_from_format("Y/m/d", $val_start);
			$val_end = date_parse_from_format("Y/m/d", $val_end);
			if ($val_start['month']<10) $val_start['month'] = "0".$val_start['month'];
			if ($val_start['day']<10) $val_start['day'] = "0".$val_start['day'];
			if ($val_end['month']<10) $val_end['month'] = "0".$val_end['month'];
			if ($val_end['day']<10) $val_end['day'] = "0".$val_end['day'];
			$val_start = "{$val_start['year']}-{$val_start['month']}-{$val_start['day']}";
			$val_end = "{$val_end['year']}-{$val_end['month']}-{$val_end['day']}";
			$val_start = $MainClass->security_query($val_start);
			$val_end = $MainClass->security_query($val_end);
			$str .= " and date_trunc('day', \"pubDate\") >= date '{$val_start}' and date_trunc('day', \"pubDate\") <= date '{$val_end}'";
			$MainClass->show_rss($str);
		}
		
	} 
	if ($nid) {
		$nid = $MainClass->security_query($nid);
		$sql = "
			update news
				set likes = likes + 1
					where nid = {$nid}
						returning likes";
		$likes = $MainClass->GSV($sql);
		$n = array("nid" => $nid, "likes" => $likes);
		echo json_encode($n);
	}
	if ($update) {
		$MainClass->show_rss($str);
	}