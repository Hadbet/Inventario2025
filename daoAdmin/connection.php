<?php

class LocalConector{
    private $host = "127.0.0.1:3306";
    private $usuario = "u909553968_InventarioUser";
    private $clave = "Inventario2025*";
    private $db = "u909553968_Inventario2025";
    private $conexion;

    public function conectar(){
        $this->conexion = mysqli_connect($this->host, $this->usuario, $this->clave, $this->db);
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
        //echo "conexion exitosa";
        return $this->conexion;
    }
}