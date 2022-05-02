
<?php
include_once("config.php");
include_once("entidades/venta.php");
include_once("entidades/producto.php");
include_once("entidades/tipoproducto.php");
include_once("entidades/cliente.php");

$venta = new Venta();
$venta->cargarFormulario($_REQUEST);

$producto = new Producto();
$aProductos = $producto->obtenerTodos();

$cliente = new Cliente();
$aClientes = $cliente->obtenerTodos();

if(isset($_GET["do"]) && $_GET["do"] == "buscarProducto"){
    if(isset($_GET["id"]) && $_GET["id"] > 0){
        $producto->idproducto = $_GET["id"];
        $producto->obtenerPorId();
        $array["cantidad"] = $producto->cantidad; 
        $array["precio"] = $producto->precio;     
        echo json_encode($array);
        exit;
    }
}

if($_POST){
    if(isset($_REQUEST["btnGuardar"])){
    //si viene id actualiza producto
        if(isset($_GET["id"])){
            $venta->actualizar();
        }//si es nuevo guarda   
        else {
            $venta->insertar();
        }
    } else if(isset($_REQUEST["btnBorrar"])){
        $venta->eliminar();
        header("Location: venta-listado.php");
    }
    //si viene un id precargardo del listado de ventas obtiene sus datos para cargar en formulario
} else if(isset($_GET["id"])){
    $venta->obtenerPorId();
}

include_once("header.php"); 
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Venta</h1>
    <div class="row">
        <div class="col-12 mb-3">
            <a href="venta-listado.php" class="btn btn-primary mr-2">Listado</a>
            <a href="venta-formulario.php" class="btn btn-primary mr-2">Nuevo</a>
            <button type="submit" class="btn btn-success mr-2" id="btnGuardar" name="btnGuardar">Guardar</button>
            <button type="submit" class="btn btn-danger" id="btnBorrar" name="btnBorrar">Borrar</button>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
        <label for="txtFechaNac" class="d-block">Fecha y hora:</label>
            <select class="form-control d-inline" name="txtDia" id="txtDia" style="width: 80px">
                <option selected="" disabled="">DD</option>
                <?php for($i=1; $i <= 31; $i++): ?>
                    <?php if($venta->fecha != "" && $i == date_format(date_create($venta->fecha), "d")): ?>
                        <option selected><?php echo $i; ?></option>
                        <?php else: ?>
                        <option><?php echo $i; ?></option>
                    <?php endif; ?>
                <?php endfor; ?>
            </select>
            <select class="form-control d-inline" name="txtMes" id="txtMes" style="width: 80px">
                <?php for($i=1; $i <= 12; $i++): ?>
                    <?php if($venta->fecha != "" && $i == date_format(date_create($venta->fecha), "m")): ?>
                    <option selected><?php echo $i; ?></option>
                    <?php else: ?>
                    <option><?php echo $i; ?></option>
                    <?php endif; ?>
                <?php endfor; ?>
            </select>
                <select class="form-control d-inline" name="txtAnio" id="txtAnio" style="width: 100px">
                    <option selected="" disabled="">YYYY</option>
                    <?php for($i=date("Y"); $i >= 1900 ; $i--): ?>
                        <?php if($venta->fecha != "" && $i == date_format(date_create($venta->fecha), "Y")): ?>
                            <option selected><?php echo $i; ?></option>
                            <?php else: ?>
                            <option><?php echo $i; ?></option>
                        <?php endif; ?>
                    <?php endfor; ?> ?>
                </select>
                <?php if($venta->fecha == ""): ?>
                <input type="time" required="" class="form-control d-inline" style="width: 120px" name="txtHora" id="txtHora" value="00:00">
                <?php else: ?>
                <input type="time" required="" class="form-control d-inline" style="width: 120px" name="txtHora" id="txtHora" value="<?php echo date_format(date_create($venta->fecha), "H:i"); ?>">
                <?php endif; ?>
        </div>   
    </div>
    <div class="row">
        <div class="col-6">
            <label for="" class="my-3">Cliente:</label>
            <select class="form-control" name="lstCliente" id="lstCliente">
            <option value="" disabled <?php echo (!isset($_REQUEST["id"])? "selected":"");?> >Seleccionar</option>
            <?php foreach($aClientes as $cliente):?>
            <option value="<?php echo $cliente->idcliente;?>" <?php if($venta->fk_idcliente == $cliente->idcliente) echo "selected";?>><?php echo $cliente->nombre;?></option>
            <?php endforeach;?>
            </select>
        </div>
        <div class="col-6">
            <label for="" class="my-3">Producto:</label>
            <select class="form-control" name="lstProducto" id="lstProducto" onchange="fBuscarPrecio()">
            <option value="" disabled <?php echo (!isset($_REQUEST["id"])? "selected":"");?> >Seleccionar</option>
            <?php foreach($aProductos as $producto):?>
            <option value="<?php echo $producto->idproducto;?>" <?php if($venta->fk_idproducto == $producto->idproducto) echo "selected";?>><?php echo $producto->nombre;?></option>
            <?php endforeach;?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <label for="" class="my-3">Precio Unitario:</label>
            <input class="form-control" type="txt" name="txtPrecioUnitarioCurrency" id="txtPrecioUnitarioCurrency" value="<?php echo $venta->preciounitario; ?>">
            <input class="form-control" type="hidden" name="txtPrecioUnitario" id="txtPrecioUnitario" value="<?php echo $venta->preciounitario; ?>">
        </div>
        <div class="col-6">
            <label for="" class="my-3">Cantidad:</label>
            <input class="form-control" type="number" name="txtCantidad" id="txtCantidad" onchange="calcularTotal()" value="<?php echo $venta->cantidad; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <label for="" class="my-3">Total:</label>
            <input class="form-control" type="txt" name="txtTotal" id="txtTotal" disabled value="<?php echo $venta->total;?>">
        </div>
    </div>
</div>

<script>

function fBuscarPrecio(){
    let idProducto = $("#lstProducto option:selected").val();
      $.ajax({
            type: "GET",
            url: "venta-formulario.php?do=buscarProducto",
            data: { id:idProducto },
            async: true,
            dataType: "json",
            success: function (respuesta) {
                strResultado = Intl.NumberFormat("es-AR", {style: 'currency', currency: 'ARS'}).format(respuesta.precio);
                $("#txtPrecioUnitarioCurrency").val(strResultado);
                $("#txtPrecioUnitario").val(respuesta.precio);
            }
        });
}

function calcularTotal(){
   let cantidad = $("#txtCantidad").val();
   let precio = $("#txtPrecioUnitario").val();
   let total = Intl.NumberFormat("es-AR").format(cantidad * precio);
   $("#txtTotal").val("$ " + total);
}

</script>

<?php
include_once("footer.php");
?>