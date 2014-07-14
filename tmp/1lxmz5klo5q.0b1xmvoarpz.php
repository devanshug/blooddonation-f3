<img src="public/images/blooddonation_logo_banner.png"/>
<ul id="topnavul" class="topnav">
    <?php if ($logged_in): ?>
		
            <li><a href="wall">Home</a></li>
		
		<?php else: ?>
            <li><a href=".">Home</a></li>
		
	<?php endif; ?>
    <li>
        <a href="javascript:void(0)">Know More</a>
        <ul class="subnav">
            <li><a href="facts">Facts</a></li>
            <li><a href="qualification">Qualification</a></li>
            <li><a href="famousquotes">Famous Quote</a></li>
            <li><a href="calculate">Calculate</a></li>
            <li><a href="bloodgroupsystem">Blood Group Systems</a></li>
        </ul>
    </li>
	<?php if ($logged_in): ?>
		
			<li><a href="urgent">Urgent Requirement</a></li>
			<li><a href="logout" <?php if (isset($type) && $type=='facebook'): ?>onclick="FBLogout();"<?php endif; ?> >Logout</a></li>
		
		<?php else: ?>
			<li><a href="login">Login</a></li>
			<li><a href="register">Register</a></li>
		
	<?php endif; ?>
</ul>