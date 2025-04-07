<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APOD</title>
    <?php
        error_reporting( E_ALL );
        ini_set( "display_errors", 1 );
    ?>
</head>
<body>
    <?php
    $api_key = "G8d3T0PthqgqDJWhdtFSKgYPYDZFgU5JqWNAgGYC";

    if(isset($_GET["date"])){
        $date = $_GET["date"];
    } else{
        $date = "";
    }

    $apiUrl = "https://api.nasa.gov/planetary/apod?api_key=$api_key&date=$date";
    $curl = curl_init(); // Inicializamos la libreria cUrl
    curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
    $respuesta = curl_exec($curl);
    curl_close($curl);

    $datos = json_decode($respuesta, true);

    ?>

    <h1>FOTO DEL DÍA <?= $datos["date"] ?></h1>
    <h3>Título: <?= $datos["title"] ?></h3> <!-- Indicamos el título de la foto -->
    <p><h3>Explicación:</h3> <?= $datos["explanation"] ?></p> <!-- Explicación de la foto -->
    
    <!-- Formulario para cambiar entre las diferentes fechas -->
    <div>  
        <form method="get">
            <label>Introduzca una fecha: </label>
            <input type="date" id="date" name="date">
            <input type="submit" value="Enviar">
        </form>
    </div>

    <!-- Aquí verificamos si es una imagen o un video -->
    <div style="margin-top: 2rem;">
        <?php
        if($datos["media_type"] == "image"){

            if($datos["hdurl"] != null){ ?>
                <img src="<?= $datos["hdurl"] ?>" alt="No se ve la foto HD" height="auto" width="100%">
            <?php } else{ ?>
                <img src="<?= $datos["url"] ?>" alt="No se ve la foto" height="auto" width="100%">
            <?php } ?>

        <?php } else { ?>

            <iframe src="<?= $datos["url"] ?>" height="450px" width="620px">

        <?php } ?>
    </div>

</body>
</html>