<?php
include_once('config.php');
include_once('entidades/usuario.php');
$usuario = new Usuario();
$aUsuarios = $usuario->obtenerTodos();
//print_r($aUsuarios); exit;
include_once('header.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Listado de usuarios</h1>
    <div class="row">
        <div class="col-12 mb-3">
            <a href="usuario-formulario.php" class="btn btn-primary mr-2">Nuevo</a>
        </div>
    </div>
    <table class="table table-hover border">
        <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Correo</th>
            <th>Acciones</th>
        </tr>
        <?php foreach($aUsuarios as $usuario):?>
            <td><?php echo $usuario->usuario;?></td>
            <td><?php echo $usuario->nombre;?></td>
            <td><?php echo $usuario->apellido;?></td>
            <td><?php echo $usuario->correo;?></td>
            <td><a href="usuario-formulario.php?id=<?php echo $usuario->idusuario; ?>"><i class="fas fa-search"></i></a></td>          

    </table>
    <?php endforeach;?>
</div>
<!-- /.container-fluid -->

<?php
include_once('footer.php');
?>