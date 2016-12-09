<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        define(DBHOST,"localhost");
        define(DBUSER,"inventory");
        define(DBPASS,"inventory7");
        define(DB,    "inventory");

        if (strlen($_POST['image'])) {
            $imagefileparts = explode("/",$_POST['image']);
            $fn = urldecode(end($imagefileparts)); 
            file_put_contents("/tmp/out.txt",$fn);
            file_put_contents("inventoryimages/".$fn, fopen($_POST['image'], 'r'));
        } else {
            $fn = "icon_octopart_blank.png";
        }

        $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
        $query = 'INSERT INTO electronicparts (quantity,manufacturer,partnumber,description,distributor,distributorsku,distributorurl,octoparturl,location,datasheeturl,photo,image) values ('.$_POST['quantity'].',"'.$_POST['manufacturer'].'","'.$_POST['partnumber'].'","'.$_POST['description'].'","'.$_POST['distributor'].'","'.$_POST['distributorsku'].'","'.$_POST['distributorurl'].'","'.$_POST['octoparturl'].'","'.$_POST['location'].'","'.$_POST['datasheeturl'].'","'.$_POST['photo'].'","'.$fn.'")';
        $result = $db->query($query); 
        // echo $query;
    };
?><html>
<meta charset="utf-8"/>
<script src="js/jquery.js"></script>
<link rel='stylesheet' href='css/entry.css'>
<script src='js/dateFormat.js'></script>
<style>
</style>

<body>
<div style='float:right'><a href='search.php'>Search</a></div>
<table>
    <tr><td class='label'>Barcode or PN</td><td><input id='v' onchange='search(this.value)'/></td></tr>
    <tr><td class='label'><button type='button' id="snap">Capture</button></td><td><video id="video" autoplay width="640" height="480"></video>&nbsp;<canvas id="preview"  width="640" height="480"></canvas>
</table>
<hr>
<form action='entry.php' method='post' onsubmit="return validateForm()">
<table>
    <tr><td class='label'>Part Number</td><td><input    name='partnumber' id='partnumber'></td></tr>
    <tr><td class='label'>Quantity</td><td><input       name='quantity'   id='quantity'></td></tr>
    <tr><td class='label'>Location</td><td><input       name='location'   id='location'></td></tr>
    <tr><td class='label'>&nbsp;</td><td><input type='submit'></td></tr>
    <tr><td class='label'>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td class='label'>Manufacturer</td><td><input   name='manufacturer'   id='manufacturer' class='inputwide'></td></tr>
    <tr><td class='label'>Description</td><td><input    name='description'    id='description' class='inputwide'></td></tr>
    <tr><td class='label'>Distributor</td><td><input     name='distributor' id='distributor' class='inputwide'></td></tr>
    <tr><td class='label'>Distributor SKU</td><td><input     name='distributorsku' id='distributorsku' class='inputwide'></td></tr>
    <tr><td class='label'>Distributor URL</td><td><input    name='distributorurl' id='distributorurl' class='inputwide'></td></tr>
    <tr><td class='label'>Datasheet URL</td><td><input    name='datasheeturl' id='datasheeturl' class='inputwide'></td></tr>
    <tr><td class='label'>Octopart URL</td><td><input   name='octoparturl'    id='octoparturl' class='inputwide'></td></tr>
    <tr><td class='label'>Image File</td><td><input   name='image'    id='image' class='inputwide'></td></tr>
    <tr><td class='label'>Photo File</td><td><input   name='photo'    id='photo' class='inputwide'></td></tr>
</table>
<form>
<hr>
<div id='data'></div>
<?php
    if ($result == 1) {
        printf("%20s <b>%20s - %s</b> entered<br>",$_POST['quantity'],$_POST['manufacturer'],$_POST['partnumber']);
    }
?>
<script src="js/entry.js"></script>
<canvas id="snapshot" width='640' height='480' style="display:none"/>

</body>
</html>
