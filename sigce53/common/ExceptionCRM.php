<?php

class CrmSqlException extends Exception{ 
    // Redefinir la excepción, por lo que el mensaje no es opcional
    public function __construct($msj,$errorSql,$query) {
        // algo de código
        $mensaje=$msj."</br>";
        $mensaje=$mensaje."Error: ".$errorSql."</br>";
        $mensaje=$mensaje." Query: ".$query."</br>";
        //asegúrese de que todo está asignado apropiadamente
        parent::__construct($mensaje, 0, null);
    }


    // representación de cadena personalizada del objeto
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function funciónPersonalizada() {
        echo "Una función personalizada para este tipo de excepción\n";
    }
}

?>
