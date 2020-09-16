var misDatos = angular.module('datosApp', ['ngSanitize']);

misDatos.controller('CargarElementos', function($scope, $http) {

    $scope.imgperfil = "assets/logo_vert.jpg";

    $scope.CargarResidentes = function() {
        $http.get('../recursos/DevolverDatos.php' + '?Opcion=CargarResidentes&Param=')
            .then(function(datos) {
                $scope.residentes = datos.data;
            })
    }

    $scope.CargarFoto = function(id) {
        $http.get('../recursos/DevolverDatos.php' + '?Opcion=CargarFoto&Param=' + id)
            .then(function(datos) {
                longitud = datos.data['NombrePaciente'].indexOf(" ");
                $scope.imgperfil = "assets/images/" + datos.data['NombrePaciente'].substr(0, longitud) + ".jpg";
                $scope.imgstate = $scope.imgperfil;
            })
    }

    $scope.AgregarElemento = function() {
        fecha = new Date(document.getElementById('Fecha').value).getTime();
        console.log(fecha);
        //anio = fecha.getYear();
        //mes = date("mm", fecha);
        $http.get('../back/AgregarElemento.php' + '?Id=' + $scope.cmbResidente +
                '&Elemento=' + $scope.cmbElemento +
                '&Fecha=' + fecha +
                '&Debe=' + $scope.Precio * $scope.Cantidad +
                '&Haber=0' +
                '&Detalle=' + $scope.Detalle +
                '&Cantidad=' + $scope.Cantidad +
                '&Precio=' + $scope.Precio
            )
            .then(function(datos) {
                $scope.Mensaje = datos.data['Mensaje'];
                //$scope.AgregarDetalle(datos, fecha);
            });
    }
});