<?
	header("Content-Type: text/html; charset=utf-8");
	
	$name = $_GET['name'];
	
	require_once("classes/MainClass.php");
	
	$MainClass = new MainClass();
	
	if ($name) {
		
		switch ($name) {
			case 'channel':
				$val = $_GET['val'];
				if ($val) {
					$val = $MainClass->security_query($val);
					$str = " where c.chid = {$val}";
				} else $val = "";
				$MainClass->show_rss($str);
				
				break;
			case 'date':
				$val_start = $_GET['val_start'];
				$val_end = $_GET['val_end'];
				if ($val_start and $val_end) {
					$val_start = date_parse_from_format("m/d/Y", $val_start);
					$val_end = date_parse_from_format("m/d/Y", $val_end);
					if ($val_start['month']<10) $val_start['month'] = "0".$val_start['month'];
					if ($val_start['day']<10) $val_start['day'] = "0".$val_start['day'];
					if ($val_end['month']<10) $val_end['month'] = "0".$val_end['month'];
					if ($val_end['day']<10) $val_end['day'] = "0".$val_end['day'];
					$val_start = "{$val_start['year']}-{$val_start['month']}-{$val_start['day']}";
					$val_end = "{$val_end['year']}-{$val_end['month']}-{$val_end['day']}";
					$val_start = $MainClass->security_query($val_start);
					$val_end = $MainClass->security_query($val_end);
					$str = " where date_trunc('day', \"pubDate\") >= date '{$val_start}' and date_trunc('day', \"pubDate\") <= date '{$val_end}'";
					$MainClass->show_rss($str);
				}
				
				
				
				break;
		}
		
	}