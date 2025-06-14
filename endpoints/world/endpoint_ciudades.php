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

    function manejarGET($_conexion){
        if(isset($_GET["name"])){
            $sql = "SELECT * FROM city WHERE name = :name";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "name" => $_GET["name"]
            ]);
        } elseif(isset($_GET["country"])){
            $sql = "SELECT city.*, country.name AS country FROM city 
                JOIN country ON city.countryCode = country.code
                WHERE country.name = :country";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "country" => $_GET["country"]
            ]);
        }else{
            $sql = "SELECT * FROM city";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute();
        }

        $resultado = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($resultado);
    }
    
    function manejarPOST($_conexion, $entrada){
        $sql = "INSERT INTO city (name, countryCode, district, population)
            VALUES (:name, :countryCode, :district, :population)";
        $stmt = $_conexion -> prepare($sql);
        $stmt -> execute([
            "name" => $entrada["name"],
            "countryCode" => $entrada["countryCode"],
            "district" => $entrada["district"],
            "population" => $entrada["population"]
        ]);

        echo json_encode(["mensaje" => "Ciudad insertada exitosamente"]);
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

        echo json_encode(["mensaje" => "Ciudad actualizada exitosamente"]);
    }

    function manejarDELETE($_conexion, $entrada){
        $sql = "DELETE FROM city WHERE name = :name";
        $stmt = $_conexion->prepare($sql);
        $stmt->execute([
            "name" => $entrada["name"]
        ]);

        echo json_encode(["mensaje" => "Ciudad eliminada exitosamente"]);
    }

?>