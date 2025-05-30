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
        // Debemos hallar una manera de que conforme vayamos añadiendo valores a la URL, añada esas condiciones
        /*
        Voy a ir anotanddo las ideas que tengo para que no se me olviden:
            - Creo que podria usar MIN(), MAX() y HAVING() para que se guarden los valores maximos, debo investigar más cómo se hace con having
                -   SELECT * FROM payment GROUP BY payment_id 
                    HAVING MIN(amount) > $_GET(min_amount); Y aquí poder añadirle condiciones como al final de la función
                    -   He encontrado un problema con el HAVING. Se usa en estos casos, pero el error está más bien en el enfoque que le estoy dando al problema
            - El problema se puede resolver llamando directamente al amount de la tabla y diciéndole que no supere el valor que introducimos 
        */  
        $cond = [];
        $param = [];

        if(isset($_GET["p_hasta"])){ // Pago máximo
            $cond[] = "amount <= :p_hasta";
            $param["p_hasta"] = $_GET["p_hasta"];
        }
        if(isset($_GET["p_desde"])){ // Pago mínimo
            $cond[] = "amount >= :p_desde";
            $param["p_desde"] = $_GET["p_desde"];
        }
        if(isset($_GET["f_hasta"])){ // Fecha máxima
            $cond[] = "payment_date <= :f_hasta";
            $param["f_hasta"] = $_GET["f_hasta"];
        }
        if(isset($_GET["f_desde"])){ // Fecha mínima
            $cond[] = "payment_date >= :f_desde";
            $param["f_desde"] = $_GET["f_desde"];
        }

        $sql = "SELECT * FROM payment";
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
        $stmt_max = $_conexion -> prepare("SELECT MAX(payment_id) AS max_id FROM payment");
        $stmt_max -> execute();
        $resultado_max = $stmt_max -> fetch(PDO::FETCH_ASSOC);
        $nuevo_id = $resultado_max["max_id"] +1;

        $sql = "INSERT INTO payment (payment_id, customer_id, staff_id, amount, payment_date)
                VALUES (:payment_id, :customer_id, :staff_id, :amount, :payment_date)";
        $stmt = $_conexion -> prepare($sql);
        $stmt -> execute([
            "payment_id" => $nuevo_id,
            "customer_id" => $entrada["customer_id"],
            "staff_id" => $entrada["staff_id"],
            "amount" => $entrada["amount"],
            "payment_date" => $entrada["payment_date"]
        ]);

        echo json_encode(["mensaje" => "Pago insertado exitosamente"]);
    }

    function manejarPUT($_conexion, $entrada){
        if(isset($entrada["payment_id"])){
            $sql = "UPDATE payment SET 
                    customer_id = :customer_id,
                    staff_id = :staff_id,
                    rental_id = :rental_id,
                    amount = :amount,
                    payment_date = :payment_date,
                    last_update = :last_update
                    WHERE payment_id = :payment_id";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "payment_id" => $entrada["payment_id"],
                "customer_id" => $entrada["customer_id"],
                "staff_id" => $entrada["staff_id"],
                "rental_id" => $entrada["rental_id"],
                "amount" => $entrada["amount"],
                "payment_date" => $entrada["payment_date"],
                "last_update" => $entrada["last_update"]
            ]);

            echo json_encode(["mensaje" => "Pago actualizado exitosamente"]);
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
        if(isset($entrada["payment_id"])){
            $sql = "DELETE FROM payment WHERE payment_id = :payment_id";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "payment_id" => $entrada["payment_id"]
            ]);
            echo json_encode(["mensaje" => "Pago eliminado exitosamente mediante ID"]);
        } elseif(isset($entrada["customer_id"])){
            $sql = "DELETE FROM payment WHERE customer_id = :customer_id";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "customer_id" => $entrada["customer_id"]
            ]);
            echo json_encode(["mensaje" => "Pago eliminado exitosamente mediante CUSTOMER_ID"]);
        } elseif(isset($entrada["staff_id"])){
            $sql = "DELETE FROM payment WHERE staff_id = :staff_id";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "staff_id" => $entrada["staff_id"]
            ]);
            echo json_encode(["mensaje" => "Pago eliminado exitosamente mediante STAFF_ID"]);
        } else{
            echo json_encode(["error" => "Debe proporcionar al menos un criterio para la eliminación"]);
            return;
        }
    }
?>