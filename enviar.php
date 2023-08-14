<?php
//enviar.php
/*
 * RECIBIMOS LA RESPUESTA
*/
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
function enviar($received, $data, $type, $sent, $idWA,$timestamp,$customer_phone) {
    require_once './conexion.php';
    //CONSULTAMOS TODOS LOS REGISTROS CON EL ID DEL MANSAJE
    $sql_quantity = "SELECT count(id) AS cantidad FROM registro WHERE id_wa='" . $idWA . "';";
    $result_quantity = $conn->query($sql_quantity);
    //OBTENEMOS LA CANTIDAD DE MENSAJES ENCONTRADOS (SI ES 0 LO REGISTRAMOS SI NO NO)
    $quantity = 0;
    //SI LA CONSULTA ARROJA RESULTADOS
    if ($result_quantity) {
        //OBTENEMOS EL PRIMER REGISTRO
        $row_quantity = $result_quantity->fetch_row();
        //OBTENEMOS LA CANTIDAD DE REGISTROS
        $quantity = $row_quantity[0];
    }
    //SI LA CANTIDAD DE REGISTROS ES 0 ENVIAMOS EL MENSAJE DE LO CONTRARIO NO LO ENVIAMOS PORQUE YA SE ENVIO
    if ($quantity == 0) {
        //TOKEN QUE NOS DA FACEBOOK
        $token=$_ENV['TOKEN'];
        //PASAMOS EL TELEFONO DEL CLIENTE
        $phone=$customer_phone;
        //IDENTIFICADOR DE NÚMERO DE TELÉFONO
        $phoneID=$_ENV['ID_PHONE'];
        //URL A DONDE SE MANDARA EL MENSAJE
        $url = 'https://graph.facebook.com/v17.0/' . $phoneID . '/messages';
        //CONFIGURACION DEL MENSAJE
        if($type === "text"){
            $message = ''
            . '{'
            . '"messaging_product": "whatsapp", '
            . '"recipient_type": "individual",'
            . '"to": "' . $phone . '", '
            . '"type": "text", '
            . '"text": '
            . '{'
            . '     "body":"' . $sent . '",'
            . '     "preview_url": true, '
            . '} '
            . '}';
        }
        elseif($type === "image"){
            $message = ''
            . '{'
            . '"messaging_product": "whatsapp", '
            . '"recipient_type": "individual",'
            . '"to": "' . $phone . '", '
            . '"type": "image", '
            . '"image": '
            . '{'
            . '     "link":"' . $sent . '"'
            . '} '
            . '} ';
        }
        elseif($type === "file"){
            $message = ''
            . '{'
            . '"messaging_product": "whatsapp", '
            . '"recipient_type": "individual",'
            . '"to": "' . $phone . '", '
            . '"type": "document", '
            . '"document": '
            . '{'
            . '     "link":"' . $sent . '",'
            . '     "filename":"UNICEF.pdf"'
            . '}'
            . '} ';
        }
        elseif($type === "button-reply"){
            $message = '{'
                . '"messaging_product": "whatsapp", '
                . '"recipient_type": "individual",'
                . '"to": "' . $phone . '", '
                . '"type": "interactive", '
                . '"interactive":'
                . '{'
                . '     "type": "button", '
                . '     "header":'
                . '{'
                . '         "type": "image",'
                . '         "image": '
                . '{'
                . '         "link":"' . $data . '"'
                . '} '
                . '}, '
                . '     "body":'
                . '{'
                . '         "text": "' . $sent . '"'
                . '}, '
                . '     "action":'
                . '{'
                . '         "buttons":'
                . '['
                . '{'
                . '                 "type": "reply", '
                . '                 "reply":'
                . '{'
                . '                     "id": "unique-postback-id-1",'
                . '                     "title": "¡Gracias!" '
                . '}'
                . '},'
                . '{'
                . '                 "type": "reply", '
                . '                 "reply":'
                . '{'
                . '                     "id": "unique-postback-id-2",'
                . '                     "title": "Quiero más info" '
                . '}'
                . '}'
                . ']'
                . '}'
                . '}'
                . '}';
            
        }


        //DECLARAMOS LAS CABECERAS
        $header = array("Authorization: Bearer " . $token, "Content-Type: application/json",);
        //INICIAMOS EL CURL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $message);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //OBTENEMOS LA RESPUESTA DEL ENVIO DE INFORMACION
        $response = json_decode(curl_exec($curl), true);
        //OBTENEMOS EL CODIGO DE LA RESPUESTA
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //CERRAMOS EL CURL
        curl_close($curl);


        //INSERTAMOS LOS REGISTROS DEL ENVIO DEL WHATSAPP
            // Preparar la consulta SQL con una sentencia preparada
        $sql = "INSERT INTO registro (mensaje_recibido, mensaje_enviado, id_wa, timestamp_wa, telefono_wa) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
            // Vincular los valores a los parámetros de la sentencia preparada
        $stmt->bind_param("sssss", $received, $sent, $idWA, $timestamp, $customer_phone);
        $stmt->execute();
        $stmt->close();
    

    }
}
