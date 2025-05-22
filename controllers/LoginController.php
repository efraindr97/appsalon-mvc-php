<?php 
    namespace Controllers;

    use Classes\Email;
    use Model\Usuario;
    use MVC\Router;

    class LoginController {
        public static function login(Router $router) {
            $alertas = [];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $auth = new Usuario($_POST);

                $alertas = $auth->validarLogin();
            

                if (empty($alertas)) {
                    //Comprobar que exista el usuario
                    $usuario = Usuario::where('email', $auth->email);
                    if ($usuario) {
                        // Verificar el password

                        if  ($usuario->comprobarPasswordAndVerificado($auth->password) ) {
                            //Autenticar el usuario
                            session_start();
                            $_SESSION['id'] = $usuario->id;
                            $_SESSION['nombre'] = $usuario->nombre ." ". $usuario->apellido;
                            $_SESSION['email'] = $usuario->email;
                            $_SESSION['login'] = true;

                            //Redireccionar al usuario
                            if ($usuario->admin === "1") {
                                //Si es Admin
                                $_SESSION['admin'] = $usuario->admin ?? null;
                                header('Location: /admin');
                            } else {
                                // Si es cliente
                                header('Location: /cita');
                            }
                        }

                    } else {
                        Usuario::setAlerta('error', 'Usuario no encontrado');
                    }
                }
            }

            $alertas = Usuario::getAlertas();

            $router->render('auth/login', [
                'alertas' => $alertas
            ]);
        }

        public static function logout() {
            session_start();
            $_SESSION = [];
            header('Location: /');

        }

        public static function olvide(Router $router) {

            $alertas = [];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $auth = new Usuario($_POST);
                $alertas = $auth->validarEmail();

                if (empty($alertas)) {
                    $usuario = Usuario::where('email', $auth->email);

                    if ($usuario && $usuario->confirmado === "1") {
                        // Generar un token
                        $usuario->crearToken();
                        $usuario->guardar();

                        //TODO: Enviar el email
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarInstrucciones();

                        // Alerta de Exito
                        Usuario::setAlerta('exito','Se ha enviado un email con las instrucciones para recuperar su contraseña');


                    } else {
                        Usuario::setAlerta('error','El usuario no existe o no se encuentra confirmado');
                    }
                }
            }

            $alertas = Usuario::getAlertas();

            $router->render('auth/olvide-password', [
                'alertas' => $alertas
            ]);
        }

        public static function recuperar( Router $router) {
            $alertas = [];
            $token = s($_GET['token']);

            $error = false;

            //Buscar usuario por su token
            $usuario = Usuario::where('token',$token);

            if (empty($usuario)) {
                Usuario::setAlerta('error','El token no es válido');
                $error = true;
                
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                //Leer el nuevo password y guardarlo
                $password = new Usuario($_POST);
                $password->validarPassword();

                if (empty($alertas)) {
                    $usuario->password = null;
                    $usuario->password = $password->password;
                    $usuario->hashPassword();
                    $usuario->token = null;
                    $resultado =$usuario->guardar();

                    if ($resultado) {
                        header('Location: /');
                    }
                }
            }

            $alertas = Usuario::getAlertas();
            $router->render('auth/recuperar-password',[
                'alertas' => $alertas,
                'error' => $error
            ]);
        }

        public static function crear( Router $router) {
            $usuario = new Usuario;

            // Alertas Vacias
            $alertas = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $usuario->sincronizar($_POST);
                $alertas = $usuario->validarNuevaCuenta();

                // Revisar que alerta esté vacio
                if (empty($alertas)) {
                    // Verificar que el usuario no esté registrado
                    $resultado = $usuario->existeUsuario();

                    if ($resultado->num_rows) {
                        $alertas = Usuario::getAlertas();
                    } else {
                        // Hashear el password
                        $usuario->hashPassword();

                        // Generar un token único
                        $usuario->crearToken();

                        //Enviar el email
                        $email = new Email($usuario->nombre, $usuario->email, $usuario->token);

                        $email->enviarConfirmacion();

                        // Crear el usuario
                        $resultado = $usuario->guardar();
                        if ($resultado){
                            header('Location: /mensaje');
                        }

                    }
                }
            }

            $router->render('auth/crear-cuenta', [
                'usuario' => $usuario,
                'alertas' => $alertas
            ]);
        }

        public static function mensaje(Router $router) {
            $router->render('auth/mensaje');
        }

        public static function confirmar( Router $router) {
            
            $alertas = [];

            $token = s($_GET['token']);

            $usuario = Usuario::where('token',$token);

            if (empty($usuario)){
                //Mostrar mensaje de error
                Usuario::setAlerta('error','El token no es valido');
            } else {
                //Modificar a usuario confirmado
                $usuario->confirmado = "1";
                $usuario->token = null;
                $usuario->guardar();
                Usuario::setAlerta('exito','Usuario confirmado correctamente');
            }

            // Obtener alertas
            $alertas = Usuario::getAlertas();
            $router->render('auth/confirmar-cuenta',[
                'alertas' => $alertas
            ]);
        }
    }
    