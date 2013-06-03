<?php
$yourEmail = "chrisdoc7@hotmail.com";//puedo anadir mas separados por coma}
$yourWebsite = "www.midominio.com";
$thanksPage = ''; // URL para "Página de Gracias por Escribirnos". Si cargo el agradecimiento en la misma página, simplemente dejo esto vacío.
$maxPoints = 4;//permite controlar el # veces de intentar enviar correo antes de que me bloqueen por spam
$requiredFields = "nombre,email,mensaje";//campos requeridos

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

<!doctype html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <title>Contáctanos</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <script src="js/modernizr.js"></script>
    </head>
    <body>
        <div data-role="page" data-theme="a">

            <header data-role="header" data-theme="a">
                <img src="img/home-logo.png" id="topHeader"/>
                <h1>Contáctanos</h1>  
            </header>
            <section data-role="content">   
                <nav data-role="navbar" data-iconpos="bottom">
                    <ul>
                        <li><a href="mobile.html" data-icon="home" rel="external">Inicio</a></li>
                        <li><a href="css3.html" data-icon="check" data-transition="turn">CSS3</a></li>
                        <li><a href="multimedia.html" data-icon="grid" rel="external">Multimedia</a></li>
                        <!-- Tag  rel="external" forza en los navegadores que tienen soporte Tipo B o Tipo C -->
                    </ul>
                    <ul>
                        <li><a href="offline.html" data-icon="gear" rel="external">Offline</a></li>
                        <li><a href="semantica.html" data-icon="star" rel="external">Semantica</a></li>
                        <li><a href="graficos.html" data-icon="grid" rel="external">Gráficos</a></li>
                    </ul>
                </nav>
                <form action="<?php echo basename(__FILE__); ?>" method="post" data-ajax="false">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre">
        
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">

                    <label for="mensaje">Comentarios</label>
                    <textarea id="mensaje" name="mensaje"></textarea>
           
                    <label for="edad">Edad</label>
                    <input type="range" min="18" max="80" value="18" id="edad" name="edad">
            
                    <fieldset data-role="controlgroup">
                        <legend>Seleccione su estado civil</legend>
                        
                        <label for="soltero">Soltero/a</label>
                        <input type="radio" name="estadocivil" id="soltero" />

                        <label for="casado">Casado/a</label>
                        <input type="radio" name="estadocivil" id="casado"  />
                        
                        <label for="divorciado">Divorciado/a</label>
                        <input type="radio" name="estadocivil" id="divorciado"  />

                        <label for="union">Union Libre</label>
                        <input type="radio" name="estadocivil" id="union"  />
                    </fieldset>
            
                    <label for="suscribe">Te suscribes?</label>
                    <select name="suscribe" id="suscribe" data-role="slider" data-theme="a">
                        <option>Si</option>
                        <option>No</option>
                    </select>
            
                    <label for="gustos">Gustos</label>
                    <select id="gustos" name="gustos" data-native-menu="false" multiple data-theme="a">
                        <option data-placeholder="true">Selecciona tus gustos</option>
                        
                        <option value="1">Tecnología</option>
                        <option value="2">Comida</option>
                        <option value="3">Artes</option>
                        <option value="4">Deportes</option>
                    </select>
           
                    <label for="pais">Pais</label>
                    <select id="pais" name="pais" data-native-menu="false" data-theme="a">
                        <optgroup label="América">
                            <option value="AR">Argentina</option>
                            <option value="MX">México</option>
                        </optgroup>
                        <optgroup label="Europa">
                            <option value="ES">España</option>
                            <option value="ES">Francia</option>
                            <option value="ES">Inglaterra</option>
                        </optgroup>
                    </select>
          
                  <div data-role="fieldcontain">
                      <fieldset data-role="controlgroup">
                            <legend>Fecha de Nacimiento</legend>
                            <select id="dia" name="dia" data-theme="a">
                                <option>Día</option>
                                <option>1</option>
                                <option>2</option>
                            </select>
                            <select id="mes" name="mes" data-native-menu="false" data-theme="c">
                                <option value="0" data-placeholder="true">Mes</option>
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="4">Mayo</option>
                            </select>
                            <select id="anio" name="anio">
                                <option>Año</option>
                                <option>2013</option>
                                <option>2012</option>
                                <option>2011</option>
                                <option>2010</option>
                                <option>2009</option>
                            </select>
                        </fieldset>
                   </div>
                <input type="submit" value="Enviar" />
           </form>
            </section>

            <footer data-role="footer">
                <nav data-role="navbar" data-iconpos="bottom">
                    <ul>
                        <li><a href="contacto.php" data-icon="info">Contáctanos</a></li>
                    </ul>
                </nav>
                <h4>Desarrollado por AA</h4>
            </footer>

        </div>
    </div>

    <script src="js/jquery-1.9.1.min.js"></script>
    <script src="js/jquery-migrate-1.1.0.min.js"></script>
    <script src="js/jquery.mobile-1.2.0.min.js"></script>
    <script src="js/prefixfree.min.js"></script>

    <link rel="stylesheet" href="css/jquery.mobile.structure-1.2.0.css" />
    <link rel="stylesheet" href="css/jquery.mobile-1.2.0.css" />
    <link rel="stylesheet" href="css/miTema.css" />
    <link rel="stylesheet" href="css/miEstilo.css" />
</body>
</html>
