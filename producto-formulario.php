
<?php
include_once("config.php");
include_once("entidades/producto.php");
include_once("entidades/tipoproducto.php");

$tipoProducto = new TipoProducto();
$aTipoProductos = $tipoProducto->obtenerTodos();
$producto = new Producto();
$producto->cargarFormulario($_REQUEST);


if($_POST){
    if(isset($_REQUEST["btnGuardar"])){
        $nombreImagen = "";
        //Almacenamos la imagen en el servidor
        if ($_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
            $nombreRandom = date("Ymdhmsi");
            $archivoTmp = $_FILES["imagen"]["tmp_name"];
            $nombreArchivo = $_FILES["imagen"]["name"];
            $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
            $nombreImagen = "$nombreRandom.$extension";
            move_uploaded_file($archivoTmp, "files/$nombreImagen");
        }
        if(isset($_GET["id"])){
            $productoAnt = new Producto();
            $productoAnt->idproducto = $_REQUEST["id"];
            $productoAnt->obtenerPorId();
            $imagenAnterior = $productoAnt->imagen;

            //Si es una actualizacion y se sube una imagen, elimina la anterior
            if ($_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
                if ($imagenAnterior != "") {
                    if(file_exists("files/$imagenAnterior"))
                        unlink("files/$imagenAnterior");
                }
            } else {
                //Si no viene ninguna imagen, setea como imagen la que habia previamente
                $nombreImagen = $imagenAnterior;
            }
            $producto->imagen = $nombreImagen;
            //Actualizo un cliente existente
            
            $producto->actualizar();
        } else {
             //si es nuevo
            $producto->imagen = $nombreImagen;
            $producto->insertar();
        }
    } else if(isset($_REQUEST["btnBorrar"])){
        $producto->eliminar();
        header("Location: producto-listado.php");
    }
    //si viene un id precargado del listado obtiene los datos del producto para cargar en formulario
} else if(isset($_GET["id"])){
    $producto->obtenerPorId();
}

include_once("header.php"); 

?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Productos</h1>
    <div class="row">
        <div class="col-12 mb-3">
            <a href="producto-listado.php" class="btn btn-primary mr-2">Listado</a>
            <a href="producto-formulario.php" class="btn btn-primary mr-2">Nuevo</a>
            <button type="submit" class="btn btn-success mr-2" id="btnGuardar" name="btnGuardar">Guardar</button>
            <button type="submit" class="btn btn-danger" id="btnBorrar" name="btnBorrar">Borrar</button>
        </div>
    </div>
    <div class="row">
        <div class="col-6 form-group">
            <label for="txtNombre">Nombre:</label>
            <input type="text" required class="form-control" name="txtNombre" id="txtNombre" value="<?php echo $producto->nombre; ?>">
        </div>
        <div class="col-6 form-group">
            <label for="">Tipo de producto:</label>
                <select class="form-control" name="lstTipoProducto" id="lstTipoProducto" onchange="" required>
                    <option value="" disabled <?php echo (!isset($_REQUEST["id"])? "selected":"");?> >Seleccionar</option>
                    <?php foreach ($aTipoProductos as $tipo):?>
                    <option value="<?php echo $tipo->idtipoproducto; ?>" <?php if($tipo->idtipoproducto == $producto->fk_idtipoproducto) echo "selected";?>><?php echo $tipo->nombre; ?></option>
                    <?php endforeach; ?>
                </select>
        </div>
        <div class="col-6 form-group">
            <label for="txtCorreo">Cantidad:</label>
            <input type="number" class="form-control" name="txtCantidad" id="txtCantidad" required value="<?php echo $producto->cantidad; ?>">
        </div>
        <div class="col-6 form-group">
            <label for="txtCorreo">Precio:</label>
            <input type="number" class="form-control" name="txtPrecio" id="txtPrecio" required value="<?php echo $producto->precio; ?>">
        </div>
    </div>
    <div class="row">
        <div class="col-12 form-group">
            <label for="txtDescripcion">Descripcion:</label>
            <textarea type="text" name="txtDescripcion" id="txtDescripcion" value=""><?php echo $producto->descripcion;?></textarea> 
        </div>
    </div>
    <div class="row">
        <div class="col-6 form-group">
            <label for="fileImagen">Imagen:</label>
            <input type="file" class="form-control-file" name="imagen" id="imagen">
            <?php if($producto->imagen != ""):?>
                <img src="files/<?php echo $producto->imagen; ?>" class="img-thumbnail">
                <?php else:?>
                    <img src="" class="img-thumbnail">
                <?php endif;?>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<script>
    ClassicEditor
        .create( document.querySelector( '#txtDescripcion' ) )
        .catch( error => {
        console.error( error );
    } );
</script>
<?php include_once("footer.php"); ?>