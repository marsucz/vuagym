<?php
/**
 * Compare share template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Compare
 * @version 1.0.0
 */
?>

<div id="yith-woocompare-share">
	<h3 class="yith-woocompare-share-title"><?php echo $share_title ?></h3>
		<ul>
			<?php foreach( $socials as $social ) : ?>
				<li>
				<?php
					switch( $social ) {

						case 'facebook':
							if( $facebook_appid ) {
								echo '<a target="_blank" class="facebook" href="https://www.facebook.com/dialog/feed?app_id='. $facebook_appid .'&display=popup&name=' . $share_link_title . '&description=' . $share_summary . '&picture='.$facebook_image.'&link=' . $share_link_url . '&redirect_uri='.home_url().'" title="' . __( 'Facebook', 'yith-woocommerce-compare' ) . '">facebook</a>';
							}
							break;
						case 'twitter':
							echo '<a target="_blank" class="twitter" href="https://twitter.com/share?url='. $share_link_url .'&amp;text='. $share_summary .'" title="'. __( 'Twitter', 'yith-woocommerce-compare' ) .'">twitter</a>';
							break;
						case 'pinterest':
							echo '<a target="_blank" class="pinterest" href="http://pinterest.com/pin/create/button/?url='. $share_link_url .'&amp;description='. $share_summary .'" title="'. __( 'Pinterest', 'yith-woocommerce-compare' ) .'" onclick="window.open(this.href); return false;">pinterest</a>';
							break;
						case 'google-plus':
							echo '<a target="_blank" class="googleplus" href="https://plus.google.com/share?url='. $share_link_url .'&amp;title='. $share_link_title .'" title="'. __( 'Google+', 'yith-woocommerce-compare' ) .'" onclick="javascript:window.open(this.href, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");return false;">google</a>';
							break;
						case 'mail':
							echo '<a class="email" href="mailto:?subject=I wanted you to see this site&amp;body='. $share_link_url .'&amp;title='. $share_link_title .'" title="'. __( 'Email', 'yith-woocommerce-compare' ) .'">email</a>';
							break;
					}
				?>
				</li>
			<?php endforeach; ?>
		</ul>
</div>