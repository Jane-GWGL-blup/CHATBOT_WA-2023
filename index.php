<?php
//DESHABILITAMOS EL MOSTRAR ERRORES
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(-1);

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
/*
 * VERIFICACION DEL WEBHOOK
*/
//TOQUEN QUE QUERRAMOS PONER 
$token = $_ENV['WEBHOOK_IDENTIFIER'];
//RETO QUE RECIBIREMOS DE FACEBOOK
$challenge_word = $_GET['hub_challenge'];
//TOQUEN DE VERIFICACION QUE RECIBIREMOS DE FACEBOOK
$verify_token = $_GET['hub_verify_token'];
//SI EL TOKEN QUE GENERAMOS ES EL MISMO QUE NOS ENVIA FACEBOOK RETORNAMOS EL RETO PARA VALIDAR QUE SOMOS NOSOTROS
if ($token === $verify_token) {
    echo $challenge_word;
    exit;
}

/*
 * RECEPCION DE MENSAJES
 */
//LEEMOS LOS DATOS ENVIADOS POR WHATSAPP
$response = file_get_contents("php://input");
//CONVERTIMOS EL JSON EN ARRAY DE PHP
$response = json_decode($response, true);
//EXTRAEMOS EL MENSAJE DEL ARRAY
$message=$response['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
//EXTRAEMOS EL TELEFONO DEL ARRAY
$customer_phone=$response['entry'][0]['changes'][0]['value']['messages'][0]['from'];
//EXTRAEMOS EL ID DE WHATSAPP DEL ARRAY
$id=$response['entry'][0]['changes'][0]['value']['messages'][0]['id'];
//EXTRAEMOS EL TIEMPO DE WHATSAPP DEL ARRAY
$timestamp=$response['entry'][0]['changes'][0]['value']['messages'][0]['timestamp'];


//SI HAY UN MENSAJE
if($message!=null){
    
    // Array para mapear caracteres con tildes a caracteres sin tildes
    $unwanted_array = array(    
        'á'=>'a', 'Á'=>'a',
        'é'=>'e', 'É'=>'e',
        'í'=>'i', 'Í'=>'i',
        'ó'=>'o', 'Ó'=>'o',
        'ú'=>'u', 'Ú'=>'u',
    );

    $message = strtr($message, $unwanted_array);  // Reemplaza los caracteres con tildes
    $message = strtolower($message); // Convertir el message a minúsculas para facilitar la comparación
    /*
    * LOGICA DE LA CONVERSACION
    */
    if (empty($message)) {
        // No se ha recibido ninguna response después del message de bienvenida anterior
        // No se envía ningún message adicional
        exit;
    } elseif (strpos($message, 'hola') !== false || strpos($message, 'hay alguien') !== false || strpos($message, 'como estas') !== false) {
        $response= "¡Hola! Soy Diana tu asistente virtual de Reliser Safety Training." .'\n'. "¿En que puedo ayudarte?".'\n\n'."1️⃣ ¿Algún asesor? 🧑🏻".'\n'."2️⃣ Dirección 🗺️".'\n'."3️⃣ Horario de atención 🕜".'\n'."4️⃣ Página Web 🌐".'\n'."5️⃣ Telefono 📱".'\n'."6️⃣ Sobre los servicios".'\n\n'.'_'."Si desea visualizar de nuevo el menú posteriormente escriba ".'*'."Menú".'*'.'_';
        $type = 'text'; // Tipo de message: texto
    } elseif (strpos($message, 'menu') !== false || strpos($message, 'brindame el menu') !== false) {
        $related_response=[
            "Por supuesto aquí esta el menú de opciones: ".'\n\n'."1️⃣ ¿Algún asesor? 🧑🏻".'\n'."2️⃣ Dirección 🗺️".'\n'."3️⃣ Horario de atención 🕜".'\n'."4️⃣ Página Web 🌐".'\n'."5️⃣ Telefono 📱".'\n'."6️⃣ Sobre los servicios",
            "¡Claro! Estas son las opciones que puedes elegir: ".'\n\n'."1️⃣ ¿Algún asesor? 🧑🏻".'\n'."2️⃣ Dirección 🗺️".'\n'."3️⃣ Horario de atención 🕜".'\n'."4️⃣ Página Web 🌐".'\n'."5️⃣ Telefono 📱".'\n'."6️⃣ Sobre los servicios"
        ];
        $response = $related_response[array_rand($related_response)];
        $type = 'text'; // Tipo de message: texto
    }
    elseif (strpos($message, '1') !== false || strpos($message, 'asesor') !== false || strpos($message, 'encargado') !== false ) {
        //En aquí pasamos algun link de algun asesor
        $response = "Interactua con algún asesor:".'\n'."- Asesor A: https://wa.me/51963043991".'\n'.'\n'."- Asesor B: https://wa.me/51963043991";
        $type = 'text'; // Tipo de message: texto
    } elseif (strpos($message, '2') !== false || strpos($message, 'direccion') !== false || strpos($message, 'ubicacion') !== false || strpos($message, 'lugar') !== false ) {
        $related_response=[
            "Nos encontramos ubicados en el pasaje Islas Marquesas, La Perla - Callao",
            "Nos ubicamos en psj. Islas Marquesas, La Perla - Callao"
        ];
        $response = $related_response[array_rand($related_response)];
        $type = 'text'; // Tipo de message: texto
    } elseif (strpos($message, '3') !== false || strpos($message, 'hora') !== false || strpos($message, 'horarios') !== false || strpos($message, 'dias') !== false || strpos($message, 'abierto') !== false) {
        $response = "Horarios de Atención:".'\n'."➡️ Lunes a viernes estamos disponibles de ".'*'."9:00 A.M. a 17:00 P.M.".'*'.'\n'."➡️ Sábados abrimos de ".'*'."08:00 a 12:00".'*'.'\n'."➡️ Domingos no hay atención";
        $type = 'text'; // Tipo de message: texto
    } elseif (strpos($message, '4') !== false || strpos($message, 'pagina web') !== false || strpos($message, 'web') !== false || strpos($message, 'pagina') !== false) {
        $response = 'Visítanos en http://www.rstraining.org.pe/';
        $type = 'text'; // Tipo de message: texto
    } elseif (strpos($message, '5') !== false || strpos($message, 'telefono') !== false || strpos($message, 'celular') !== false || strpos($message, 'cel') !== false) {
        $response = 'Nuestro número de teléfono es 963043991';
        $type = 'text'; // Tipo de message: texto
    } elseif (strpos($message, '6') !== false || strpos($message, 'servicios') !== false || strpos($message, 'cuales son sus servicios') !== false || strpos($message, 'que servicios tiene') !== false) {
            $response = "Los servicios que ofrecemos son: ".'\n'.'*'."7.".'*'." Seguridad y Salud en el Trabajo".'\n'.'*'."8.".'*'." Salud Ocupacional".'\n'.'*'."9.".'*'." HSE y Safety".'\n'.'*'."10.".'*'." Formación y Entrenamiento".'\n'.'*'."11.".'*'." Centro de Entrenamiento internacional ⭐".'\n\n'.'_'."Escribe la opción que desea para mas información.".'_';
            $type = 'text'; // Tipo de message: texto
        
    } elseif (strpos($message, 'aviso') !== false || strpos($message, 'oferta') !== false) {
        $response = "https://i.imgur.com/GOYNyt3.png"; //Aquí va el enlace de la imagen que quieres mostrar
        $type = 'image'; // Tipo de message: imagen
    } elseif (strpos($message, 'doc') !== false || strpos($message, 'documento') !== false) {
        $response = "https://www.unicef.org/media/48611/file"; //Aquí va el enlace del archivo que quieres mandar
        $type = 'file'; // Tipo de message: documento
    }  else {
        $unrelated_response = [
            "Lo siento, no puedo ayudarte con esa consulta en este momento. ¿Hay algo más en lo que pueda asistirte?",
            "No estoy seguro de entender tu consulta. ¿Podrías reformularla de otra manera?",
            "Esa consulta está fuera del alcance de mis capacidades actuales. ¿Hay algo más con lo que pueda ayudarte?"
        ];
        $response = $unrelated_response[array_rand($unrelated_response)];
        $type = 'text'; // Tipo de message: texto
    }

    //LLAMAMMOS A LA FUNCION DE ENVIAR response
    require_once './enviar.php';
    //ENVIAMOS LA response VIA WHATSAPP
    enviar($message, $type, $response,$id,$timestamp,$customer_phone);
    $message = ''; 
}



?>

