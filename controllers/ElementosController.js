var misDatos = angular.module('datosApp', ['ngSanitize']);

misDatos.controller('ElementosController', function($scope, $http) {

    $scope.imgperfil = "assets/logo_vert.jpg";

    $scope.CargarResidentes = function() {
        //fecha = new Date(document.getElementById('fecha').value).getTime();
        $http.get('recursos/DevolverDatos.php' + '?Opcion=CargarResidentes&Param=')
            .then(function(datos) {
                $scope.residentes = datos.data;
                //$scope.Mensaje = datos.data.Mensaje;
                //console.log(datos.data);

                //$mystring = 'abc';
                //$findme   = 'a';
                //$pos = strpos($mystring, $findme);

                //$scope.imgperfil = "assets/images/" + datos.data['FotFoto'].indexOf(" ");
                //$scope.imgperfil = "assets/images/" + datos.data['NombrePaciente'].indexOf(" ") + ".jpg";
                //console.log(datos.data['FotFoto']);
                //console.log("Nombre del paciente:");
                //console.log($scope.imgperfil);
            });
    }

    $scope.CargarDatosResidente = function(dni) {
        $scope.MostrarPanales = 1;
        $scope.MostrarDescartables = 1;
        $scope.MostrarServicios = 1;
        $scope.MostrarInsumos = 1;

        //$scope.MostrarAgradecimiento = 0;

        $http.get('recursos/DevolverDatos.php' + '?Opcion=CargarDatosResidente&Param=' + dni)
            .then(function(datos) {
                if (datos.data['NombrePaciente']) {
                    $scope.Nombre = datos.data['NombrePaciente'];
                    $scope.ID = datos.data['IdPaciente'];
                    $scope.CargarElementos(datos.data['IdPaciente']);

                    if (!datos.data['FotFoto']) {
                        longitud = datos.data['NombrePaciente'].indexOf(" ");
                        $scope.imgperfil = "assets/images/" + datos.data['NombrePaciente'].substr(0, longitud) + ".jpg";
                    } else {
                        $scope.imgperfil = "assets/sinfoto.png";
                    }
                    //$scope.imgperfil = "assets/" + datos.data['FotFoto'];
                    $scope.MostrarError = 0;
                } else {
                    $scope.MostrarAgradecimiento = 0;
                    $scope.MostrarDescartables = 0;
                    $scope.MostrarInsumos = 0;
                    $scope.MostrarPanales = 0;
                    $scope.MostrarServicios = 0;
                    $scope.MostrarError = 1;
                }
                console.log(datos.data['FotFoto']);
                //console.log("Id:" + $scope.ID);
            });
    }

    // ELEMENTOS


    $scope.CargarElementos = function(id) {
        $scope.MostrarAgradecimiento = 1;
        $http.get('recursos/DevolverDatos.php' + '?Opcion=CargarElementos_panales&Param=' + id)
            .then(function(datos) {
                $scope.SaldoPanales = datos.data['SaldoPanales'];
                $scope.SaldoPanalesCantidad = datos.data['SaldoPanalesCantidad'];
                if (datos.data['SaldoPanales'] > 0) {
                    $scope.MostrarPanales = 1;
                    $scope.MostrarAgradecimiento = 0;
                } else { $scope.MostrarPanales = 0; }

            });
        $http.get('recursos/DevolverDatos.php' + '?Opcion=CargarElementos_descartables&Param=' + id)
            .then(function(datos) {
                $scope.SaldoDescartables = datos.data['SaldoDescartables'];
                if (datos.data['SaldoDescartables'] > 0) {
                    $scope.MostrarDescartables = 1;
                    $scope.MostrarAgradecimiento = 0;
                } else { $scope.MostrarDescartables = 0; }
                //$scope.SaldoDescartablesCantidad = datos.data['SaldoDescartablesCantidad'];
                //$scope.MostrarAgradecimiento = 0;
            });
        $http.get('recursos/DevolverDatos.php' + '?Opcion=CargarElementos_servicios&Param=' + id)
            .then(function(datos) {
                $scope.SaldoServicios = datos.data['SaldoServicios'];
                if (datos.data['SaldoServicios'] > 0) {
                    $scope.MostrarServicios = 1;
                    $scope.MostrarAgradecimiento = 0;
                } else { $scope.MostrarServicios = 0; }
                //$scope.SaldoDescartablesCantidad = datos.data['SaldoDescartablesCantidad'];
                //$scope.MostrarAgradecimiento = 0;
            });

        $http.get('recursos/DevolverDatos.php' + '?Opcion=CargarElementos_insumos&Param=' + id)
            .then(function(datos) {
                $scope.SaldoInsumos = datos.data['SaldoInsumos'];
                if (datos.data['SaldoInsumos'] > 0) {
                    $scope.MostrarInsumos = 1;
                    $scope.MostrarAgradecimiento = 0;
                } else { $scope.MostrarInsumos = 0; }
                //$scope.SaldoDescartablesCantidad = datos.data['SaldoDescartablesCantidad'];
                console.log(datos.data['SaldoInsumos']);
                //$scope.MostrarAgradecimiento = 0;
            });
    }


    // DETALLE
    $scope.Cargar_detalle_panales = function(id) {
            $http.get('recursos/pagos/pagar2.php')
                .then(function(datos) {
                    $scope.detalle_panales = datos.data;
                    $scope.preferenciaId = datos.data['preferenciaId'];
                    $scope.preferenciaId2 = datos.data['preferenciaId2'];
                });
        }
        /*$scope.Cargar_detalle_panales = function(id) {
            $http.get('recursos/DevolverDatos.php' + '?Opcion=Cargar_detalle_panales&Param=' + id)
                .then(function(datos) {
                    $scope.detalle_panales = datos.data;
                    $scope.preferenciaId = datos.data['preferenciaId'];
                });
        }*/
    $scope.Cargar_detalle_descartablesa = function(id) {
        $http.get('recursos/DevolverDatos.php' + '?Opcion=Cargar_detalle_descartables&Param=' + id)
            .then(function(datos) {
                $scope.detalle_panales = datos.data;
            });
    }
    $scope.Cargar_detalle_serviciosa = function(id) {
        $http.get('recursos/DevolverDatos.php' + '?Opcion=Cargar_detalle_servicios&Param=' + id)
            .then(function(datos) {
                $scope.detalle_panales = datos.data;
            });
    }
    $scope.Cargar_detalle_insumosa = function(id) {
        $http.get('recursos/DevolverDatos.php' + '?Opcion=Cargar_detalle_insumos&Param=' + id)
            .then(function(datos) {
                $scope.detalle_panales = datos.data;
            });
    }

});