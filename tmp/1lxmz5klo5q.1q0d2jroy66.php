<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Blooddonation</title>
        <script type="text/javascript" src="public/js/jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="public/js/fblogin.js"></script>
        <script type="text/javascript" src="public/js/requests.js"></script>
        <link rel="stylesheet" type="text/css" href="public/css/style.css"/>
    </head>
    <body>
        <div id="main-container">
            <nav id="topnav" class="topnav">
                <?php echo $this->render('nav.htm',$this->mime,get_defined_vars()); ?>
            </nav>
            <div class="clear"></div>
				<?php if ($banner): ?>
					
						<div id="banner">
							<?php echo $this->render('banner.htm',$this->mime,get_defined_vars()); ?>
						</div>
					
				<?php endif; ?>
            <div id="content-area">
                <div id="content">
					<?php if (isset($pagename)): ?>  <?php echo $this->render($pagename,$this->mime,get_defined_vars()); ?>  <?php endif; ?>
                </div>
                <div id="rightarea">
                    <?php echo $this->render('donorlist.htm',$this->mime,get_defined_vars()); ?>
                </div>
            </div>
            <div class="clear"></div>
            <div id="footer">
                <?php echo $this->render('footer.htm',$this->mime,get_defined_vars()); ?>
            </div>
        </div>
    </body>
</html>