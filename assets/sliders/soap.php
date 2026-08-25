<?php

$wsdl   = 'http://213.7.198.218:8080/SKEMPEDJIS/ws/ws1.1cws?wsdl';

try {
    $SOAP = new SoapClient($wsdl, array('trace' => 1,'login' => 'ecommerce','password' => '3c0mm3rc3*'));

    //$Data = array('Page'=>1); 
    $Response = $SOAP->GetItems();

    //echo "REQUEST:\n" . $SOAP->__getLastResponse() . "\n";

    //$RESULT = $soap->__getFunctions();
    //print_r($RESULT);

    //$xmlsoap = $SOAP->__getLastResponse();
    //$xml = simplexml_load_string($xmlsoap);
    


    $array = json_decode(json_encode($Response), true);
    $firstXML = $array; 
    $items = $array['return']['Item'];
    $itemsavailable=$array['return']['Item'][0]['TotalItems'];

    //for ($i=2;$i<=ceil((int)$itemsavailable/250);$i++) {
      //$Data = array('Page'=>2); 
      //$Response = $SOAP->GetItems($Data);
      //$array = json_decode(json_encode($Response), true);
      //$items = array_merge($items, $array['return']['Item']);
      
      //$Data = array('Page'=>3); 
      //$Response = $SOAP->GetItems($Data);
      //$array = json_decode(json_encode($Response), true);
      //$items = array_merge($items, $array['return']['Item']);
      
      //$xmlsoap = $SOAP->__getLastResponse();
      //$xmlmore = simplexml_load_string($xmlsoap);

      //$docmore = new DOMDocument();
      //$docmore->formatOutput = TRUE;
      //$docmore->loadXML($xmlmore->asXML());
      //$thedocument = $docmore->documentElement;
      //$list .= $thedocument->getElementsByTagName('Item>');
    //}
    //print_r($items);
    //echo count($items);
    //$firstXML['return']['Item'] = $items;
    //$xml = simplexml_load_string(json_decode($firstXML));
    //$doc = new DOMDocument();
    //$doc->formatOutput = TRUE;
    //$doc->loadXML($xml->asXML());
    //$newxml = $doc->saveXML();
    
    //$domReplace  = dom_import_simplexml($xml2);
    //$nodeImport  = $domToChange->ownerDocument->importNode($list, TRUE);
    //$domToChange->parentNode->replaceChild($nodeImport, $domToChange);
    //$xml = simplexml_load_string($list);
    //$doc->loadXML($xml->asXML());
    

    $outputFilename   = 'items.json';
    //$fh = fopen( $outputFilename, 'w' );
    //fclose($fh);


    $handle = fopen($outputFilename, "w");
    fwrite($handle, json_encode($items,JSON_PRETTY_PRINT));
    fclose($handle);
    
    
}
catch (SoapFault $fault) {
    //echo "SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})";
}
catch (Exception $e) {
    //echo $e->getMessage();
    //echo $e->getTraceAsString();
}