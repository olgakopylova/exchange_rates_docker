<?php
require_once "class/DB.php";

if(!isset($db)) $db = new DB();

$day = date("d", time());
$month = date("m", time());
$year = date("Y", time());

$data = file_get_contents("http://www.cbr.ru/scripts/XML_daily.asp?date_req=".$day."/".$month."/".$year);
$xml = new SimpleXMLElement($data);
foreach ($xml->children() as $item){
    $id = $db->getOneData("SELECT id FROM currencies_directory WHERE id_currency LIKE '%".$item->attributes()['ID']->__toString()."%'")['id'];
    foreach ($item as $element){
        if($element->getName()=="Nominal")
            $nominal = $element->__toString();
        if($element->getName()=="Value")
            $value = $element->__toString();

    }
    $db->query(sprintf("INSERT INTO `exchange_rates`(`id_currency`, `nominal`, `date`, `value`) VALUES
        (".$id.",". $nominal.",".  strtotime($year."-".$month."-".$day).",".
        (float)str_replace(',','.',$value).")"));
}