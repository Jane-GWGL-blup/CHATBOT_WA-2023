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
//comprobaciones antes de acceder a los datos del array
if (isset($_GET['hub_challenge']) && isset($_GET['hub_verify_token'])) {
    //RETO QUE RECIBIREMOS DE FACEBOOK
    $challenge_word = $_GET['hub_challenge'];
    //TOQUEN DE VERIFICACION QUE RECIBIREMOS DE FACEBOOK
    $verify_token = $_GET['hub_verify_token'];
    //SI EL TOKEN QUE GENERAMOS ES EL MISMO QUE NOS ENVIA FACEBOOK RETORNAMOS EL RETO PARA VALIDAR QUE SOMOS NOSOTROS
    if ($token === $verify_token) {
        echo $challenge_word;
        exit;
    }
}

/*
 * RECEPCION DE MENSAJES
 */
//LEEMOS LOS DATOS ENVIADOS POR WHATSAPP
$response = file_get_contents("php://input");
//CONVERTIMOS EL JSON EN ARRAY DE PHP
$response = json_decode($response, true);
//EXTRAEMOS EL MENSAJE DEL ARRAY
$message = $response['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
//EXTRAEMOS EL TELEFONO DEL ARRAY
$customer_phone=$response['entry'][0]['changes'][0]['value']['messages'][0]['from'];
//EXTRAEMOS EL ID DE WHATSAPP DEL ARRAY
$id=$response['entry'][0]['changes'][0]['value']['messages'][0]['id'];
//EXTRAEMOS EL TIEMPO DE WHATSAPP DEL ARRAY
$timestamp=$response['entry'][0]['changes'][0]['value']['messages'][0]['timestamp'];

//SI HAY UN MENSAJE
if($message!=null ){
    
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
    $data = '';
    /*
    * LOGICA DE LA CONVERSACION
    */
    if (empty($message)) {
        // No se ha recibido ninguna response después del message de bienvenida anterior
        // No se envía ningún message adicional
        exit;
    } elseif (strpos($message, 'hola') !== false || strpos($message, 'hay alguien') !== false || strpos($message, 'como estas') !== false || strpos($message, 'Buenos dias') !== false || strpos($message, 'Buenas tardes') !== false || strpos($message, 'Buenas noches') !== false) {
        $response= "¡Hola! Soy Diana tu asistente virtual de Reliser Safety Training." .'\n'. "¿En que puedo ayudarte?".'\n\n'."1️⃣ ¿Algún asesor? 🧑🏻".'\n'."2️⃣ Dirección 🗺️".'\n'."3️⃣ Horario de atención 🕜".'\n'."4️⃣ Página Web 🌐".'\n'."5️⃣ Sobre los servicios".'\n\n'.'_'."Si desea visualizar de nuevo el menú posteriormente escriba ".'*'."Menú".'*'.'_';
        $type = 'text'; // Tipo de message: texto
        
    } elseif (strpos($message, 'menu') !== false || strpos($message, 'brindame el menu') !== false) {
        $related_response=[
            "Por supuesto, aquí esta el menú de opciones: ".'\n\n'."1️⃣ ¿Algún asesor? 🧑🏻".'\n'."2️⃣ Dirección 🗺️".'\n'."3️⃣ Horario de atención 🕜".'\n'."4️⃣ Página Web 🌐".'\n'."5️⃣ Sobre los servicios",
            "¡Claro! Estas son las opciones que puedes elegir: ".'\n\n'."1️⃣ ¿Algún asesor? 🧑🏻".'\n'."2️⃣ Dirección 🗺️".'\n'."3️⃣ Horario de atención 🕜".'\n'."4️⃣ Página Web 🌐".'\n'."5️⃣ Sobre los servicios"
        ];
        $response = $related_response[array_rand($related_response)];
        $type = 'text'; // Tipo de message: texto
    }
    elseif (strpos($message, 'sst1') !== false ) {
        $response = "Evaluación y prevención de riesgos laborales";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/bmP8vKd.jpg';
    }
    elseif (strpos($message, 'sst2') !== false ) {
        $response = "Capacitación para empleados y empleadores en seguridad y salud";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/bI4wop4.jpg';
    }
    elseif (strpos($message, 'sst3') !== false ) {
        $response = "Asesoramiento en la implementación de medidas preventivas y elaboración de planes de emergencia";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/vPtyirx.jpg';
    }
    elseif (strpos($message, 'sst4') !== false ) {
        $response = "Cumplimiento de la ley SST";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/puVRSX1.jpg';
    }
    elseif (strpos($message, 'sst5') !== false ) {
        $response = "Capacitaciones obligatorias por la ley SST";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/PXWd7HE.jpg';
    }
    elseif (strpos($message, 'so1') !== false ) {
        $response = "Capacitación en seguridad y salud en el trabajo";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/R6ZmG1T.jpg';
    }
    elseif (strpos($message, 'so2') !== false ) {
        $response = "Prevención de accidentes laborales y enfermedades profesionales";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/9yD0TCN.jpg';
    }
    elseif (strpos($message, 'so3') !== false ) {
        $response = "Programas de rehabilitación";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/lOnf3Fx.jpg';
    }
    elseif (strpos($message, 'so4') !== false ) {
        $response = "Seguimiento a la salud de los trabajadores";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/LgbbtOy.jpg';
    }
    elseif (strpos($message, 'so5') !== false ) {
        $response = "Entrenamiento emocional";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/9hQbiwC.jpg';
    }
    elseif (strpos($message, 'so6') !== false ) {
        $response = "Ergonomía";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/Fnruv2n.jpg';
    }
    elseif (strpos($message, 'hs1') !== false ) {
        $response = "Identificación de Peligros y Evaluación de Riego (IPERC)";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/nJg5QUf.jpg';
    }
    elseif (strpos($message, 'hs2') !== false ) {
        $response = "Análisis de Trabajo (ATS)";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/m1WaumX.jpg';
    }
    elseif (strpos($message, 'hs3') !== false ) {
        $response = "Formación de brigadas de Emergencia";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/L7gRmob.jpg';
    }
    elseif (strpos($message, 'hs4') !== false ) {
        $response = "Primeros auxilios";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/BAYw2SV.jpg';
    }
    elseif (strpos($message, 'hs5') !== false ) {
        $response = "Gestión de respuesta ante una emergencia";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/WhQNZS1.jpg';
    }
    elseif (strpos($message, 'hs6') !== false ) {
        $response = "Brigada de Lucha Contra Incendios";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/nIAcdOC.jpg';
    }
    elseif (strpos($message, 'fe1') !== false ) {
        $response = "Lucha Contra Incendios";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/0HnUqT7.jpg';
    }
    elseif (strpos($message, 'fe2') !== false ) {
        $response = "Brigadas de Emergencia";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/L7gRmob.jpg';
    }
    elseif (strpos($message, 'fe3') !== false ) {
        $response = "Primeros Auxilios";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/Ocb9C4c.jpg';
    }
    elseif (strpos($message, 'fe4') !== false ) {
        $response = "Trabajo en Equipo";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/vG3pjaK.jpg';
    }
    elseif (strpos($message, 'fe5') !== false ) {
        $response = "Liderazgo";
        $type = 'button-reply'; 
        $data = 'https://i.imgur.com/orUzBxl.jpg';
    }
    elseif (strpos($message, '10') !== false ) {
        $response = '*'."Centro de Entrenamiento Internacional ⭐".'*'.'\n\n'."Consultar con un asesor https://wa.me/51963043991";
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
    } elseif (strpos($message, '5') !== false || strpos($message, 'servicios') !== false || strpos($message, 'cuales son sus servicios') !== false || strpos($message, 'que servicios tiene') !== false) {
            $response = "Los servicios que ofrecemos son: ".'\n'.'*'."6.".'*'." Seguridad y Salud en el Trabajo".'\n'.'*'."7.".'*'." Salud Ocupacional".'\n'.'*'."8.".'*'." HSE y Safety".'\n'.'*'."9.".'*'." Formación y Entrenamiento".'\n'.'*'."10.".'*'." Centro de Entrenamiento Internacional ⭐".'\n\n'.'_'."Escribe la opción que desea para mas información.".'_';
            $type = 'text'; // Tipo de message: texto
    } elseif (strpos($message, '6') !== false ) {
        $response = '*'."Servicio Seguridad y Salud en el Trabajo".'*'.'\n\n'."⏺️ SST1. Evaluación y prevención de riesgos laborales".'\n'."⏺️ SST2. Capacitación para empleados y empleadores en seguridad y salud".'\n'."⏺️ SST3. Asesoramiento en la implementación de medidas preventivas y elaboración de planes de emergencia".'\n'."⏺️ SST4. Cumplimiento de la ley SST".'\n'."⏺️ SST5. Capacitaciones obligatorias por la ley SST".'\n\n'.'_'."Escribe la opción que desea para mas información. (Por ejemplo escriba: SST1)".'_';
        $type = 'text'; // Tipo de message: texto
    }  elseif (strpos($message, '7') !== false ) {
        $response = '*'."Salud Ocupacional".'*'.'\n\n'."⏺️ SO1. Capacitación en seguridad y salud en el trabajo".'\n'."⏺️ SO2. Prevención de accidentes laborales y enfermedades profesionales".'\n'."⏺️ SO3. Programas de rehabilitación".'\n'."⏺️ SO4. Seguimiento a la salud de los trabajadores".'\n'."⏺️ SO5. Entrenamiento emocional".'\n'."⏺️ SO6. Ergonomía".'\n\n'.'_'."Escribe la opción que desea para mas información.".'_';
        $type = 'text'; // Tipo de message: texto
    }  elseif (strpos($message, '8') !== false ) {
        $response = '*'."HSE y Safety".'*'.'\n\n'."⏺️ HS1. Identificación de Peligros y Evaluación de Riego (IPERC)".'\n'."⏺️ HS2. Análisis de Trabajo (ATS)".'\n'."⏺️ HS3. Formación de brigadas de Emergencia".'\n'."⏺️ HS4. Primeros auxilios".'\n'."⏺️ HS5. Gestión de respuesta ante una emergencia".'\n'."⏺️ HS6. Brigada de Lucha Contra Incendios".'\n\n'.'_'."Escribe la opción que desea para mas información.".'_';
        $type = 'text'; // Tipo de message: texto
    }  elseif (strpos($message, '9') !== false ) {
        $response = '*'."Formación y Entrenamiento".'*'.'\n\n'."⏺️ FE1. Lucha Contra Incendios".'\n'."⏺️ FE2. Brigadas de Emergencia".'\n'."⏺️ FE3. Primeros Auxilios".'\n'."⏺️ FE4. Trabajo en Equipo".'\n'."⏺️ FE5. Liderazgo".'\n\n'.'_'."Escribe la opción que desea para mas información.".'_';
        $type = 'text'; // Tipo de message: texto
    }  
    elseif (strpos($message, 'aviso') !== false || strpos($message, 'oferta') !== false) {
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
    echo "Response: " . $response; 
    //LLAMAMMOS A LA FUNCION DE ENVIAR response
    require_once './enviar.php';
    //ENVIAMOS LA response VIA WHATSAPP
    enviar($message, $data, $type, $response,$id,$timestamp,$customer_phone);
    $message = ''; 
}



?>

