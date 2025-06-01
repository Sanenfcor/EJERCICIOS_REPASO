<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagos</title>
    <?php
        error_reporting( E_ALL );
        ini_set( "display_errors", 1 );
    ?>
</head>
<body>

    <?php
    $customer = isset($_GET["customer"]) ? urlencode($_GET["customer"]) : "";
    $staff = isset($_GET["staff"]) ? urlencode($_GET["staff"]) : "";

    if(isset($_GET["page"])){
        $pag = $_GET["page"];
        if($pag < 2){
            $pag = 1;
        }
    } else{
        $pag = 1;
    }

    if(isset($_GET["limit"])){
        $limite = $_GET["limit"];
        if($limite < 1){
            $limite = 10;
        }
    } else{
        $limite = 10;
    }
    
    if(isset($_GET["customer"])){
        $customer = $_GET["customer"];
        if($customer < 1){
            $customer = 1;
        }
    } else{
        $customer = null;
    }

    if(isset($_GET["staff"])){
        $staff = $_GET["staff"];
        if($staff < 1){
            $staff = 1;
        }
    } else{
        $staff = null;
    }

    $url = "http://localhost:8081/CLASES_REPASO/endpoints/sakila/endpoint_payments.php?page=$pag&limit=$limite&customer=$customer&staff=$staff";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    $payments = json_decode($response, true);
    ?>

    <h1>Lista de Pagos</h1>

    <form method="get">
        <label for="customer">Cliente (id):</label>
        <input type="text" name="customer" id="customer" value="<?= $customer ?>">

        <label for="staff">Empleado (id):</label>
        <input type="text" name="staff" id="staff" value="<?= $staff ?>">

        <input type="submit" value="Filtrar">
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente (ID)</th>
                <th>Empleado (ID)</th>
                <th>€</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $pago){ ?>
                <tr>
                    <td><?= $pago['payment_id'] ?></td>
                    <td><?= $pago['customer_id'] ?></td>
                    <td><?= $pago['staff_id'] ?></td>
                    <td><?= $pago['amount'] ?></td>
                    <td><?= $pago['payment_date'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div>
        <?php if ($pag > 1){ ?>
            <a href="?page=<?= $pag - 1 ?>&customer=<?= $customer ?>&staff=<?= $staff ?>">Anterior</a>
        <?php } ?>
        <a href="?page=<?= $pag + 1 ?>&customer=<?= $customer ?>&staff=<?= $staff ?>">Siguiente</a>
    </div>
</body>
</html>
