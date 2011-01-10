<html>
<head>
	<title>s3xml + jQuery mobile</title>
</head>
<body>
<?php

    $bucketUrl = 'backups.xml';
    #$bucketUrl = 'https://s3.amazonaws.com/azizi.ilri.cgiar.org/';
    $bucketXml = file_get_contents($bucketUrl);
    $xmlObj = simplexml_load_string($bucketXml);

#   echo "<pre>\n";
#   print_r($xmlObj);
#   echo "</pre>\n";

    foreach ( $xmlObj->Contents as $content ) { 
        echo 'Name: ' . $content->Key . "<br />\n";
        echo 'Size: ' . $content->Size . "<br />\n";
        echo "<br />\n";
    }   
    
    echo count($xmlObj->Contents);
?>
</body>
</html>
