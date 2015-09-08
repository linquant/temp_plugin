<?php
/*
@name histo des températures
@author linquant <linquant@gmail.com>
@link
@licence CC by nc sa
@version 1.0.0
@description historique des températures
*/

//Si vous utiliser la base de donnees a ajouter
include('Temp.class.php');

//Cette fonction va generer un nouveau element dans le menu
function temp_plugin_menu(&$menuItems){
    global $_;
    $menuItems[] = array('sort'=>10,'content'=>'<a href="index.php?module=temp"><i class="fa fa-line-chart"></i> Température</a>');
}

//Cette fonction ajoute une commande vocale
function temp_plugin_vocal_command(&$response,$actionUrl){
    global $conf;
    //Création de la commande vocale "Yana, commande de temp" avec une sensibilité de 0.90 et un appel
    // vers l'url /action.php?action=temp_plugin_vocal_temp après compréhension de la commande
    $response['commands'][] = array(
        'command'=>$conf->get('VOCAL_ENTITY_NAME').' commande vocale de temp',
        'url'=>$actionUrl.'?action=temp_plugin_vocal_temp','confidence'=>('0.90'+$conf->get('VOCAL_SENSITIVITY'))
    );
}

//cette fonction comprends toutes les actions du plugin qui ne nécessitent pas de vue html
function temp_plugin_action(){
    global $_,$conf;

    //Action de réponse à la commande vocale "Yana, commande de temp"
    switch($_['action']){
        case 'temp_plugin_vocal_temp':
            $response = array('responses'=>array(
                array('type'=>'talk','sentence'=>'Ma réponse à la commande de temp est inutile.')
            )
            );
            $json = json_encode($response);
            echo ($json=='[]'?'{}':$json);
            break;
    }
}


//Cette fonction va generer une page quand on clique sur Modele dans menu
function temp_plugin_page($_){
    if(isset($_['module']) && $_['module']=='temp'){



        ?>

        <div>
            <?php
            //récupre les tempéatures
            $tempM = new Temp();
            $temp = $tempM->populate();

            $moyenne = null;
            $mini = 1000;
            $maxi = 0;

            if (isset($_POST['heure']) && $_POST['heure'] <= count($temp) ){

                $heure = intval($_POST['heure']);
            }
            // Si la valeur sisie est sup à l'historique on affecte la valeur maximale
            elseif ($_POST['heure'] > count($temp)) {

                $heure =  count($temp);
            }
            // si le nombre de valeurs est inférieurs au nombre demandé
            elseif (count($temp) < 24)
            {
            	$heure =  count($temp);	
            }
            // par défaut affiche 24h
            else{
                $heure = 24;
            }



            $values = '[';
            $date = "[";
            $i = 0;

            foreach($temp as $t){

                $i++;

                if ($i > count($temp) - $heure) {
                    $values=$values."\"".$t->gettemp()."\",";

                    $date=$date."\"".$t->getdate()."\",";

                    $moyenne= $moyenne + $t->gettemp();


                    if ($t->gettemp() < $mini){


                        $mini = $t->gettemp();
                        $heuremini = $t->getdate();

                    }

                    if ($t->gettemp() > $maxi){


                        $maxi = $t->gettemp();
                        $heuremaxi = $t->getdate();
                    }

                }
            }


            $values= $values."]";
            $date= $date."]";
            $moyenne= round($moyenne/ $heure,2);

            ?>
            <div class="row">

                <h2>Température</h2>

                <div class ="span-5">
                    <div class="well">

                        <legend> Statistique.</legend>

                        <span class="label label-default"><?php echo "température actuelle : ".temperature_get(); ?></span>
                        <span class="label label-sucess"><?php echo "température moyenne : ".$moyenne; ?></span>
                        <br/>
                        <br/>

                        <span class="label label-info"><?php echo "température minimun : ".round($mini,2)." le ".$heuremini ; ?></span>
                        <span class="label label-warning"><?php echo "température maximun : ".round($maxi,2)." le ".$heuremaxi ?></span>

                    </div>
                </div>
                <div class ="span-4">

                    <div class="well">

                        <form action="index.php?module=temp" method="POST">
                            <p>Saisir nombre d'heure a afficher </p>
                            <input type="number" class="form-control" name="heure" value="<?php echo $heure ;?>" >
                            <button type="submit" class="btn btn-default">Valider</button>

                        </form>


                    </div>

                </div>

            </div>


            <div class="span-12">

                <script src='/yana-server/templates/default/js/chart.min.js'></script>
                <!-- line chart canvas element -->
                <canvas id="buyers" width="1000" height="600"></canvas>

                <script>
                    // line chart data
                    var buyerData = {
                        labels : <?php echo $date; ?> ,
                        datasets : [
                            {
                                fillColor : "rgba(172,194,132,0.4)",
                                strokeColor : "#ACC26D",
                                pointColor : "#fff",
                                pointStrokeColor : "#9DB86D",
                                data : <?php echo $values; ?>
                            }
                        ]
                    }

                    var options = {
                        responsive: true,

                    };
                    // get line chart canvas
                    var buyers = document.getElementById('buyers').getContext('2d');
                    // draw line chart
                    new Chart(buyers).Line(buyerData,options);

                </script>


            </div>



        </div>
    <?php
    }
}

Plugin::addCss("/css/style.css");
//Plugin::addJs("/js/main.js");

Plugin::addHook("menubar_pre_home", "temp_plugin_menu");
Plugin::addHook("home", "temp_plugin_page");
Plugin::addHook("action_post_case", "temp_plugin_action");
Plugin::addHook("vocal_command", "temp_plugin_vocal_command");
?>