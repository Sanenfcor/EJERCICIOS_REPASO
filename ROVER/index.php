<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ROVER</title>
</head>
<body>
    <?php
    $api_key = "G8d3T0PthqgqDJWhdtFSKgYPYDZFgU5JqWNAgGYC";

    if(isset($_GET["page"])){
        $pag = $_GET["page"];
        if($pag < 2){
            $pag = 1;
        }
    } else{
        $pag = 1;
    }

    if(isset($_GET["sol"])){
        $sol = $_GET["sol"];
        if($sol < 1){
            $sol = 0;
        }
    } else{
        $sol = 0;
    }


    //Primera llamada a la API

    $apiUrl = "https://api.nasa.gov/mars-photos/api/v1/rovers/curiosity/photos?sol=$sol&page=$pag&api_key=$api_key";
    $curl = curl_init(); // Inicializamos la libreria cUrl
    curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
    $respuesta = curl_exec($curl);
    curl_close($curl);

    $datos = json_decode($respuesta, true);
    $fotos = $datos["photos"];


    //Segunda llamada a la API, aquí obtenemos los datos de las fotos totales, el total de "sol" y la cantidad de fotos por cada uno

    $apiUrl_v2 = "https://api.nasa.gov/mars-photos/api/v1/manifests/curiosity?api_key=$api_key";
    $curl_v2 = curl_init(); // Inicializamos la libreria cUrl
    curl_setopt($curl_v2, CURLOPT_URL, $apiUrl_v2); // Indicamos que la conexion va por URL e indicamos la URL
    curl_setopt($curl_v2, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
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

    <!-- PAGINACIÓN SUPERIOR -->

    <div style="border: solid 1px; width:fit-content; padding:3px;">
        <?php if($pag >= 3){ ?>
            <a href="?sol=<?=$sol?>&page=<?= 1 ?>" style="border: solid 1px; padding:3px;"><<</a>
        <?php } else { ?>
            <a href="" hidden><<</a>
        <?php } ?>

        <?php if($pag > 1){ ?>
            <a href="?sol=<?=$sol?>&page=<?= $pag - 1 ?>" style="border: solid 1px; padding:3px;"><</a>
        <?php } else { ?>
            <a href="" hidden><</a>
        <?php } ?>

        <?php if($pag < $ultima_pagina){ ?>
            <a href="?sol=<?=$sol?>&page=<?= $pag + 1 ?>" style="border: solid 1px; padding:3px;">></a>
        <?php } else { ?>
            <a href="" hidden>></a>
        <?php } ?>

        <?php if($pag < ($ultima_pagina - 2)){ ?>
            <a href="?sol=<?=$sol?>&page=<?= $ultima_pagina ?>" style="border: solid 1px; padding:3px;">>></a>
        <?php } else { ?>
            <a href="" hidden>>></a>
        <?php } ?>
    </div>
    

    <h1>FOTOS ROVER</h1>
    <h3>Página <?= $pag ?></h3> <!-- Indicamos la página en la que nos encontramos -->
    <h3>Sol <?= $sol ?></h3> <!-- Indicamos el "sol" en el que nos encontramos -->
    <h3>Fotos por sol: <?= $fotos_por_sol ?></h3> <!-- Indicamos el "sol" en el que nos encontramos -->
    
    <!-- Formulario para cambiar entre los diferentes "sol", podría mejorarlo para que no permita un número menor a 0 ni superior al máximo de "sol"-->
    <div>  
        <form method="get">
            <label>Seleccione el "sol" que quiere ver: </label>
            <input type="number" id="sol" name="sol" placeholder="max. <?= $MAX_sol ?>">
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
                    foreach($fotos as $foto){ ?>
                        <tr>
                            <td><h3><?= $foto["id"] ?></h3></td>
                            <td><img src="<?= $foto["img_src"] ?>" alt="fotito" height="300px" width="300px"></td>
                            <td><h3><?= $foto["camera"]["name"] ?></h3></td>
                            <td><h3><?= $foto["earth_date"] ?></h3></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <h1>Este ROVER no tiene fotos</h1>
    <?php } ?>

    <!-- PAGINACIÓN INFERIOR -->

    <?php if($pag >= 3){ ?>
        <a href="?sol=<?=$sol?>&page=<?= 1 ?>">Inicio</a>
    <?php } else { ?>
        <a href="" hidden>Inicio</a>
    <?php } ?>

    <?php if($pag > 1){ ?>
        <a href="?sol=<?=$sol?>&page=<?= $pag - 1 ?>">Anterior</a>
    <?php } else { ?>
        <a href="" hidden>Anterior</a>
    <?php } ?>

    <?php if($pag < $ultima_pagina){ ?>
        <a href="?sol=<?=$sol?>&page=<?= $pag + 1 ?>">Siguiente</a>
    <?php } else { ?>
        <a href="" hidden>Siguiente</a>
    <?php } ?>

    <?php if($pag < ($ultima_pagina - 2)){ ?>
            <a href="?sol=<?=$sol?>&page=<?= $ultima_pagina ?>">Final</a>
        <?php } else { ?>
            <a href="" hidden>Final</a>
        <?php } ?>
</body>
</html>