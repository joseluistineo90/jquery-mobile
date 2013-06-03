<?php
$yourEmail = "albertomarioangulo29@hotmail.com";
$yourWebsite = "www.midominio.com";
$thanksPage = ''; // URL para "Página de Gracias por Escribirnos". Si cargo el agradecimiento en la misma página, simplemente dejo esto vacío.
$maxPoints = 4;
$requiredFields = "nombre,email,mensaje";

// NO TOCAR ESTO
$error_msg = null;
$result = null;

$requiredFields = explode(",", $requiredFields);

function clean($data) {
    $data = trim(stripslashes(strip_tags($data)));
    return $data;
}
function isBot() {
    $bots = array("Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot", "Teoma", "alexa", "froogle", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot", "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz");

    foreach ($bots as $bot)
        if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
            return true;

    if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ")
        return true;
    
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isBot() !== false)
        $error_msg .= "No bots please! UA reported as: ".$_SERVER['HTTP_USER_AGENT'];
        
    $points = (int)0;
    
    $badwords = array("adult", "beastial", "bestial", "blowjob", "clit", "cum", "cunilingus", "cunillingus", "cunnilingus", "cunt", "ejaculate", "fag", "felatio", "fellatio", "fuck", "fuk", "fuks", "gangbang", "gangbanged", "gangbangs", "hotsex", "hardcode", "jism", "jiz", "orgasim", "orgasims", "orgasm", "orgasms", "phonesex", "phuk", "phuq", "pussies", "pussy", "spunk", "xxx", "viagra", "phentermine", "tramadol", "adipex", "advai", "alprazolam", "ambien", "ambian", "amoxicillin", "antivert", "blackjack", "backgammon", "texas", "holdem", "poker", "carisoprodol", "ciara", "ciprofloxacin", "debt", "dating", "porn", "link=", "voyeur", "content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript");

    foreach ($badwords as $word)
        if (
            strpos(strtolower($_POST['mensaje']), $word) !== false || 
            strpos(strtolower($_POST['nombre']), $word) !== false
        )
            $points += 2;
    
    if (strpos($_POST['mensaje'], "http://") !== false || strpos($_POST['mensaje'], "www.") !== false)
        $points += 2;
    if (isset($_POST['nojs']))
        $points += 1;
    if (preg_match("/(<.*>)/i", $_POST['mensaje']))
        $points += 2;
    if (strlen($_POST['nombre']) < 3)
        $points += 1;
    if (strlen($_POST['mensaje']) < 15 || strlen($_POST['mensaje'] > 1500))
        $points += 2;

    foreach($requiredFields as $field) {
        trim($_POST[$field]);
        
        if (!isset($_POST[$field]) || empty($_POST[$field]))
            $error_msg .= "Por favor diligencia todos los campos del formulario e intenta enviar nuevamente el mensaje.\r\n";
    }

    if (!preg_match("/^[a-zA-Z-'\s]*$/", stripslashes($_POST['nombre'])))
        $error_msg .= "Este campo no puede contener caracteres especiales.\r\n";
    if (!preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', strtolower($_POST['email'])))
        $error_msg .= "Esta no es una dirección válida de correo.\r\n";
    if (!empty($_POST['url']) && !preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $_POST['url']))
        $error_msg .= "URL Inválida.\r\n";
    
    if ($error_msg == NULL && $points <= $maxPoints) {
        $subject = "Un visitante de Midominio.com te ha enviado un mensaje";
        
        $message = "Has recibido un mensaje de e-mail de un visitante de Midominio.com: \n\n";
        foreach ($_POST as $key => $val) {
            $message .= ucwords($key) . ": " . clean($val) . "\r\n";
        }
        $message .= "\r\n";
        $message .= 'IP: '.$_SERVER['REMOTE_ADDR']."\r\n";
        $message .= 'Navegador: '.$_SERVER['HTTP_USER_AGENT']."\r\n";
        $message .= 'Puntos: '.$points;

        if (strstr($_SERVER['SERVER_SOFTWARE'], "Win")) {
            $headers   = "Mensaje desde: $yourEmail\n";
            $headers  .= "Responder este mensaje a: {$_POST['email']}\n";
        } else {
            $headers   = "Mensaje desde: $yourEmail\n";
            $headers  .= "Responder este mensaje a: {$_POST['email']}";
        }

        if (mail($yourEmail,$subject,$message,$headers)) {
            if (!empty($thanksPage)) {
                header("Ubicación: $thanksPage");
                exit;
            } else {
                $result = 'Su mensaje de correo ha sido enviado con éxito. Dentro de poco me estaré contactando con usted.';
                $disable = true;
            }
        } else {
            $error_msg = 'Su mensaje de correo no ha podido ser enviado. Por favor intente nuevamente. ['.$points.']';
        }
    } else {
        if (empty($error_msg))
            $error_msg = 'Su correo electrónico luce para nuestros filtros de seguridad como spam. Intente enviar el mensaje, escribiendo un correo electrónico diferente. ['.$points.']';
    }
}
function get_data($var) {
    if (isset($_POST[$var]))
        echo htmlspecialchars($_POST[$var]);
}
?>