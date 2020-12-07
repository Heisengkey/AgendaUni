<?php
    function alta_calendario ($conexion, $calendario)
    {
        try {
            $consulta = "CALL INSERTAR_CALENDARIO(:nombre, :descripcion, : tipoC, :esPublico, :oidu)";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':nombre', $calendario["nombre"]);
            $stmt->bindParam(':descripcion', $calendario["descripcion"]);
            $stmt->bindParam(':tipoC', $calendario["tipoC"]);
            $stmt->bindParam(':esPublico', $calendario["esPublico"]);
            $stmt->bindParam(':oidu', $calendario["oidu"]);
            $stmt->execute();
            return true;
        } catch (PDOException $error) {
            echo "Se ha producido un error al crear el calendario";
            return false;
        }
    }

    function consultar_calendarios($conexion, $oidu){
        try {
            $consulta = "SELECT NOMBRE, DESCRIPCION FROM CALENDARIOS WHERE OID_U=:oidu";

            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':oidu', $oidu);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $_SESSION['excepcion'] = $e->GetMessage();
            header("Location: excepcion.php");
        }
    }

    function total_calendarios($conexion, $query){
        $total_consulta = "SELECT COUNT(*) AS TOTAL FROM ($query)";

        $stmt = $conexion->query($total_consulta);
        $result = $stmt->fetch();
        $total = $result['TOTAL'];
        return  $total;
    }

    function follow_calendario($conexion,$calendario)
        {
        try {
            $consulta = "CALL FOLLOW_CALENDARIO(:oidu, :oidc )";
            $stmt=$conexion->prepare($consulta);
            $stmt->bindParam('oidu',$calendario["oidu"]);
            $stmt->bindParam(':oidc',$calendario["oidc"]);
            $stmt->execute();
            return true;
        }  catch(PDOException $error) {
                echo "Se ha producido un error y no se ha podido seguir al calendario";
            return false;
        }
}
    function unfollow_calendario($conexion,$calendario)
    {
        try {
            $consulta = "CALL UNFOLLOW_CALENDARIO(:oidu, :oidc )";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':oidu', $calendario["oidu"]);
            $stmt->bindParam(':oidc', $calendario["oidc"]);

            $stmt->execute();
            return true;
        } catch (PDOException $error) {
            echo "Se ha producido un error y no se ha podido dejar de seguir al calendario";
            return false;
        }
    }

    function modifica_calendario($conexion,$calendario) {
        try {
            $consulta = "CALL MODIFICA_CALENDARIO(:nombre,:latitud,:longitud, :fechainicio, :fechafin,:tipoevento,:diacompleto,:descripcion)";
            $stmt=$conexion->prepare($consulta);
            $stmt->bindParam(':nombre',$evento["nombre"]);
            $stmt->bindParam(':latitud',$evento["latitud"]);
            $stmt->bindParam(':longitud',$evento["longitud"]);
            $stmt->bindParam(':fechainicio',$evento["fechainicio"]);
            $stmt->bindParam(':horafin',$evento["horafin"]);
            $stmt->bindParam(':tipoevento',$evento["tipoevento"]);
            $stmt->bindParam(':diacompleto',$evento["diacompleto"]);
            $stmt->bindParam(':descripcion',$evento["descripcion"]);
            $stmt->execute();
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }


    function eliminar_calendario($conexion,$oidc, $oidu, $nombre)
    {
        $delete = "DELETE FROM CALENDARIOS WHERE OID_U=:oidu  AND OID_C=oid_c AND NOMBRE=:nombre" ;
        $stmt = $conexion->prepare($delete);
        $stmt->bindParam(':oidc',$oidc);
        $stmt->bindParam(':oidu',$oidu);
        $stmt->bindParam(':nombre',$nombre);
        $stmt->execute();
        return true;
    }

    function permisosCalendario ($conexion,$oidu)
    {
        $consulta = "SELECT NOMBRE FROM PERMISOS WHERE OID_U=:oidu";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(":oidu", $oidu);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    ?>