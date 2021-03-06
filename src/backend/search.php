<?php
    define(DBHOST,"localhost");
    define(DBUSER,"inventory");
    define(DBPASS,"inventory7");
    define(DB,    "inventory");

    if ($_GET['action'] == 'bydesc') {
        $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
        $query = "SELECT quantity,reference,description,distributorsku FROM electronicparts where description LIKE '%".$_GET['q']."%' and deleted=0 order by description" or die("Error in the consult.." . mysqli_error($db)); 
        $result = $db->query($query); 
        //header('Content-Type: plain/text');
        while($row = mysqli_fetch_assoc($result)) { 
            echo $row['reference']."\t".$row['quantity']."\t<br>".$row['manufacturer']."</br>\t".$row['partnumber']."\t".$row['description']."\t".$row['distributor']."\t".$row['distributorsku']."\t".$row['location']."\n";
        }
        return;
    }


    if ($_GET['action'] == 'tsv') {
        $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
        $query = "SELECT * FROM electronicparts where deleted=0 order by description" or die("Error in the consult.." . mysqli_error($db)); 
        $result = $db->query($query); 
        header('Content-Type: plain/text');
        while($row = mysqli_fetch_assoc($result)) { 
            echo $row['reference']."\t".$row['quantity']."\t".$row['manufacturer']."\t".$row['partnumber']."\t".$row['description']."\t".$row['distributor']."\t".$row['distributorsku']."\t".$row['location']."\n";
        }
        return;
    }

    function sendItems() {
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        } else {
            $limit = 10000;
        }
        $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
        $query = "SELECT * FROM electronicparts where deleted=0 order by timestamp desc limit ".$limit or die("Error in the consult.." . mysqli_error($db)); 
        $result = $db->query($query); 
        $items = array('items'=>array());
        while($row = mysqli_fetch_assoc($result)) { 
            $row['search'] = implode("|",array_values($row));
            $items['items'][] = $row;
        } 


/*
        $query = "SELECT * FROM tubes where deleted=0 order by description" or die("Error in the consult.." . mysqli_error($db)); 
        $result = $db->query($query); 
        while($row = mysqli_fetch_assoc($result)) { 
            $row['search'] = implode("|",array_values($row));
            $items['items'][] = $row;
        } 
*/



        header('Content-Type: application/json');
        echo json_encode($items);
    }

    if ($_GET['action'] == 'add') {
        if ($_GET['distributor']) {
            $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
            $query = 'INSERT INTO electronicparts (quantity,manufacturer,partnumber,description,distributor,distributorsku,location) values ('.$_GET['quantity'].',"'.$_GET['manufacturer'].'","'.$_GET['partnumber'].'","'.$_GET['description'].'","'.$_GET['distributor'].'","'.$_GET['distributorsku'].'","'.$_GET['location'].'")';
            // echo $query;
            $result = $db->query($query); 
            print_r($result);
            return;
        } else {
            $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
            $query = "INSERT INTO electronicparts (quantity) values (0)";
            $result = $db->query($query); 
            sendItems();
            return;
        }
    }

    if ($_GET['action'] == 'delete') {
        if ($_GET['id']) {
            $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
            $query = "UPDATE electronicparts SET deleted=1 WHERE id=".$_GET['id'];
            // $query = "delete from electronicparts where id=".$_GET['id'];
            $result = $db->query($query); 
            sendItems();
        }
        return;
    }

    if ($_GET['action'] == 'items') {
        sendItems();
        return;
    }

    if ($_GET['action'] == 'update') {
        $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
        $query = "UPDATE electronicparts SET ".$_GET['field']."=\"".$_GET['text']."\" WHERE id=".$_GET['id'];
        $result = $db->query($query); 
        //echo $query;
        //sendItems();
        return;
    }
?><!DOCTYPE html>
<html ng-app="myApp">
<head>
<link rel="stylesheet" href = "css/bootstrap.min.css">
<script src= "js/angular.min-1.3.20.js"></script>
<script src= "js/jquery.js"></script>
<link href="css/zoom.css" rel="stylesheet">
<style>
input {
    background-color:transparent;
    border: 0px solid;
}
inputa:focus {
    outline:none;
}

tr,td,table {
    padding-right:2px;
    vertical-align: text-bottom;
}

.octothumb:hover {
    position:relative;
    top:-25px;
    left:-35px;
    width:auto;
    height:100px;
    display:block;
    z-index:999;
}

.photothumbcrap:hover {
    position:relative;
    top:0px;
    left:35px;
    width:auto;
    height:200px;
    display:block;
    z-index:999;
}

.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
   background-color: #dddddd;
}

.imagelabel {
    width:70px;
    display:inline-block;
}
</style>
</head>

<body ng-controller="appController">
<div style='float:right;margin-right:5px'><a href='shop.php'>Shop</a></div>
<div style='float:right;margin-right:5px'><a href='entry.php'>Entry</a></div>
<div style='float:right;margin-right:5px'><a href='/inventory/search.php#?limit=25'>Limit25</a></div>
<table class="tablea table-striped">
  <thead>
    <tr>
      <th>Q <img src="images/icon_plus.gif" ng-click='add()'/></th>
      <th>Manu</th>
      <th>PartNum</th>
      <th>Description <input type='field' ng-model="searchText" style='border: 1px solid' ng-model-options="{updateOn : 'change blur'}"/></th>
      <th>Dist</th>
      <th>Dist SKU</th>
      <th>Notes</th>
      <th>Location</th>
      <th>Reference</th>
      <th><label class='imagelabel'><input type='checkbox' ng-model='showimage'>Image</label></th>
    </tr>
  </thead>
  <tbody>
    <tr ng-repeat="item in items | filter:{search:searchText}">
      <td><input type='text' value='{{::item.quantity}}' size='2' ng-keyup="$event.keyCode == 13 ? update(item.id,'quantity',$event) : null"></input></td>
      <td><input type='text' value='{{::item.manufacturer}}' size='8' 
            ng-blur="update(item.id,'manufacturer',$event)" 
            ng-keyup="$event.keyCode == 13 ? update(item.id,'manufacturer',$event) : null"></input></td>
      <td>
        <a href='http://www.google.com/search?q={{::item.manufacturer}}+{{::item.partnumber}}' target='_blank'><img src='images/icon_goto.gif'></a>
      <a href='{{::item.datasheeturl}}' target='_blank'><img src='images/icon_pdf.png'></a>
      <a href='{{::item.octoparturl}}' ng-if='item.octoparturl' target='_blank'><img src='images/icon_octopart.png'></a>
      <img src='images/icon_octopart_blank.png' ng-if='!item.octoparturl'>
        <input type='text' value='{{::item.partnumber}}' size='24' 
            ng-blur="update(item.id,'partnumber',$event)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'partnumber',$event) : null"></input>
      </td>
      <td>
            <img src='inventoryimages/{{::item.image}}' width='16px' height='16px' data-action='zoom'><img src='images/icon_octopart_blank.png' width='16px' height='16px' ng-if='!item.image'></a>
            <input type='text' value='{{::item.description}}' size='60' 
            ng-blur="update(item.id,'description',$event)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'description',$event) : null"></input></td>
      <td>
        <input type='text' value='{{::item.distributor}}' size='5' 
            ng-blur="update(item.id,'distributor',$event)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'distributor',$event) : null"></input>
        <a href='http://search.digikey.com/scripts/DkSearch/dksus.dll?Detail&name={{::item.distributorsku}}' target='_blank' ng-if='item.distributorsku.length > 0'><img src='images/icon_goto.gif'></a> 
      </td>
      <td style="width:150px">
        <input type='text' value='{{::item.distributorsku}}' size='25' 
            ng-blur="update(item.id,'distributorsku',$event)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'distributorsku',$event) : null"></input>
      </td>
      <td><input type='text' value='{{::item.notes}}' 
            ng-blur="update(item.id,'notes',$event)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'notes',$event) : null"></input></td>
      <td><input type='text' value='{{::item.location}}' size='17' 
            ng-blur="update(item.id,'location',$event)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'location',$event) : null"></input></td>
      <td><input type='text' value='{{::item.reference}}' size='20' 
            ng-blur="update(item.id,'reference',$event)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'reference',$event) : null"></input></td>
      <td><img src='inventoryimages/{{item.photo}}' class='photothumb' height='100' data-action='zoom' ng-if="showimage"></td>
      <td><img src='images/icon_delete.png' ng-click='delete(item.id);'></td>
      <td>ID:{{::item.id}}</td>
    </tr>
  </tbody>
</table>

<script src= "js/search.js"></script>
<script src="js/transition.js"></script>
<script src="js/zoom.js"></script>

</body>
</html>

