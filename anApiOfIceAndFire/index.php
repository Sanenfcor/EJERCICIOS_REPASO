<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php
        error_reporting( E_ALL );
        ini_set( "display_errors", 1 );
    ?>
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

        $url = "https://www.anapioficeandfire.com/api/characters?page=$pag&pageSize=$limite";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($curl);
        curl_close($curl);
    
        $personajes = json_decode($respuesta, true);
        //print_r($personajes);
        //$personajes = $datos["data"];
        $ultima_pagina = 12;
    ?>

    <div>  
        <form method="get">
            <label>Seleccione la cantidad de personajes: </label>
            <input type="number" id="limit" name="limit">
            <input type="submit" value="Enviar">
        </form>
    </div>
    <div>
        <?php
        foreach($personajes as $personaje) { ?>
            <h1>
                <?php 
                if($personaje["name"] != "") {
                    echo $personaje["name"]; 
                } else {
                    echo "Nombre desconocido";
                }
                ?>
            </h1>
            <h2>
                <?php
                if($personaje["gender"] != "") {
                    echo $personaje["gender"];
                } else {
                    echo "Género desconocido";
                }
                ?>
            </h2>
            <h2>
                <?php
                if($personaje["culture"] != "") {
                    echo $personaje["culture"];
                } else {
                    echo "Cultura desconocida";
                }
                ?>
            </h2>
            <h2>Alias</h2>
            <ul>
                <?php
                $aliases = $personaje["aliases"];
                
                if(count($aliases) == 0) { ?>
                    <li>Este personaje no tiene alias</li>
                <?php } else {
                    foreach($aliases as $alias) { ?>
                        <li>
                            <?php echo $alias ?>
                        </li>
                    <?php }
                } ?>
            </ul>
            <h2>Libros</h2>
            <ol>
                <?php
                $urlsLibros = $personaje["books"];
                
                foreach($urlsLibros as $urlLibro) { 
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $urlLibro);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $respuesta = curl_exec($curl);
                    curl_close($curl);
                
                    $libro = json_decode($respuesta, true);
                    ?>
                    <li>
                        <a href="<?php echo "libro.php?urlLibro=$urlLibro" ?>">
                            <?php echo $libro["name"] ?>
                        </a>
                    </li>
                <?php } ?>
            </ol>
            <br>
        <?php } ?>
    </div>
    <!-- PAGINACION -->
    
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