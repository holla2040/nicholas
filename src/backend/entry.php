<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        define(DBHOST,"localhost");
        define(DBUSER,"inventory");
        define(DBPASS,"inventory7");
        define(DB,    "inventory");

        $db = mysqli_connect(DBHOST,DBUSER,DBPASS,DB) or die("Error " . mysqli_error($link)); 
        $query = 'INSERT INTO electronicparts (quantity,manufacturer,partnumber,description,distributor,distributorsku,distributorurl,octoparturl,location,datasheeturl) values ('.$_POST['quantity'].',"'.$_POST['manufacturer'].'","'.$_POST['partnumber'].'","'.$_POST['description'].'","'.$_POST['distributor'].'","'.$_POST['distributorsku'].'","'.$_POST['distributorurl'].'","'.$_POST['octoparturl'].'","'.$_POST['location'].'","'.$_POST['datasheeturl'].'")';
        $result = $db->query($query); 
    };
?><html>
<script src="http://code.jquery.com/jquery-1.11.0.js"></script>
<style>
    input {
        width: 200px;
    }
    .label {
        width: 130px;
    }
</style>

<script>
    $(document).ready(function () {
        $("#v").focus();
    });

    function search(v) {
        $("#location").focus();
        // $('#data').append(v.length);
        if (v.length == 22) {
            searchDigikey(v);
            $('#distributor').val('Digi-Key');
        } else {
            searchOctopart(v);
        }
    }

    function searchDigikey(v) {
        // console.log(v);
        var args = {'barcode':v};
        $.getJSON("dkBarcodeToPart.php", args, function(response){
            $('#quantity').val(response.quantity);
            searchOctopart(response.partnumber);
        });
    };

    // C0805C106K8PACTU
    function searchOctopart(mpn) {
        var url = "http://octopart.com/api/v3/parts/match";
        url += '?apikey=629371be&include[]=descriptions&include[]=datasheets'
        url += '&callback=?';

        var queries = [
            {'mpn': mpn}
        ];

        var args = {
            queries: JSON.stringify(queries)
        };

        $.getJSON(url, args, function(response){
            $('#data').empty();
            $('#mpn').val("");
            var queries = response['request']['queries'];
            var results = response['results']
            $('#partnumber').val(queries[0].mpn);
            $.each(results, function(i, result) {
                item = result.items[0];
console.log(item);
                $('#manufacturer').val(item.manufacturer.name);
                $('#octoparturl').val(item.octopart_url);

                $.each(item.descriptions, function(j, description) {
                    if (description.attribution.sources[0].name == 'Digi-Key') {
                        desc = description.value;
                        $('#description').val(desc);
                    };
                    // console.log(description);
                });

                $('#datasheeturl').val(item.datasheets[0].url);
                    
                $.each(item.offers, function(j, offer) {
                    if (offer.seller.name == 'Digi-Key') {
                        if (offer.packaging == "Cut Tape") {
                            $('#distributorsku').val(offer.sku);
                            $('#distributorurl').val(offer.product_url);
console.log(offer);
                        }
                    }
                });
            });
            var now = new Date();
        });
    };
    
    function submitData() {
        console.log($("#distributorsku").val());
        return true;
    };
</script>
<body>
<table>
    <tr><td class='label'>Barcode</td><td><input id='v' onchange='search(this.value)'/></td></tr>
</table>
<hr>
<form action='entry.php' method='post'>
<table>
    <tr><td class='label'>Part Number</td><td><input    name='partnumber' id='partnumber'></td></tr>
    <tr><td class='label'>Quantity</td><td><input       name='quantity'   id='quantity'></td></tr>
    <tr><td class='label'>Location</td><td><input       name='location'   id='location'></td></tr>
    <tr><td class='label'>&nbsp;</td><td><input type='submit'></td></tr>
    <tr><td class='label'>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td class='label'>Manufacturer</td><td><input   name='manufacturer'   id='manufacturer' ></td></tr>
    <tr><td class='label'>Description</td><td><input    name='description'    id='description' ></td></tr>
    <tr><td class='label'>Distributor</td><td><input     name='distributor' id='distributor'></td></tr>
    <tr><td class='label'>Distributor SKU</td><td><input     name='distributorsku' id='distributorsku'></td></tr>
    <tr><td class='label'>Distributor URL</td><td><input    name='distributorurl' id='distributorurl'></td></tr>
    <tr><td class='label'>Datasheet URL</td><td><input    name='datasheeturl' id='datasheeturl'></td></tr>
    <tr><td class='label'>Octopart URL</td><td><input   name='octoparturl'    id='octoparturl' ></td></tr>
</table>
<form>
<hr>
<div id='data'></div>
<?php
    if ($result == 1) {
        printf("%20s <b>%20s - %s</b> entered<br>",$_POST['quantity'],$_POST['manufacturer'],$_POST['partnumber']);
    }
?>
</body>
</html>
