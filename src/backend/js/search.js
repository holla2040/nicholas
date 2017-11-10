// from http://www.angularcode.com/simple-task-manager-application-using-angularjs-php-mysql/
function partsController($scope,$http,$location) {
    var partsurl = "search.php";

    function getParts() {
        document.body.style.cursor = 'wait';
    
        if ($location.search().limit) {
            url = partsurl+"?action=items&limit="+$location.search().limit; 
        } else {
            url = partsurl+"?action=items";
        }
        $http.get(url).then(function(response){
            $scope.items = response.data.items;
            document.body.style.cursor = 'auto';
        })
    };

    $scope.update = function(id,field,e) {
        if (e) {
            var target = e.target || e.srcElement;
            var text = target.value;
            console.log('update '+id+" "+field+" "+text);
            document.activeElement.blur();

            $http.get(partsurl+"?action=update&id="+id+"&field="+field+"&text="+text).then(function(response){

    //            console.log(response.data.items);
    // this make interface really slow            $scope.items = response.data.items;
                document.body.style.cursor = 'auto';
            })
        }
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
