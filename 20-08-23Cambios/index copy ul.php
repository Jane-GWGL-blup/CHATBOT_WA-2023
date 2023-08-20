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

if(isset($response['entry'][0]['changes'][0]['value']['messages'][0]) || isset($response['entry'][0]['changes'][0]['value']['messages'][0]['from']) || isset($response['entry'][0]['changes'][0]['value']['messages'][0]['id']) || isset($response['entry'][0]['changes'][0]['value']['messages'][0]['timestamp'])){
    //EXTRAEMOS EL MENSAJE DEL ARRAY
    $message = $response['entry'][0]['changes'][0]['value']['messages'][0];
    //EXTRAEMOS EL TELEFONO DEL ARRAY
    $customer_phone=$response['entry'][0]['changes'][0]['value']['messages'][0]['from'];
    //EXTRAEMOS EL ID DE WHATSAPP DEL ARRAY
    $id=$response['entry'][0]['changes'][0]['value']['messages'][0]['id'];
    //EXTRAEMOS EL TIEMPO DE WHATSAPP DEL ARRAY
    $timestamp=$response['entry'][0]['changes'][0]['value']['messages'][0]['timestamp'];

/*
 * LOGICA DE LA CONVERSACION
 */
    //SI HAY UN MENSAJE TIPO TEXTO o TIPO INTERACTIVP
    if(isset($message['text']['body'])){
        $message=$message['text']['body'];
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
        $data1 = '';
        $data2 = '';
        $data3 = '';
        $data4 = '';
        $data5 = '';
        $des_data = '';
        $des_data1 = '';
        $des_data2 = '';
        $des_data3 = '';
        $des_data4 = '';
        $des_data5 = '';

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
            $response = "Servicio:".'\n'.'_'."🢂 Seguridad y Salud en el Trabajo".'_';
            $type = "button_list";
            $data = '*'."Prevención de riesgos".'*';
            $des_data = "Evaluación y prevención de riesgos laborales";
            $data1 = '*'."Capacitación en SST".'*';
            $des_data1 = "Capacitación para empleados y empleadores en seguridad y salud";
            $data2 = '*'."Asesoramiento".'*';
            $des_data2 = "Asesoramiento Planificación de Medidas Preventivas y Emergencias";
            $data3 = '*'."Cumplimiento".'*';
            $des_data3 = "Cumplimiento de la ley SST";
            $data4 = '*'."Capacitación por ley SST".'*';
            $des_data4 = "Capacitaciones obligatorias por la ley SST";
        }  elseif (strpos($message, '7') !== false ) {
            $response = "Servicio:".'\n'.'_'."🢂 Salud Ocupacional".'_';
            $type = "button_list2";
            $data = '*'."Capacitación".'*';
            $des_data = "Capacitación en seguridad y salud en el trabajo";
            $data1 = '*'."Prevención".'*';
            $des_data1 = "Prevención de accidentes laborales y enfermedades profesionales";
            $data2 = '*'."Programas".'*';
            $des_data2 = "Programas de rehabilitación";
            $data3 = '*'."Seguimiento".'*';
            $des_data3 = "Seguimiento a la salud de los trabajadores";
            $data4 = '*'."Entrenamiento emocional".'*';
            $data5 = '*'."Ergonomía".'*';
        } elseif (strpos($message, '8') !== false ) {
            $response = "Servicio:".'\n'.'_'."🢂 HSE y Safety".'_';
            $type = "button_list2";
            $data = '*'."IPERC".'*';
            $des_data = "Identificación de Peligros y Evaluación de Riesgo";
            $data1 = '*'."ATS".'*';
            $des_data1 = "Análisis de Trabajo";
            $data2 = '*'."Formación de brigadas".'*';
            $des_data2 = "Formación de brigadas de Emergencia";
            $data3 = '*'."Primeros auxilios".'*';
            $data4 = '*'."Gestión".'*';
            $des_data4 = "Gestión de respuesta ante una emergencia";
            $data5 = '*'."Brigada".'*';
            $des_data5 = '*'."Brigada de Lucha Contra Incendios".'*';
        } elseif (strpos($message, '9') !== false ) {
            $response = "Servicio:".'\n'.'_'."🢂 Formación y Entrenamiento".'_';
            $type = "button_list";
            $data = '*'."Lucha Contra Incendios".'*';
            $data1 = '*'."Brigadas de Emergencia".'*';
            $data2 = '*'."Primeros Auxilios (F&E)".'*';
            $data3 = '*'."Trabajo en Equipo".'*';
            $des_data3 = "Formación esencial para mejorar la productividad";
            $data4 = '*'."Liderazgo".'*';
            $des_data4 = "Impulsa el crecimiento del equipo y el éxito organizacional.";
        } elseif (strpos($message, 'gracias') !== false) {
            $related_response = [
                "Fue un placer ayudarte. Si necesitas más información, no dudes en contactarnos.",
                "No hay de qué, siempre estoy disponible para responder tus preguntas.",
                "¡De nada! Si tienes más preguntas en el futuro, no dudes en preguntar."
            ];
            $response = $related_response[array_rand($related_response)];
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
        echo "Response: " . $response; 
        //LLAMAMMOS A LA FUNCION DE ENVIAR response
        require_once './enviar.php';
        //ENVIAMOS LA response VIA WHATSAPP
        enviar($message, $data, $data1, $data2, $data3, $data4, $data5, $des_data, $des_data1, $des_data2, $des_data3, $des_data4, $des_data5, $type, $response,$id,$timestamp,$customer_phone);
        $message = ''; 
    }
    elseif(isset($message["interactive"])){

        $data = '';
        $data1 = '';
        $data2 = '';
        $data3 = '';
        $data4 = '';
        $data5 = '';
        $des_data = '';
        $des_data1 = '';
        $des_data2 = '';
        $des_data3 = '';
        $des_data4 = '';
        $des_data5 = '';

        $message = $message["interactive"];
        if ($message['type'] === 'button_reply') {
            $message = $message['button_reply']['title'];
            if (strpos($message, 'Quiero más info') !== false) {
                // Opción 1 seleccionada
                $response = "Consulte con un asesor para mas información:".'\n'."↳ https://wa.me/51963043991";
            }
            else{
                $response = "Fue un placer ayudarte. Si necesitas más información, no dudes en contactarnos.";
            }
            
            $type = 'text';
            // Llamar a la función 'enviar' solo con los parámetros necesarios
            require_once './enviar.php'; 
            enviar($message, $data, $data1, $data2, $data3, $data4, $data5, $des_data, $des_data1, $des_data2, $des_data3, $des_data4, $des_data5, $type, $response,$id,$timestamp,$customer_phone);
        }
        elseif($message['type'] === 'list_reply'){
            $message = $message['list_reply']['title'];
            if (strpos($message, 'Prevención de riesgos') !== false ) {
                $response = '*'."Evaluación y prevención de riesgos laborales".'*'.'\n\n'."En este ámbito se promueve el cumplimiento de las normativas y se garantiza un entorno de trabajo seguro. Además, ayuda a prevenir accidentes y enfermedades profesionales, protegiendo la integridad física y mental de los empleados.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/bmP8vKd.jpg';
            } elseif (strpos($message, 'Capacitación en STT') !== false ) {
                $response = '*'."Capacitación para empleados y empleadores en seguridad y salud".'*'.'\n\n'."a capacitación en esta área previene accidentes, enfermedades y cumple con las normativas legales. Promueve una cultura de prevención, mejorando la calidad de vida de los empleados y la eficiencia de las empresas.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/bI4wop4.jpg';
            } elseif (strpos($message, 'Asesoramiento') !== false ) {
                $response = '*'."Asesoramiento en la implementación de medidas preventivas y elaboración de planes de emergencia".'*'.'\n\n'."Garantiza la seguridad de los empleados y minimiza los riesgos en el lugar de trabajo, ayuda a prevenir accidentes, controlar situaciones de emergencia y proteger la vida y el bienestar de las personas involucradas.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/vPtyirx.jpg';
            } elseif (strpos($message, 'Cumplimiento') !== false ) {
                $response = '*'."Cumplimiento de la ley SST".'*'.'\n\n'."El cumplimiento de ley SST(Seguridad y Salud en el Trabajo) es vital para proteger la seguridad y salud de los trabajadores, evitar sanciones, preservar la integridad de la empresa, promover un entorno laboral seguro y saludable, protegiendo el bienestar de los empleados.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/puVRSX1.jpg';
            } elseif (strpos($message, 'Capacitación por ley SST') !== false ) {
                $response = '*'."Capacitaciones obligatorias por la ley SST".'*'.'\n\n'."En esta área se capacita sobre riesgos laborales y uso de equipos de protección personal para garantizar la seguridad y salud de los trabajadores. Cumplirlas demuestra el compromiso de las empresas con el bienestar de sus empleados y evitan sanciones legales.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/PXWd7HE.jpg';
            } elseif (strpos($message, 'Capacitación') !== false ) {
                $response = '*'."Capacitación en seguridad y salud en el trabajo".'*'.'\n\n'."Esta área proporciona conocimientos necesarios para prevenir los riesgos laborales, reducir accidentes y enfermedades ocupacionales, y cumplir con las regulaciones promoviendo un entorno laboral seguro y saludable.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/R6ZmG1T.jpg';
            } elseif (strpos($message, 'Prevención') !== false ) {
                $response = '*'."Prevención de accidentes laborales y enfermedades profesionales".'*'.'\n\n'."Es fundamental hoy en día para proteger la vida y salud de los trabajadores, evitar costos asociados a lesiones y enfermedades, y promover un ambiente laboral seguro y productivo.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/9yD0TCN.jpg';
            } elseif (strpos($message, 'Programas') !== false ) {
                $response = '*'."Programas de rehabilitación".'*'.'\n\n'."Son esenciales porque permiten a los empleados recuperarse de lesiones, enfermedades o discapacidades, mejorar su funcionalidad y calidad de vida, y reintegrarse en la sociedad y en su entorno laboral.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/lOnf3Fx.jpg';
            } elseif (strpos($message, 'Seguimiento') !== false ) {
                $response = '*'."Seguimiento a la salud de los trabajadores".'*'.'\n\n'."Este seguimiento ayuda a prevenir enfermedades laborales, promover estilos de vida saludables y garantizar el bienestar de los empleados. Además, ayuda a reducir costos y a mantener un entorno de trabajo seguro y saludable.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/LgbbtOy.jpg';
            } elseif (strpos($message, 'Entrenamiento emocional') !== false ) {
                $response = '*'."Entrenamiento emocional".'*'.'\n\n'."Fortalece las habilidades emocionales de los empleados, lo cual mejora el clima laboral, la resolución de conflictos y el manejo del estrés. Contribuye a un mayor rendimiento y satisfacción laboral.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/9hQbiwC.jpg';
            } elseif (strpos($message, 'Ergonomía') !== false ) {
                $response = '*'."Ergonomía".'*'.'\n\n'."Es importante, debido a que se adapta el entorno laboral a los trabajadores, mejorando seguridad, confort y eficiencia. Además, previene lesiones y enfermedades, promoviendo la productividad y el bienestar de los empleados.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/Fnruv2n.jpg';
            } elseif (strpos($message, 'IPERC') !== false ) {
                $response = '*'."Identificación de Peligros y Evaluación de Riego (IPERC)".'*'.'\n\n'."Es fundamental que los empleados puedan identificar riesgos laborales, implementar medidas de control y proteger la salud de los empleados y sus compañeros de trabajo.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/nJg5QUf.jpg';
            } elseif (strpos($message, 'ATS') !== false ) {
                $response = '*'."Análisis de Trabajo (ATS)".'*'.'\n\n'."En ATS es para identificar riesgos, mejorar la eficiencia y seguridad laboral, establecer medidas preventivas y proteger la salud y bienestar de los trabajadores. Además, contribuye a un entorno laboral seguro y productivo.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/m1WaumX.jpg';
            } elseif (strpos($message, 'Formación de brigadas') !== false ) {
                $response = '*'."Formación de brigadas de Emergencia".'*'.'\n\n'."Esta formación es esencial para preparar a los empleados en la respuesta eficiente ante situaciones críticas, protegiendo vidas, minimizando daños y asegurando la continuidad laboral.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/L7gRmob.jpg';
            } elseif (strpos($message, 'Primeros auxilios') !== false ) {
                $response = '*'."Primeros auxilios".'*'.'\n\n'."Son importantes para brindar atención inmediata y adecuada, reducir complicaciones y preservar vidas. Promueven un entorno laboral seguro y protegen el bienestar de los empleados.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/BAYw2SV.jpg';
            } elseif (strpos($message, 'Gestión') !== false ) {
                $response = '*'."Gestión de respuesta ante una emergencia".'*'.'\n\n'."Esta área se brindará las habilidades y conocimientos ante emergencias para proteger vidas en situaciones críticas. Además, el cumplimiento de estas capacitaciones es requerido por la Ley de Seguridad y Salud en el Trabajo";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/WhQNZS1.jpg';
            } elseif (strpos($message, 'Brigada') !== false ) {
                $response = '*'."Brigada de Lucha Contra Incendios".'*'.'\n\n'."Es necesario que los empleados estén preparados y actuar de manera eficiente en caso de un incendio. El curso cumple con las normas seguridad, protege vidas con extinción incendios, manejo equipos y evacuación segura.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/nIAcdOC.jpg';
            } elseif (strpos($message, 'Lucha Contra Incendios') !== false ) {
                $response = '*'."Lucha Contra Incendios".'*'.'\n\n'."Una formación importante para preparar a los empleados en la respuesta eficaz ante incendios, minimizando los riesgos, protegiendo vidas y salvaguardando los activos de la empresa.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/0HnUqT7.jpg';
            } elseif (strpos($message, 'Brigadas de Emergencia') !== false ) {
                $response = '*'."Brigadas de Emergencia".'*'.'\n\n'."Estas brigadas son esenciales, ya que permiten que los empleados den una respuesta eficaz ante situaciones críticas, protegiendo vidas y asegurando la seguridad en el entorno de trabajo.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/L7gRmob.jpg';
            } elseif (strpos($message, 'Primeros Auxilios (F&E)') !== false ) {
                $response = '*'."Primeros Auxilios".'*'.'\n\n'."Es necesario que los empleados se formen para brindar atención inmediata, reducir complicaciones y salvar vidas en casos de lesiones o enfermedades repentinas. Garantiza un entorno laboral seguro y protege a los empleados.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/Ocb9C4c.jpg';
            } elseif (strpos($message, 'Trabajo en Equipo') !== false ) {
                $response = '*'."Trabajo en Equipo".'*'.'\n\n'."Esta formación es esencial para promover la colaboración, la comunicación efectiva y la eficiencia en las tareas, mejorando la productividad y fortaleciendo las relaciones entre los miembros del equipo.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/vG3pjaK.jpg';
            } elseif (strpos($message, 'Liderazgo') !== false ) {
                $response = '*'."Liderazgo".'*'.'\n\n'."La formación en liderazgo en el ámbito laboral es esencial para desarrollar habilidades de dirección, motivación y toma de decisiones, impulsando el crecimiento del equipo y el éxito organizacional.";
                $type = 'button-reply'; 
                $data = 'https://i.imgur.com/orUzBxl.jpg';
            }
            require_once './enviar.php'; 
            enviar($message, $data, $data1, $data2, $data3, $data4, $data5, $des_data, $des_data1, $des_data2, $des_data3, $des_data4, $des_data5, $type, $response,$id,$timestamp,$customer_phone);
        } 
    }
}

?>