<?php

/**
 * clean and save a text
 *
 * @param $text
 *
 * @return string
 */
function Rec($text)
{
    $text = htmlspecialchars(trim($text), ENT_QUOTES);
    if (1 === get_magic_quotes_gpc()) {
        $text = stripslashes($text);
    }

    return nl2br($text);
}

/**
 * verify the email syntax
 *
 * @param $email
 *
 * @return bool
 */
function IsEmail($email)
{
    $value = preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);

    return !((($value === 0) || ($value === false)));
}

$recipient = '';
$copy = 'oui';

$sentMessage = "Votre message est bien parvenu !";
$unsentMessage = "L'envoi du mail a échoué, veuillez réessayer SVP.";
$errorFormMessage = "Vous devez d'abord <a href=\"../contact.html\">envoyer le formulaire</a>.";
$invalidFormMessage = "Vérifiez que tous les champs soient bien remplis et que l'email soit sans erreur.";

if (!isset($_POST['send'])) {
    echo '<p>' . $errorFormMessage . '</p>' . "\n";
} else {
    $surname = (isset($_POST['surname'])) ? Rec($_POST['surname']) : '';
    $name = (isset($_POST['name'])) ? Rec($_POST['name']) : '';
    $email = (isset($_POST['email'])) ? Rec($_POST['email']) : '';
    $object = (isset($_POST['object'])) ? Rec($_POST['object']) : '';
    $message = (isset($_POST['message'])) ? Rec($_POST['message']) : '';

    $email = (IsEmail($email)) ? $email : '';

    if (($surname != '') && ($name != '') && ($email != '') && ($object != '') && ($message != '')) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'From:' . $surname . ' ' . $name . ' <' . $email . '>' . "\r\n" .
          'Reply-To:' . $email . "\r\n" .
          'Content-Type: text/plain; charset="utf-8"; DelSp="Yes"; format=flowed ' . "\r\n" .
          'Content-Disposition: inline' . "\r\n" .
          'Content-Transfer-Encoding: 7bit' . " \r\n" .
          'X-Mailer:PHP/' . phpversion();

        if ($copy == 'oui') {
            $target = $recipient . ';' . $email;
        } else {
            $target = $recipient;
        };

        $message = str_replace("&#039;", "'", $message);
        $message = str_replace("&#8217;", "'", $message);
        $message = str_replace("&quot;", '"', $message);
        $message = str_replace('<br>', '', $message);
        $message = str_replace('<br />', '', $message);
        $message = str_replace("&lt;", "<", $message);
        $message = str_replace("&gt;", ">", $message);
        $message = str_replace("&amp;", "&", $message);

        $num_emails = 0;
        $tmp = explode(';', $target);

        foreach ($tmp as $email_recipient) {
            if (mail($email_recipient, $object, $message, $headers))
                $num_emails++;
        }

        if ((($copy == 'oui') && ($num_emails == 2)) || (($copy == 'non') && ($num_emails == 1))) {
            ?>

            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "[http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd]">
            <html lang="fr" xmlns="[http://www.w3.org/1999/xhtml]" xml:lang="fr">
            <head>
              <title>Contact</title>
              <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
              <meta http-equiv="content-language" content="fr"/>
              <style type="text/css">
                  @import url('https://fonts.googleapis.com/css?family=Poppins');

                  html {
                      font-family: Poppins, "Helvetica Neue", Arial, sans-serif;
                      margin: 0;
                      padding: 0;
                  }

                  body {
                      width: 772px;
                      margin: 0 auto;
                      padding: 0;
                  }

                  p#success {
                      padding: 10px 20px;
                      border: 1px dotted #0f0;
                      color: #ccc;
                      font-weight: bold;
                  }
              </style>
            </head>

            <body>
            <h1>Contact</h1>
            <hr/>
            <p id="success">Votre message a bien été envoyé.</p>
            <p><strong>Courriel pour la réponse :</strong><br/><?php echo($email); ?></p>
            <p><strong>Objet :</strong><br/><?php echo($object); ?></p>
            <p><strong>Message :</strong><br/><?php echo(nl2br(htmlspecialchars($message))); ?></p><br><br><br>
            </body>
            </html>

            <?php
            echo "Vous allez être redirigé vers le site";
            header("refresh:5;url=../contact.html");
        } else {
            echo '<p>' . $unsentMessage . '</p>';
        }
    } else {
        echo '<p>' . $invalidFormMessage . ' <a href="../contact.html">Retour au formulaire</a></p>' . "\n";
    }
}

?>
