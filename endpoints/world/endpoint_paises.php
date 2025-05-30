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
        if(isset($_GET["name"])){
            $sql = "SELECT * FROM country WHERE name = :name";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "name" => $_GET["name"]
            ]);
        } elseif(isset($_GET["capital"])){
            $sql = "SELECT country.*, city.name AS capital FROM country 
                JOIN city ON country.capital = city.id
                WHERE city.name = :capital";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "capital" => $_GET["capital"]
            ]);
        } else{
            $sql = "SELECT * FROM country";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute();
        }

        $resultado = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($resultado);
    }
    
    function manejarPOST($_conexion, $entrada){
        $sql = "INSERT INTO country (code, name, continent, region, surfaceArea, population, localName, governmentForm, code2)
            VALUES (:code, :name, :continent, :region, :surfaceArea, :population, :localName, :governmentForm, :code2)";
        $stmt = $_conexion -> prepare($sql);
        $stmt -> execute([
            "code" => $entrada["code"],
            "name" => $entrada["name"],
            "continent" => $entrada["continent"],
            "region" => $entrada["region"],
            "surfaceArea" => $entrada["surfaceArea"],
            "population" => $entrada["population"],
            "localName" => $entrada["localName"],
            "governmentForm" => $entrada["governmentForm"],
            "code2" => $entrada["code2"]
        ]);
    }
?>