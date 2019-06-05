<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Manager</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <?php
        Yii::app()->clientScript->registerPackage('manager');
        ?>
    </head>
    <body class="skin-black">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="<?php echo Yii::app()->getBaseUrl(true); ?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                <?php echo Yii::app()->name; ?> CMS
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="<?php echo $this->createUrl('manager/logout'); ?>" class="btn btn-flat" style="padding-bottom: 2px;">Sign out</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <aside class="left-side sidebar-offcanvas">
                <section class="sidebar">
                    <ul class="sidebar-menu">
                        <li>
                            <a href="<?php echo Yii::app()->createUrl('user/index'); ?>">
                                <i class="fa fa-th"></i> <span>Users</span>
                            </a>
                        </li>
                        
                        <!--                        <li>
                                                    <a href="<?php //echo Yii::app()->createUrl('config/index');  ?>">
                                                        <i class="fa fa-th"></i> <span>Configuración</span>
                                                    </a>
                                                </li>-->
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <?php echo $content; ?>
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->


    </body>
</html>