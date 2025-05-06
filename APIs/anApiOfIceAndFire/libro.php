<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $urlLibro = $_GET["urlLibro"];
    echo "<h1>$urlLibro</h1>";
    $apiUrl = $urlLibro;
    $curl = curl_init(); // Inicializamos la libreria cUrl
    curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
    $respuesta = curl_exec($curl);
    curl_close($curl);

    $libro = json_decode($respuesta, true);
    $personajes = $libro["povCharacters"];
    ?>

    
    <h1><b>Título: </b><?php echo $libro["name"] ?></h1><br>
    <?php
        $findme = "-";
        $pos = strpos($libro["released"], $findme);
    ?>
    <h2><b>Año de lanzamiento: </b><?php echo substr($libro["released"], 0, $pos); ?></h2><br>
    <h2>Nombre personaje POV: </h2>
    <ul>
        <?php
        if(count($personajes) == 0){ ?>
            <li>No tiene personajes</li>
        <?php } else{
            foreach($personajes as $personaje) { ?>
                <li>
                    <?php 
                    $apiUrl = $personaje;
                    $curl = curl_init(); // Inicializamos la libreria cUrl
                    curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de libro
                    $respuesta = curl_exec($curl);
                    curl_close($curl);

                    $libro = json_decode($respuesta, true);

                    if($libro["name"] == null){
                        echo "Nombre desconocido";
                    } else {
                        echo $libro["name"];
                    }
                    ?>
                </li>
            <?php }
        } ?>
    </ul>
</body>
</html>