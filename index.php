<!DOCTYPE html>
<html>
<head>
	<title>s3xml + jQuery mobile</title>
	<link rel="stylesheet" href="jquery.mobile-1.0a2.min.css" />
	<script src="jquery-1.4.4.min.js"></script>
	<script src="jquery.mobile-1.0a2.min.js"></script>
</head>
<body>
<?php

    $bucketUrl = 'backups.xml';
    #$bucketUrl = 'https://s3.amazonaws.com/azizi.ilri.cgiar.org/';
    $bucketXml = file_get_contents($bucketUrl);
    $xmlObj = simplexml_load_string($bucketXml);

	$daily = array();
	$weekly = array();
	$monthly = array();

    foreach ( $xmlObj->Contents as $content ) { 
		# check if the current file ("Key" in Amazon's S3 XML) is daily, weekly, etc
		# example: //mysql/monthly/azizi/azizi_2010-09-01_03h15m.September.azizi.sql.gz
		if(preg_match('/^mysql\/daily/i',$content->Key)) {
			# grab the dbname from the Key
			list(,,$dbname,$filename) = explode('/',$content->Key); // is there a better way to extract
															// only one or two elements from an array?
															// like in perl's array("1","2")[0]?
			$size = (string) $content->Size; # cast Size to a string, otherwise it's still a SimpleXML object
			$filename = (string) $content->Key;
#			$lastmodified = $content->LastModified;
			if(!array_key_exists("$dbname",$daily)) {
				$daily["$dbname"] = array();
			}
			array_push($daily["$dbname"],array('size'=>$size,'filename'=>$filename));
		}
		else if(preg_match('/^mysql\/weekly/i',$content->Key)) {
			list(,,$dbname,) = explode('/',$content->Key);
			$filename = (string) $content->Key;
			if(!array_key_exists("$dbname",$weekly)) {
				$weekly["$dbname"] = array();
			}
			array_push($weekly["$dbname"],array('size'=>$size,'filename'=>$filename));
		}
		else if(preg_match('/^mysql\/monthly/i',$content->Key)) {
			list(,,$dbname,) = explode('/',$content->Key);
			if(!array_key_exists("$dbname",$monthly)) {
				$monthly["$dbname"] = array();
			}
			array_push($monthly["$dbname"],array('size'=>$size));
		}
    }   
    
?>
<div data-role="page">
	<div data-role="header">
		<h1>Amazon S3 Backups</h1>
	</div><!-- /header -->
		<ul data-role="listview">
			<li>Daily
				<ul>
<?
	# cycle through the backups and create the structure of our nested list for jQuery Mobile
	while($daily_backup = current($daily)) {
		echo "				<li>".key($daily)."\n";
		echo "					<ul>\n";
		foreach($daily_backup as $backup) {
			echo "						<li>".$backup['filename']."</li>\n";
		}
		echo "				</ul>\n";
		echo "			</li>\n";
		next($daily);
	}
?>
				</ul>
			</li>
			<li>Weekly
				<ul>
<?
	while($weekly_backup = current($weekly)) {
		echo "				<li>".key($weekly)."\n";
		echo "					<ul>\n";
		foreach($weekly_backup as $backup) {
			echo "						<li>".$backup['filename']."</li>\n";
		}
		echo "				</ul>\n";
		echo "			</li>\n";
		next($weekly);
	}
?>
				</ul>
			</li>
			<li>Monthly
				<ul>
					<li>avid_portal
						<ul>
							<li>monthly1</li>
							<li>monthly2</li>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
</div>

</body>

</html>
