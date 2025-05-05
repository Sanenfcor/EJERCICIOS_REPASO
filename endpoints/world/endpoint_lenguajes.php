<?php
    header("content-type: application/json");
    include("conexion.php");

    $metodo = $_SERVER["REQUEST_METHOD"];
    $entrada = json_decode(file_get_contents('php://input'), true);

    switch($metodo){
        case "GET":
            manejarGet($_conexion);
            break;
        case "POST":
            manejarPOST($_conexion, $entrada);
            break;
        case "PUT":
            echo json_encode(["metodo" => "put"]);
            break;
        case "DELETE":
            echo json_encode(["metodo" => "delete"]);
            break;
    }

    function manejarGet($_conexion){
        if(isset($_GET["language"])){
            $sql = "SELECT * FROM countrylanguage WHERE language = :language";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "language" => $_GET["language"]
            ]);
        } else{
            $sql = "SELECT * FROM countrylanguage";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute();
        }

        $resultado = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($resultado);
    }

    function manejarPOST($_conexion, $entrada){
        $sql = "INSERT INTO countrylanguage (countryCode, language, isOfficial, percentage)
            VALUES (:countryCode, :language, :isOfficial, :percentage)";
        $stmt = $_conexion -> prepare($sql);
        $stmt -> execute([
            "countryCode" => $entrada["countryCode"],
            "language" => $entrada["language"],
            "isOfficial" => $entrada["isOfficial"],
            "percentage" => $entrada["percentage"]
        ]);
    }
?>