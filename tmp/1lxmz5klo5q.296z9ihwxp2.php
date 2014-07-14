<?php if (isset($response_message)): ?><?php echo $response_message; ?><?php endif; ?>
<?php if (isset($logged_in) && $logged_in): ?><h2>Welcome <?php echo $username; ?>!</h2><?php endif; ?>
<div id="request_change">
<h1>Urgent Requirements for blood</h1>
<ul class="quotes">
	<?php foreach (($urgent?:array()) as $request): ?>
        <div class="post">
            <div class="post-container">
                <li class="message">
					<div id="message-body"><?php echo $request->message; ?></div>
                    <ul class="message_section">
                        <li class="author">help for <?php echo $request->name; ?>, <?php echo $request->bloodgroup; ?></li>
                        <li class="author">asked by <?php echo $request->username; ?> from <?php echo $request->city; ?>, <?php echo $request->state; ?> (<?php echo $request->mobile; ?>)</li>
                    </ul>
                </li>
            </div>
        </div>
	<?php endforeach; ?>
</ul>
<?php if (isset($prev_requests)): ?><button id="prev_requests" style="margin: 0px; padding:0px; background-color: transparent; border: none; " onclick="loadRequests(<?php echo $prev_requests; ?>);"><img src="public/images/back.png" alt="<?php echo $prev_requests; ?>" height="50%" width="50%"/></button><?php endif; ?>
<?php if (isset($next_requests)): ?><button id="next_requests" style="margin: 0px; padding:0px; background-color: transparent; border: none;" onclick="loadRequests(<?php echo $next_requests; ?>);"><img src="public/images/next.png" alt="<?php echo $next_requests; ?>" height="50%" width="50%"/></button><?php endif; ?>
</div>