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

    if ($_GET['action'] == 'items') {
        sendItems();
        return;
    }
?><!DOCTYPE html>
<html ng-app="">
<head>
<link rel="stylesheet" href = "bootstrap.min.css">
<script src= "angular.min.js"></script>
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
      <th>Q</th>
      <th>Manu</th>
      <th>PartNum</th>
      <th>Description <input type='field' ng-model="searchText" style='border: 1px solid'/> </th>
      <th>Dist</th>
      <th>Dist SKU</th>
      <th>Notes</th>
      <th>Location</th>
      <th>Reference</th>
      <th>Image</th>
    </tr>
  </thead>
  <tbody>
    <tr ng-repeat="item in items | filter:{description:searchText}">
      <td><input type='text' value='{{ item.quantity }}' size='2'></input></td>
      <td><input type='text' value='{{ item.manufacturer }}' size='8'</input></td>
      <td>
        <a href='http://www.google.com/search?q={{ item.manufacturer }}+{{ item.partnumber }}' target='_blank'><img src='images/icon_goto.gif'></a>
        <input type='text' value='{{ item.partnumber }}' size='20'></input>
      </td>
      <td><input type='text' value='{{ item.description }}' size='50'></input></td>
      <td><input type='text' value='{{ item.distributor }}' size='5'></input>
        <a href='http://search.digikey.com/scripts/DkSearch/dksus.dll?Detail&name={{ item.distributorsku }}' target='_blank' ng-if='item.distributorsku.length > 0'><img src='images/icon_goto.gif'></a> 
      </td>
      <td style="width:150px">
        <input type='text' value='{{ item.distributorsku }}' size='12'></input>
      </td>
      <td><input type='text' value='{{ item.notes }}'></input></td>
      <td><input type='text' value='{{ item.location }}' size='5' ></input></td>
      <td><input type='text' value='{{ item.reference }}' size='15'></input></td>
      <td><a target="_blank" href='inventoryimages/{{item.imagefile}}'><img src='inventoryimages/{{item.imagefile}}' height='30'></a></td>
    </tr>
  </tbody>
</table>

<script src= "search.js"></script>

</body>
</html>

