<?php
//cron a executer toutes les heures pour enregistrer la température

date_default_timezone_set('Europe/Paris');

include('Temp.class.php');



$temp= new temp();
$temp->setdate();
$temp->settemp();
$temp->save();




?>