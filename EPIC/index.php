<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPIC</title>
    <?php
        error_reporting( E_ALL );
        ini_set( "display_errors", 1 );
    ?>
</head>
<body>
    <?php
    $api_key = "SrTyHppF4HKyPesBVfHSzFEASnTXvA8m3TGYewKa";

    if(isset($_GET["date"])){
        $date = $_GET["date"];
    } else{
        $date = "2025-04-05";
    }

    //Primera llamada a la API

    $apiUrl = "https://api.nasa.gov/EPIC/api/natural/date/$date?api_key=$api_key";
    $curl = curl_init(); // Inicializamos la libreria cUrl
    curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
    $respuesta = curl_exec($curl);
    curl_close($curl);

    $datos = json_decode($respuesta, true);

    //var_dump($datos);
    ?>

    <h1>FOTOS EPIC</h1>
    
    <!-- Formulario para cambiar entre los diferentes "sol", podría mejorarlo para que no permita un número menor a 0 ni superior al máximo de "sol"-->
    <div>  
        <form method="get">
            <label>Introduzca una fecha: </label>
            <input type="date" id="date" name="date">
            <input type="submit" value="Enviar">
        </form>
    </div>

    <!-- ¿Se podría hacer otro formulario para seleccionar el rover que queremos usar? -->

    <!-- Aquí verificamos si los "sol" están vacíos o no, pues hay alguno que no tiene fotos -->
    <?php
    if(count($datos) != null){ ?>
        <div>
            <table border="5px solid black" style="margin:auto; margin-top:2rem; margin-bottom:2rem; text-align:center; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="height:300px; width:300px;"><h1>ID</h1></th> <!-- El id son la fecha y hora, pero con el delay con el que le llegan al ordenador las imagenes desde el satélite, 288 ~ 289 s. -->
                        <th><h1>IMG</h1></th>
                        <th style="height:300px; width:300px;"><h1>Fecha</h1></th>
                        <th style="height:300px; width:300px;"><h1>Hora</h1></th>
                    </tr>    
                </thead>
                <tbody>
                    <?php
                    foreach($datos as $dato){ 
                        $pos = strpos($dato["date"], " ");
                        $anio = substr($dato["date"], 0, 4);
                        $mes = substr($dato["date"], 5, 2);
                        $dia = substr($dato["date"], 8, 2);
                    ?>
                        <tr>
                            <td><h3><?= $dato["identifier"] ?></h3></td>
                            <td>
                                <a href="https://api.nasa.gov/EPIC/archive/natural/<?= $anio ?>/<?= $mes ?>/<?= $dia ?>/png/<?= $dato["image"] ?>.png?api_key=<?= $api_key ?>">
                                    <img src="https://api.nasa.gov/EPIC/archive/natural/<?= $anio ?>/<?= $mes ?>/<?= $dia ?>/png/<?= $dato["image"] ?>.png?api_key=<?= $api_key ?>" height="300px" width="300px">
                                </a>
                            </td>
                            <td><h3><?= substr($dato["date"], 0, $pos) ?></h3></td>
                            <td><h3><?= substr($dato["date"], $pos) ?></h3></td>
                        </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <h1>Este día no tiene fotos</h1>
    <?php } ?>
</body>
</html>