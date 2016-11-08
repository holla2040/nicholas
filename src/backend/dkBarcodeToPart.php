<?php
// uses http 1.0 to force server to not keep alive, http://stackoverflow.com/questions/7927566/is-an-http-1-1-request-implicitly-keep-alive-by-default

if (isset($_GET['barcode'])) {
    $barcode = $_GET['barcode'];
} else {
    $barcode = "0729389000001000652773";
}

header("Content-Type: application/json");
    

$soaptemplate='POST /Mobile/MobileV1.asmx HTTP/1.0
User-Agent: ksoap2-android/2.6.0+
SOAPAction: http://services.digikey.com/MobileV1/GetProductInfo
Content-Type: text/xml;charset=utf-8
Content-Length: 981
Host: services.digikey.com

<v:Envelope xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns:d="http://www.w3.org/2001/XMLSchema" xmlns:c="http://schemas.xmlsoap.org/soap/encoding/" xmlns:v="http://schemas.xmlsoap.org/soap/envelope/"><v:Header><Security xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><UsernameToken><Username>iPhoneMobileApplication</Username><Password>yu78r5e3w2</Password></UsernameToken></Security><PartnerInformation xmlns="http://services.digikey.com/MobileV1"><PartnerID>{42E9A111-22AB-4E95-91AE-BC509F8F16F5}</PartnerID></PartnerInformation><n0:CustomerNumber xmlns:n0="http://services.digikey.com/MobileV1">0</n0:CustomerNumber><n1:Language xmlns:n1="http://services.digikey.com/MobileV1">en</n1:Language><n2:Site xmlns:n2="http://services.digikey.com/MobileV1">US</n2:Site></v:Header><v:Body><GetProductInfo xmlns="http://services.digikey.com/MobileV1"><partId i:type="d:string">REPLACEME</partId></GetProductInfo></v:Body></v:Envelope>

';

$post = str_replace("REPLACEME",substr($barcode,0,7),$soaptemplate);
$quantity = substr($barcode,8,8);

$fp = fsockopen("services.digikey.com", 80, $errno, $errstr, 10.0);
$rv = "";
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
        fwrite($fp, $post);
        while (!feof($fp)) {
            $seg = fgets($fp, 1000);
            $rv .= $seg;
        }
    }
fclose($fp);

// list($header, $body) = preg_split("/\R\R/", $rv, 2);

preg_match('#ManufacturerPartNumber>(.*?)</ManufacturerPartNumber#s',$rv,$matches);

$json = '{"barcode":"'.$barcode.'","partnumber":"'.$matches[1].'","quantity":'.(int)$quantity.'}';

echo $json;

$matches[1]
?>
