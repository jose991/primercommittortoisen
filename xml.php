<?php

include('conexion.php');

$file_factura = $_FILES["input_factura"];

$xml_content = file_get_contents($file_factura["tmp_name"]);

$xml_content = str_replace("<tfd:", "<cfdi:", $xml_content);
$xml_content = str_replace("<cfdi:", "<", $xml_content);
$xml_content = str_replace("</cfdi:", "</", $xml_content);

$xml_content = str_replace("<nomina12:", "<", $xml_content);
$xml_content = str_replace("</nomina12:", "</", $xml_content);
$xml_content = str_replace("<nomina11:", "<", $xml_content);
$xml_content = str_replace("</nomina11:", "</", $xml_content);

$xml_content = str_replace("<pago10:", "<", $xml_content);
$xml_content = str_replace("</pago10:", "</", $xml_content);

$xml_content = str_replace("@attributes", "attributes", $xml_content);


$xml_content = simplexml_load_string(utf8_encode($xml_content));

$xml_content = (array) $xml_content;

// xml data
$xml_data["version"]       = $xml_content["@attributes"]["Version"];
$xml_data["fecha"]       = $xml_content["@attributes"]["Fecha"];
$xml_data["total"]       = $xml_content["@attributes"]["Total"];
$xml_data["subtotal"]       = $xml_content["@attributes"]["SubTotal"] ;
$xml_data["moneda"]       = $xml_content["@attributes"]["Moneda"] ;
$xml_data["sello"]       = $xml_content["@attributes"]["Sello"];

$xml_content["Emisor"] = (array) $xml_content["Emisor"];
$xml_content["Receptor"] = (array) $xml_content["Receptor"];
$xml_content["Complemento"] = (array) $xml_content["Complemento"];
$xml_content["Complemento"]["TimbreFiscalDigital"] = (array) $xml_content["Complemento"]["TimbreFiscalDigital"];

$xml_data["rfc_emisor"]  = $xml_content["Emisor"]["@attributes"]["Rfc"];
$xml_data["rfc_receptor"]  = $xml_content["Receptor"]["@attributes"]["Rfc"];
$xml_data["uuid"]       = $xml_content["Complemento"]["TimbreFiscalDigital"]["@attributes"]["UUID"];


// insert data
$sql = "INSERT INTO facturas (version, fecha, subtotal, total, moneda, sello, rfc_emisor, rfc_receptor, uuid)
		VALUES (:version, :fecha, :subtotal, :total, :moneda, :sello, :rfc_emisor, :rfc_receptor, :uuid)";
$stm = $conexion->prepare($sql);
$stm->execute($xml_data); 


print_r("Registro agregado"); exit; 


?>