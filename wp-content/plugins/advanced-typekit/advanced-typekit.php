<?php
/*
Plugin Name: Advanced Typekit
Plugin URI: http://wpprogrammer.com/advanced-typekit/
Description: Allows you to add Typekit fonts to any element on your page, by entering the css selectors for it from the admin panel. Uses the new Typekit API to fetch font information.
Author: Utkarsh Kukreti
Version: 1.0.1
Author URI: http://utkar.sh
*/

AdvancedTypekit::init();
abstract class AdvancedTypekit
{
	static $fonts, $options;
	static function init()
	{
		self::$options  = $options = get_option( 'advanced_typekit' );
		
		if( is_admin() )
		{
			add_action( 'admin_init', array( 'AdvancedTypekit', 'admin_init' ) );
			add_action( 'admin_menu', array( 'AdvancedTypekit', 'admin_menu' ) );
			add_action( 'wp_ajax_advanced-typekit', array( 'AdvancedTypekit', 'ajax' ) );
		}
		else
		{
			if( isset( $options['api_key'] ) && isset( $options['enabled'] ) && $options['enabled'] )
			{
				add_action( 'wp_print_scripts', array( 'AdvancedTypekit', 'wp_print_scripts' ) );
				add_action( 'wp_print_styles', array( 'AdvancedTypekit', 'wp_print_styles' ) );
			}
		}
	}
	
	/* Frontend */
	static function wp_print_scripts()
	{
		$api_key = self::$options['api_key'];
		?>
		<script type="text/javascript">
			WebFontConfig = { typekit: { id: '<?php echo $api_key ?>'  } };
			(function() {
				var wf = document.createElement('script');
				wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
				'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
				wf.type = 'text/javascript';
				wf.async = 'true';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(wf, s);
			})();
		</script>
		<?php 
	}
	
	static function wp_print_styles()
	{
		$fonts = self::$options['fonts'];
		$data = self::$options['data'];
		?>
		<style type="text/css">
<?php
				foreach( $fonts as $font )
				{
					if( ! isset( $data[$font->id] ) )
						continue;
					
					$d = $data[$font->id];
					
					if( strlen( $d['selectors'] ) == 0 )
						continue;
					
					$base = 'html.wf-active ';
					
					$selectors = (array) explode( "\n", $d['selectors'] );
					$extra_css = (array) explode( "\n", $d['extra_css'] );

					$selectors = array_map( 'trim', $selectors );
					$extra_css = array_map( 'trim', $extra_css );

					foreach( $selectors as $i => $selector )
					{
						$final_selector = array();
						
						foreach( explode( ',', $selector ) as $s )
							array_push( $final_selector, $base . trim( $s ) );
						
						$final_selector = join(', ', $final_selector);
						$final_extra_css = isset( $extra_css[$i] ) ? $extra_css[$i] : '';
						$font_family = join( ', ', $font->css_names );
						
						echo "\t\t\t{$final_selector} { font-family: {$font_family}; {$final_extra_css} }\n";
					}

					
				}
			?>
		</style>
<?php 
	}
	
	/* Plugin Options */
	static function admin_init()
	{
		/* Plugin Options */
		register_setting( 'advanced_typekit', 'advanced_typekit', array( 'AdvancedTypekit', 'options_validate' ) );
	}
	static function admin_menu()
	{
		add_options_page( 'Advanced Typekit', 'Advanced Typekit', 'manage_options', 'advanced-typekit', array( 'AdvancedTypekit', 'options_page' ) );
	}
	static function options_page()
	{
		$options = self::$options;
		?>
		<script type="text/javascript">
			(function($) {
				$(document).ready(function() {
					$form = $('form.advanced-typekit');
					$form.find('.fetch-fonts').click(function(){
					
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'html',
							data: {
									'api_key' : $('#api-key').val(),
									'action' : 'advanced-typekit',
									'do' : 'fetch-fonts'
								},
							success: function(data) {
									$('#advanced-typekit-options').html(data);
								}
						});
					});
				});
			})(jQuery)
		</script>
		<div class="wrap">
			<h2>Advanced Typekit</h2>
			<form action="options.php" method="post" class="advanced-typekit">
				<?php settings_fields( 'advanced_typekit' ) ?>
				<p>
					<input type="checkbox" name="advanced_typekit[enabled]" id="enabled" value="1" <?php echo isset($options['enabled']) && $options['enabled'] ? 'checked="checked"' : '' ?>/>
					<label for="enabled">Enable Typekit</label>
				</p>
				<p>
					<label for="api-key">API Key</label>
					<input type="text" id="api-key" name="advanced_typekit[api_key]" value="<?php echo $options['api_key']; ?>" />
					<a class="button-secondary fetch-fonts">Fetch Fonts</a>
				</p>
				<div id="advanced-typekit-options">
					<?php self::build_form() ?>
				</div>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>

		</div>
		<?php
	}
	
	static function options_validate( $input )
	{
		$input['fonts'] = self::$options['fonts'];
		
		return $input;
	}
	
	static function ajax()
	{
		if( isset( $_POST['do'] ) )
		{
			switch( $_POST['do'] )
			{
				case 'fetch-fonts':
				
					if( ! isset( $_POST['api_key'] ) )
						die;
						
					$api_key = $_POST['api_key'];
					
					$data = wp_remote_get( "https://typekit.com/api/v1/json/kits/$api_key/published" );
					
					if( is_wp_error( $data ) )
						die(1);
					
					$data = json_decode( $data['body'] );
					
					$fonts = $data->kit->families;
					
					// save fonts and api_key
					self::$options['fonts'] = $fonts;
					self::$options['api_key'] = $api_key;
					self::save();
					
					self::build_form();					

				break;
			}
		}		
		die;
	}
	
	static function build_form()
	{
		$fonts = (array) self::$options['fonts'];
		$data = isset( self::$options['data'] ) ? self::$options['data'] : array();
		$header = <<<EOT
			<tr>
			<!--	<th class="manage-column check-column" scope="col" style="width:5em; text-align:center;">Enabled</th> -->
				<th class="manage-column" scope="col">Font Name</th>
				<th class="manage-column" scope="col">Apply to Selectors (one group per line)</th>
				<th class="manage-column" scope="col">Extra CSS (Applies to corresponding line of selectors on the left)</th>
			</tr>
EOT;
		?>
			<table class="widefat">
				<thead>
					<?php echo $header ?>
				</thead>
				
				<tfoot>
					<?php echo $header ?>
				</tfoot>
				<tbody class="plugins">
		<?php 
		foreach( $fonts as $font )
		{
			?>
			<tr class="active" style="padding:10px">
			<!-- <th class="check-column" scope="row" style="padding:15px 10px;"><input type="checkbox" value="advanced-typekit/advanced-typekit.php" name="checked[]"></th> -->
				<td class="plugin-title"><strong><?php echo $font->name ?></strong></td>
				<td class="desc"><textarea name="advanced_typekit[data][<?php echo $font->id ?>][selectors]" type="text" style="width:90%; height:200px;"><?php echo isset( $data[$font->id]['selectors'] ) ? $data[$font->id]['selectors'] : ''  ?></textarea></td>
				<td class="desc" style="padding:7px"><textarea name="advanced_typekit[data][<?php echo $font->id ?>][extra_css]"type="text" style="width:90%; height:200px;"><?php echo isset( $data[$font->id]['extra_css'] ) ? $data[$font->id]['extra_css'] : '' ?></textarea></td>
			</tr>

			<?php 
		}
		
		?>
				</tbody>
			</table>
		<?php 
	}
	
	static function save()
	{
		update_option( 'advanced_typekit', self::$options );
	}
	
}