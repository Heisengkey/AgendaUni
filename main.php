<?php
session_start();
require ("gestionBD.php");
require ("gestionarUsuarios.php");
require ("gestionarCalendarios.php");

//este array es para traducir el mes a español
$meses = array(
    'January' => 'Enero',
    'February' => 'Febrero',
    'March' => 'Marzo',
    'April' => 'Abril',
    'May' => 'Mayo',
    'June' => 'Junio',
    'July' => 'Julio',
    'August' => 'Agosto',
    'September' => 'Septiembre',
    'October' => 'Octubre',
    'November' => 'Noviembre',
    'December' => 'Diciembre'
);

    function getMesAnyo($numMes){
        $fecha = DateTime::createFromFormat('!m', $numMes);
        return $fecha->format('F');
    }

    if (!isset($_SESSION['login']))
    Header("Location: login.php");
    else {
    if (isset($_SESSION['login'])) {
        $uvus = $_SESSION['login'];
    }
}

    if (isset($_SESSION["paginacion"]))
        $paginacion = $_SESSION["paginacion"];

    $pagina_seleccionada = isset($_GET["PAG_NUM"]) ? (int)$_GET["PAG_NUM"] : (isset($paginacion) ? (int)$paginacion["PAG_NUM"] : 1);
    $pag_tam = isset($_GET["PAG_TAM"]) ? (int)$_GET["PAG_TAM"] : (isset($paginacion) ? (int)$paginacion["PAG_TAM"] : 5);

    if ($pagina_seleccionada < 1) 		$pagina_seleccionada = 1;
    if ($pag_tam < 1) 		$pag_tam = 5;

    unset($_SESSION["paginacion"]);

    $conexion = crearConexionBD();
    $oidU = getOIDUsuario($conexion, $uvus);


    $query = 'SELECT NOMBRE, DESCRIPCION FROM CALENDARIOS WHERE OID_U=' . $oidU;

    $total_calendarios = total_calendarios($conexion, $query);
    $total_paginas = (int)($total_calendarios / $pag_tam);

    if ($total_calendarios % $pag_tam > 0)		$total_paginas++;

    if ($pagina_seleccionada > $total_paginas)		$pagina_seleccionada = $total_paginas;

    $paginacion["PAG_NUM"] = $pagina_seleccionada;
    $paginacion["PAG_TAM"] = $pag_tam;
    $_SESSION['paginacion'] = $paginacion;
    $filas = consultar_calendarios($conexion, $oidU);

    $conexion = cerrarConexionBD($conexion);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link href="img/favicon.ico" rel="icon" type="image/ico"/>
    <link rel="stylesheet" type="text/css" href="css/estilos.css"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
          integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <title>AgendaUni</title>
</head>
<body>
<?php
include_once("menu.php");
include_once("form_nuevo_calendario.php");
?>

<main>
    <div class="boton_creaCalen">
        <button name="btn_creaCalen" onclick=esconderDiv();><i class="fas fa-plus"></i>Nuevo Calendario</button>
    </div>

    <div class="calendarios_paginados">

        <nav>

            <div id="enlaces">

                <?php

                for( $pagina = 1; $pagina <= $total_paginas; $pagina++ )

                    if ( $pagina == $pagina_seleccionada) { 	?>

                        <span class="current"><?php echo $pagina; ?></span>

                    <?php }	else { ?>

                        <a href="main.php?PAG_NUM=<?php echo $pagina; ?>&PAG_TAM=<?php echo $pag_tam; ?>"><?php echo $pagina; ?></a>

                    <?php } ?>

            </div>



            <form method="get" action="main.php">

                <input id="PAG_NUM" name="PAG_NUM" type="hidden" value="<?php echo $pagina_seleccionada?>"/>

                Mostrando

                <input id="PAG_TAM" name="PAG_TAM" type="number"

                       min="1" max="<?php echo $pag_tam; ?>"

                       value="<?php echo $pag_tam?>" autofocus="autofocus" />

                entradas de <?php echo $total_calendarios?>

                <input type="submit" value="Cambiar">

            </form>



        </nav>

        <div class="lista_Cal">
            <?php
            foreach($filas as $fila){
                ?>
                <div class="elemento-lista">
                    <?php if (!(is_null($fila['DESCRIPCION']))) { ?>
                        <div class="elemento-cal"><h4 class="nombre"><?php echo $fila['NOMBRE']  ?> <a href="form_update_calendario.php"><img src="img/editar.png" width="1%" height="1%"> </a> <button type="submit" onclick=" eliminar_calendario($conexion,$oidc, $oidu, $nombre)"<img src="img/basura.png" width="1%" height="1%"> </button> </h4>
                            <h5 class="desc-cal" ><?php echo $fila['DESCRIPCION']; ?></h5></div>
                    <?php }else{ ?>
                        <div class="elemento-cal"><h4 class="nombre"><?php echo $fila['NOMBRE'] ?></div>
                    <?php
                }
            ?>
                </div>
                <?php
            }
            cerrarConexionBD($conexion);
            ?>

        </div>

    </div>

    <div>
        <?php
        include_once ("semana.php");
        ?>
    </div>

    <div id="crear_evento" style="display:none;">
        <?php
        include_once ("form_nuevo_evento.php");
        ?>
    </div>

</main>

<?php
include_once("pie.php");
?>
</body>
</html>