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
            })
    }
});