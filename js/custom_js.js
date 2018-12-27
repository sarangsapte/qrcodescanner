var validationApp = angular.module('validationApp', ['ngMessages', 'ngCookies']);

// create angular controller
validationApp.controller('mainController', function($scope, $http, $window, $cookies) {

  $scope.yes_no= 'No';
  $scope.$watch('yes_no', function(yes_no) {
         console.log(yes_no);     

         if(yes_no == 'No'){
            $scope.ok_to_call = '';
            $scope.ok_to_sms = '';
            $scope.ok_to_whatsapp = '';
            $scope.ok_to_email = '';
         }

  });

  /* Error Alert */
  $scope.ErrAlert = function (msg, myYes) {
            
            var confirmBox = $("#ErrmSG");
            confirmBox.find(".message").text(msg);
            confirmBox.find(".yes").unbind().click(function() {
               confirmBox.hide();
               
            });
            confirmBox.find(".yes").click(myYes);
            confirmBox.show();

  }

  /* Success Alert */
  $scope.SuccessAlert = function (msg, myYes, redurl) {
            $scope.isDisabled = true;
            var confirmBox = $("#mSG");
            confirmBox.find(".message").text(msg);
            confirmBox.find(".yes").unbind().click(function() {
               confirmBox.hide();
               $window.location.href = redurl;
               
            });
            confirmBox.find(".yes").click(myYes);
            confirmBox.show();
  }

  /* General Alert */
  $scope.GeneralAlert = function (msg, myYes) {

            var confirmBox = $("#GeneralAlert");
            confirmBox.find(".message").text(msg);
            confirmBox.find(".yes").unbind().click(function() {
               confirmBox.hide();

            });
            confirmBox.find(".yes").click(myYes);
            confirmBox.show();
  }

  $scope.onSubmit = function(){

    //alert($scope.get_notifications_of_events);
         
          // Simple GET request example:
          var data = 'fullname=' + $scope.fullname + '&mobile=' + $scope.mobile + '&email=' + $scope.email+ '&yes_no=' + $scope.yes_no+ '&ok_to_call=' + $scope.ok_to_call+ '&ok_to_sms=' + $scope.ok_to_sms+ '&ok_to_whatsapp=' + $scope.ok_to_whatsapp+ '&ok_to_email=' + $scope.ok_to_email+ '&get_notifications_of_events=' + $scope.get_notifications_of_events+'&action=qrcoderegi';

          $http({
            method: 'post',
            //url: 'http://www.bizinfosystems.in/api/qrcoderegi',
            url: 'webapi.php?action=qrcoderegi',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: data
          }).then(function successCallback(response) {             
              console.log(response.data);
              //alert(response.data);
              if(response.data == 'Already Exist'){  

                $scope.ErrAlert('Mobile number already exist, Please try another number');
              }else{
                var success = 0; 
                $scope.isDisabled = true;
                $scope.SuccessAlert('You are successfully Register with your Mobile number', '', 'index.html');
                //$window.location.href = 'index.html';
              }
            }, function errorCallback(response) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
            });        
            
  }


  $scope.onSubmitLogin = function(){
          //alert("form submitted");
          // Simple GET request example:
          var data = 'mobile=' + $scope.mobile+'&action=qrcoderegiLogin';

          $http({
            method: 'post',
            //url: 'http://www.bizinfosystems.in/api/qrcoderegiLogin',
            url: 'webapi.php?action=qrcoderegiLogin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: data
          }).then(function successCallback(response) {           
              console.log(response.data);
              
              if(response.data == 'Auth'){
                //console.log('Login mobile');

                // Cookies Storage
                $cookies.remove('mobileID');
                $cookies.put('mobileID', $scope.mobile);
                
                // Cookies Storage & Redirect
                $scope.SuccessAlert('You are successfully Login with you Credentials', '', 'qrcodescanner.html');
                //$window.location.href = 'qrcodescanner.html';

              }else{
                $scope.ErrAlert('Sorry, You are not Authorized for Login, Please Register your Mobile no.');
              }
            }, function errorCallback(response) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
            });        
            
  }

  // Send Message
  $scope.onSubmitSendMessage = function(){    

          // Add it to DB
          var data = 'mobile=' + $scope.mobile+'&action=whatsappmessage';

          $http({
            method: 'post',
            //url: 'http://www.bizinfosystems.in/api/qrcoderegiLogin',
            url: 'webapi.php?action=whatsappmessage',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: data
          }).then(function successCallback(response) {           
              console.log(response.data);              
             
            }, function errorCallback(response) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
            });


          var url_string = decodeURIComponent('https://api.whatsapp.com/send?phone=91'+$scope.mobile+'&text='+$scope.message+'');
    console.log( url_string );


    // Save to DB 

                $window.open(url_string, '_blank');

  }

});


validationApp.controller('mainControllerCamera', function($scope, $http, $window, $cookies) {
  console.log($cookies.get('mobileID'));


                // ################################

                      var data = 'mobileid=' + $cookies.get('mobileID')+'&action=showuserdata';
                      $http({
                          method: 'post',
                          url: 'webapi.php?action=showuserdata',
                          headers: {
                              'Content-Type': 'application/x-www-form-urlencoded'
                          },
                          data: data
                      }).then(function successCallback(response) {             
                          //console.log(response.data);
                          $scope.records = response.data;
                          console.log($scope.records);
                      //alert(response.data);
                     
                      }, function errorCallback(response) {
                   
                      });


           // ################################

           $scope.logout = function(){
              //alert(); 
              $cookies.remove('mobileID');
              $window.location.href = 'index.html';          
           }

          var data = 'mobile=' + $cookies.get('mobileID')+'&action=qrcoderegiLogin';

          $http({
            method: 'post',
            //url: 'http://www.bizinfosystems.in/api/qrcoderegiLogin',
            url: 'webapi.php?action=qrcoderegiLogin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: data
          }).then(function successCallback(response) {           
              console.log(response.data);
              
              if(response.data == 'Auth'){
                
                // Cookies Storage & Redirect
                console.log('You are successfully Login with you Credentials');

                $scope.login_details = $cookies.get('mobileID');

              }else{
                $window.location.href = 'index.html';
                console.log('Sorry, You are not Authorized for Login, Please Register your Mobile no.');
              }
            }, function errorCallback(response) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
            });




});