<div id="content-main">
    <div class="container">
        <div id="fillformtitle">
            Urgent Blood Registration
        </div>
        <form method="post" action="" id="fillform" class="group">
            <p id="error"><?php echo $error; ?></p>
            <div class="inputelement">
                <div class="inputtext"><label for="bloodgroup">Blood Group : </label></div>
                <div class="inputcomponent">
                    <select name="bloodgroup" id="bloodgroup" class="comboboxcomponent">
                        <?php foreach (($groups?:array()) as $group): ?>
                                <option value="<?php echo $group->bloodgroup; ?>" <?php if ($bloodgroup==$group->bloodgroup): ?>selected<?php endif; ?>><?php echo $group->bloodgroup; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="inputelement">
                <div class="inputtext"><label for="name">Recipient Name : </label></div>
                <div class="inputcomponent"><input type="text" name="name" id="name" class="<?php echo $textcomponent; ?>" value="<?php echo $name; ?>" required/></div>
            </div>
            <div class="inputelement">
                <div class="inputtext"><label for="mobile">Mobile Number : </label></div>
                <div class="inputcomponent"><input type="text" name="mobile" id="mobile" class="<?php echo $textcomponent; ?>" value="<?php echo $mobile; ?>" required/></div>
            </div>
            <div class="inputelement">
                <div class="inputtext"><label for="location">Location : </label></div>
                <div class="inputcomponent"><input type="text" name="location" id="location" class="<?php echo $textcomponent; ?>" value="<?php echo $location; ?>" required/></div>
            </div>
            <div class="inputelement">
                <div class="inputtext"><label for="state">State : </label></div>
                <div class="inputcomponent">
                    <select name="state" id="state" class="comboboxcomponent">
                        <option value="">---Select---</option>
                        <?php foreach (($states?:array()) as $state_val): ?>
                                <option value="<?php echo $state_val->state_name; ?>" <?php if ($state_val->state_name == $state): ?>selected<?php endif; ?>><?php echo $state_val->state_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="inputelement">
                <div class="inputtext"><label for="city">City : </label></div>
                <div class="inputcomponent">
                    <select name="city" id="city" class="comboboxcomponent">
                        <!-- City -->
						<?php foreach (($cities?:array()) as $city_val): ?>
							<option value="<?php echo $city_val->city_name; ?>" <?php if ($city_val->city_name == $city): ?>selected<?php endif; ?> ><?php echo $city_val->city_name; ?></option>
						<?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="inputelement">
                <div class="inputtext"><label for="message">Enter your message : </label></div>
                <div class="inputcomponent"><textarea name="message" id="message" class="<?php echo $textcomponent; ?> textareacomponent" value="<?php echo $message; ?>" required><?php echo $message; ?></textarea></div>
            </div>
            <div id="captcha_input">
                    <input type="hidden" name="random" id="random" value="<?php echo $random; ?>" />
                    <div class="inputtext"><label for="captcha.net">Enter the captcha : </label></div>
                    <div id="captcha_image"><img src="<?php echo $image_url; ?>" name="captcha.net" id="captcha.net"/></div>				 <!-- Captcha Image -->
                    <div class="inputcomponent"><input type="text" name="captchacode"  id="captchacode" class="textcomponent-active" value="Enter the captcha code"/></div>
                    <input type="button" name="captchaverify" id="captchaverify" class="buttoncomponent" value="Verify My Captcha Code"/>
                    <p id="responsestring"></p>
            </div>
            <div id="submitbutton">
                <input type="submit" name="submit" id="submit" class="buttoncomponent" value="Ask for help"/>
            </div>
            <script type="text/javascript">
                var cap_id = 'captcha.net';
            </script>
            <script type="text/javascript" src="public/js/captchaapi.js"></script>
            <script type="text/javascript" src="public/js/appjs.js"></script>
            <script type="text/javascript" src="public/js/validation.js"></script>
        </form>
    </div>
</div>