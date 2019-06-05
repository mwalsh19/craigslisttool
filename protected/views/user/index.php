<?php
Yii::app()->clientScript->registerPackage('dataTables');

Yii::app()->clientScript->registerScript('dataTableInit', '
         $("#distributors").dataTable({
         });
        ');
?>
<section class="content-header">
    <h1>
        Usuarios
        <small>manager</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Manager</a></li>
        <li><a href="#">Usuarios</a></li>
        <li class="active">Registros</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Usuarios</h3>
                    <div class="pull-right" style="margin: 10px 10px 0 0; overflow: hidden;">
                        <a href="<?php echo $this->createUrl('user/create'); ?>" class="btn btn-info btn-sm" style="color: #fff;">
                            <i class="glyphicon glyphicon-plus"></i> Crear nuevo usuario
                        </a>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="distributors" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($data)) {
                                foreach ($data as $user) {
                                    ?>
                                    <tr>
                                        <td><?php echo $user->first_name ?></td>
                                        <td><?php echo $user->last_name ?></td>
                                        <td><?php echo $user->email ?></td>
                                        <td><?php echo  $user->status? 'Activo': 'Inactivo' ?></td>
                                        <td><?php echo $user->role == 'A'? 'Administrador': 'Usuario' ?></td>
                                        <td>
                                            <a href="<?php echo $this->createUrl('user/update', array('id' => $user->id_user)); ?>" class="btn btn-primary btn-sm">
                                                <i class="glyphicon glyphicon-edit"></i> Editar
                                            </a>
                                            <a href="<?php echo $this->createUrl('user/reset', array('id' => $user->id_user)); ?>" class="btn btn-success btn-sm">
                                                <i class="glyphicon glyphicon-edit"></i> Cambiar contraseña
                                            </a>
                                            <?php
                                            echo CHtml::link('<i class="glyphicon glyphicon-trash"></i> Borrar', array('user/delete', 'id' => $user->id_user), array(
                                                'class' => 'btn btn-danger btn-sm',
                                                'confirm' => '¿Esta seguro que desea eliminar este registro?',
//                                                'params' => $params,
                                            ));
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div>
</section><!-- /.content -->
