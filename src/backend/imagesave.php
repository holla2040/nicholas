<?php
    $rawData = $_POST['imgBase64'];
    $filteredData = explode(',', $rawData);
    $unencoded = base64_decode($filteredData[1]);

    $datime = date("Y-m-d-H.i.s", time() ) ; # - 3600*7

    $userid  = $_POST['userid'] ;
    $imagefile  = $_POST['imagefile'] ;

    // name & save the image file 
    $fp = fopen('inventoryimages/'.$imagefile, 'w');
    fwrite($fp, $unencoded);
    fclose($fp);
?>
