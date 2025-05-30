<?php
    error_reporting( E_ALL );
    ini_set("display_errores", 1);

    header("Content-Type: application/json");
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
        
        $cond = [];
        $param = [];

        if(isset($_GET["address_id"])){
            $cond[] = "address_id = :address_id";
            $param["address_id"] = $_GET["address_id"];
        }
        if(isset($_GET["address"])){
            $cond[] = "address = :address";
            $param["address"] = $_GET["address"];
        }
        if(isset($_GET["address2"])){
            $cond[] = "address2 = :address2";
            $param["address2"] = $_GET["address2"];
        }
        if(isset($_GET["district"])){
            $cond[] = "district = :district";
            $param["district"] = $_GET["district"];
        }
        if(isset($_GET["city_id"])){
            $cond[] = "city_id = :city_id";
            $param["city_id"] = $_GET["city_id"];
        }
        if(isset($_GET["postal_code"])){
            $cond[] = "postal_code = :postal_code";
            $param["postal_code"] = $_GET["postal_code"];
        }
        if(isset($_GET["phone"])){
            $cond[] = "phone = :phone";
            $param["phone"] = $_GET["phone"];
        }

        $sql = "SELECT address_id, address, address2, district, city_id, postal_code, phone, last_update 
                FROM address";
        if(!empty($cond)){
            $sql .= " WHERE " . implode(" AND ", $cond); // AND || OR
        }

        $stmt = $_conexion -> prepare($sql);
        $stmt -> execute($param);

        $resultado = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($resultado);
    }

    function manejarPOST($_conexion, $entrada){
        // SELECCIONAR EL ID MAXIMO DE LA TABLA DE DATOS PARA INTRODUCIRLO DE MANERA AUTOMÁTICA
        $stmt_max = $_conexion -> prepare("SELECT MAX(address_id) AS max_id FROM address");
        $stmt_max -> execute();
        $resultado_max = $stmt_max -> fetch(PDO::FETCH_ASSOC);
        $nuevo_id = $resultado_max["max_id"] +1;

        $sql = "INSERT INTO address (address_id, address, district, city_id, phone, location, last_update)
                VALUES (:address_id, :address, :district, :city_id, :phone, :location, :last_update)";
        $stmt = $_conexion -> prepare($sql);
        $stmt -> execute([
            "address_id" => $nuevo_id,
            "address" => $entrada["address"],
            "district" => $entrada["district"],
            "city_id" => $entrada["city_id"],
            "phone" => $entrada["phone"],
            "location" => $entrada["location"],
            "last_update" => $entrada["last_update"]
        ]);

        echo json_encode(["mensaje" => "Dirección insertada exitosamente"]);
    }

    function manejarPUT($_conexion, $entrada){
        if(isset($entrada["address_id"])){
            $sql = "UPDATE address SET 
                    address = :address,
                    district = :district,
                    city_id = :city_id,
                    phone = :phone,
                    location = :location,
                    last_update = :last_update
                    WHERE address_id = :address_id";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "address_id" => $entrada["address_id"],
                "address" => $entrada["address"],
                "district" => $entrada["district"],
                "city_id" => $entrada["city_id"],
                "phone" => $entrada["phone"],
                "location" => $entrada["location"],
                "last_update" => $entrada["last_update"]
            ]);

            echo json_encode(["mensaje" => "Dirección actualizada exitosamente"]);
        } else{
            echo json_encode(["error" => "El ID debe ser introducido"]);  
        }
    }

    function manejarDELETE($_conexion, $entrada){
        /* 
        Este código funciona para direcciones introducidas por el usuario, pues no tienen dependencias con otras tablas. 
        En el caso de querer eliminar direcciones que ya estén introducidas, debemos eliminar las dependencias que comparten.
        Se debe eliminar desde el código raw de Postman, de introducirlo en la URL no se elimina.
        */
        if(isset($entrada["address"])){
            $sql = "DELETE FROM address WHERE address = :address";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "address" => $entrada["address"]
            ]);
            echo json_encode(["mensaje" => "Direccion eliminada exitosamente mediante ADDRESS"]);
        } elseif(isset($entrada["district"])){
            $sql = "DELETE FROM address WHERE district = :district";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "district" => $entrada["district"]
            ]);
            echo json_encode(["mensaje" => "Direccion eliminada exitosamente mediante DISTRICT"]);
        } elseif(isset($entrada["address_id"])){
            $sql = "DELETE FROM address WHERE address_id = :address_id";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "address_id" => $entrada["address_id"]
            ]);
            echo json_encode(["mensaje" => "Dirección eliminada exitosamente mediante ID"]);
        } else{
            echo json_encode(["error" => "Debe proporcionar al menos un criterio para la eliminación"]);
            return;
        }
    }
?>