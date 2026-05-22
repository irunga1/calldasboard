<?php 
    class Utilery {
        public function __construct(){
            
        }
        public  function sanitize($phrase){
            $erase = ["<", ">", "!", "=", "-", "*", "+",".","-", "/", "'", "\"","(",")","|",";","@","$","%","#","^","&"];
            foreach($erase as $it){          
                $phrase = str_replace($it, "", $phrase);
            }
            return $phrase;
        }
        public  function sanitizeMail($phrase){
            $erase = ["<", ">", "!", "=",  "*", "+","/", "'", "\"","(",")","|",";","#","^","&"];
            foreach($erase as $it){          
                $phrase = str_replace($it, "", $phrase);
            }
            return $phrase;
        }
        public  function sanitizeDate($phrase){
            $erase = ["<", ">", "!", "=",  "*", "+",".", "/", "'", "\"","(",")","|",";","@","$","?","%","#","^","&"];
            foreach($erase as $it){          
                $phrase = str_replace($it, "", $phrase);
            }
            return $phrase;
        }
        public function sanitizePassword($password){
            // Aqu�, se permiten solo caracteres alfanum�ricos y ciertos caracteres especiales
            // Se pueden ajustar seg�n las pol�ticas de contrase�as espec�ficas
            $allowed_chars = "/[^a-zA-Z0-9]/";
            $sanitized_password = preg_replace($allowed_chars, '', $password);
            return $sanitized_password;
        }
        public function debug($obj = []){
            echo "debug";
            echo "<pre>";
            print_r($obj);
            echo "</pre>";
        }
        public function getVars()
        {            
            // $ruta_archivo = '.env';
            $ruta_archivo = dirname(__DIR__) . '/.env';
            $vars= [];
            if (file_exists($ruta_archivo)) {                
                $lineas = file($ruta_archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $vars = array();
                foreach ($lineas as $linea) {
                    list($clave, $valor) = explode('=', $linea, 2);
                    $clave = trim($clave);
                    $valor = trim($valor);
                    $vars[$clave] = $valor;
                }
            } else {
                echo "El archivo no existe.";
            }
            return $vars;
        }
        
    }
?>
