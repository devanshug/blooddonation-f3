window.fbAsyncInit = function() {
	FB.init({
	appId      : '500221736756389', // replace your app id here
	status     : true,
	cookie     : true,
	xfbml      : true
	});
};

(function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	ref.parentNode.insertBefore(js, ref);
}(document));

function FBLogin(){
	FB.login(function(response){
		console.log(response);
		if(response.authResponse){
			window.location.href = "login/fb?accessToken="+response.authResponse.accessToken+"&signedRequest="+response.authResponse.signedRequest;
		}
	}, {scope: 'email,user_likes'});
}

function FBLogout(){
	FB.logout(function(response) {
		window.location.href = "logout";
	});
}