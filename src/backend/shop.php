<?php
    define(DBHOST,"localhost");
    define(DBUSER,"inventory");
    define(DBPASS,"inventory7");
    define(DB,    "inventory");

    function sendItems() {
        $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
        $query = "SELECT * FROM electronicparts where deleted=0 order by location,partnumber" or die("Error in the consult.." . mysqli_error($db)); 
        $result = $db->query($query); 
        $items = array('items'=>array());
        while($row = mysqli_fetch_assoc($result)) { 
            $items['db'] = "parts";
            $row['photo'] = 'inventoryimages/'.$row['photo'];
            $row['search'] = implode("|",array_values($row));
            $items['items'][] = $row;
        } 

        $query = "SELECT * FROM tubes where deleted=0 order by description" or die("Error in the consult.." . mysqli_error($db)); 
        $result = $db->query($query); 
        while($row = mysqli_fetch_assoc($result)) { 
            $items['db'] = "tube";
            $row["reference"] = "tubeDB";
            $row['photo'] = 'tubeimages/'.$row['photofile'];
            $row['search'] = implode("|",array_values($row));
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
        vertical-align: center;
        padding-left:2px;
    }

    .tubespec {
        width:50px;
        border-left: 1px solid #bbbbbb;
        border-right: 1px solid #bbbbbb;
        text-align:center;
    }

    th {
        text-align:left;
    }

    textarea { 
        border: none;
    }

    .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
       background-color: #f0f0f0;
    }

    td {
        vertical-align:top;
    }

    hr {
        margin: 1px;
        padding: 1px;
    }
    
    h4 {
        margin: 1px;
        padding: 1px;
        text-align:center;
        font-weight: bold;
    }

    table {
        border:1px solid black;
    }

</style>


</head>

<body ng-controller="partsController">
<b><a href='list.php' target='_blank'>List</a></b>&nbsp;&nbsp;&nbsp;Search <input type='field' ng-model="searchText" style='border: 1px solid'/>
<hr>

<img src='{{item.photo}}' title='Part Number - {{item.partnumber}}&#10;Description - {{item.description}} &#10;Location - {{item.location}} &#10;Quantity - {{item.quantity}}' width='320' height='240' ng-repeat='item in items | filter:{search:searchText}'  data-action='zoom'>

<script src= "js/shop.js"></script>
<script src="js/transition.js"></script>
<script src="js/zoom.js"></script>


</body>
</html>

