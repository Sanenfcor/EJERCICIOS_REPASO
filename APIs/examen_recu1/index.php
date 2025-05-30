<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de tronos</title>
</head>
<body>
    <?php
    if(isset($_GET["page"])){
        $pag = $_GET["page"];
        if($pag < 2){
            $pag = 1;
        }
    } else{
        $pag = 1;
    }

    if(isset($_GET["limit"])){
        $limite = $_GET["limit"];
        if($limite < 1){
            $limite = 5;
        }
    } else{
        $limite = 5;
    }

    $apiUrl = "https://www.anapioficeandfire.com/api/characters?page=$pag&limit=$limite";
    $curl = curl_init(); // Inicializamos la libreria cUrl
    curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
    $respuesta = curl_exec($curl);
    curl_close($curl);

    $datos = json_decode($respuesta, true);
    //$libros = $datos["name"];
    //print_r($datos);
    $ultima_pagina = 12;
    echo "<h1>$ultima_pagina</h1>";
    echo "<h1>$pag</h1>";
    ?>

    <form method="get">
        <label>Seleccione la cantidad de personajes: </label>
        <input type="number" id="limit" name="limit">
        <input type="submit" value="Enviar">
    </form>
    <?php foreach($datos as $dato) {?>
        <?php if ($dato["name"] == null) {
            $nombre = "Desconocido";
        } else {
            $nombre = $dato["name"];
        }?>
        <?php if ($dato["culture"] == null) {
            $cultura = "Cultura desconocida";
        } else {
            $cultura = $dato["culture"];
        }?>
        <h1>Nombre: <?php echo $nombre ?></h1>
        <h2>Genero: <?php echo $dato["gender"] ?></h2>
        <h2>Cultura: <?php echo $cultura ?></h2>
        <h2>Alias: </h2>
        <ul>
            <li><?php echo implode($dato["aliases"]) ?></li>
        </ul>
        <h2>Libros en los que aparece: </h2>
        <ol>
            <?php 
                $urlsLibros = $dato["books"];
                foreach($urlsLibros as $urlLibro) {
                    $apiUrl = $urlLibro;
                    $curl = curl_init(); // Inicializamos la libreria cUrl
                    curl_setopt($curl, CURLOPT_URL, $apiUrl); // Indicamos que la conexion va por URL e indicamos la URL
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Para habilitar la transferencia de datos
                    $respuesta = curl_exec($curl);
                    curl_close($curl);

                    $librito = json_decode($respuesta, true);
                ?>
                <li>
                    <a href="libro.php?urlLibro=<?=$urlLibro ?>">
                        <?php echo $librito["name"] ?>
                </a>
                </li>
            <?php }?>
        </ol><br>
    <?php }?>
    <!-- Poner un if y un else por cada funcion-->
    
    <?php if($pag > 1){ ?>
        <a href="?limit=<?= $limite ?>&page=<?= $pag - 1 ?>">Anterior</a>
    <?php } else { ?>
        <a href="" hidden>Anterior</a>
    <?php } ?>

    <?php if($pag < $ultima_pagina){ ?>
        <a href="?limit=<?= $limite ?>&page=<?= $pag + 1 ?>">Siguiente</a>
    <?php } else { ?>
        <a href="" hidden>Siguiente</a>
    <?php } ?>
    
</body>
</html>