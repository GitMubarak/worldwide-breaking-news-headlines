<?php
/**
* Adds HM Recent Posts widget in Widget area
*/
class HMNFW_Widget extends WP_Widget {
	
	protected $hmnfw_api, $hmnfw_api_data, $hmnfw_api_cached_data;
	/**
	* Register widget with WordPress.
	*/
	function __construct() {
		parent::__construct(
			'hmnewsfeedwidgetactivater',
			esc_html__('Worldwide Breaking News Headlines', HMNFW_TXT_DOMAIN ),
			array( 'description' => esc_html__( 'Worldwide Breaking News Headlines', HMNFW_TXT_DOMAIN ), )
		);

		// This is where we add the style and script
        add_action( 'load-widgets.php', array(&$this, 'hmnfw_color_picker_load') );
	}

	function hmnfw_color_picker_load() {    
		wp_enqueue_style( 'wp-color-picker' );        
		wp_enqueue_script( 'wp-color-picker' );    
	}
	
	/**
	* Front-end display of widget.
	*
	* @see WP_Widget::widget()
	*
	* @param array $args Widget arguments.
	* @param array $instance Saved values from database.
	*/
	function widget( $args, $instance ) {
		echo $args['before_widget'];

		$newsSource 			= (isset($instance['newsSource'])) ? $instance['newsSource'] : 'cnn';
		$apiKey 				= (isset($instance['apiKey'])) ? $instance['apiKey'] : '';
		//print_r($instance);
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		$numberOfNews 			= (isset($instance[ 'numberOfNews' ])) ? $instance[ 'numberOfNews' ] : 5;
		
		$general_border_enable 	= ( isset( $instance['general_border_enable'] ) && 'on' === $instance['general_border_enable'] ) ? '1px solid #DDD' : '';
		//=======================
		//echo $instance['thumbnailBorder'];
		$thumbnail_border 		= ( isset( $instance['thumbnailBorder'] ) && 'on' === $instance['thumbnailBorder'] ) ? 'solid' : '';
		$image_border_width		= filter_var( $instance['image_border_width'], FILTER_SANITIZE_NUMBER_INT ) ? $instance['image_border_width'] : '';
		$image_border_color		= sanitize_text_field( $instance['image_border_color'] ) ? sanitize_text_field( $instance['image_border_color'] ) : '#EAEAEA';
		$image_border_radius	= filter_var( $instance['image_border_radius'], FILTER_SANITIZE_NUMBER_INT ) ? $instance['image_border_radius'] : '';
		//========================
		$news_title_length		= filter_var( $instance['news_title_length'], FILTER_SANITIZE_NUMBER_INT ) ? $instance['news_title_length'] : '';
		$news_title_font_size	= filter_var( $instance['news_title_font_size'], FILTER_SANITIZE_NUMBER_INT ) ? $instance['news_title_font_size'] : '10';
		$news_title_font_style 	= ( isset($instance[ 'news_title_font_style' ]) && filter_var( $instance['news_title_font_style'], FILTER_SANITIZE_STRING ) ) ? $instance['news_title_font_style'] : 'normal';
		$news_title_color 		= sanitize_text_field( $instance['news_title_color'] ) ? sanitize_text_field( $instance['news_title_color'] ) : '#222222';
		$news_title_color_hover	= sanitize_text_field( $instance['news_title_color_hover'] ) ? sanitize_text_field( $instance['news_title_color_hover'] ) : '#666';
		$news_title_font_weight	= ( isset($instance[ 'news_title_font_weight' ]) && filter_var( $instance['news_title_font_weight'], FILTER_SANITIZE_STRING ) ) ? $instance['news_title_font_weight'] : 'normal';
		?>
		<style>
		.hmnfw-main-wrapper {
			border: <?php echo esc_attr( $general_border_enable ); ?>!important;
		}
		.hmnfw-thumbnail img {
			border-width: <?php echo esc_attr( $image_border_width ); ?>px!important;
			border-style: <?php echo esc_attr( $thumbnail_border ); ?>!important;
			border-color: <?php echo esc_attr( $image_border_color ); ?>!important;
			border-radius: <?php echo esc_attr( $image_border_radius ); ?>px!important;
		}
		.hmnfw-feeds a.hmnfw-feeds-title {
			font-size: <?php echo esc_attr( $news_title_font_size ); ?>px!important;
			font-style: <?php echo esc_attr( $news_title_font_style ); ?>!important;
			color: <?php echo esc_attr( $news_title_color ); ?>!important;
			font-weight: <?php echo esc_attr( $news_title_font_weight ); ?>!important;
		}
		.hmnfw-feeds a.hmnfw-feeds-title:hover {
			color: <?php echo esc_attr( $news_title_color_hover ); ?>!important;
		}
		</style>
		<div class="hmnfw-main-wrapper">
			<?php
			$hmnfw_news_init_stdclass = $this->hmnfw_get_api_data( $newsSource, $apiKey );
			for( $i = 0; $i < $numberOfNews; $i++ ):
				$hmnfw_news = (array)$hmnfw_news_init_stdclass[$i]; 
				?>
				<div class="hmnfw-feed-container">
					<div class="hmnfw-thumbnail">
						<img src="<?php echo $hmnfw_news['urlToImage']; ?>" />
					</div>
					<div class="hmnfw-feeds">
						<a href="<?php echo $hmnfw_news['url']; ?>" target="_blank" class="hmnfw-feeds-title">
							<?php echo wp_trim_words( $hmnfw_news['title'], $news_title_length, '...'); ?>
						</a>
						<span><?php echo date( 'd M, Y', strtotime( $hmnfw_news['publishedAt'] ) ); ?> | <?php $hmnfw_source = (array)$hmnfw_news['source']; echo $hmnfw_source['name']; ?></span>
					</div>
				</div>
				<?php 
			endfor; ?>
		</div>
		<?php
		echo $args['after_widget'];
	}
	
	/**
	* Back-end widget form.
	*
	* @see WP_Widget::form()
	*
	* @param array $instance Previously saved values from database.
	*/
	function form( $instance ) {
		
		$instance 							= wp_parse_args( (array) $instance, array( 'title' => 'Top News', 'numberOfNews' => 5 ) );
		$title 								= $instance['title'];
		$numberOfNews 						= (isset($instance['numberOfNews'])) ? $instance['numberOfNews'] : 5;
		$newsSource 						= (isset($instance['newsSource'])) ? $instance['newsSource'] : 'cnn';
		$apiKey 							= (isset($instance['apiKey'])) ? $instance['apiKey'] : '';
		
		$instance['general_border_enable']	= ( isset($instance['general_border_enable']) && filter_var( $instance['general_border_enable'], FILTER_SANITIZE_STRING ) ) ? $instance['general_border_enable'] : '';

		$instance['news_title_font_style'] 	= isset( $instance['news_title_font_style'] ) ? $instance['news_title_font_style'] : 'normal';
		$instance['news_title_font_weight']	= ( isset($instance['news_title_font_weight']) && filter_var( $instance['news_title_font_weight'], FILTER_SANITIZE_STRING ) ) ? $instance['news_title_font_weight'] : 'normal';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Widget Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p style="margin-bottom:4px;">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">API Key:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'apiKey' ); ?>" name="<?php echo $this->get_field_name( 'apiKey' ); ?>" type="text" value="<?php echo esc_attr( $apiKey ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numberOfNews' ); ?>">Number of News:</label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'numberOfNews' ); ?>" name="<?php echo $this->get_field_name( 'numberOfNews' ); ?>" type="number" value="<?php echo esc_attr( $numberOfNews ); ?>" step="1" min="5" max="10">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'newsSource' ); ?>">News Source:</label>
			<select class="tiny-text" id="<?php echo $this->get_field_id( 'newsSource' ); ?>" name="<?php echo $this->get_field_name( 'newsSource' ); ?>">
				<?php 
				$hmnfwGetNewsSources = $this->hmnfwGetNewsSources();
				foreach($hmnfwGetNewsSources as $source => $name): ?>
					<option value="<?php printf('%s', esc_attr($source)); ?>" <?php echo ($source == $newsSource) ? 'selected' : '' ?>><?php printf('%s', esc_attr($name)); ?></option>
				<?php endforeach; ?>
            </select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('news_title_length'); ?>">News Title Length:</label>
			<input type="number" class="tiny-text" id="<?php echo $this->get_field_id('news_title_length'); ?>" name="<?php echo $this->get_field_name('news_title_length'); ?>" value="<?php echo esc_attr( $instance['news_title_length'] ); ?>" step="1" min="1" max="20">
			<?php echo esc_html( 'Words', HMNFW_TXT_DOMAIN ); ?>
		</p>
		<p class="section-head"><?php echo esc_html( 'Style', HMNFW_TXT_DOMAIN ); ?></p>
		<hr>
		<?php echo esc_html( 'General Style', HMNFW_TXT_DOMAIN ); ?>
		<hr>
		<p> 
			<label for="<?php echo esc_attr( $this->get_field_id('general_border_enable') ); ?>"><?php _e( 'Border', HMNFW_TXT_DOMAIN ); ?>:</label>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('general_border_enable') ); ?>" name="<?php echo esc_attr( $this->get_field_name('general_border_enable') ); ?>" <?php checked( $instance['general_border_enable'], 'on' ); ?> />
		</p>
		<hr>
		<?php echo esc_html( 'Image Style', HMNFW_TXT_DOMAIN ); ?>
		<hr>
		<p> 
			<label for="<?php echo $this->get_field_id( 'thumbnailBorder' ); ?>">Border:</label>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'thumbnailBorder' ); ?>" name="<?php echo $this->get_field_name( 'thumbnailBorder' ); ?>" <?php checked( $instance[ 'thumbnailBorder' ], 'on' ); ?> />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('image_border_width'); ?>">Border Width:</label>
			<input type="number" class="tiny-text" id="<?php echo $this->get_field_id('image_border_width'); ?>" name="<?php echo $this->get_field_name('image_border_width'); ?>" value="<?php echo esc_attr( $instance['image_border_width'] ); ?>" step="1" min="0" max="10">
			<?php echo esc_html( 'px', HMNFW_TXT_DOMAIN ); ?>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_border_color' ) ); ?>"><?php _e( 'Border Color', HMNFW_TXT_DOMAIN ); ?>:</label>
			<input type="text" class="hmnfw-color-picker" id="<?php echo esc_attr( $this->get_field_id('image_border_color') ); ?>" name="<?php echo esc_attr( $this->get_field_name('image_border_color') ); ?>" value="<?php echo esc_attr( $instance['image_border_color'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('image_border_radius') ); ?>"><?php _e( 'Border Radius', HMNFW_TXT_DOMAIN ); ?>:</label>
			<input type="number" class="tiny-text" id="<?php echo esc_attr( $this->get_field_id('image_border_radius') ); ?>" name="<?php echo esc_attr( $this->get_field_name('image_border_radius') ); ?>" value="<?php echo esc_attr( $instance['image_border_radius'] ); ?>" step="1" min="0" max="50">
			<?php echo esc_html( 'px', HMNFW_TXT_DOMAIN ); ?>
		</p>
		<hr>
		<?php echo esc_html( 'News Title Style', HMNFW_TXT_DOMAIN ); ?>
		<hr>
		<p>
			<label for="<?php echo $this->get_field_id('news_title_font_size'); ?>">Font Size:</label>
			<input type="number" class="tiny-text" id="<?php echo $this->get_field_id('news_title_font_size'); ?>" name="<?php echo $this->get_field_name('news_title_font_size'); ?>" value="<?php echo esc_attr( $instance['news_title_font_size'] ); ?>" step="1" min="10" max="50">
			<?php echo esc_html( 'px', HMNFW_TXT_DOMAIN ); ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('news_title_font_style'); ?>">Font Style:</label>
			<select class="tiny-text" id="<?php echo $this->get_field_id( 'news_title_font_style' ); ?>" name="<?php echo $this->get_field_name( 'news_title_font_style' ); ?>">
				<option value="<?php echo esc_attr( 'normal', HMNFW_TXT_DOMAIN ); ?>" <?php echo ( 'normal' === $instance['news_title_font_style'] ) ? 'selected' : ''; ?>><?php echo esc_html( 'Normal', HMNFW_TXT_DOMAIN ); ?></option>
				<option value="<?php echo esc_attr( 'italic', HMNFW_TXT_DOMAIN ); ?>" <?php echo ( 'italic' === $instance['news_title_font_style'] ) ? 'selected' : ''; ?>><?php echo esc_html( 'Italic', HMNFW_TXT_DOMAIN ); ?></option>
				<option value="<?php echo esc_attr( 'oblique', HMNFW_TXT_DOMAIN ); ?>" <?php echo ( 'oblique' === $instance['news_title_font_style'] ) ? 'selected' : ''; ?>><?php echo esc_html( 'Oblique', HMNFW_TXT_DOMAIN ); ?></option>
            </select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('news_title_font_weight') ); ?>"><?php _e( 'Font Weight', HMNFW_TXT_DOMAIN ); ?>:</label>
			<select class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'news_title_font_weight' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'news_title_font_weight' ) ); ?>">
				<option value="<?php echo esc_attr( 'normal', HMNFW_TXT_DOMAIN ); ?>" <?php echo ( 'normal' === $instance['news_title_font_weight'] ) ? 'selected' : ''; ?>><?php echo esc_html( 'Normal', HMNFW_TXT_DOMAIN ); ?></option>
				<option value="<?php echo esc_attr( '400', HMNFW_TXT_DOMAIN ); ?>" <?php echo ( '400' === $instance['news_title_font_weight'] ) ? 'selected' : ''; ?>><?php echo esc_html( '400', HMNFW_TXT_DOMAIN ); ?></option>
				<option value="<?php echo esc_attr( '600', HMNFW_TXT_DOMAIN ); ?>" <?php echo ( '600' === $instance['news_title_font_weight'] ) ? 'selected' : ''; ?>><?php echo esc_html( '600', HMNFW_TXT_DOMAIN ); ?></option>
				<option value="<?php echo esc_attr( '900', HMNFW_TXT_DOMAIN ); ?>" <?php echo ( '900' === $instance['news_title_font_weight'] ) ? 'selected' : ''; ?>><?php echo esc_html( '900', HMNFW_TXT_DOMAIN ); ?></option>
            </select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'news_title_color' ) ); ?>"><?php _e( 'Color', HMNFW_TXT_DOMAIN ); ?>:</label>
			<input type="text" class="hmnfw-color-picker" id="<?php echo esc_attr( $this->get_field_id('news_title_color') ); ?>" name="<?php echo esc_attr( $this->get_field_name('news_title_color') ); ?>" value="<?php echo esc_attr( $instance['news_title_color'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'news_title_color_hover' ) ); ?>"><?php _e( 'Hover Color', HMNFW_TXT_DOMAIN ); ?>:</label>
			<input type="text" class="hmnfw-color-picker" id="<?php echo esc_attr( $this->get_field_id('news_title_color_hover') ); ?>" name="<?php echo esc_attr( $this->get_field_name('news_title_color_hover') ); ?>" value="<?php echo esc_attr( $instance['news_title_color_hover'] ); ?>">
		</p>
		<p style="margin-bottom: 20px! important; width: 100%; text-align:center;"><a href='https://www.paypal.me/mhmrajib' class="button button-primary" target="_blank">Donate us to keep this plugin alive!</a></p>
		
		<?php
	}
	
	/*
	* Sanitize widget form values as they are saved.
	*
	* @see WP_Widget::update()
	*
	* @param array $new_instance Values just sent to be saved.
	* @param array $old_instance Previously saved values from database.
	*
	* @return array Updated safe values to be saved.
	*/
	function update( $new_instance, $old_instance ) {
		
		$instance 							= $old_instance;
		$instance['title'] 					= $new_instance['title'];
		$instance['numberOfNews'] 			= $new_instance['numberOfNews'];
		$instance['newsSource'] 			= $new_instance['newsSource'];
		$instance['apiKey'] 				= $new_instance['apiKey'];

		// Style
		$instance['general_border_enable'] 	= filter_var( $new_instance['general_border_enable'], FILTER_SANITIZE_STRING ) ? $new_instance['general_border_enable'] : '';

		$instance['thumbnailBorder'] 		= $new_instance['thumbnailBorder'];
		$instance['image_border_width'] 	= filter_var( $new_instance['image_border_width'], FILTER_SANITIZE_NUMBER_INT ) ? $new_instance['image_border_width'] : '';
		$instance['image_border_color'] 	= sanitize_text_field( $new_instance['image_border_color'] ) ? sanitize_text_field( $new_instance['image_border_color'] ) : '';
		$instance['image_border_radius'] 	= filter_var( $new_instance['image_border_radius'], FILTER_SANITIZE_NUMBER_INT ) ? $new_instance['image_border_radius'] : '';
		
		$instance['news_title_length'] 		= filter_var( $new_instance['news_title_length'], FILTER_SANITIZE_NUMBER_INT ) ? $new_instance['news_title_length'] : '';
		$instance['news_title_font_size'] 	= filter_var( $new_instance['news_title_font_size'], FILTER_SANITIZE_NUMBER_INT ) ? $new_instance['news_title_font_size'] : '';
		$instance['news_title_font_style'] 	= filter_var( $new_instance['news_title_font_style'], FILTER_SANITIZE_STRING ) ? $new_instance['news_title_font_style'] : '';
		$instance['news_title_color'] 		= sanitize_text_field( $new_instance['news_title_color'] ) ? sanitize_text_field( $new_instance['news_title_color'] ) : '';
		$instance['news_title_color_hover']	= sanitize_text_field( $new_instance['news_title_color_hover'] ) ? sanitize_text_field( $new_instance['news_title_color_hover'] ) : '';
		$instance['news_title_font_weight'] = filter_var( $new_instance['news_title_font_weight'], FILTER_SANITIZE_STRING ) ? $new_instance['news_title_font_weight'] : '';
		$this->hmnfw_set_api_data_to_cache( $instance['newsSource'], $instance['apiKey'] );
		return $instance;
	}
	
	protected function hmnfw_get_api_data( $source, $ap ) {
		$this->hmnfw_api_cached_data = get_transient( 'hmnfw_api_cached_data' );
		//delete_transient( 'hmnfw_api_cached_data' );
		if( (false === $this->hmnfw_api_cached_data) or (empty($this->hmnfw_api_cached_data)) ) { //echo "new";
			delete_transient( 'hmnfw_api_cached_data' );
			add_filter( 'https_ssl_verify', '__return_false' );
			$urla = "https://newsapi.org/v2/top-headlines?sources={$source}&apiKey={$ap}";

			$headers = array(
				'Content-Type' => 'application/json',
				'User-Agent' => esc_html( get_bloginfo( 'name' ) ),
			);

			$this->hmnfw_api = wp_remote_get( $urla, array( 'headers' => $headers ) );

			$this->hmnfw_api_data = (array)json_decode(wp_remote_retrieve_body( $this->hmnfw_api ));
			
			set_transient( 'hmnfw_api_cached_data', $this->hmnfw_api_data, 16*60 ); //
			
			$this->hmnfw_api_cached_data = get_transient( 'hmnfw_api_cached_data' );
		}
		
		return (!empty($this->hmnfw_api_cached_data['articles'])) ? $this->hmnfw_api_cached_data['articles'] : die($this->hmnfw_api_cached_data['message']);
	}
	
	protected function hmnfw_set_api_data_to_cache( $s, $a ) {
		delete_transient( 'hmnfw_api_cached_data' );
		$url = "https://newsapi.org/v2/top-headlines?sources={$s}&apiKey={$a}";
		$api = wp_remote_get($url);
		$api_data = (array)json_decode(wp_remote_retrieve_body( $api ));
		set_transient( 'hmnfw_api_cached_data', $api_data, 16*60 );
		return true;
	}

	protected function hmnfwGetNewsSources() {
		return array( 
					'abc-news' 				=> 'ABC News',
					'abc-news-au' 			=> 'ABC News (AU)',
					'al-jazeera-english' 	=> 'Al Jazeera English',
					'ary-news' 				=> 'Ary News',
					'bbc-news' 				=> 'BBC News',
					'bbc-sport' 			=> 'BBC Sport',
					'bloomberg' 			=> 'Bloomberg',
					'business-insider' 		=> 'Business Insider',
					'business-insider-uk'	=> 'Business Insider (UK)',
					'cbc-news' 				=> 'CBC News',
					'cbs-news' 				=> 'CBS News',
					'cnbc'					=> 'CNBC',
					'cnn' 					=> 'CNN',
					'cnn-es' 				=> 'CNN Spanish',
					'daily-mail'			=> 'Daily Mail',
					'der-tagesspiegel'		=> 'Der Tagesspiegel', //Germany
					'el-mundo'				=> 'El Mundo',
					'espn'					=> 'ESPN',
					'fox-news' 				=> 'Fox News',
					'google-news' 			=> 'Google News',
					'marca'					=> 'Marca',
					'mirror'				=> 'Mirror',
					'nbc-news' 				=> 'NBC News',
					'rt'					=> 'RT',
					'the-huffington-post' 	=> 'The Huffington Post',
					'the-new-york-times' 	=> 'The New York Times',
					'the-guardian-uk' 		=> 'The Guardian (UK)',
					'the-economist' 		=> 'The Economist',
					'the-washington-post' 	=> 'The Washington Post',
					'the-washington-times' 	=> 'The Washington Times',
					'the-hindu' 			=> 'The Hindu'
				);
    }
}
?>