<?php
  function get_time($t)
	{	
		$offset = preg_replace('/\\D/i',"",$t);
		if(eregi('m',$t))
		{
			return mktime(date("G"),date("i") - $offset, 0 ,date("m"),date("d"),date("Y"));
		}
		if(eregi('h',$t))
		{
			return mktime(date("G") - $offset,date("i"), 0 ,date("m"),date("d"),date("Y"));
		}
	}
	$uid = $_GET['id'];
	if(!isset($uid))
	{
		echo "UID Is NULL!!";
		die();
	}
	$url = "http://osu.ppy.sh/pages/include/profile-history.php?u=".$uid;
	$ch = curl_init($url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
	$cret = curl_exec($ch);
	$Httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($Httpcode <> '200')
	{
		echo "Http errcode:" . $Httpcode;
	}
	///   \d+\w+\s+-\s+<a\s.*?'>(.*)</a>\s+([\w,]+)\s+(\(\w\))
	$pat = "/(\\d+\\w+)\\s+-\\s+<a\\s.*?'>([\\w\\s\\-'\\[\\]\\(\\)\\+\\~\\!\\&\\%\\^\\$\\#\\@]+)\\s*<\/a>\\s*([\\d,]+)\\s*\\((\\w+)\\)\\s*<br\/>/i";
	$matchRet = preg_match_all($pat,$cret,$m);
	
	if(!$matchRet)
	{
		die();
	}
	//开始输出RSS
	echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
	echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:hcg="'.$url.'">'."\n";
	echo "<channel>\n";
	echo "<title> OSU! 24小时内的记录</title>\n";
	echo '<link>http://hcg.im/osu24.php?id='.$uid."</link>\n";
	echo "<description>Osu! 记录读取工具</description>\n";
	$item = 0;
	$SL = false;
	if(isset($_GET['s'])&& $_GET['s'] == 'on')
	{
		$SL = true;
	}
	while($item < count($m[2]))
	{
		if($SL && eregi('S',$m[4][$item]))
		{
			echo "<item>\n";
			echo '<title>'.$m[2][$item]."</title>\n";
			echo '<description>在'.$m[1][$item]."前,玩过". $m[2][$item].' 且获得: '.$m[4][$item] . '级,分数为: '. $m[3][$item] . "</description>\n";
			echo '<guid>'.md5($m[2][$item].$m[4][$item].$m[3][$item])."</guid>\n";
			echo '<pubdate>' . date("D,j Y G:i:s T",get_time($m[1][$item])) ."</pubdate>\n";
			echo "</item>\n";
		}
		else
		{
			if(!$SL)
			{
				echo "<item>\n";
				echo '<title>'.$m[2][$item]."</title>\n";
				echo '<description>在'.$m[1][$item]."前,玩过". $m[2][$item].' 且获得: '.$m[4][$item] . '级,分数为: '. $m[3][$item] . "</description>\n";
				echo '<guid>'.md5($m[2][$item].$m[4][$item].$m[3][$item])."</guid>\n";
				echo '<pubdate>' . date("D,j Y G:i:s T",get_time($m[1][$item])) ."</pubdate>\n";
				echo "</item>\n";
			}
		}
		$item++;
	}
	echo "</channel>\n";
	echo "</rss>\n";
?>
