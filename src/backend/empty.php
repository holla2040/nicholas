<?php
    define(DBHOST,"localhost");
    define(DBUSER,"inventory");
    define(DBPASS,"inventory7");
    define(DB,    "inventory");

    function sendItems() {
        $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
        $query = "SELECT * FROM electronicparts where deleted=0 and description='' order by timestamp desc" or die("Error in the consult.." . mysqli_error($db)); 
        $result = $db->query($query); 
        $items = array('items'=>array());
        while($row = mysqli_fetch_assoc($result)) { 
            $row['search'] = implode("|",array_values($row));
            $items['items'][] = $row;
        } 

        header('Content-Type: application/json');
        echo json_encode($items);
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
        sendItems();
        return;
    }
?><!DOCTYPE html>
<html ng-app="">
<head>
<link rel="stylesheet" href = "css/bootstrap.min.css">
<script src= "js/angular.min.js"></script>
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

input {
    border:1px solid black
};

.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
   background-color: #dddddd;
}



</style>


</head>

<body ng-controller="partsController">
<div style='float:right;margin-right:5px'><a href='shop.php'>Shop</a></div>
<div style='float:right;margin-right:5px'><a href='entry.php'>Entry</a></div>
<table class="tablea table-striped">
  <thead>
    <tr>
      <th>Q <img src="images/icon_plus.gif" ng-click='add()'/></th>
      <th>Manu</th>
      <th>PartNum</th>
      <th>Description <input type='field' ng-model="searchText" style='border: 1px solid'/> </th>
      <th>Notes</th>
      <th>Location</th>
      <th>Image</th>
    </tr>
  </thead>
  <tbody>
    <tr ng-repeat="item in items | filter:{search:searchText}">
      <td><input type='text' value='{{ item.quantity }}' size='2' ng-keyup="$event.keyCode == 13 ? update(item.id,'quantity',$event.srcElement.value) : null"></input></td>
      <td><input type='text' value='{{ item.manufacturer }}' size='8' 
            ng-blur="update(item.id,'manufacturer',$event.srcElement.value)" 
            ng-keyup="$event.keyCode == 13 ? update(item.id,'manufacturer',$event.srcElement.value) : null"></input></td>
      <td>
        <input type='text' value='{{ item.partnumber }}' size='24' 
            ng-blur="update(item.id,'partnumber',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'partnumber',$event.srcElement.value) : null"></input>
      </td>
      <td>
            <input type='text' value='{{ item.description }}' size='60' class='desc'
            ng-blur="update(item.id,'description',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'description',$event.srcElement.value) : null"></input></td>
      <td><input type='text' value='{{ item.notes }}' 
            ng-blur="update(item.id,'notes',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'notes',$event.srcElement.value) : null"></input></td>
      <td><input type='text' value='{{ item.location }}' size='17' 
            ng-blur="update(item.id,'location',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'location',$event.srcElement.value) : null"></input></td>
      <td><input type='text' value='{{ item.reference }}' size='20' 
            ng-blur="update(item.id,'reference',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'reference',$event.srcElement.value) : null"></input></td>
      <td><img src='inventoryimages/{{item.photo}}' class='photothumb' height='200' data-action='zoom'></td>
      <td><img src='images/icon_delete.png' ng-click='delete(item.id);'></td>
      <td>ID:{{item.id}}</td>
    </tr>
  </tbody>
</table>

<script src= "js/empty.js"></script>
<script src="js/transition.js"></script>
<script src="js/zoom.js"></script>


</body>
</html>

