// from http://www.angularcode.com/simple-task-manager-application-using-angularjs-php-mysql/
function partsController($scope,$http) {
    var partsurl = "http://hollabaugh.com/inventory/search.php";

    function getParts() {
        document.body.style.cursor = 'wait';
        $http.get(partsurl+"?action=items").then(function(response){
            $scope.items = response.data.items;
            document.body.style.cursor = 'auto';
        })
    };

    $scope.update = function(id,field,text) {
//        console.log('update '+id+" "+field+" "+text);
        document.body.style.cursor = 'wait';
        $http.get(partsurl+"?action=update&id="+id+"&field="+field+"&text="+text).then(function(response){
//            console.log(response.data.items);
            $scope.items = response.data.items;
            document.body.style.cursor = 'auto';
        })
    }

    $scope.delete = function(id) {
        $http.get(partsurl+"?action=delete&id="+id).then(function(response){
            $scope.items = response.data.items;
        });
    }

    $scope.add = function() {
        $http.get(partsurl+"?action=add").then(function(response){
            $scope.items = response.data.items;
        });
    }


    $scope.show = function() {
        console.log('show');
    }


    getParts();
}
