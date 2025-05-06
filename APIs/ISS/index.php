<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $url = "http://api.open-notify.org/iss-now.json";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $respuesta = curl_exec($curl);
    curl_close($curl);

    $datos = json_decode($respuesta, true);

    $longitud = $datos["iss_position"]["longitude"];
    $latitud = $datos["iss_position"]["latitude"];
    ?>
    <form method="GET">
        <button type="submit">Actualizar posición</button>
    </form>
    <h1>Ubicación de la estación espacial internacional</h1>
    <h3>Longitud: <?= $longitud ?></h3>
    <h3>Latitud: <?= $latitud ?></h3>
    <iframe 
        src="https://maps.google.com/maps?q=<?= $latitud ?>,<?= $longitud ?>&z=4&output=embed" 
        width="600"
        height="400"
        frameborder="0"
    />
    <br>
    
</body>
</html>