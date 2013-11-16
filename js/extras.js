//Angular Routes

var myApp = angular.module('myApp', ['ui.bootstrap']);

myApp.config(function ($routeProvider){
    $routeProvider
        .when('/',
        {
            controller:"loadFeaturesCtrl",
            templateUrl: 'dashboard.html'
        })
        .when('/cardlist',
        {
            controller: 'PostsCtrlAjax',
            templateUrl: 'cardlist.html'
        })
        .when('/cardshow',
        {
            controller: 'MyCardCtrl',
            templateUrl: 'cardshow.html'
        })
        .when('/newcard',
        {
            controller: 'NewCardCtrl',
            templateUrl: 'newcard.html'
        })
        .when('/login',
        {
            controller: 'LoginCtrlAjax',
            templateUrl: 'login.html'
        })
        .when('/register',
        {
            controller: 'RegisterCtrlAjax',
            templateUrl: 'register.html'
        })
        .when('/admin',
        {
            templateUrl: 'admin.html'
        })
        .otherwise({redirectTo:'/'});
});

// Angular Controllers

function loadFeaturesCtrl($scope){

   $scope.data = JSON.parse(localStorage.getItem('Object'));
//    $scope.data = {message:"hello"};
//    alert(localStorage.getItem('Object'));

}
myApp.controller('loadFeaturesCtrl',loadFeaturesCtrl);

function LoginCtrlAjax($scope, $http){
    $scope.clickMe = function(){
        $http({method: 'POST', url: 'api/userlogic.php?username=' + $scope.username +"&password="+ $scope.password }).success(function(data) {
            saveUserData(data);
            window.location ="#/";
        })
    };
}
myApp.controller('LoginCtrlAjax',LoginCtrlAjax);

function RegisterCtrlAjax($scope, $http){
    $scope.clickMe = function(){
        $http({method: 'POST', url: 'api/userlogic.php?username=' + $scope.username +"&password="+ $scope.password+"&compassword="+ $scope.password  }).success(function(data) {
            saveUserData(data);
            window.location ="#/login";
        })
    };
}
myApp.controller('RegisterCtrlAjax',RegisterCtrlAjax);

function LogoffCtrlAjax($scope, $http){
    $scope.clickMeOff = function(){
        $http({method: 'POST', url: "api/userlogic.php?logout="+ UserString()+"&token="+TokenString()}).success(function(data) {
            window.location ="#/login";
        })
    };
}
myApp.controller('LogoffCtrlAjax',LogoffCtrlAjax);

function NewCardCtrl($scope, $http){

    $scope.clickMe = function(){
        var url ='api/cardlogic.php?name=' + "&username=" + UserString() + '&token=' + TokenString() + '&name='+ $scope.cardname + "&description=" + $scope.carddescription+ '';
        $http({method: 'POST', url: url }).success(function(data) {
            $scope.data=data;
        })

    };
}
myApp.controller('NewCardCtrl',NewCardCtrl);

function PostsCtrlAjax($scope, $http){
    $http({method: 'POST', url: 'api/cardlogic.php?username=' + UserString() + '&token=' + TokenString() + ''}).success(function(data) {
        $scope.posts = data; // response data
    }).error(function(data, status, headers, config) {
            $scope.posts = data;
        });
}
myApp.controller('PostsCtrlAjax',PostsCtrlAjax);

function MyCardCtrl($scope, $http){
    var url = 'api/cardlogic.php?username=' + UserString() + '&token=' + TokenString() + '&loadmycard='+ UserString();
    $http({method: 'POST', url:url }).success(function(data) {
        $scope.posts = data; // response data

        $scope.rate = 5;////sets the default
        $scope.max = 10;
        $scope.isReadonly = true;
        $scope.ratingStates = [
                {stateOn: 'icon-star', stateOff: 'icon-star-empty'},
                {stateOff: 'icon-off'}
        ];
    });
    $scope.clickMe = function(post){
        var url ='api/cardlogic.php?name=' + "&username=" + UserString() + '&token=' + TokenString() + '&thiscard='+ post.id + '&votes=true';
        $http({method: 'POST', url: url }).success(function(data) {

            post.votes=Number(data.votes);


        })
    };
}
myApp.controller('MyCardCtrl',MyCardCtrl);

function saveUserData(data) {
    if (typeof(Storage) !== "undefined") {
        var Object = data;
        localStorage.setItem('Object', JSON.stringify(Object));
    } else {
        alert("Your browser does not support web storage.");
    }
}

// functions

function UserString() {
    var data = JSON.parse(localStorage.getItem('Object'));
    var userSession = data.Session;
    var username = userSession.username;
    return username;
}

function TokenString() {
    var data = JSON.parse(localStorage.getItem('Object'));
    var userSession = data.Session;
    var token = userSession.token;
    return token;
}