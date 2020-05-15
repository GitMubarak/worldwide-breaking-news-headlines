<?php
error_reporting(0);
/**
* Adds HM Recent Posts widget in Widget area
*/
class HmNewsFeedWidgetActivater extends WP_Widget {
	
	protected $hmnfw_api, $hmnfw_api_data, $hmnfw_api_cached_data;
	/**
	* Register widget with WordPress.
	*/
	function __construct() {
		parent::__construct(
			'hmnewsfeedwidgetactivater', // Base ID
			__('Worldwide Breaking News Headlines', 'text_domain'), // Widget name which will display in the widget area
			array( 'description' => __( 'Worldwide Breaking News Headlines', 'text_domain' ), ) // Args
		);
	}
	
	/**
	* Front-end display of widget.
	*
	* @see WP_Widget::widget()
	*
	* @param array $args Widget arguments.
	* @param array $instance Saved values from database.
	*/
	public function widget( $args, $instance )
	{	
		echo $args['before_widget'];
		
		$newsSource = (isset($instance[ 'newsSource' ])) ? $instance[ 'newsSource' ] : 'cnn';
		$apiKey = (isset($instance[ 'apiKey' ])) ? $instance[ 'apiKey' ] : '972bbddf9e73488db0a0db78a981c4d8';
		$hmnfw_news_init_stdclass = $this->hmnfw_get_api_data( $newsSource, $apiKey );
		if ( ! empty( $instance['title'] ) ){
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'].' '.$newsSource ). $args['after_title'];
		}
		$numberOfNews = (isset($instance[ 'numberOfNews' ])) ? $instance[ 'numberOfNews' ] : 5;
		$thumbnailBorder = (isset($instance[ 'thumbnailBorder' ])) ? 'true' : 'false';
		$thumbnail_border = ($thumbnailBorder == 'true') ? '3px solid #CCC' : '0px';
		?>
		<style>
		.hmnfw-main-container{
			border:1px solid #DDD;
			width:100%; 
			min-height:100px;
			padding: 5px;
		}
		.hmnfw-main-container a.hmnfw-powered-by, .hmnfw-main-container a.hmnfw-powered-by:hover{
			font-size:8px; text-decoration:none; color:#CCC;
			width:100px; margin:0 auto;
			display:block;
			padding:5px;
		}
		.hmnfw-feed-container{
			clear: both;
			border-bottom:1px solid #DDD;
			width:100%; min-height:40px; padding:10px 0px;
			display:flex;
			flex-wrap: wrap;
		}
		.hmnfw-thumbnail-container{
			border: 0px solid #009900; min-height:40px;
			padding-top:3px;
			width: 30%;
			text-align: center;
		}
		.hmnfw-feeds{
			flex:1;
			border:0px solid #FF0000; width:69.8%; min-height:50px; float:left; padding:0; margin:0; padding-left:2px;
		}
		.hmnfw-thumbnail-container img {
			width: 90%;
			margin: 0 auto;
			height: auto;
		}
		.hmnfw-feeds a.hmnfw-feeds-title{
			margin:0; padding:0; 
			font-size:14px; 
			display: block;
			line-height:18px!important; 
			text-decoration:none;
			border: 0px solid #000;
			color:#222;
		}
		.hmnfw-feeds a.hmnfw-feeds-title:hover{
			color:#666;
		}
		.hmnfw-feeds span{
			margin-top:5px; font-size:11px; color:#999999; display:block; line-height:11px;
		}
		</style>
		<div class="hmnfw-main-container">
		<?php 
			for( $i = 0; $i < $numberOfNews; $i++ ):
			$hmnfw_news = (array)$hmnfw_news_init_stdclass[$i]; ?>
			<div class="hmnfw-feed-container">
				<div class="hmnfw-thumbnail-container">
					<img src="<?php echo $hmnfw_news['urlToImage']; ?>" />
				</div>
				<div class="hmnfw-feeds">
					<a href="<?php echo $hmnfw_news['url']; ?>" target="_blank" class="hmnfw-feeds-title">
						<?php
						echo substr($hmnfw_news['title'], 0, 50); 
						echo (strlen($hmnfw_news['title']) > 50) ? '...' : '';
						?>
					</a>
					<span><?php echo date('d M, Y',strtotime($hmnfw_news['publishedAt'])); ?> | <?php $hmnfw_source = (array)$hmnfw_news['source']; echo $hmnfw_source['name']; ?></span>
				</div>
				<div style="clear:both"></div>
			</div>
		<?php endfor; ?>
			<a href="https://newsapi.org/" target="_blank" class="hmnfw-powered-by">powered by NewsAPI.org</a>
		</div>
		<br /><br />
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
	public function form( $instance ) 
	{
		//$aaa = get_transient( 'hmnfw_api_cached_data' );
		//print_r($aaa);
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Top News', 'numberOfNews' => 5 ) );
		//print_r($instance);
        $title = $instance['title'];
		$numberOfNews = (isset($instance[ 'numberOfNews' ])) ? $instance[ 'numberOfNews' ] : 5 ;
		$newsSource = (isset($instance[ 'newsSource' ])) ? $instance[ 'newsSource' ] : 'cnn' ;
		$apiKey = (isset($instance[ 'apiKey' ])) ? $instance[ 'apiKey' ] : '972bbddf9e73488db0a0db78a981c4d8';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Widget Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p style="margin-bottom:4px;">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">API Key:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'apiKey' ); ?>" name="<?php echo $this->get_field_name( 'apiKey' ); ?>" type="text" value="<?php echo esc_attr( $apiKey ); ?>">
		</p>
		<p style="margin-top:-5px; padding-top:-5px;"><small>Default: 972bbddf9e73488db0a0db78a981c4d8</small></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numberOfNews' ); ?>">Number of news to display:</label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'numberOfNews' ); ?>" name="<?php echo $this->get_field_name( 'numberOfNews' ); ?>" type="number" value="<?php echo esc_attr( $numberOfNews ); ?>" step="1" min="5" max="10">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'newsSource' ); ?>">News sources:</label>
			<select class="tiny-text" id="<?php echo $this->get_field_id( 'newsSource' ); ?>" name="<?php echo $this->get_field_name( 'newsSource' ); ?>">
				<?php 
				$hmnfwGetNewsSources = $this->hmnfwGetNewsSources();
				foreach($hmnfwGetNewsSources as $source => $name): ?>
					<option value="<?php printf('%s', esc_attr($source)); ?>" <?php echo ($source == $newsSource) ? 'selected' : '' ?>><?php printf('%s', esc_attr($name)); ?></option>
				<?php endforeach; ?>
            </select>
		</p>
		<hr />
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'thumbnailBorder' ); ?>" name="<?php echo $this->get_field_name( 'thumbnailBorder' ); ?>" <?php checked( $instance[ 'thumbnailBorder' ], 'on' ); ?> /> 
    		<label for="<?php echo $this->get_field_id( 'thumbnailBorder' ); ?>">Show Thumbnail Border</label><br>
		</p>
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
	public function update( $new_instance, $old_instance ) 
	{
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['numberOfNews'] = $new_instance['numberOfNews'];
		$instance['newsSource'] = $new_instance['newsSource'];
		$instance['thumbnailBorder'] = $new_instance['thumbnailBorder'];
		$instance['apiKey'] = $new_instance['apiKey'];
		$this->hmnfw_set_api_data_to_cache( $instance['newsSource'], $instance['apiKey'] );
		
		return $instance;
	}
	
	public function hmnfw_get_api_data( $source, $ap )
	{
		$this->hmnfw_api_cached_data = get_transient( 'hmnfw_api_cached_data' );
		//delete_transient( 'hmnfw_api_cached_data' );
		if( (false === $this->hmnfw_api_cached_data) or (empty($this->hmnfw_api_cached_data)) ) { //echo "new";
			delete_transient( 'hmnfw_api_cached_data' );
			add_filter( 'https_ssl_verify', '__return_false' );
			$urla = "https://newsapi.org/v2/top-headlines?sources={$source}&apiKey={$ap}";
			$this->hmnfw_api = wp_remote_get($urla);
			$this->hmnfw_api_data = (array)json_decode(wp_remote_retrieve_body( $this->hmnfw_api ));
			set_transient( 'hmnfw_api_cached_data', $this->hmnfw_api_data, 16*60 ); //
			$this->hmnfw_api_cached_data = get_transient( 'hmnfw_api_cached_data' );
		}	
		return (!empty($this->hmnfw_api_cached_data['articles'])) ? $this->hmnfw_api_cached_data['articles'] : die($this->hmnfw_api_cached_data['message']);
	}
	
	public function hmnfw_set_api_data_to_cache( $s, $a )
	{
		delete_transient( 'hmnfw_api_cached_data' );
		$url = "https://newsapi.org/v2/top-headlines?sources={$s}&apiKey={$a}";
		$api = wp_remote_get($url);
		$api_data = (array)json_decode(wp_remote_retrieve_body( $api ));
		set_transient( 'hmnfw_api_cached_data', $api_data, 16*60 );
		return true;
	}

	private function hmnfwGetNewsSources(){
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