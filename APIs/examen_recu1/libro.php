<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros</title>
</head>
<body>
    <?php

    //$book = $_GET["book"];
    $urlLibro = $_GET($urlLibro);
    $apiUrl = "https://www.anapioficeandfire.com/api/books/$urlLibro";
    $curl = curl_init(); // Inicializamos la libreria cUrl
    curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
    $respuesta = curl_exec($curl);
    curl_close($curl);

    $datos = json_decode($respuesta, true);
    $personajes = $datos["povCharacters"];
    ?>

    <h1><b>Título: </b><?php echo $datos["name"] ?></h1><br>
    <?php
        $findme = "-";
        $pos = strpos($datos["released"], $findme);
    ?>
    <h2><b>Año de lanzamiento: </b><?php echo substr($datos["released"], 0, $pos); ?></h2><br>
    <h2>Nombre personaje POV: </h2>
    <ul>
        <?php foreach($personajes as $personaje) {?>
            <li>
                <?php 
                $apiUrl = $personaje;
                $curl = curl_init(); // Inicializamos la libreria cUrl
                curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
                $respuesta = curl_exec($curl);
                curl_close($curl);

                $datos = json_decode($respuesta, true); 
                
                echo $datos["name"] ?>
            </li>
        <?php } ?>
    </ul>
</body>
</html>