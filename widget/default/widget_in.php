<?php 
/*
 * This is the page users will see logged in.
*/

	$user = wp_get_current_user();

?>
	<div class="sl sl-default">
        <form class="sl-logout" action="<?php echo esc_attr(SmoothLogin::$url_login); ?>" method="post">
        	<div>
			<table id="lost">
			    <tr class="sl-username"> 
				    <td rowspan="2" class="avatar sl-avatar">
                        <?php echo get_avatar( $user->ID, $size = '50' );  ?>
                    </td>
					<td rowspan="2" class="sl-info">
                        <?php echo $user->display_name; ?><br/>
						<a href="<?php echo trailingslashit(get_admin_url()); ?>profile.php"><?php echo "profile"; ?></a><br/>
						<?php  if( current_user_can('list_users') ) {  ?>
						<a href="<?php echo get_admin_url(); ?>"><?php echo "panel"; ?></a>
						<?php  }  ?>
                    </td>
				    <td class="sl-username-input">
                        <input class="structural" type="text" name="slim" />
                    </td>	
                </tr>
				<tr class="sl-password">
                    <td class="sl-password-input">
                        <input class="structural" type="text" name="slim2" />
                    </td>
                </tr>
				<tr><td colspan="2"><?php do_action('login_form'); ?></td></tr>
                <tr class="sl-submit">
                    <td class="sl-submit-button">
                        <input type="submit" name="wp-submit" id="sl_wp-submit" value="<?php echo 'Log Out'; ?>" tabindex="100" />
                        <input type="hidden" name="smooth-login" value="logout" />
                    </td>
                    <td class="sl-submit-links">
                    </td>
                </tr>
            </table>
			<span class="sl-status">&nbsp;</span>
            </div>
        </form>
	</div>