<?php
/*
 @nom: temp
 @auteur:
 @description:

*/
//Ce fichier permet de gerer vos donnees en provenance de la base de donnees
//Il faut changer le nom de la classe ici (je sens que vous allez oublier)

date_default_timezone_set('Europe/Paris');
define('__ROOT__','/var/www/yana-server/');
require_once('/var/www/yana-server/constant.php');

require_once('/var/www/yana-server/classes/SQLiteEntity.class.php');
require_once('/var/www/yana-server/plugins/Cheminee/temperature.php');


class Temp extends SQLiteEntity{

    protected $id,$date,$temp; //Pour rajouter des champs il faut ajouter les variables ici...
    protected $TABLE_NAME = 'plugin_temp'; 	//Penser a mettre le nom du plugin et de la classe ici
    protected $CLASS_NAME = 'temp';
    protected $object_fields =
        array( //...Puis dans l'array ici mettre nom du champ => type
            'id'=>'key',
            'date'=>'string',
            'temp'=>'string'
        );
    function __construct(){
        parent::__construct();
    }
//Methodes pour recuperer et modifier les champs (set/get)
    function setId($id){
        $this->id = $id;
    }

    function getId(){
        return $this->id;
    }
    function getdate(){
        return $this->date;
    }
    function setdate(){
        $this->date = date("d m  H:i");
    }
    function gettemp(){
        return $this->temp;
    }
    function settemp(){
    	
    	$temp[0] = temperature_get();
    	$temp[1] = temperature_get();
    	$temp[2] = temperature_get();
    	
    	//tri par ordre croissant pour exclure du calcul la valeur la plus faible pour évter erreur de sonde
    	sort($temp);
    	
    	// moyenne des deux dernières valeurs du tableau
    	$temperature = ($temp[1]+$temp[2])/2;
        
        $this->temp = $temperature;
    }
}
?>