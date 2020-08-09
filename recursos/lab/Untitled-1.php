
        /*require '../../vendor/autoload.php';
        MercadoPago\SDK::setAccessToken('TEST-1562349347568381-080402-1818024ae10d94b0da38c762088672dc-620380348'); // Agrega credenciales
        $preference = new MercadoPago\Preference(); // Crea un objeto de preferencia
        $preference->back_urls = array(
            "success" => "https://localhost/cuentascorrientes/recursos/pagos/aprobado.php",
            "failure" => "http://www.tu-sitio/failure",
            "pending" => "http://www.tu-sitio/pending"
        );
        $preference->auto_return = "approved";
        $item = new MercadoPago\Item(); // Crea un ítem en la preferencia
        $item->title = 'Pañales absorventes';
        $item->quantity = $CantidadParcial;
        $item->unit_price = $saldoParcial;
        $preference->items = array($item);
        $preference->save();

        $datos['preferenciaId'] = $preference->id;  // Prepara la preferencia para enviarla a través del controlador