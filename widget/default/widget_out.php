<?php 
/*
 * This is the page users will see logged out. 
*/
?>
	<div class="sl sl-default">
        <form class="sl-form" action="<?php echo esc_attr(SmoothLogin::$url_login); ?>" method="post">
        	<div>
            <table id="lost">
                <tr class="sl-username">
                    <td colspan="2" class="sl-username-input">
                        <input type="text" name="log" placeholder="Username"/>
                    </td>
                </tr>
                <tr class="sl-password">
                    <td colspan="2" class="sl-password-input">
                        <input type="password" name="pwd" placeholder="Password" />
                    </td>
                </tr>
                <tr class="sl-submit">
                    <td class="sl-submit-button">
                        <input type="submit" name="wp-submit" id="sl_wp-submit" value="<?php echo "Log In"; ?>" tabindex="100" />
                        <input type="hidden" name="smooth-login" value="login" />
                    </td>
                    <td class="sl-submit-links">
						<a class="sl-links-remember" href="<?php echo esc_attr(SmoothLogin::$url_remember); ?>"><?php echo "Password"; ?></a><br />
						<a href="<?php echo esc_attr(SmoothLogin::$url_register); ?>" class="sl-links-register sl-links-modal"><?php echo "Register"; ?></a>   
                    </td>
                </tr>
            </table>
			<span class="sl-status">&nbsp;</span>
            </div>
        </form>
        <form class="sl-remember" action="<?php echo esc_attr(SmoothLogin::$url_remember) ?>" method="post" style="display:none;">
        	<div>
            <table id="recovering">
			    <tr class="sl-username">
                    <td colspan="2" class="sl-username-input">
                        <input disabled="disabled" class="nonstructural" type="text" value=""><br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="sl-remember-email">  
                        <input type="text" name="user_login" class="sl-user-remember" value="" placeholder="Enter your e-mail" />
                        <?php do_action('lostpassword_form'); ?>
                    </td>
                </tr>	
                <tr>
                    <td class="sl-remember-buttons">
                        <input type="submit" value="Continue" class="sl-button-remember" id="remember-now" />
                        <input type="hidden" name="smooth-login" value="remember" />
                    </td>
					<td class="sl-submit-links">
						<a href="#" class="sl-links-remember-cancel"><?php echo "Cancel"; ?></a>
                    </td>
                </tr>
            </table>
			<span class="sl-status">&nbsp;</span>
            </div>
        </form>
		<form class="sl-register" action="<?php echo esc_attr(SmoothLogin::$url_register); ?>" method="post" style="display:none;">
        	<div>
            <table id="admitting">
                <tr class="sl-username">
                    <td colspan="2" class="sl-username-input">
						<input type="text" name="user_login" id="user_login" placeholder="Username" />
                    </td>
                </tr>
                <tr class="sl-password">
                    <td colspan="2" class="sl-password-input">
						<input type="text" name="user_email" id="user_email" placeholder="E-mail" />
                    </td>
                </tr>
                <tr><td colspan="2"><?php do_action('register_form'); ?><?php do_action('sl_register_form'); ?></td></tr>
                <tr class="sl-submit">
                    <td class="sl-submit-button">
						<input type="submit" name="wp-submit" id="register-now" value="<?php echo "Register"; ?>" tabindex="100" />
						<input type="hidden" name="smooth-login" value="register" />
                    </td>
                    <td class="sl-submit-links">
						<a href="#" class="sl-links-register-cancel"><?php echo "Cancel"; ?></a>
                    </td>
                </tr>
            </table>
			<span class="sl-status">&nbsp;</span>
            </div>
        </form>
	</div>