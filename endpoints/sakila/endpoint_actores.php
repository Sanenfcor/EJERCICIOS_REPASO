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

        if(isset($_GET["actor_id"])){
            $cond[] = "actor_id = :actor_id";
            $param["actor_id"] = $_GET["actor_id"];
        }
        if(isset($_GET["first_name"])){
            $cond[] = "first_name = :first_name";
            $param["first_name"] = $_GET["first_name"];
        }
        if(isset($_GET["last_name"])){
            $cond[] = "last_name = :last_name";
            $param["last_name"] = $_GET["last_name"];
        }

        $sql = "SELECT * FROM actor";
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
        $stmt_max = $_conexion -> prepare("SELECT MAX(actor_id) AS max_id FROM actor");
        $stmt_max -> execute();
        $resultado_max = $stmt_max -> fetch(PDO::FETCH_ASSOC);
        $nuevo_id = $resultado_max["max_id"] +1;

        $sql = "INSERT INTO actor (actor_id, first_name, last_name, last_update)
                VALUES (:actor_id, :first_name, :last_name, :last_update)";
        $stmt = $_conexion -> prepare($sql);
        $stmt -> execute([
            "actor_id" => $nuevo_id,
            "first_name" => $entrada["first_name"],
            "last_name" => $entrada["last_name"],
            "last_update" => $entrada["last_update"]
        ]);

        echo json_encode(["mensaje" => "Actor insertado exitosamente"]);
    }

    function manejarPUT($_conexion, $entrada){
        if(isset($entrada["actor_id"])){
            $sql = "UPDATE actor SET 
                    first_name = :first_name,
                    last_name = :last_name,
                    last_update = :last_update
                    WHERE actor_id = :actor_id";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "actor_id" => $entrada["actor_id"],
                "first_name" => $entrada["first_name"],
                "last_name" => $entrada["last_name"],
                "last_update" => $entrada["last_update"]
            ]);

            echo json_encode(["mensaje" => "Actor actualizado exitosamente"]);
        } else{
            echo json_encode(["error" => "El ID debe ser introducido"]);  
        }
    }

    function manejarDELETE($_conexion, $entrada){
        /* 
        Este código funciona para actores introducidos por el usuario, pues no tienen dependencias con otras tablas. 
        En el caso de querer eliminar actores que ya estén introducidos, debemos eliminar las dependencias que comparten.
        Se debe eliminar desde el código raw de Postman, de introducirlo en la URL no se elimina.
        */
        if(isset($entrada["first_name"])){
            $sql = "DELETE FROM actor WHERE first_name = :first_name";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "first_name" => $entrada["first_name"]
            ]);
            echo json_encode(["mensaje" => "Pelicula eliminada exitosamente mediante FIRST_NAME"]);
        } elseif(isset($entrada["last_name"])){
            $sql = "DELETE FROM actor WHERE last_name = :last_name";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "last_name" => $entrada["last_name"]
            ]);
            echo json_encode(["mensaje" => "Pelicula eliminada exitosamente mediante LAST_NAME"]);
        } elseif(isset($entrada["actor_id"])){
            $sql = "DELETE FROM actor WHERE actor_id = :actor_id";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "actor_id" => $entrada["actor_id"]
            ]);
            echo json_encode(["mensaje" => "Actor eliminado exitosamente mediante ID"]);
        } else{
            echo json_encode(["error" => "Debe proporcionar al menos un criterio para la eliminación"]);
            return;
        }
    }
?>