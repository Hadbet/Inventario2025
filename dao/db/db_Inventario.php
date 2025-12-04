<?php

class LocalConector
{
    private $host = "127.0.0.1:3306";
    //private $usuario = "u909553968_InventarioUser";
    //private $clave = "Inventario2025*";
    //private $db = "u909553968_Inventario2025";

    private $usuario = "u909553968_inv_2025_user";
    private $clave = "LogisticaEvidencias1.";
    private $db = "u909553968_inv_2025_ger";
    public $conexion;

    public function conectar()
    {
        $con = mysqli_connect($this->host, $this->usuario, $this->clave, $this->db);
        return $con;
    }
}

?>