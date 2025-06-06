<?php 
namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion()
    {
        // Crear el objeto de email
       // Looking to send emails in production? Check out our Email API/SMTP product!
        $mail = new PHPMailer();
        $mail->isSMTP();
       $mail->Host = $_ENV['EMAIL_HOST'];
       $mail->SMTPAuth = true;
       $mail->Port = $_ENV['EMAIL_PORT'];
       $mail->Username = $_ENV['EMAIL_USER'];
       $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentras@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com','AppSalon.com');
        $mail->Subject = 'Confirma tu cuenta';

        // Set HTML 
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        $contenido = "<html>";
        $contenido .= "<p><strong>". $this->nombre ."</strong> Has creado tu cuenta en AppSalon.com, solo debes confirmar tu correo presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='". $_ENV['APP_URL'] ."/confirmar-cuenta?token=". $this->token ."'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();

    }

    public function enviarInstrucciones(){
                // Crear el objeto de email
       // Looking to send emails in production? Check out our Email API/SMTP product!
       $mail = new PHPMailer();
       $mail->isSMTP();
       $mail->Host = $_ENV['EMAIL_HOST'];
       $mail->SMTPAuth = true;
       $mail->Port = $_ENV['EMAIL_PORT'];
       $mail->Username = $_ENV['EMAIL_USER'];
       $mail->Password = $_ENV['EMAIL_PASS'];

       $mail->setFrom('cuentras@appsalon.com');
       $mail->addAddress('cuentas@appsalon.com','AppSalon.com');
       $mail->Subject = 'Restablece tu password';

       // Set HTML 
       $mail->isHTML(true);
       $mail->CharSet = 'UTF-8';
       
       $contenido = "<html>";
       $contenido .= "<p><strong>". $this->nombre ."</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo</p>";
       $contenido .= "<p>Presiona aquí: <a href='<a href='". $_ENV['APP_URL'] ."/recuperar?token=". $this->token ."'>Restablecer Password</a></p>";
       $contenido .= "<p>Si tu no solicitaste estas instrucciones, puedes ignorar este mensaje</p>";
       $contenido .= "</html>";

       $mail->Body = $contenido;

       //Enviar el mail
       $mail->send();
    }
}