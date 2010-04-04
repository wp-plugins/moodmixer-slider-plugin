<?php
/*
Plugin Name: Moodmixer Slider Plugin
Plugin URI: http://www.dynamic-slider.com/
Description: Add Moodmixer Sliders to your weblog using shortcodes, widgets and even a template function. Requires WP 2.8+ to work properly.
Author: Fabian Wolf
Version: 0.5.2
Author URI: http://fwolf.info/
License: GNU GPL v2
*/
/**
 * Moodmixer Slider Plugin
 *
 * @author Fabian Wolf (http://fwolf.info/)
 * @version 0.5.2
 * @package moodmixer
 * @link http://www.dynamic-slider.com/
 *
 */

// define('XRAF_MMF_INCLUDE_PATH', ABSPATH . PLUGINDIR . '/mm-forms/includes/');
//exit('<pre>' . __FILE__ . ', ' . WP_PLUGIN_DIR . '</pre>');

if(strpos(__FILE__, WP_PLUGIN_DIR) === false) { // if the plugin is eg. included with a symlink and not sitting in a real directory in WP_PLUGIN_DIR
	define('MXSLIDER_PATH', '/' . dirname( plugin_basename(__FILE__) ) );
	define('MXSLIDER_TEMPLATE_PATH', $strExternalDivider. MXSLIDER_PATH . '/templates/');
} else {
	define('MXSLIDER_PATH', dirname( plugin_basename(__FILE__) ) );
	define('MXSLIDER_TEMPLATE_PATH', WP_PLUGIN_DIR . '/moodmixer-slider/templates/');
}

// define('MXSLIDER_TEMPLATE_PATH', $strExternalDivider. MXSLIDER_PATH . '/templates/');
//define('MXSLIDER_TEMPLATE_PATH', 'templates/');
define('MXSLIDER_SELF', '/' . plugin_basename(__FILE__) );



class moodmixerSlider {
	private $arrSettings = array();
	private $strTableSliders = '';
	public $iExcerptMaxChars = 0;
	public $iExcerptMaxSentences = 0;

/**
 * Initialization
 */

	function moodmixerSlider() {
		global $wpdb;

		// base settings
		$this->arrSettings = get_option('moodmixer_settings');
		$this->strTableSliders = $wpdb->prefix . 'moodmixer_slider';

		$this->iExcerptMaxChars = 100;
		$this->iExcerptMaxSentences = 3;

		// setup actions
		register_activation_hook(WP_PLUGIN_DIR .'/moodmixer-slider/moodmixer-slider.php', array(&$this, 'mxs_install_hook'));
		register_deactivation_hook(WP_PLUGIN_DIR .'/moodmixer-slider/moodmixer-slider.php', array(&$this, 'mxs_uninstall_hook'));

		// base actions

// 		add_action('init', array(&$this, 'load_plugin_textdomain'));
		add_action('admin_menu', array(&$this, 'add_pages'));
		add_action('admin_head', array(&$this, 'admin_head'));
//      	add_action('wp_head', array(&$this, 'wp_head'));
		add_action('wp_print_scripts', array(&$this, 'load_js'));
		add_shortcode('slider', array(&$this, 'mxs_shortcode_handler')); // shortcodes
		add_action( 'widgets_init', array(&$this, 'mxs_load_widgets') );

 		//add_action('init', array(&$this, 'mxs_tinymce_addbuttons'), 10);
	}

/**
 * Header related
 */

/*
	function wp_head() {

	}*/

	function load_js() {
		if( is_admin() ) {
			wp_enqueue_script('jquery');
			wp_enqueue_script('thickbox');
		}
	}


/**
 * Admin panel
 */

	function admin_head() {
		global $plugin_page;

		if( isset($plugin_page) != false && $plugin_page == plugin_basename(__FILE__) ) {
			//$admin_stylesheet_url = get_option('siteurl') . '/wp-content/plugins/mm-forms/admin-stylesheet.css';
			$strPluginURL = get_option('siteurl') . '/wp-content/plugins/moodmixer-slider/'; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo get_option('siteurl') . '/wp-includes/js/thickbox/thickbox.css'; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo $strPluginURL; ?>admin.css" />
		<!--<script type="text/javascript" src="<?php echo $strPluginURL . 'admin.js'; ?>"></script>-->
<?php
		}
	}



	function add_pages() {
		$base_url = get_option('siteurl') . '/wp-admin/options-general.php';
		$page = str_replace('\\', '%5C', plugin_basename(__FILE__));
		//$contact_forms = $this->get_contact_forms();

		add_options_page('Moodmixer Slider', 'Moodmixer Slider', 'manage_options', __FILE__, array(&$this, 'option_page'));

	}


	function option_page() {
        global $wpdb,$includes, $user_ID;

// 		$this->prepare_form_data(); // run only when called by admin interface

		$base_url = get_option('siteurl') . '/wp-admin/options-general.php';
		$page = plugin_basename(__FILE__);

// 		$arrSettings['form_id'] = get_option('xraf_form_id');
		$arrSettings = get_option('mxs_settings');

		/*
		foreach($arrFormFields as $strFormFieldKey => $dump) {
			$arrFormFieldSettings[$strFormFieldKey] = str_replace('-', '_', $arrSettings[$strFormFieldKey] );
		}*/

		$strImagePath = get_option('siteurl').'/wp-content/plugins/moodmixer-slider/images/';

		$strAction = $_REQUEST['action'];

		if(!empty($strAction) && $strAction != 'settings') {
			$strPlural1 = ''; $strPlural2 = ''; $strItemNumber = '';

			if(isset($_POST['id']) != false && is_array($_POST['id']) != false && sizeof($_POST['id']) > 0) {
				$strPlural1 = ''; $strPlural2 = 'n';
				$strItemNumber = sizeof( $_POST['id'] ) . ' ';
			}
		}


		// actions
		switch($strAction) {
			default:
			case 'main':
			case 'overview':
 				$arrSliders = $this->get_sliders(); // no need to flush db cache
				break;
			case 'add':
				$wpdb->flush();
				$arrShortcodes = $this->get_shortcodes();
				break;
			case 'edit':
				$wpdb->flush();
				$slider = $this->get_slider( intval($_GET['id']) );
				$arrShortcodes = $this->get_shortcodes();
				break;
			case 'save':
				if( !isset($_POST['id']) ) { // add
					$updated_message = ( $this->handle_action($strAction) ) ? 'Neuen Eintrag gespeichert.' : 'Fehler aufgetreten beim Einfügen des Eintrags in die Datenbank!';
				} else { // edit
					$updated_message = ( $this->handle_action($strAction) ) ? 'Eintrag gespeichert.' : 'Eintrag nicht gespeichert, da entweder keine Änderungen vorgenommen worden sind - oder aber der Shortcode bereits vergeben ist.';
				}

				$wpdb->flush();
				$arrSliders = $this->get_sliders();
				break;

			case 'remove':
				$updated_message = ( $this->handle_action($strAction) ) ?  'Slider gel&ouml;scht.' : 'Slider konnte' . $strPlural2 . ' <em>nicht</em> gel&ouml;scht werden.';

				$wpdb->flush();
 				$arrSliders = $this->get_sliders();
 				break;
		}




		require_once( MXSLIDER_TEMPLATE_PATH.'admin.php' );
	}
/**
 * Actions
 */


	function handle_action($strAction, $bAjaxResponse = false) {
		global $wpdb;

		$return = false;

		switch($strAction) {
			case 'save':


				if(isset($_POST['id']) != false && intval($_POST['id']) > 0) { // update
					$sliderData = $this->get_slider( intval($_POST['id']) );
					$arrShortcodes = $this->get_shortcodes();

					if($sliderData != false && is_object($sliderData) != false) {
						foreach($sliderData as $sliderFieldKey => $sliderFieldValue) {


							if( isset($_POST[$sliderFieldKey]) != false && stripslashes(trim($_POST[$sliderFieldKey])) != stripslashes($sliderFieldValue) ) { // if form field value != known value, add it to the update-able field array

								switch($sliderFieldKey) {
									case 'slider_shortcode':
										if( in_array($_POST[$sliderFieldKey], $arrShortcodes) != false ) {
											break 2;
										}
									default:
										$arrSaveEntry[] = $wpdb->prepare('SET '.$sliderFieldKey.' = %s', stripslashes($_POST[$sliderFieldKey]) );
								}

							}
						}
					}

// 					$this->_debug(array('arrSaveEntry' => $arrSaveEntry));

					if(isset($arrSaveEntry) != false && sizeof($arrSaveEntry) > 0) {
						$strSQLQuery = 'UPDATE ' . $this->strTableSliders . ' ' . implode(', ', $arrSaveEntry) . ' WHERE slider_ID = ' . intval($_POST['id']);
					}

// 					$this->_debug(array('strSQLQueryUpdate' => $strSQLQuery ) );
				} else { // insert

					$sliderData = $this->get_table_fields();

// 					echo '<p>debug:</p><pre>' . print_r($sliderData) . '</pre>';
					$arrSaveEntryKey[0] = 'slider_ID';
					$arrSaveEntryValue[0] = 'NULL';

					foreach($sliderData as $strFieldKey) {

						if( isset($_POST[$strFieldKey]) != false && !empty($_POST[$strFieldKey]) ) {
							$arrSaveEntryKey[] = $strFieldKey;
							$arrSaveEntryValue[] = "'".stripslashes($_POST[$strFieldKey])."'";


						}
					}

					if(isset($arrSaveEntryKey) && sizeof($arrSaveEntryKey) > 1) {
						// compile SQL query
						$strSQLQuery = 'INSERT INTO ' . $this->strTableSliders . ' (' . implode(',', $arrSaveEntryKey) . ') VALUES (' . implode(', ', $arrSaveEntryValue) . ')';
					}
				}



				if(isset($strSQLQuery) != false) {
					$result = $wpdb->query( $strSQLQuery );

					if($result != false) {
						$return = true;
					}
				}
				break;
			case 'remove':
				if( isset($_POST['id']) != false || isset($_GET['id']) != false) {
					if( isset($_GET['id']) != false) {
						$arrRemoveID[] = intval($_GET['id']);
					} else {
						foreach($_POST['id'] as $value) {
							$arrRemoveID[] = intval($value);
						}
					}

					$strSQLRemoveQuery = 'DELETE FROM ' . $this->strTableSliders . ' WHERE slider_ID IN (' . implode(', ', $arrRemoveID ) . ')';

					$result = $wpdb->query($strSQLRemoveQuery);

// 					$this->_debug(array('result' => bool2string($result) ) );

					if($result != false) {
						$return = true;
					}
					$wpdb->flush();
				}
				break;
		}

		if($bAjaxResponse == false) {
			return $return;
		} else { // do dat ajax thingy
			$this->ajax_respond($return);
		}
	}

/**
 * AJAX comm
 */

	function ajax_respond($data, $strType = 'XML') {
		$return = false;


		if( is_bool($data) != false ) {
			$arrInput['respond'] = ($data == false) ? 'false' : 'true';
		} elseif( is_array($data) != false) {
			$arrInput = $data;
		} else { // anything else, eg. integer, string or object
			$arrInput['respond'] = $data;
		}

		switch(strtolower($strType)) {
			default: // xml

				$this->generate_XML($arrInput);
				break;
			case 'json':

				$this->generate_JSON($arrInput);
				break;
		}


		return $return;
	}


	function generate_XML($arrData, $bEcho = true, $strCharset = 'utf-8') {
		$return = '';

		// add xml dtd
		$return = '<?xml version="1.0" encoding="' . $strCharset . '" standalone="yes"?>';

		// generate data
		foreach($arrData as $elemKey => $elemValue) {
			$return .= '<'. $elemKey . '>' . $elemValue . '</' . $elemKey . '>' . "\n";
		}

		if($bEcho != false) {
			// send header
			header('Content-Type: application/xml; charset=' . $strCharset);

			// send xml stuff
			echo $return;
		} else {
			return $return;
		}
	}


	function generate_JSON($arrData = array(), $bEcho = true, $strCharset = 'utf-8') {
		$return = '';
		$iElemCount = 0;

		// generate data
		if( sizeof($arrData > 0) ) {
			foreach($arrData as $elemKey => $elemValue) {
				$arrReturn[$iElemCount] = $elemKey . ':';
				if(is_int($elemValue) != false) {
					$arrReturn[$iElemCount] .= $elemValue;
				} else {
					$arrReturn[$iElemCount] .= "'" . $elemValue . "'";
				}
				$iElemCount++;
			}
		}
		if(isset($arrReturn) != false && sizeof($arrReturn) > 0) {
			$return = '{'.implode(',', $arrReturn).'}';
		}

		if($bEcho != false) {
			// send header
			header('Content-Type: application/json; charset=' . strtoupper($strCharset) );

			// send JSON stuff
			echo $return;
		} else {
			return $return;
		}
	}



/**
 * Base functions
 */

	function get_slider_by_shortcode( $strShortcode ) {
		global $wpdb;
		$return = array();

		$strSQLQuery = 'SELECT * FROM ' . $this->strTableSliders . ' WHERE slider_shortcode = "' . $strShortcode . '"';
		$result = $wpdb->get_row($strSQLQuery);

		if($result != false) {
			$return = $result;
		}

		return $return;
	}


	function get_slider( $iSliderID ) {
		global $wpdb;
		$return = array();



		$strSQLQuery = 'SELECT * FROM ' . $this->strTableSliders . ' WHERE slider_ID = ' . intval($iSliderID);
		$result = $wpdb->get_row($strSQLQuery);

		//$this->_debug(array('strSQLQuery' => $strSQLQuery, 'result' => $this->bool2string($result) ) );

		if($result != false) {
			$return = $result;
		}


		return $return;
	}

	function get_sliders( $iLimit = 0 ) {
		global $wpdb;
		$return = array();

		$strSQLQuery = 'SELECT * FROM ' . $this->strTableSliders;


		if($iLimit > 0 && is_int($iLimit) != false) {
			$strSQLQuery .= ' LIMIT ' . $iLimit;
		}

		$strSQLQuery .= ' ORDER BY slider_ID DESC';

		$result = $wpdb->get_results($strSQLQuery);

		if($result != false && is_array($result) != false && sizeof($result) > 0 ) {
			foreach($result as $arrRow) {
				$return[$arrRow->slider_ID] = $arrRow;
			}

		}

		return $return;
	}

	function get_shortcodes( $iLimit = 0 ) {
		global $wpdb;
		$return = array();

		// construct sql query
		$strSQLQuery = 'SELECT slider_shortcode FROM ' . $this->strTableSliders;

		if($iLimit > 0 && is_int($iLimit) != false) {
			$strSQLQuery .= ' LIMIT ' . $iLimit;
		}

		$strSQLQuery .= ' ORDER BY slider_shortcode ASC';

		// fetch data
		$result = $wpdb->get_results($strSQLQuery);

		// prepare data
		if($result != false && is_array($result) != false && sizeof($result) > 0 ) {
			foreach($result as $arrRow) {
				$return[] = $arrRow->slider_shortcode;
			}
		}

		return $return;
	}

	function generate_excerpt($strContent, $strMore = '', $strSentenceFullstop = '.') {
		$return = $strContent;

		if( !empty($strContent ) ) {
			if( strpos($strContent, $strSentenceFullstop) !== false) { // sentence count
				$arrContentParts = explode($strSentenceFullstop, $strContent);

				for($n = 0; $n < $this->iExcerptMaxSentences; $n++) {
					$return .= $arrContentParts[$n] . $strSentenceFullstop . ' ';
				}

				$return = trim($return);
			}

			// char count
			if(count_chars($return) > 100) {
				$return = substr($return, 0, 100);
			}

			$return .= $strMore;
		}

		return $return;
	}

	function bool2string($bValue) {
		$strReturn = '';

		$strReturn = ($bValue ? 'true': 'false');

		/*
		if($bValue == true) {
			$strReturn = 'true';
		} else {
			$strReturn = 'false';
		}

		// */

		return $strReturn;
	}

	function get_table_fields() {
		global $wpdb;
		$return = array();

		$strSQLQuery = 'SHOW COLUMNS FROM ' . $this->strTableSliders;
		$result = $wpdb->get_results($strSQLQuery);

		if(sizeof($result) > 0) {
			foreach($result as $fieldData) {
				if( $fieldData->Field != 'slider_ID' ) {
					$return[] = $fieldData->Field;
				}
			}
		}

		return $return;
	}


/**
 * Settings
 */

	function mxs_install_hook() {
		global $wpdb;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		//exit('bang!');

		if($wpdb->get_var('SHOW TABLES LIKE "' . $this->strTableSliders  . '"') != $this->strTableSliders) {

			// create tables
			$strSQLCreateTable = 'CREATE TABLE ' . $this->strTableSliders . ' ('
								.'`slider_ID` INT(11) NOT NULL AUTO_INCREMENT, '
								.'`slider_title` VARCHAR(100) NOT NULL, '
								.'`slider_shortcode` VARCHAR(100) NOT NULL, '
								.'`slider_content` TEXT NOT NULL, '
								.'PRIMARY KEY ( `slider_ID` ) '
								.') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';


			dbDelta($strSQLCreateTable);
		}

	}

	function mxs_uninstall_hook() {
		global $wpdb;

		$strSQLRemoveTable = 'DROP TABLE ' . $this->strTableSliders;

		$wpdb->query($strSQLRemoveTable);

	}

	/**
 	 * moodmixerSlider Shortcode
 	 *
 	 * @param array $attributes Array of attributes => array('code' => 'slidercode')
 	 * @param string $content Text within enclosing form of shortcode element
 	 *
 	 * @syntax [slider code='slidercode']
 	 *
 	 * @examples 	[my-shortcode]
	 *				[my-shortcode/]
	 *				[my-shortcode foo='bar']
	 *				[my-shortcode foo='bar'/]
	 *				[my-shortcode]content[/my-shortcode]
	 *				[my-shortcode foo='bar']content[/my-shortcode]
	 */

	function mxs_shortcode_handler($arrAttributes, $strContent = null) {
		/*extract(
			shortcode_atts(
				array(
					'code' => 'default_shortcode'
				),
				$arrAttributes
			)
		);*/

		$strShortcode = $arrAttributes['code'];

		$sliderData = $this->get_slider_by_shortcode($strShortcode);

		if($sliderData != false) {
			return '<div class="moodmixer-slider slider-'.str_replace(' ', '-', $strShortcode).'">' . $sliderData->slider_content . '</div>';
		}

	}

	function mxs_load_widgets() {
		register_widget( 'moodmixerSliderWidget' );
	}

/**
 * Debugging
 */
 	function _debug($var, $bEcho = true) {
		$return = '';

		if($bEcho != false) {
			echo '<p>debug:</p><pre>' . htmlentities( print_r($var, true) ) . '</pre>';
			return;
		} else {
			return print_r($var,true);
		}
 	}


 	function is_assoc($array) {
  		return (is_array($array) && (0 !== count(array_diff_key($array, array_keys(array_keys($array)))) || count($array)==0));
	}



}

/**
 * moodmixerSlider Widget
 *
 * @package WordPress 2.9
 */

class moodmixerSliderWidget extends WP_Widget {
	function moodmixerSliderWidget() {
		$widget_ops = array(
			'classname' => 'moodmixer-slider-widget',
			'description' => 'Moodmixer Slider einfügen'
		);

		$control_ops = array( 'slider_id' => 1, 'id_base' => 'moodmixer-slider-widget' );

		$this->WP_Widget( 'moodmixer-slider-widget', 'Moodmixer Slider', $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		global $mxs;
		extract( $args );

		/* User-selected settings. */
		$iSliderID = intval($instance['slider_id']);
		$slider = $mxs->get_slider( $iSliderID );

		/* Before widget (defined by themes). */
		echo $before_widget;

		//$mxs->_debug( array('sliderData' => $slider) );
		echo $slider->slider_content;

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['slider_id'] = intval( $new_instance['slider_id'] );

		return $instance;
	}

/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {
		global $mxs;

		$sliders = $mxs->get_sliders();
		?>

		<!-- Slider Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'slider_id' ); ?>">Slider:</label>
			<select id="<?php echo $this->get_field_id( 'slider_id' ); ?>" name="<?php echo $this->get_field_name( 'slider_id' ); ?>" class="widefat" style="width:100%;">
			<?php foreach($sliders as $iSliderID => $sliderData) { ?>
				<option value="<?php echo $iSliderID; ?>"<?php
				if( intval($instance['slider_id']) == $iSliderID ) {
					echo ' selected="selected"';
				}
				?>><?php echo $sliderData->slider_title; ?></option>
			<?php } ?>
			</select>
		</p>

	<?php
// 		$mxs->_debug( array('instance' => $instance, 'sliders' => $sliders) );
	}
}




/**
 * moodmixerSlider - Template function
 *
 * @param string $sliderCode Code of specific slider (required).
 * @param boolean $echo Echo or return data; default = true.
 * @return mixed If echo is false, either returns the required JS data or an empty string, if sliderCode is not found
 */

function moodmixerSlider($strSliderCode, $bEcho = true) {
	global $mxs;
	$return = '';

	$result = $mxs->get_slider_by_shortcode($strSliderCode);

	if($result != false) {
		$return = $result;
	}

	if($bEcho != false) {
		echo $return;
	} else {
		return $return;
	}
}

$mxs = new moodmixerSlider();
?>