<?php
//DESHABILITAMOS EL MOSTRAR ERRORES
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(-1);

require 'vendor/autoload.php';
//IMPORTAMOS LAS LIBRERIRAS DE Rivescript
use \Axiom\Rivescript\Rivescript;

/*
 * VERIFICACION DEL WEBHOOK
*/
//TOQUEN QUE QUERRAMOS PONER 
$token = 'HSChatBotWA';
//RETO QUE RECIBIREMOS DE FACEBOOK
$palabraReto = $_GET['hub_challenge'];
//TOQUEN DE VERIFICACION QUE RECIBIREMOS DE FACEBOOK
$tokenVerificacion = $_GET['hub_verify_token'];
//SI EL TOKEN QUE GENERAMOS ES EL MISMO QUE NOS ENVIA FACEBOOK RETORNAMOS EL RETO PARA VALIDAR QUE SOMOS NOSOTROS
if ($token === $tokenVerificacion) {
    echo $palabraReto;
    exit;
}

/*
 * RECEPCION DE MENSAJES
 */
//LEEMOS LOS DATOS ENVIADOS POR WHATSAPP
$respuesta = file_get_contents("php://input");
//CONVERTIMOS EL JSON EN ARRAY DE PHP
$respuesta = json_decode($respuesta, true);
//EXTRAEMOS EL MENSAJE DEL ARRAY
$mensaje=$respuesta['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
//EXTRAEMOS EL TELEFONO DEL ARRAY
$telefonoCliente=$respuesta['entry'][0]['changes'][0]['value']['messages'][0]['from'];
//EXTRAEMOS EL ID DE WHATSAPP DEL ARRAY
$id=$respuesta['entry'][0]['changes'][0]['value']['messages'][0]['id'];
//EXTRAEMOS EL TIEMPO DE WHATSAPP DEL ARRAY
$timestamp=$respuesta['entry'][0]['changes'][0]['value']['messages'][0]['timestamp'];


//SI HAY UN MENSAJE
if($mensaje!=null){
    //file_put_contents("text.txt", $mensaje);
    //INICIALIZAMOS RIVESCRIPT Y CARGAMOS LA CONVERSACION
    $rivescript = new Rivescript();
    $rivescript->load('rstrainingia.rive');
    //OBTENEMOS LA RESPUESTA
    $respuesta= $rivescript->reply($mensaje);
    //ESCRIBIMOS LA RESPUESTA
    //file_put_contents("text.txt", $respuesta);

    //LLAMAMMOS A LA FUNCION DE ENVIAR RESPUESTA
    require_once './enviar.php';
    //ENVIAMOS LA RESPUESTA VIA WHATSAPP
    enviar($mensaje,$respuesta,$id,$timestamp,$telefonoCliente);
}

// Función para generar el enlace
function generateLink($query) {
    $url = "http://google.com/search?q=" . urlencode($query);
    $link = '<a href="' . $url . '">Click aquí</a>';
    return $link;
}

// Obtén la respuesta de RiveScript
$reply = $bot->reply($input);

// Procesa las variables y llamadas a funciones en RiveScript
$processedReply = $bot->stream->setUservar('botreply', $reply)->getUservars();

// Reemplaza la variable [link] con el enlace HTML generado
$processedReply = str_replace("[link]", "<?php echo generateLink", $processedReply);
$processedReply = str_replace("]", "; ?>", $processedReply);

// Evalúa el código PHP en la respuesta
ob_start();
eval("?> $processedReply <?php ");
$finalReply = ob_get_clean();

// Imprime la respuesta del bot
echo $finalReply;



?>

