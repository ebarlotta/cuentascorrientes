var misDatos = angular.module('datosApp', []);

misDatos.controller('ElementosController', function($scope, $http) {

    $scope.CargarResidentes = function() {
        //fecha = new Date(document.getElementById('fecha').value).getTime();
        $http.get('DevolverDatos.php' + '?Opcion=CargarResidentes&Param=')
            .then(function(datos) {
                $scope.residentes = datos.data;
                //$scope.Mensaje = datos.data.Mensaje;
                //console.log(datos.data);
                $scope.imgperfil = datos.data['FotFoto'];
                console.log(datos.data['FotFoto']);
            });
    }

    $scope.AgregarElemento = function() {
        fecha = new Date(document.getElementById('Fecha').value).getTime();
        console.log(fecha);
        //anio = fecha.getYear();
        //mes = date("mm", fecha);
        $http.get('AgregarElemento.php' + '?Id=' + $scope.cmbResidente +
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

    $scope.CargarDatosResidente = function(dni) {
        $http.get('DevolverDatos.php' + '?Opcion=CargarDatosResidente&Param=' + dni)
            .then(function(datos) {
                $scope.Nombre = datos.data['NombrePaciente'];
                $scope.ID = datos.data['IdPaciente'];

                //console.log("pepep2" + datos.data['IdPaciente']);
                $scope.CargarElementos(datos.data['IdPaciente']);
                $scope.imgperfil = datos.data['FotFoto'];

                console.log(datos.data['FotFoto']);
                console.log("Id:" + $scope.ID);
            });
    }

    // ELEMENTOS


    $scope.CargarElementos = function(id) {
            $http.get('DevolverDatos.php' + '?Opcion=CargarElementos_panales&Param=' + id)
                .then(function(datos) {
                    $scope.SaldoPanales = datos.data['SaldoPanales'];
                    $scope.SaldoPanalesCantidad = datos.data['SaldoPanalesCantidad'];
                });
            $http.get('DevolverDatos.php' + '?Opcion=CargarElementos_descartables&Param=' + id)
                .then(function(datos) {
                    $scope.SaldoDescartables = datos.data['SaldoDescartables'];
                    //$scope.SaldoDescartablesCantidad = datos.data['SaldoDescartablesCantidad'];
                });
            $http.get('DevolverDatos.php' + '?Opcion=CargarElementos_servicios&Param=' + id)
                .then(function(datos) {
                    $scope.SaldoServicios = datos.data['SaldoServicios'];
                    //$scope.SaldoDescartablesCantidad = datos.data['SaldoDescartablesCantidad'];
                });

            $http.get('DevolverDatos.php' + '?Opcion=CargarElementos_insumos&Param=' + id)
                .then(function(datos) {
                    $scope.SaldoInsumos = datos.data['SaldoInsumos'];
                    //$scope.SaldoDescartablesCantidad = datos.data['SaldoDescartablesCantidad'];
                });
        }
        /*$scope.AgregarDetalle = function(datos, fecha) {
            $http.get('AgregarElemento.php' + '?Id=' + datos.IdElemento +
                    '&Debe=' + $scope.Cantidad * $scope.Precio +
                    '&Detalle=' + $scope.Detalle +
                    '&Fecha=' + fecha +
                    '&Detalle=' + $scope.Detalle)
                .then(function(datos) {
                    $scope.residentes = datos.data;
                    //$scope.Mensaje = datos.data.Mensaje;
                    console.log(datos.data);
                    $scope.imgperfil = datos.data['FotFoto'];
                    console.log(datos.data['FotFoto']);
                });

        }*/
});