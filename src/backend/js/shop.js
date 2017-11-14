// from http://www.angularcode.com/simple-task-manager-application-using-angularjs-php-mysql/
var myApp = angular.module('myApp', []);
myApp.controller('appController', ['$scope','$http','$location',
function($scope,$http,$location) {
    var partsurl = "shop.php";

    function getParts() {
        document.body.style.cursor = 'wait';
        $http.get(partsurl+"?action=items").then(function(response){
            $scope.items = response.data.items;
            document.body.style.cursor = 'auto';
        })
    };

    $scope.show = function() {
        console.log('show');
    }


    getParts();
}]);
