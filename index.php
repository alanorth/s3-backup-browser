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
#        echo 'Name: ' . $content->Key . "<br />\n";
#        echo 'Size: ' . $content->Size . "<br />\n";
#        echo "<br />\n";
		# check if the current file ("Key" in Amazon's S3 XML) is daily, weekly, etc
		# example: //mysql/monthly/azizi/azizi_2010-09-01_03h15m.September.azizi.sql.gz
		if(preg_match('/^mysql\/daily/i',$content->Key)) {
			# grab the dbname from the Key
			list(,,$dbname,$filename) = explode('/',$content->Key); // is there a better way to extract
															// only one or two elements from an array?
															// like in perl's array("1","2")[0]?
			$size = (string) $content->Size; # cast Size to a string, otherwise it's still a SimpleXML object
#			$lastmodified = $content->LastModified;
			if(!array_key_exists("$dbname",$daily)) {
				#array_push($daily,"$dbname"=>array());
				$daily["$dbname"] = array();
			}
			array_push($daily["$dbname"],array('size'=>$size));
		}
		else if(preg_match('/^mysql\/weekly/i',$content->Key)) {
			list(,,$dbname,) = explode('/',$content->Key);
			if(!array_key_exists("$dbname",$weekly)) {
				$weekly["$dbname"] = array();
			}
			array_push($weekly["$dbname"],array('size'=>$size));
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
					<li>avid_portal
						<ul>
							<li>avid_portal_2010-12-13_03h15m.Monday.sql.gz</li>
							<li>avid_portal_2010-12-14_03h15m.Tuesday.sql.gz</li>
							<li>avid_portal_2010-12-15_03h15m.Wednesday.sql.gz</li>
						</ul>
					</li>
				</ul>
			</li>
			<li>Weekly
				<ul>
					<li>avid_portal
						<ul>
							<li>avid_portal_week.01.2011-01-08_03h15m.sql.gz</li>
							<li>avid_portal_week.32.2010-08-14_13h59m.sql.gz</li>
						</ul>
					</li>
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

<?
//	echo "<pre>";
//	print_r($daily);
//	echo "</pre>";
//	echo "<pre><h2>weekly</h2>";
//	print_r($weekly);
//	echo "</pre>";

    //echo count($xmlObj->Contents);
?>
</body>
</html>
