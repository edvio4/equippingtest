<?php
$focus_area_class   = $current_attrs['_thrive_meta_focus_color'][0];
$section_position   = ( $position == "bottom" ) ? "fab" : "fat";
$action_link_target = ( isset( $current_attrs['_thrive_meta_focus_new_tab'] ) && $current_attrs['_thrive_meta_focus_new_tab'][0] == 1 ) ? "_blank" : "_self";
?>

<div class="far fat f1 <?php echo $current_attrs['_thrive_meta_focus_color'][0]; ?>"
     <?php if ( ! empty( $current_attrs['_thrive_meta_focus_image2'][0] ) ): ?>style="background-image: url('<?php echo $current_attrs['_thrive_meta_focus_image2'][0]; ?>')"<?php endif; ?>>
	<div class="wrp">
		<div class="fab-i">
			<div class="fl">
				<?php if ( ! empty( $current_attrs['_thrive_meta_focus_image'][0] ) ): ?>
					<img src="<?php echo $current_attrs['_thrive_meta_focus_image'][0]; ?>" alt=""/>
				<?php endif; ?>
			</div>
			<div class="fm">
				<h3><?php echo $current_attrs['_thrive_meta_focus_heading_text'][0]; ?></h3>

				<p><?php echo nl2br( do_shortcode( $current_attrs['_thrive_meta_focus_subheading_text'][0] ) ); ?></p>
			</div>
			<div class="fr">
				<a href="<?php echo $current_attrs['_thrive_meta_focus_button_link'][0]; ?>"
				   target="<?php echo $action_link_target; ?>"
				   class="btn small <?php echo $current_attrs['_thrive_meta_focus_button_color'][0]; ?>">
					<span><?php echo $current_attrs['_thrive_meta_focus_button_text'][0]; ?></span>
				</a>
			</div>
		</div>
	</div>
</div>