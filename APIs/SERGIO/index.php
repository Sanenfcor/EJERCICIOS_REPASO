<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asteroides Cercanos - NASA</title>
    <?php
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
    ?>
</head>
<body>
    <?php
        $apiKey = "DEMO_KEY";
        $today = date("Y-m-d");
        $url = "https://api.nasa.gov/neo/rest/v1/feed?start_date=$today&end_date=$today&api_key=$apiKey";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);
        $asteroides = $data["near_earth_objects"][$today] ?? [];

        if (!empty($asteroides)) {
            $asteroide = $asteroides[array_rand($asteroides)];
        }
    ?>

    <h1>Asteroide Cercano a la Tierra</h1>

    <?php if ($asteroide) { ?>
        <p><strong>Nombre:</strong> <?= $asteroide["name"] ?></p>
        <p><strong>Diámetro estimado:</strong>
            <?= round($asteroide["estimated_diameter"]["meters"]["estimated_diameter_min"]) ?> -
            <?= round($asteroide["estimated_diameter"]["meters"]["estimated_diameter_max"]) ?> metros
        </p>
        <p><strong>¿Es peligroso?</strong>
            <?= $asteroide["is_potentially_hazardous_asteroid"] ? "Sí" : "No" ?>
        </p>
        <p><strong>Velocidad</strong>
            <?= round($asteroide["close_approach_data"][0]["relative_velocity"]["kilometers_per_hour"]) ?> km/h
        </p>
        <p><strong>Distancia:</strong>
            <?= round($asteroide["close_approach_data"][0]["miss_distance"]["kilometers"]) ?> km
        </p>
    <?php } else { ?>
        <p>No hay asteroides registrados para hoy.</p>
    <?php } ?>

    <br>
    <form method="GET">
        <button type="submit">Obtener otro asteroide</button>
    </form>
</body>
</html>