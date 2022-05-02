<?php

class Venta
{
    private $idventa;
    private $fk_idcliente;
    private $fk_idproducto;
    private $fecha;
    private $cantidad;
    private $preciounitario;
    private $total;
    private $nombre_cliente;
    private $nombre_producto;

    public function __construct()
    {

    }

    public function __get($atributo)
    {
        return $this->$atributo;
    }

    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
        return $this;
    }

    public function cargarFormulario($request)
    {
        $this->idventa = isset($request["id"]) ? $request["id"] : "";
        $this->fk_idcliente = isset($request["lstCliente"]) ? $request["lstCliente"] : "";
        $this->fk_idproducto = isset($request["lstProducto"]) ? $request["lstProducto"] : "";
        //$this->fecha = isset($request["txtFecha"]) ? $request["txtFecha"] : "";
        $this->cantidad = isset($request["txtCantidad"]) ? $request["txtCantidad"] : 0;
        $this->preciounitario = isset($request["txtPrecioUnitario"]) ? $request["txtPrecioUnitario"] : 0;
        $this->total = $this->preciounitario * $this->cantidad;
        if (isset($request["txtAnio"]) && isset($request["txtMes"]) && isset($request["txtDia"]) && isset($request["txtHora"])) {
            $this->fecha = $request["txtAnio"] . "-" . $request["txtMes"] . "-" . $request["txtDia"] . " " . $request["txtHora"];
        }
    }

    public function insertar()
    {
        //Instancia la clase mysqli con el constructor parametrizado
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        //Arma la query
        $sql = "INSERT INTO ventas (
                    fecha,
                    cantidad,
                    preciounitario,
                    total,
                    fk_idcliente,
                    fk_idproducto
                ) VALUES (
                    '$this->fecha',
                    '$this->cantidad',
                    '$this->preciounitario',
                    '$this->total',
                    '$this->fk_idcliente',
                    '$this->fk_idproducto'
                );";
        //print_r($sql);exit;
        //Ejecuta la query
        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }
        //Obtiene el id generado por la inserción
        $this->idventa = $mysqli->insert_id;
        //Cierra la conexión
        $mysqli->close();
    }

    public function actualizar()
    {

        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "UPDATE ventas SET
                fecha = '" . $this->fecha . "',
                cantidad = '" . $this->cantidad . "',
                preciounitario = '" . $this->preciounitario . "',
                total = '" . $this->total . "',
                fk_idcliente =  '" . $this->fk_idcliente . "',
                fk_idproducto =  '" . $this->fk_idproducto . "'
                WHERE idventa = " . $this->idventa;

        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }
        $mysqli->close();
    }

    public function eliminar()
    {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "DELETE FROM ventas WHERE idventa = " . $this->idcliente;
        //Ejecuta la query
        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }
        $mysqli->close();
    }

    public function obtenerPorId()
    {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT idventa,
                        fecha,
                        cantidad,
                        preciounitario,
                        total,
                        fk_idcliente,
                        fk_idproducto
                FROM ventas
                WHERE idventa = $this->idventa";
        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        //Convierte el resultado en un array asociativo
        if ($fila = $resultado->fetch_assoc()) {
            $this->idventa = $fila["idventa"];
            $this->fecha = $fila["fecha"];
            $this->cantidad = $fila["cantidad"];
            $this->preciounitario = $fila["preciounitario"];
            $this->total = $fila["total"];
            $this->fk_idcliente = $fila["fk_idcliente"];
            $this->fk_idproducto = $fila["fk_idproducto"];
        }
        $mysqli->close();

    }

     public function obtenerTodos(){
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT 
                    idventa,
                    fecha,
                    cantidad,
                    preciounitario,
                    total,
                    fk_idcliente,
                    fk_idproducto
                FROM ventas";
        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $aResultado = array();
        if($resultado){
            //Convierte el resultado en un array asociativo

            while($fila = $resultado->fetch_assoc()){
                $entidadAux = new Cliente();
                $entidadAux->idventa = $fila["idventa"];
                $entidadAux->fecha = $fila["fecha"];
                $entidadAux->cantidad = $fila["cantidad"];
                $entidadAux->cuit = $fila["preciounitario"];
                $entidadAux->telefono = $fila["total"];
                $entidadAux->correo = $fila["fk_idcliente"];
                $entidadAux->fecha_nac = $fila["fk_idproducto"];
                $aResultado[] = $entidadAux;
            }
        }
        return $aResultado;
    }

    public function cargarGrilla(){
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT 
                    A.idventa,
                    A.fecha,
                    A.cantidad,
                    A.fk_idcliente,
                    B.nombre AS nombre_cliente,
                    A.fk_idproducto,
                    C.nombre AS nombre_producto,
                    A.preciounitario,
                    A.total
                FROM ventas A
                INNER JOIN clientes B ON A.fk_idcliente = B.idcliente
                INNER JOIN productos C ON A.fk_idproducto = C.idproducto
                ORDER BY A.fecha DESC";
        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }
        $aResultado = array();
        if($resultado){
            //Convierte el resultado en un array asociativo
            while($fila = $resultado->fetch_assoc()){
                $entidadAux = new Venta();
                $entidadAux->idventa = $fila["idventa"];
                $entidadAux->fecha = $fila["fecha"];
                $entidadAux->cantidad = $fila["cantidad"];
                $entidadAux->fk_idcliente = $fila["fk_idcliente"];
                $entidadAux->nombre_cliente = $fila["nombre_cliente"];
                $entidadAux->fk_idproducto = $fila["fk_idproducto"];
                $entidadAux->nombre_producto = $fila["nombre_producto"];
                $entidadAux->preciounitario = $fila["preciounitario"];
                $entidadAux->total = $fila["total"];
                $aResultado[] = $entidadAux;
            }
        }
        return $aResultado;
    }

    public function obtenerFacturacionMensual($mes){
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT SUM(total) as total FROM ventas WHERE MONTH(fecha)=$mes";
        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }   
        $fila = $resultado->fetch_assoc();
        $mysqli->close();
        return $fila["total"] != "" ? $fila["total"] : 0;
    }

    public function obtenerFacturacionAnual($anio){
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT SUM(total) as total FROM ventas WHERE YEAR(fecha)=$anio";
        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }   
        $fila = $resultado->fetch_assoc();
        $mysqli->close();
        return $fila["total"] != "" ? $fila["total"] : 0;
    }
}
?>