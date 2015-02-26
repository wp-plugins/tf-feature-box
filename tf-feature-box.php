<?php
/*
Plugin Name: TF Feature Box
Plugin URI: http://www.timfitt.com/work/wordpress/plugins/tf-feature-box
Description: This plugin adds a feature box with a title, description, coloured background, icon and main image.
Version: 1.0.0
Author: Tim Fitt
Author URI: http://www.timfitt.com
License: GPL2
*/

/*  Copyright 2013  Tim Fitt  (email : developer@timfitt.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Action to register the widget
 */
add_action('widgets_init', 'tf_feature_box_widget');

/**
 * Register the widget
 */
function tf_feature_box_widget() {
	register_widget( 'TF_Feature_Box' );
} // END function tf_feature_box_widget()

function tf_feature_box_load_scripts($hook) {
	if('widgets.php' != $hook)
		return;
	wp_enqueue_media();
	
	wp_enqueue_style('tf-feature-box-cpicker-styles', plugins_url('css/colorpicker.css', __FILE__));
	wp_enqueue_script( 'tf-feature-box-cpicker-scripts', plugins_url( 'js/colorpicker.js' , __FILE__ ), array('jquery') );
	wp_enqueue_script( 'tf-feature-box-eye-scripts', plugins_url( 'js/eye.js' , __FILE__ ), array('jquery') );
	wp_enqueue_script( 'tf-feature-box-utils-scripts', plugins_url( 'js/utils.js' , __FILE__ ), array('jquery') );
	wp_enqueue_script( 'tf-feature-box-scripts', plugins_url( 'js/scripts.js' , __FILE__ ), array('jquery') );
}
add_action('admin_enqueue_scripts', 'tf_feature_box_load_scripts');

class TF_Feature_Box extends WP_Widget {

		/**
		 *
		 * @var string
		 */
		public $version = '1.0.0';

	public function __construct() {
		parent::__construct(
			'tf_feature_box', // Base ID
			'TF Feature Box', // Name
			array( 'description' => __( 'Display a feature box.', 'text_domain' ), ) // Args
		);
		
		// Define version constant
		define ( 'TF_FEATURE_BOX_VERSION', $this->version );
		wp_enqueue_style('tf-feature-box-styles', plugins_url('css/styles.css', __FILE__));
	} // END public function __construct()

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $wpdb, $wp;

		$title = $instance['title'];
		$text = $instance['text'];
		$rgb_colour = $instance['rgb_colour'];
		$image_uri = $instance['image_uri'];
		$icon_uri = $instance['icon_uri'];
		$url = $instance['url'];		
		?>
		<style scoped>
			#<?php echo $args['widget_id'];?> .tf-feature-box-outer {
				background-color: <?php echo $rgb_colour; ?>;
			}
		</style>
		<?php
		echo $args['before_widget'];
		?>
		<div class="tf-feature-box-outer">
			
			<?php
			if($icon_uri != "") {
				?>
				<div class="tf-feature-box-icon">
				<?php
				if($url != "") {
					?>
					<a href="<?php echo $url;?>"><img src="<?php echo $icon_uri;?>" alt="icon_<?php echo $args['widget_id'];?>"></a>
					<?php 
				} else {
					?>
					<img src="<?php echo $icon_uri;?>" alt="icon_<?php echo $args['widget_id'];?>">
					<?php 
				}
				?>
				</div>
				<?php
			}
			
			if($title != "") {
				?>
				<div class="tf-feature-box-title"><h2>
				<?php
				if($url != "") {
					?>
					<a href="<?php echo $url;?>"><?php echo $title;?></a>
					<?php 
				} else {
					echo $title;
				}
				echo '</h2></div>';
			}
			
			if($image_uri != "") {
				if($url != "") {
					?>
					<a href="<?php echo $url;?>"><img src="<?php echo $image_uri;?>" alt="image_<?php echo $args['widget_id'];?>"></a>
					<?php 
				} else {
					?>
					<img src="<?php echo $image_uri;?>" alt="image_<?php echo $args['widget_id'];?>">
					<?php
				}
			}
			
			if($text != "") {
				?>
				<div class="tf-feature-text-box"><p><?php echo $text;?></p></div>
				<?php
			}
			?>
			
		</div>
		
		<?php
		echo $args['after_widget'];
	} // END public function widget($args, $instance)
	

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( '', 'text_domain' );
		}
		
		if ( isset( $instance[ 'url' ] ) ) {
			$url = $instance[ 'url' ];
		}
		else {
			$url = __( '', 'text_domain' );
		}
		
		if ( isset( $instance[ 'text' ] ) ) {
			$text = $instance[ 'text' ];
		}
		else {
			$text = __( '', 'text_domain' );
		}
		
		if ( isset( $instance[ 'rgb_colour' ] ) ) {
			$rgb_colour = "#".str_replace("#", "", $instance[ 'rgb_colour' ]);
		}
		else {
			$rgb_colour = __( '#FFFFFF', 'text_domain' );
		}
		
		if ( isset( $instance[ 'image_uri' ] ) ) {
			$image_uri = $instance[ 'image_uri' ];
		}
		else {
			$image_uri = __( '', 'text_domain' );
		}
		
		if ( isset( $instance[ 'icon_uri' ] ) ) {
			$icon_uri = $instance[ 'icon_uri' ];
		}
		else {
			$icon_uri = __( '', 'text_domain' );
		}
		
		echo $args['widget_id'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'URL:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Text:' ); ?></label> 
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo esc_attr( $text ); ?></textarea>
		</p>
		<p class="cpicker">
			<label for="<?php echo $this->get_field_id( 'rgb_colour' ); ?>"><?php _e( 'RGB Colour:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'rgb_colour' ); ?>" name="<?php echo $this->get_field_name( 'rgb_colour' ); ?>" type="text" value="<?php echo esc_attr( $rgb_colour ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('image_uri'); ?>">Image</label><br />
        	<?php if(!empty($instance['icon_uri'])){ ?><img class="<?php echo $this->get_field_id('image_uri'); ?>_image" src="<?php if(!empty($instance['image_uri'])){echo $instance['image_uri'];} ?>" style="margin:0;padding:0;max-width:100px;float:left;display:inline-block" /><?php } ?>
        	<input type="text" class="widefat <?php echo $this->get_field_id('image_uri'); ?>_image_url" name="<?php echo $this->get_field_name('image_uri'); ?>" id="<?php echo $this->get_field_id('image_uri'); ?>" value="<?php echo $instance['image_uri']; ?>">
        	<a href="#" class="button custom_media_upload" id="<?php echo $this->get_field_id('image_uri'); ?>_image"><?php _e('Upload', THEMENAME); ?></a>
    	</p>
    	<p>
			<label for="<?php echo $this->get_field_id('icon_uri'); ?>">Icon</label><br />
        	<?php if(!empty($instance['icon_uri'])){ ?><img class="<?php echo $this->get_field_id('image_uri'); ?>_icon" src="<?php if(!empty($instance['icon_uri'])){echo $instance['icon_uri'];} ?>" style="margin:0;padding:0;max-width:100px;float:left;display:inline-block" /><?php } ?>
        	<input type="text" class="widefat <?php echo $this->get_field_id('image_uri'); ?>_icon_url" name="<?php echo $this->get_field_name('icon_uri'); ?>" id="<?php echo $this->get_field_id('icon_uri'); ?>" value="<?php echo $instance['icon_uri']; ?>">
        	<a href="#" class="button custom_media_upload" id="<?php echo $this->get_field_id('image_uri'); ?>_icon"><?php _e('Upload', THEMENAME); ?></a>
    	</p>
		<?php 
	} // END public function form($instance)
	

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';
		$instance['url'] = ( ! empty( $new_instance['url'] ) ) ? strip_tags( $new_instance['url'] ) : '';
		$instance['text'] = ( ! empty( $new_instance['text'] ) ) ? strip_tags( $new_instance['text'] ) : '';
		$instance['rgb_colour'] = ( ! empty( $new_instance['rgb_colour'] ) ) ? strip_tags( $new_instance['rgb_colour'] ) : '';
		$instance['image_uri'] = strip_tags( $new_instance['image_uri'] );
		$instance['icon_uri'] = strip_tags( $new_instance['icon_uri'] );

		return $instance;
	} // END public function update($new_instance, $old_instance)

} // END class TF_Feature_Box