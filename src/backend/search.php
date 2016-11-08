<?php
    define(DBHOST,"localhost");
    define(DBUSER,"inventory");
    define(DBPASS,"inventory7");
    define(DB,    "inventory");

    if ($_GET['action'] == 'bydesc') {
        $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
        $query = "SELECT quantity,reference,description,distributorpart FROM electronicparts where description LIKE '%".$_GET['q']."%' and deleted=0 order by description" or die("Error in the consult.." . mysqli_error($db)); 
        $result = $db->query($query); 
        //header('Content-Type: plain/text');
        while($row = mysqli_fetch_assoc($result)) { 
            echo $row['reference']."\t".$row['quantity']."\t<br>".$row['manufacturer']."</br>\t".$row['manufacturerpart']."\t".$row['description']."\t".$row['distributor']."\t".$row['distributorpart']."\t".$row['location']."\n";
        }
        return;
    }


    if ($_GET['action'] == 'tsv') {
        $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
        $query = "SELECT * FROM electronicparts where deleted=0 order by description" or die("Error in the consult.." . mysqli_error($db)); 
        $result = $db->query($query); 
        header('Content-Type: plain/text');
        while($row = mysqli_fetch_assoc($result)) { 
            echo $row['reference']."\t".$row['quantity']."\t".$row['manufacturer']."\t".$row['manufacturerpart']."\t".$row['description']."\t".$row['distributor']."\t".$row['distributorpart']."\t".$row['location']."\n";
        }
        return;
    }

    function sendItems() {
        $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
        $query = "SELECT * FROM electronicparts where deleted=0 order by description" or die("Error in the consult.." . mysqli_error($db)); 
        $result = $db->query($query); 
        $items = array('items'=>array());
        while($row = mysqli_fetch_assoc($result)) { 
            $items['items'][] = $row;
        } 
        header('Content-Type: application/json');
        echo json_encode($items);
    }

    if ($_GET['action'] == 'add') {
        if ($_GET['distributor']) {
            $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
            $query = 'INSERT INTO electronicparts (quantity,manufacturer,manufacturerpart,description,distributor,distributorpart,location) values ('.$_GET['quantity'].',"'.$_GET['manufacturer'].'","'.$_GET['manufacturerpart'].'","'.$_GET['description'].'","'.$_GET['distributor'].'","'.$_GET['distributorpart'].'","'.$_GET['location'].'")';
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
        sendItems();
        return;
    }
?><!DOCTYPE html>
<html ng-app="">
<head>
<link rel="stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<script src= "http://ajax.googleapis.com/ajax/libs/angularjs/1.2.26/angular.min.js"></script>
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

}


</style>
</head>

<body ng-controller="partsController">
<table class="tablea table-striped">
  <thead>
    <tr>
      <th>Q <img src="images/icon_plus.gif" ng-click='add()'/></th>
      <th>Manu</th>
      <th>Manu Part</th>
      <th>Description
<input type='field' ng-model="searchText" style='border: 1px solid'/>
</th>
      <th>Distributor</th>
      <th>Dist Part</th>
      <th>Notes</th>
      <th>Location</th>
      <th>URL</th>
      <th>Reference</th>
    </tr>
  </thead>
  <tbody>
    <tr ng-repeat="item in items | filter:{description:searchText}">
      <td><input type='text' value='{{ item.quantity }}' size='2' ng-keyup="$event.keyCode == 13 ? update(item.id,'quantity',$event.srcElement.value) : null"></input></td>
      <td><input type='text' value='{{ item.manufacturer }}' size='8' 
            ng-blur="update(item.id,'manufacturer',$event.srcElement.value)" 
            ng-keyup="$event.keyCode == 13 ? update(item.id,'manufacturer',$event.srcElement.value) : null"></input></td>
      <td>
        <a href='http://www.google.com/search?q={{ item.manufacturer }}+{{ item.manufacturerpart }}' target='_blank'><img src='images/icon_goto.gif'></a>
        <input type='text' value='{{ item.manufacturerpart }}' size='20' 
            ng-blur="update(item.id,'manufacturerpart',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'manufacturerpart',$event.srcElement.value) : null"></input>
      </td>
      <td><input type='text' value='{{ item.description }}' size='50' 
            ng-blur="update(item.id,'description',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'description',$event.srcElement.value) : null"></input></td>
      <td>
<input type='text' value='{{ item.distributor }}' size='5' 
            ng-blur="update(item.id,'distributor',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'distributor',$event.srcElement.value) : null"></input>
        <a href='http://search.digikey.com/scripts/DkSearch/dksus.dll?Detail&name={{ item.distributorpart }}' target='_blank' ng-if='item.distributorpart.length > 0'><img src='images/icon_goto.gif'></a> 
      </td>
      <td style="width:150px">
        <input type='text' value='{{ item.distributorpart }}' size='12' 
            ng-blur="update(item.id,'distributorpart',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'distributorpart',$event.srcElement.value) : null"></input>
      </td>
      <td><input type='text' value='{{ item.notes }}' 
            ng-blur="update(item.id,'notes',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'notes',$event.srcElement.value) : null"></input></td>
      <td><input type='text' value='{{ item.location }}' size='5' 
            ng-blur="update(item.id,'location',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'location',$event.srcElement.value) : null"></input></td>
      <td><input type='text' value='{{ item.url }}' size='5' 
            ng-blur="update(item.id,'url',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'url',$event.srcElement.value) : null"></input></td>
      <td><input type='text' value='{{ item.reference }}' size='15' 
            ng-blur="update(item.id,'reference',$event.srcElement.value)"
            ng-keyup="$event.keyCode == 13 ? update(item.id,'reference',$event.srcElement.value) : null"></input></td>
      <td><img src='images/icon_delete.png' ng-click='delete(item.id);'></td>
    </tr>
  </tbody>
</table>

<script src= "electronicparts.js"></script>

</body>
</html>

