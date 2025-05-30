<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Objeto</title>
</head>
<body>
<?php
    $api_key = "G8d3T0PthqgqDJWhdtFSKgYPYDZFgU5JqWNAgGYC";

    $urlIndi = $_GET["urlIndi"];
    $apiUrl = $urlIndi;
    $curl = curl_init(); // Inicializamos la libreria cUrl
    curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, $api_key . ":");
    $respuesta = curl_exec($curl);
    curl_close($curl);

    $datos = json_decode($respuesta, true);

    //Calculamos la media del diámetro del objeto usando las dos medidas límite que tenemos
    $diametro = ($datos["estimated_diameter"]["meters"]["estimated_diameter_min"] + $datos["estimated_diameter"]["meters"]["estimated_diameter_max"])/2;

    //Variable booleana para declarar si tenemos o no tenemos datos de un acercamiento
    $mu_lejos = false;
    //Inicializamos las variables únicamente si tenemos datos
    if($datos["close_approach_data"] != null){
        $acercamientos = $datos["close_approach_data"];
        $cerca = round($acercamientos["0"]["miss_distance"]["kilometers"]);
    } else {
        $mu_lejos = true;
    }
?>

    <h3>ID: <?= $datos["id"] ?></h3>
    <h3>Nombre: <?= $datos["name"] ?></h3>
<?php if(isset($datos["name_limited"])){ ?>
    <h3>Nombre abreviado: <?= $datos["name_limited"] ?></h3>
    <!-- En caso de que no tenga una abreviatura de nombre, no entra a la variable -->
<?php } ?>
    <h3>Primera fecha de observacion: <?= $datos["orbital_data"]["first_observation_date"] ?></h3>
    <h3>Última fecha de observacion: <?= $datos["orbital_data"]["last_observation_date"] ?></h3>
    <h3>Minimo diametro: <?= round($datos["estimated_diameter"]["meters"]["estimated_diameter_min"]); ?> metros</h3>
    <h3>Máximo diametro: <?= round($datos["estimated_diameter"]["meters"]["estimated_diameter_max"]); ?> metros</h3>
    <h3>Media diametro: <?= round($diametro) ?> metros</h3>
    <!-- Enlace que nos lleva a una visualización de la órbita del objeto en el Sistema Solar -->
    <a href="<?= $datos["nasa_jpl_url"] ?>&view=VOP">Órbita</a>
    <?php
    // Verificamos que se realicen estas acciones si el objeto tiene datos que cotejar
    if(!$mu_lejos){
        foreach($acercamientos as $acercamiento){
            /* Cuando inicializamos la variable "cerca" le dimos el valor del primer acercamiento.
            El foreach va a comparar las distancias de los otros acercamientos para ver cual es la menor y 
            guardarla en esa variable, al igual que la de su velocidad en ese momento y la fecha. */
            if(round($acercamiento["miss_distance"]["kilometers"]) >= $cerca){
                $cerca = round($acercamiento["miss_distance"]["kilometers"]);
                $fecha_cerca = $acercamiento["close_approach_date_full"];
                $velocidad = $acercamiento["relative_velocity"]["kilometers_per_hour"];
            }
        }
        ?>
        <h3>Menor distancia de órbita: <?= $cerca ?> km</h3>
        <h3>Fecha de menor distancia: <?= $fecha_cerca ?></h3>
        <h3>Velocidad: <?= $velocidad ?> km/h</h3>
    <?php } ?>
</body>
</html>