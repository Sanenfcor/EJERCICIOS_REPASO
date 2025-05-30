<?php
    header("content-type: application/json");
    include("conexion.php");

    $metodo = $_SERVER["REQUEST_METHOD"];
    $entrada = json_decode(file_get_contents('php://input'), true);

    switch($metodo){
        case "GET":
            //echo json_encode(["metodo" => "GET"]);
            manejarGET($_conexion);
            break;
        case "POST":
            //echo json_encode(["metodo" => "POST"]);
            manejarPOST($_conexion, $entrada);
            break;
        case "PUT":
            //echo json_encode(["metodo" => "PUT"]);
            manejarPUT($_conexion, $entrada);
            break;
        case "DELETE":
            //echo json_encode(["metodo" => "DELETE"]);
            manejarDELETE($_conexion, $entrada);
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
        
        echo json_encode(["mensaje" => "Lenguaje actualizado exitosamente"]);
    }

    function manejarPUT($_conexion, $entrada){
        $sql = "UPDATE city SET countryCode = :countryCode, district = :district, population = :population
            WHERE name = :name";
        $stmt = $_conexion->prepare($sql);
        $stmt -> execute([
            "name" => $entrada["name"],
            "countryCode" => $entrada["countryCode"],
            "district" => $entrada["district"],
            "population" => $entrada["population"]
        ]);

        echo json_encode(["mensaje" => "Lenguaje actualizado exitosamente"]);
    }

    function manejarDELETE($_conexion, $entrada){
        $sql = "DELETE FROM city WHERE name = :name";
        $stmt = $_conexion->prepare($sql);
        $stmt->execute([
            "name" => $entrada["name"]
        ]);

        echo json_encode(["mensaje" => "Lenguaje eliminado exitosamente"]);
    }

?>