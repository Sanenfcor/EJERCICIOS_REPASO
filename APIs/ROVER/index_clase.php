<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ROVER</title>
    <?php
        error_reporting( E_ALL );
        ini_set( "display_errors", 1 );
    ?>
</head>
<body>
    <?php
    $api_key = "G8d3T0PthqgqDJWhdtFSKgYPYDZFgU5JqWNAgGYC";

    if(isset($_GET["indice"])) {
        $indice = $_GET["indice"];
    } else {
        $indice = 0;
    }

    $siguiente = $indice+5;
    $anterior = $indice-5;

    ?>

    <?php if($indice > 5){ ?>
        <a href="index_clase.php?indice=<?= $anterior ?>" style="border: solid 1px; padding:3px;"><<</a>
    <?php } else { ?>
        <a href="" hidden><<</a>
    <?php } ?>
    <!-- 
    <a href="index_clase.php?indice=<?= $anterior ?>">
        Anterior
    </a> -->

    <a href="index_clase.php?indice=<?= $siguiente ?>">
        Siguiente
    </a>

    <?php

    if(isset($_GET["sol"])){
        $sol = $_GET["sol"];
        if($sol < 1){
            $sol = 0;
        }
    } else{
        $sol = 0;
    }

    if(isset($_GET["rover"])){
        $rover = $_GET["rover"];
    } else{
        $rover = "curiosity";
    }

    //Primera llamada a la API

    $apiUrl = "https://api.nasa.gov/mars-photos/api/v1/rovers/$rover/photos?sol=$sol";
    $curl = curl_init(); // Inicializamos la libreria cUrl
    curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // Para habilitar la transferencia de datos
    curl_setopt($curl, CURLOPT_USERPWD, $api_key . ":"); // Para habilitar la transferencia de datos
    $respuesta = curl_exec($curl);
    curl_close($curl);

    $datos = json_decode($respuesta, true);
    //echo (var_dump($datos));
    $fotos = $datos["photos"];

    //Segunda llamada a la API, aquí obtenemos los datos de las fotos totales, el total de "sol" y la cantidad de fotos por cada uno

    $apiUrl_v2 = "https://api.nasa.gov/mars-photos/api/v1/manifests/$rover";
    $curl_v2 = curl_init(); // Inicializamos la libreria cUrl
    curl_setopt($curl_v2, CURLOPT_URL, $apiUrl_v2); // Indicamos que la conexion va por URL e indicamos la URL
    curl_setopt($curl_v2, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
    curl_setopt($curl_v2, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl_v2, CURLOPT_USERPWD, $api_key . ":");
    $results = curl_exec($curl_v2);
    curl_close($curl_v2);

    $data = json_decode($results, true);
    $manifesto = $data["photo_manifest"];

    /* SOL = días en la superficie de Marte */
    $MAX_sol = $manifesto["max_sol"]; //La cantidad de días marcianos que ha durado la misión, está a su última actualización

    $fotos_por_sol = 0;
    foreach($manifesto["photos"] as $manifest){
        if($manifest["sol"] == $sol){
            $fotos_por_sol = $manifest["total_photos"]; // Indica la cantidad de fotos por cada "sol" para utilizar esa cantidad en la paginación, este valor está mal
        }
    }

    $ultima_pagina = ceil($fotos_por_sol/25); // Última página que ayuda en la paginación

    ?>    

    <h1>FOTOS ROVER</h1>
    <h3>Sol <?= $sol ?></h3> <!-- Indicamos el "sol" en el que nos encontramos -->
    <h3>Fotos por sol: <?= $fotos_por_sol ?></h3> <!-- Indicamos el "sol" en el que nos encontramos -->
    
    <!-- Formulario para cambiar entre los diferentes "sol", podría mejorarlo para que no permita un número menor a 0 ni superior al máximo de "sol"-->
    <div>  
        <form method="get">
            <label>Seleccione el "sol" que quiere ver: </label>
            <input type="number" id="sol" name="sol" placeholder="max. <?= $MAX_sol ?>">
            <br>
            <label>Seleccione el rover: </label>
            <select name="rover">
                <option value="curiosity" <?= $rover == "curiosity" ? "selected" : "" ?>>Curiosity</option>
                <option value="opportunity" <?= $rover == "opportunity" ? "selected" : "" ?>>Opportunity</option>
                <option value="spirit" <?= $rover == "spirit" ? "selected" : "" ?>>Spirit</option>
            </select>

            <input type="submit" value="Enviar">
        </form>
    </div>

    <!-- ¿Se podría hacer otro formulario para seleccionar el rover que queremos usar? -->

    <!-- Aquí verificamos si los "sol" están vacíos o no, pues hay alguno que no tiene fotos -->
    <?php
    if(count($fotos) != null){ ?>
        <div>
            <table border="5px solid black" style="margin:auto; margin-top:2rem; margin-bottom:2rem; text-align:center; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="height:300px; width:300px;"><h1>ID</h1></th>
                        <th><h1>IMG</h1></th>
                        <th style="height:300px; width:300px;"><h1>Cámara</h1></th>
                        <th style="height:300px; width:300px;"><h1>Fecha</h1></th>
                    </tr>    
                </thead>
                <tbody>
                    <?php
                    $contador = 0;
                    while($contador < 5){ ?>
                        <tr>
                            <td><h3><?= $fotos[$indice]["id"] ?></h3></td>
                            <td><img src="<?= $fotos[$indice]["img_src"] ?>" alt="fotito" height="300px" width="300px"></td>
                            <td><h3><?= $fotos[$indice]["camera"]["name"] ?></h3></td>
                            <td><h3><?= $fotos[$indice]["earth_date"] ?></h3></td>
                        </tr>
                    <?php 
                    $contador++;
                    $indice++;
                    } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <h1>Este ROVER no tiene fotos</h1>
    <?php } ?>

</body>
</html>