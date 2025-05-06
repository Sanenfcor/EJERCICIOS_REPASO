<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoWs</title>
</head>
<body>
    <?php
    $api_key = "G8d3T0PthqgqDJWhdtFSKgYPYDZFgU5JqWNAgGYC";

    if(isset($_GET["page"])){
        $pag = $_GET["page"];
        if($pag < 1){
            $pag = 0;
        }
    } else{
        $pag = 0;
    }

    if(isset($_GET["size"])){
        $size = $_GET["size"];
        if($size < 1){
            $size = 5;
        }
    } else{
        $size = 5;
    }

    //Llamada a la API

    $apiUrl = "https://api.nasa.gov/neo/rest/v1/neo/browse?size=$size&page=$pag";
    $curl = curl_init(); // Inicializamos la libreria cUrl
    curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, $api_key . ":");
    $respuesta = curl_exec($curl);
    curl_close($curl);

    $datos = json_decode($respuesta, true);

    $objetos = $datos["near_earth_objects"];

    $ultima_pagina = ($datos["page"]["total_pages"] - 1);

    ?>

    <!-- PAGINACIÓN SUPERIOR -->

    <div style="border: solid 1px; width:fit-content; padding:3px;">
        <?php if($pag >= 2){ ?>
            <a href="?size=<?= $size ?>&page=<?= 0 ?>" style="border: solid 1px; padding:3px;"><<</a>
        <?php } else { ?>
            <a href="" hidden><<</a>
        <?php } ?>

        <?php if($pag > 0){ ?>
            <a href="?size=<?= $size  ?>&page=<?= $pag - 1 ?>" style="border: solid 1px; padding:3px;"><</a>
        <?php } else { ?>
            <a href="" hidden><</a>
        <?php } ?>

        <?php if($pag < $ultima_pagina){ ?>
            <a href="?size=<?= $size ?>&page=<?= $pag + 1 ?>" style="border: solid 1px; padding:3px;">></a>
        <?php } else { ?>
            <a href="" hidden>></a>
        <?php } ?>

        <?php if($pag <= ($ultima_pagina - 2)){ ?>
            <a href="?size=<?= $size ?>&page=<?= $ultima_pagina ?>" style="border: solid 1px; padding:3px;">>></a>
        <?php } else { ?>
            <a href="" hidden>>></a>
        <?php } ?>
    </div>


    <!-- FORMULARIO DE LÍMITE -->

    <div>
        <form method="get">
            <label>Seleccione la cantidad de objetos por página:</label>
            <input type="number" id="size" name="size">
            <input type="submit" value="Enviar">
        </form>
    </div>

    <!-- Aquí verificamos si están vacíos o no -->
    <?php
    if(count($objetos) != null){ ?>
        <div>
            <?php
                foreach($objetos as $objeto){ 
                    $id = $objeto["id"];
                    $urlIndi = "https://api.nasa.gov/neo/rest/v1/neo/$id"
                ?>
                    <hr>
                    <h3>ID: <?= $id ?></h3>
                    <h3>name: <a href="<?= "individual.php?urlIndi=$urlIndi" ?>"> <?= $objeto["name"] ?> </a></h3>
                <?php if(isset($objeto["name_limited"])){ ?>
                    <h3>name_limited: <?= $objeto["name_limited"] ?></h3>
                <?php } ?>
                    <h3>designation: <?= $objeto["designation"] ?></h3>
                    <hr>
                    <br>
            <?php } ?>
        </div>
    <?php } else { ?>
        <h1>Esta página no tiene elementos</h1>
    <?php } ?>

    <!-- PAGINACIÓN INFERIOR -->

    <div style="border: solid 1px; width:fit-content; padding:3px;">
        <?php if($pag >= 2){ ?>
            <a href="?size=<?= $size ?>&page=<?= 0 ?>" style="border: solid 1px; padding:3px;"><<</a>
        <?php } else { ?>
            <a href="" hidden><<</a>
        <?php } ?>

        <?php if($pag > 0){ ?>
            <a href="?size=<?= $size  ?>&page=<?= $pag - 1 ?>" style="border: solid 1px; padding:3px;"><</a>
        <?php } else { ?>
            <a href="" hidden><</a>
        <?php } ?>

        <?php if($pag < $ultima_pagina){ ?>
            <a href="?size=<?= $size ?>&page=<?= $pag + 1 ?>" style="border: solid 1px; padding:3px;">></a>
        <?php } else { ?>
            <a href="" hidden>></a>
        <?php } ?>

        <?php if($pag <= ($ultima_pagina - 2)){ ?>
            <a href="?size=<?= $size ?>&page=<?= $ultima_pagina ?>" style="border: solid 1px; padding:3px;">>></a>
        <?php } else { ?>
            <a href="" hidden>>></a>
        <?php } ?>
    </div>
</body>
</html>