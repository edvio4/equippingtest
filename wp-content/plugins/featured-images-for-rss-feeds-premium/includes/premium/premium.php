<?php
/**
 * Premium features.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'firss_register_settings', 'firss_register_settings' );
add_action( 'firss_update_default_settings', 'firss_update_settings' );

add_action( 'admin_enqueue_scripts', 'firss_register_admin_scripts', 20 );
add_action( 'admin_enqueue_scripts', 'firss_enqueue_admin_scripts', 21 );

add_action( 'rss2_ns', 'add_media_namespace' );
add_action( 'rss2_item', 'firss_add_image_tag' );
add_action( 'firss_settings_after', 'firss_settings_after' );

add_action( 'init', 'firss_add_rss_image_size' );

add_filter( 'the_excerpt_rss', 'firss_insert_ads', 15 );
add_filter( 'the_content_rss', 'firss_insert_ads', 15 );

add_filter( 'the_excerpt_rss', 'firss_featured_images_in_rss_first_image', 1001, 1 );
add_filter( 'the_content_feed', 'firss_featured_images_in_rss_first_image', 1001, 1 );

add_filter( 'posts_where', 'firss_publish_later_on_feed' );
add_filter( 'firss_image_sizes', 'firss_custom_size' );

add_filter( 'pre_get_posts', 'firss_exclude_cats' );
add_filter( 'wp_get_attachment_image_attributes', 'firss_maybe_disable_responsive_images', 999 );
add_filter( 'wp_calculate_image_srcset', 'firss_maybe_disable_srcset' );


// Freemius

if ( is_admin() ) {
	fifrf_fs()->add_filter( 'support_forum_url', 'firss_support_forum_url', 10, 2 );

	/**
	 * Hook into the Freemius support forum URL to redirect user to premium support forum URL.
	 */
	function firss_support_forum_url( $url ) {
		if ( 'featured-images-for-rss-feeds' !== fifrf_fs()->get_slug() ) {
			return $url;
		}
		return 'http://pluginsupport.helpscoutdocs.com/';
	}

}
//


// __Enqueue scripts/styles

/**
 * Register admin JS scripts and CSS styles.
 *
 * @since 1.4
 */
function firss_register_admin_scripts( $hook ) {

	// Selective load.
	if ( 'toplevel_page_featured-images-for-rss-feeds' !== $hook ) {
		return;
	}

	wp_register_script(
		'firss',
		FIRSS_PLUGIN_URL . '/includes/premium/js/scripts.js',
		array( 'jquery' ),
		FIRSS_VERSION,
		true
	);

	wp_register_script(
		'select2',
		FIRSS_PLUGIN_URL . '/includes/premium/js/select2/4.0.2/js/select2.min.js',
		array( 'jquery' ),
		FIRSS_VERSION,
		true
	);

	wp_register_style(
		'firss',
		FIRSS_PLUGIN_URL . '/includes/premium/css/styles.css'
	);

	wp_register_style(
		'select2',
		FIRSS_PLUGIN_URL . '/includes/premium/js/select2/4.0.2/css/select2.min.css'
	);

}

/**
 * Enqueue registered admin JS scripts and CSS styles.
 *
 * @since 1.4
 */
function firss_enqueue_admin_scripts( $hook ) {

	// Selective load.
	if ( 'toplevel_page_featured-images-for-rss-feeds' !== $hook ) {
		return;
	}

	wp_enqueue_script( 'firss' );
	wp_enqueue_script( 'select2' );
	wp_enqueue_style( 'firss' );
	wp_enqueue_style( 'select2' );

	$excluded_cats = get_option( 'featured_images_in_rss_cat_exclude' );

	wp_localize_script( 'firss', 'firss_l18n', array(
		'categories_placheolder' => __( 'Click to select categories to exclude', 'featured_images_in_rss'  ),
		'excluded_cats'          => $excluded_cats
	) );
}


// __Premium Settings Display/Handle

/**
 * Register premium settings.
 *
 * @since 1.4
 */
function firss_register_settings( $group ) {
	register_setting( $group, 'featured_images_in_rss_media_tag' );
	register_setting( $group, 'featured_images_in_rss_enclosure_tag' );
	register_setting( $group, 'featured_images_in_rss_pre_feed' );
	register_setting( $group, 'featured_images_in_rss_post_feed' );
	register_setting( $group, 'featured_images_in_rss_delay' );
	register_setting( $group, 'featured_images_in_rss_thumb_size_w' );
	register_setting( $group, 'featured_images_in_rss_thumb_size_h' );
	register_setting( $group, 'featured_images_in_rss_first_image' );
	register_setting( $group, 'featured_images_in_rss_cat_exclude' );
	register_setting( $group, 'featured_images_in_rss_disable_responsive_images' );
}

/**
 * Default values for premium settings.
 *
 * @since 1.4
 */
function firss_update_settings() {

	$featured_images_in_rss_thumb_size_w = get_option( 'featured_images_in_rss_thumb_size_w' );
	if ( empty( $featured_images_in_rss_thumb_size_w ) ) {
		update_option( 'featured_images_in_rss_thumb_size_w', get_option('thumbnail_size_w') );
	}

	$featured_images_in_rss_thumb_size_h = get_option( 'featured_images_in_rss_thumb_size_h' );
	if ( empty( $featured_images_in_rss_thumb_size_h ) ) {
		update_option( 'featured_images_in_rss_thumb_size_h', get_option('thumbnail_size_h') );
	}

}


// __Features

/**
 * Pre-Feed and Post-Feed settings.
 *
 * @since 1.4
 */
function firss_settings_after() {
	$media_tag           = (bool) get_option( 'featured_images_in_rss_media_tag' );
	$enclosure_tag       = (bool) get_option( 'featured_images_in_rss_enclosure_tag' );
	$firss_pre_feed      = get_option( 'featured_images_in_rss_pre_feed' );
	$firss_post_feed     = get_option( 'featured_images_in_rss_post_feed' );
	$firss_delay         = get_option( 'featured_images_in_rss_delay' );
	$firss_cat_exclude   = get_option( 'featured_images_in_rss_cat_exclude' );
	$firss_first_image   = get_option( 'featured_images_in_rss_first_image' );
	$firss_no_responsive = (bool) get_option( 'featured_images_in_rss_disable_responsive_images' );
?>
	<tr>
		<th scope="column" colspan=2><h1><?php echo sprintf( __( 'Premium Features%s', 'featured-images-for-rss-feeds' ), fifrf_fs()->is_trial() ? ' ' .  __( '(Free Trial)', 'featured-images-for-rss-feeds' ) : '' ); ?></h1></th>
	</tr>
	<tr>
		<th scope="column"><?php echo __( 'Use first image in body in RSS feed', 'featured-images-for-rss-feeds' ); ?></th>
		<td>
			<input type="checkbox" value="yes" name="featured_images_in_rss_first_image" <?php checked( 'yes' === $firss_first_image ) ?> >
			<?php echo __( 'Enable first image', 'featured-images-for-rss-feeds' ) ?>
			<p><small><?php echo __( 'Use this option for themes that do not support Featured Images.', 'featured-images-for-rss-feeds' ) ?></small></p>

		</td>
	</tr>
	<tr>
		<th scope="column"><?php echo __( 'Media Tag', 'featured-images-for-rss-feeds' ); ?></th>
		<td>
			<input type="checkbox" name="featured_images_in_rss_media_tag" <?php checked( $media_tag ) ?> >
			<?php echo __( 'Enable media tag', 'featured-images-for-rss-feeds' ) ?>
			<p><small><?php echo __( 'Outputs post thumbnails inside &lt;media&gt; and &lt;image&gt; tags.', 'featured-images-for-rss-feeds' ) ?></small></p>
		</td>
	</tr>
	<tr>
		<th scope="column"><?php echo __( 'Enclosure Tag', 'featured-images-for-rss-feeds' ); ?></th>
		<td>
			<input type="checkbox" name="featured_images_in_rss_enclosure_tag" <?php checked( $enclosure_tag ) ?> >
			<?php echo __( 'Enable enclosure tag', 'featured-images-for-rss-feeds' ) ?>
			<p><small><?php echo __( 'Outputs post thumbnails inside &lt;enclosure&gt; tags.', 'featured-images-for-rss-feeds' ) ?></small></p>
		</td>
	</tr>
	<tr>
		<th scope="column"><?php echo __( 'Pre-Feed Content', 'featured-images-for-rss-feeds' ); ?></th>
		<td>
			<textarea name="featured_images_in_rss_pre_feed" rows="5" class="large-text"><?php echo esc_textarea( $firss_pre_feed ); ?></textarea>
			<small><?php echo __( 'HTML is allowed. This will display on every post.', 'featured-images-for-rss-feeds' ) ?></small>
		</td>
	</tr>
	<tr>
		<th scope="column"><?php echo __( 'Post-Feed Content', 'featured-images-for-rss-feeds' ); ?></th>
		<td>
			<textarea name="featured_images_in_rss_post_feed" rows="5" class="large-text"><?php echo esc_textarea( $firss_post_feed ); ?></textarea>
			<small><?php echo __( 'HTML is allowed. This will display on every post.', 'featured-images-for-rss-feeds' ) ?></small>
		</td>
	</tr>
	<tr>
		<th scope="column"><?php echo __( 'Publish Delay', 'featured-images-for-rss-feeds' ); ?></th>
		<td>
			<input name="featured_images_in_rss_delay" value="<?php echo esc_attr( $firss_delay ); ?>" class="small-text"> <?php echo __( 'minute(s)', 'featured-images-for-rss-feeds' ); ?>
			<p><small><?php echo __( 'Delays the publishing of the feed after post is published. Leave empty to publish immediately.', 'featured-images-for-rss-feeds' ) ?></small></p>
		</td>
	</tr>
	<tr>
		<th scope="column"><?php echo __( 'Exclude Categories', 'featured-images-for-rss-feeds' ); ?></th>
		<td>
			<?php wp_dropdown_categories( array( 'name' => 'featured_images_in_rss_cat_exclude[]', 'id' => 'featured_images_in_rss_cat_exclude', 'class' => 'firss-dropdown', 'hide_empty' => false ) ); ?>
			<p><small><?php echo __( 'Leave empty to display featured images in posts from all categories.', 'featured-images-for-rss-feeds' ) ?></small></p>
		</td>
	</tr>
	<tr>
		<th scope="column"><?php echo __( 'Disable Responsive Images', 'featured-images-for-rss-feeds' ); ?></th>
		<td>
			<input type="checkbox" name="featured_images_in_rss_disable_responsive_images" <?php checked( $firss_no_responsive ) ?> >
			<?php echo __( 'Disable responsive images under RSS feeds.', 'featured-images-for-rss-feeds' ) ?>
		</td>
	</tr>
<?php
}

/**
 * Add < media > tag to RSS feed.
 *
 * @since 1.4
 */
function firss_add_image_tag() {
	global $post;

	// Apply the <media> tag to the featured image if exists or apply it to first embedded image.
	if ( ( get_option('featured_images_in_rss_media_tag') || get_option('featured_images_in_rss_enclosure_tag') ) && ( has_post_thumbnail( $post->ID ) || $embed_thumb_id = firss_get_featured_images_in_rss_first_image() ) ) {

		// If there's no featured image apply the <media> tag to the first embeded image (if option enabled).
		if ( ! empty( $embed_thumb_id ) ) {
			$thumb_id = $embed_thumb_id;
			$image    = wp_get_attachment_image_src( $embed_thumb_id );
		} else {
			$thumb_id = get_post_thumbnail_id( $post->ID );
			$image    = wp_get_attachment_image_src( $thumb_id );
		}

		list( $src, $width, $height, $original ) = $image;

		if ( $src ) {
			extract( wp_check_filetype_and_ext( $src, $src ) );
			
			if ( get_option('featured_images_in_rss_media_tag') ) {
				echo sprintf( '<media:content url="%1$s" width="%2$d" height="%3$d" medium="image" type="%4$s" />', esc_url( $src ), esc_attr( $width ), esc_attr( $height ), esc_attr( $type ) );
			}

			if ( get_option('featured_images_in_rss_enclosure_tag') ) {
				echo sprintf( '<enclosure url="%1$s" length="%2$s" type="%3$s" />', $src, filesize( get_attached_file( $thumb_id ) ), $type );
			}

			echo '<image>' . esc_url( $src ) . '</image>';

			// Add support for Feedly's webfeeds:cover metadata
			echo '<webfeeds:cover image="' . esc_url( $src ) . '" />';
		}
	}

}

/**
 * Insert pre and/or post content into the feed.
 *
 * @since 1.4
 */
function firss_insert_ads( $content ) {

	if ( $firss_pre_feed = get_option( 'featured_images_in_rss_pre_feed' ) ) {
		$content = wpautop( $firss_pre_feed ) . $content;
	}

	if ( $firss_post_feed = get_option( 'featured_images_in_rss_post_feed' ) ) {
		$content .= wpautop( $firss_post_feed );
	}

    return $content;
}


/**
 * Delays the publishing of the feed.
 *
 * @since 1.4
 */
function firss_publish_later_on_feed( $where ) {
	global $wpdb;

	if ( is_feed() && ( $wait = get_option( 'featured_images_in_rss_delay' ) ) ) {
		$now = gmdate('Y-m-d H:i:s');

		// Waiting time.
		$wait = (int) $wait;

		$device = 'MINUTE';

		// Override the query.
		$where .= " AND TIMESTAMPDIFF( $device, $wpdb->posts.post_date_gmt, '$now' ) > $wait ";
	}
	return $where;
}

/**
 * Provides an additional 'custom' size option for the sizes dropdown.
 *
 * @since 1.4
 */
function firss_custom_size( $sizes ) {

	$sizes[] = 'custom';

	return $sizes;
}

/**
 * Set a custom 'rss-thumb' size for RSS featured images.
 *
 * Also used as helper to retrieve the selected featured image size.
 *
 * @since 1.4
 */
function firss_add_rss_image_size() {

	if ( 'custom' !== get_option( 'featured_images_in_rss_size' ) ) {
		return get_option('featured_images_in_rss_size');
	}

	$custom_size = 'rss-thumb';

	$firss_thumb_size_w = get_option( 'featured_images_in_rss_thumb_size_w' );
	$firss_thumb_size_h = get_option( 'featured_images_in_rss_thumb_size_h' );

	add_image_size ( $custom_size, $firss_thumb_size_w, $firss_thumb_size_h, true );

	/**
	 *  Hook into the thumbnail sizes to override the size.
	 */
	add_filter( 'post_thumbnail_size', 'firss_custom_thumb_size', 20 );

	return $custom_size;
}

/**
 * Apply the custom thumb size to the RSS featured image.
 *
 * @since 1.4
 */
function firss_custom_thumb_size( $size ) {
	global $wp_query;

	if ( ! is_feed() ) {
		return $size;
	}

	$size = 'rss-thumb';

	return $size;
}

/**
 * Get the first embedded image if related option is enabled, in RSS feeds.
 *
 * @since 1.4
 */
function firss_get_featured_images_in_rss_first_image() {
	global $post;

	if ( 'yes' !== get_option( 'featured_images_in_rss_first_image' ) ) {
		return false;
	}

	$attachment_id = false;

	// If there's not a featured image get the first image from the post content using 'loadHTML' since using 'get_children()' through the 'post_parent'
	// can be unreliable when the same image is shared across multiple posts (e.g: an image could be displayed on the feed even without being embedded on the content).
	if ( ! has_post_thumbnail( $post->ID ) ) {

		$doc = new DOMDocument();
		$doc->loadHTML( $post->post_content );

		$images = $doc->getElementsByTagName('img');

		// Get the first item only.
		foreach( $images as $image ) {
			// Get the first embeded image.
			$orig_url = $image->getAttribute('src');
			break;
		}

		if ( $orig_url ) {

			firss_settings_init();

			// Remove the size part of the image by spliting the URL and looking for something like '-WxH' (e.g: -300x195).
			$parts = explode( '-', $orig_url );
			$total = count( $parts );

			$size = $parts[ $total-1 ];
			$path_parts = pathinfo( $size );

			// If the last part of the URL is in fact the image size (e.g: -300x195.jpg), remove it.
			if ( false !== strpos( $size, 'x' ) ) {
				$url = str_replace( "-{$size}", '', $orig_url ) . '.' . $path_parts['extension'];
			} else {
				$url = $orig_url;
			}

			$attachment_id = firss_get_image_id( $url );

			// Image does not have an attachment ID. Return the original URL.
			if ( ! $attachment_id ) {
				return $orig_url;
			}

		}
	}
	return $attachment_id;
}

/**
 * Feature the the first embedded image in RSS feeds, if enabled.
 *
 * @since 1.4
 */
function firss_featured_images_in_rss_first_image( $content ) {

	if ( $attachment = firss_get_featured_images_in_rss_first_image() ) {

		$featured_images_in_rss_size     = firss_add_rss_image_size();
		$featured_images_in_rss_css_code = firss_eval_css( get_option( 'featured_images_in_rss_css' ) );

		// Get WP native responsive image.
		if ( is_numeric( $attachment ) ) {
			$image = wp_get_attachment_image( $attachment, $featured_images_in_rss_size, false, array( 'style' => $featured_images_in_rss_css_code ) );

		// If we only have an image URL, set the sizes manually.
		} else {
			$size  = firss_get_image_size( $featured_images_in_rss_size );
			$image = '<img src="' . esc_url( $attachment ) . '" width="' . esc_attr( $size['width'] ) . '" height="' . esc_attr( $size['height'] )  . '" style="' . esc_attr( $featured_images_in_rss_css_code ) . '">';
		}

		$content = $image . $content;
	}
	return $content;
}

/**
 * Exclude categories from feed.
 *
 * @since 1.4
 */
function firss_exclude_cats( $query ) {

	if ( ! $query->is_feed ) {
		return $query;
	}

	$excluded_cats = get_option( 'featured_images_in_rss_cat_exclude' );

	$query->set( 'category__not_in', $excluded_cats );

	return $query;
}

/**
 * Disable the 'srcset' used for responsive images if requested by the user.
 *
 * @since 1.4
 */
function firss_maybe_disable_srcset( $sources ) {

	if ( ! is_feed() || ! get_option( 'featured_images_in_rss_disable_responsive_images' ) ) {
		return $sources;
	}
	return array();
}


/**
 * Disables responsive images in RSS feeds.
 *
 * @since 1.4
 */
function firss_maybe_disable_responsive_images( $attr ) {

	if ( is_feed() ) {

		if ( ! get_option( 'featured_images_in_rss_disable_responsive_images' ) ) {
			return $attr;
		}

		if ( isset( $attr['sizes'] ) ) {
			unset( $attr['sizes'] );
		}

		if ( isset( $attr['srcset'] ) ) {
			unset( $attr['srcset'] );
		}

	}
	return $attr;
}


// Helpers.

/**
 * Retrieves the attachment ID from the file URL.
 */
function firss_get_image_id( $image_url ) {
	global $wpdb;
	$attachment = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid = '%s';", $image_url ) );

	if ( ! empty( $attachment[0] ) ) {
		return $attachment[0];
	}
	return false;
}

/**
 * Get size information for all currently-registered image sizes.
 *
 * @global $_wp_additional_image_sizes
 * @uses   get_intermediate_image_sizes()
 * @return array $sizes Data for all currently-registered image sizes.
 */
function firss_get_image_sizes() {
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
			$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
			$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}

	return $sizes;
}

/**
 * Get size information for a specific image size.
 *
 * @uses   get_image_sizes()
 * @param  string $size The image size for which to retrieve data.
 * @return bool|array $size Size data about an image size or false if the size doesn't exist.
 */
function firss_get_image_size( $size ) {
	$sizes = firss_get_image_sizes();

	if ( isset( $sizes[ $size ] ) ) {
		return $sizes[ $size ];
	}

	return false;
}

/**
 * Add media namespace to RSS feed namespace action
 */
function add_media_namespace() {
	echo 'xmlns:media="http://search.yahoo.com/mrss/"';
}
