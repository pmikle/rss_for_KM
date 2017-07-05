<?
	class MainClass {
	
		private $conn = NULL;
		
		public function query($query) {
			
			$result = pg_query($this->conn, $query);
			if (!$result) {
				return NULL;
			} else {
				return $result;
			}
		
		}
		
		public function GSV ($query){
		
			$result = pg_query($this->conn, $query);
			if (!$result) {
				return NULL;
			} else {
				$result = pg_fetch_array($result);
				return $result[0];
			}		
		}
		
		private function dbconnect() {
		
			require_once("configuration.php");
			return $dbconn3 = pg_connect("host={$host} dbname={$dbname} user={$user} password={$password}");
			
		}
		
		public function print_r_pre ($array, $it=0) {
			$it++;
			foreach ($array as $key => $value) {
				for ($i=0; $i<$it; $i++) { echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; }
				if (gettype($array[$key])<>"array") {
					echo "[$key] -> $value<br>";
				}
				else {
					echo "[$key] -> <br>";
					$this->print_r_pre ($array[$key], $it);
				}
			}
		}
		
		private function update_rss() {
			
			$r = $this->query("select chid, name, url, link from rss_channels");
			while ($row = pg_fetch_assoc($r)) {

				$url = $row['url'];
				$chid = $row['chid'];
				
				$xmlstring = file_get_contents($url);
				$xml = simplexml_load_string($xmlstring);
			
				foreach ($xml->channel->item as $key => $val) {
					
					if ( !$this->GSV("select nid from news where chid = {$row[chid]} and \"pubDate\" = '{$val->pubDate}' and title = '{$val->title}'") ) {
						$sql = "INSERT INTO news 
						(chid, title, link, description, \"pubDate\", likes)
						VALUES ({$row[chid]}, '{$val->title}', '{$val->link}', '{$val->description}', '{$val->pubDate}', 0)";
						$this->query($sql);
					}
				}
			}
		}
		
		private function regular_update_rss() {
			
			$r = $this->query("select chid, name, url, link from rss_channels");
			$i = 0;
			while ($row = pg_fetch_assoc($r)) {

				$url = $row['url'];
				$chid = $row['chid'];
				
				$re = '/<item>.*?\s*\n*<title>(.*?)<\/title>.*?\s*\n*<link>(.*?)<\/link>.*?\s*\n*<description>(.*?)<\/description>.*?\s*\n*<pubDate>(.*?)<\/pubDate>/';
				$str = file_get_contents($url);
				preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
				
				$title = $matches[$i][1];
				$link = $matches[$i][2];
				$description = $matches[$i][3];
				$pubDate = $matches[$i][4];

				foreach ($matches as $key => $val) {
					
					if ( !$this->GSV("select nid from news where chid = {$row[chid]} and \"pubDate\" = '{$pubDate}' and title = '{$title}'") ) {
						$sql = "INSERT INTO news 
						(chid, title, link, description, \"pubDate\", likes)
						VALUES ({$row[chid]}, '{$title}', '{$link}', '{$description}', '{$pubDate}', 0)";
						$this->query($sql);
					}
				}
				
				$i++;
			}
		}
		public function show_rss ($str="") {
			$s = $this->query("
				select 
					row_number() over() as rn
					, * from (
						select 
							CASE
								WHEN likes>=10 THEN '<img src=\'css/images/cub.png\' class=\'likes\'>'
								ELSE '<span name=\"lp\" nid=\"'|| nid ||'\">' || likes || '<img src=\"css/images/like.png\" class=\"likes cll\" nid=\"' || nid || '\"></span>'
							END as lk
							, *
						from news as n join rss_channels as c using(chid){$str} order by likes desc, \"pubDate\"
					) as t");
			?><table class="table">
				<tr>
					<td>№</td>
					<td>Канал</td>
					<td>Новость</td>
					<td>Дата</td>
					<td>Рейтинг</td>
				</tr>
			<?
			while ($r = pg_fetch_assoc($s)) {
				?>
				<tr>
					<td><?=$r['rn']?>.</td>
					<td><?=$r['name']?></td>
					<td><?=$r['title']?></td>
					<td><?=$r['pubDate']?></td>
					<td><?=$r['lk']?></td>
				<tr>
				<?

			}
			?></table><?
		}
		
		public function show_filter_by_channel() {
			$s = $this->query("select chid, name from rss_channels");
			?>
			<div id="channels_filter"> Источник новостей:
				<select name="channel">
					<option value="">все</option><?
				while ($r = pg_fetch_assoc($s)) {
					?><option value="<?=$r['chid']?>"><?=$r['name']?></option><?
				}
				?></select>
			</div><?
		}
		
		public function show_filter_by_date() {
			?>
			<div id="dates_filter">
				<div id="date_range"></div>
				<input name="startDate">
				<input name="endDate">
				<span id="dater">выбрать</span>
			</div><?
		}
		
		public function security_query($str) {
		
			/* Здесь можно много чего придумать */
			return $str =  htmlspecialchars($str);
			
		}
		
		function __construct() {
			
			$this->conn = $this->dbconnect();
			
			//$this->update_rss();
			
			$this->regular_update_rss();

		}
	
	
	
	
	}