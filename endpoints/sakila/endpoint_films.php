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
        /* 
            ESTE CODIGO ESTÁ REGULAR, en casode querer buscar por nombre y por id a la vez, nos aparece el primer valor introducido, pero no lo quiero así.
            Cuando se introduzcan dos valores, quiero que coincidan, en caso de no hacerlo, no aparece nada, o aparece un mensaje indicando que los parámetros no coinciden.
            Esto creo que debo hacerlo con un array e introducirle los valores. El otro problema de esto, es que de querer que se miren todos es quitando los "elseif" por "if",
            Lo único que hace esto es que siempre salgan todos los valores.
        */
        /* if(isset($_GET["film_id"])){
            $sql = "SELECT * FROM film WHERE film_id = :film_id";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "film_id" => $_GET["film_id"]
            ]);
        } elseif(isset($_GET["title"])){
            $sql = "SELECT * FROM film WHERE title = :title";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "title" => $_GET["title"]
            ]);
        } elseif(isset($_GET["rating"])){
            $sql = "SELECT * FROM film WHERE rating = :rating";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "rating" => $_GET["rating"]
            ]);
        } else{
            $sql = "SELECT * FROM film";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute();
        } */

        $cond = []; // Condiciones de la búsqueda
        $param = []; // Lo que buscamos de verdad

        if(isset($_GET["film_id"])){
            $cond[] = "film_id = :film_id"; // Aquí, en caso de que se introduzca algún valor, se añade al array de condiciones lo mismo que hemos estado añadiendo después del WHERE en los casos anteriores
            $param["film_id"] = $_GET["film_id"]; // Añadimos al array el valor de lo que hemos introducido en la URL
        }
        if(isset($_GET["title"])){
            $cond[] = "title = :title";
            $param["title"] = $_GET["title"];
        }
        if(isset($_GET["rating"])){
            $cond[] = "rating = :rating";
            $param["rating"] = $_GET["rating"];
        }

        $sql = "SELECT * FROM film";
        if(!empty($cond)){
            $sql .= " WHERE " . implode(" AND ", $cond); // La condicion AND hace que todas las condiciones que le añadamos deban ser ciertas para que nos aparezca el resultado por pantalla
        }

        $stmt = $_conexion -> prepare($sql);
        $stmt -> execute($param);

        $resultado = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($resultado);
    }

    function manejarPOST($_conexion, $entrada){
        // SELECCIONAR EL ID MAXIMO DE LA TABLA DE DATOS PARA INTRODUCIRLO DE MANERA AUTOMÁTICA
        //$max_id = "SELECT MAX(film_id) AS max_id FROM film";    Idea original pero hay que hacer un statement.
        $stmt_max = $_conexion -> prepare("SELECT MAX(film_id) AS max_id FROM film");
        $stmt_max -> execute();
        $resultado_max = $stmt_max -> fetch(PDO::FETCH_ASSOC);
        $nuevo_id = $resultado_max["max_id"] +1;

        $sql = "INSERT INTO film (film_id, title, language_id, rental_duration, rental_rate, replacement_cost, last_update)
                VALUES (:film_id, :title, :language_id, :rental_duration, :rental_rate, :replacement_cost, :last_update)";
        $stmt = $_conexion -> prepare($sql);
        $stmt -> execute([
            // HECHO -> Tomar valor maximo a traves de código y peticiones a la base de datos, de tal manera que sea automática la inserción del id
            "film_id" => $nuevo_id,
            "title" => $entrada["title"],
            "language_id" => $entrada["language_id"],
            "rental_duration" => $entrada["rental_duration"],
            "rental_rate" => $entrada["rental_rate"],
            "replacement_cost" => $entrada["replacement_cost"],
            "last_update" => $entrada["last_update"]
        ]);

        echo json_encode(["mensaje" => "Pelicula insertada exitosamente"]);
    }

    function manejarPUT($_conexion, $entrada){
        if(isset($entrada["film_id"])){
            $sql = "UPDATE film SET 
                    title = :title,
                    language_id = :language_id,
                    rental_duration = :rental_duration,
                    rental_rate = :rental_rate,
                    replacement_cost = :replacement_cost,
                    last_update = :last_update
                    WHERE film_id = :film_id";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                // Aquí se pone el film_id, pero este debe ser el mismo, debe ser el unico valor que no se modifique.
                "film_id" => $entrada["film_id"],
                "title" => $entrada["title"],
                "language_id" => $entrada["language_id"],
                "rental_duration" => $entrada["rental_duration"],
                "rental_rate" => $entrada["rental_rate"],
                "replacement_cost" => $entrada["replacement_cost"],
                "last_update" => $entrada["last_update"]
            ]);

            echo json_encode(["mensaje" => "Pelicula actualizada exitosamente"]);
        } else{
            echo json_encode(["error" => "El ID debe ser introducido"]);  
        }
    }

    function manejarDELETE($_conexion, $entrada){
        /* 
        Este código funciona para películas introducidas por el usuario, pues no tienen dependencias con otras tablas. 
        En el caso de querer eliminar películas que ya estén introducidas, debemos eliminar las dependencias que comparten.
        Se debe eliminar desde el código raw de Postman, de introducirla en la URL no se elimina.
        */
        if(isset($entrada["title"])){
            $sql = "DELETE FROM film WHERE title = :title";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "title" => $entrada["title"]
            ]);
            echo json_encode(["mensaje" => "Pelicula eliminada exitosamente mediante TITLE"]);
        } elseif(isset($entrada["rating"])){
            $sql = "DELETE FROM film WHERE rating = :rating";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "rating" => $entrada["rating"]
            ]);
            echo json_encode(["mensaje" => "Pelicula eliminada exitosamente mediante RATING"]);
        } elseif(isset($entrada["film_id"])){
            $sql = "DELETE FROM film WHERE film_id = :film_id";
            $stmt = $_conexion -> prepare($sql);
            $stmt -> execute([
                "film_id" => $entrada["film_id"]
            ]);
            echo json_encode(["mensaje" => "Pelicula eliminada exitosamente mediante ID"]);
        } else{
            echo json_encode(["error" => "Debe proporcionar al menos un criterio para la eliminación"]);
            return;
        }
    }
?>