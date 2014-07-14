<div id="content">
    <div id="content-main">
        <div id="loginSection">
            <div class="container">
                <div id="fillformtitle">
                    Login Form
                </div>
                <form action="" method="post" class="group">
					<p id="error"><?php if (isset($error)): ?><?php echo $error; ?><?php endif; ?></p>
                    <label for="username">User name:</label>
                    <input type="text" name="username" value="" id="username">
                    <label for="password">Password:</label>
                    <input type="password" name="password" value="" id="password">
                    <input type="submit" value="Login">
                </form>
                <div id="registerSection">
                    <div id="or">New User???</div>
                    <a href="register">
                        <img src="public/images/register.png" id="registerpage" >
                    </a>
					<div id="status"></div>
					<img src="public/images/login_facebook.png" id="fb" alt="Fb Connect" title="Login with facebook" onclick="FBLogin(); return false;"/>
                </div>
            </div>
        </div>
    </div>
</div>