<?php
/*
Plugin Name: WP-Pro-Quiz
Plugin URI: http://wordpress.org/extend/plugins/wp-pro-quiz
Description: A powerful and beautiful quiz plugin for WordPress.
Version: 0.28
Author: Julius Fischer
Author URI: http://www.it-gecko.de
Text Domain: wp-pro-quiz
Domain Path: /languages
*/

define('WPPROQUIZ_VERSION', '0.28');

// If the WordPress 'SCRIPT_DEBUG' is set then we also set our 'WPPROQUIZ_DEV' so we are serving non-minified scripts
//if ( ( defined( 'SCRIPT_DEBUG' ) ) && ( SCRIPT_DEBUG === true ) && ( !defined( 'WPPROQUIZ_DEV' ) ) ) {
//	define('WPPROQUIZ_DEV', true);
//}

define('WPPROQUIZ_PATH', dirname(__FILE__));
define('WPPROQUIZ_URL', plugins_url('', __FILE__));
define('WPPROQUIZ_FILE', __FILE__);
define('WPPROQUIZ_PPATH', dirname(plugin_basename(__FILE__)));
define('WPPROQUIZ_PLUGIN_PATH', WPPROQUIZ_PATH.'/plugin');
//define('WPPROQUIZ_TEXT_DOMAIN', 'learndash' );

$uploadDir = wp_upload_dir();

define('WPPROQUIZ_CAPTCHA_DIR', $uploadDir['basedir'].'/wp_pro_quiz_captcha');
define('WPPROQUIZ_CAPTCHA_URL', $uploadDir['baseurl'].'/wp_pro_quiz_captcha');

spl_autoload_register('wpProQuiz_autoload');

$WpProQuiz_Answer_types_labels = array();
global $WpProQuiz_Answer_types_labels;

// This is never called. 
//register_activation_hook(__FILE__, array('WpProQuiz_Helper_Upgrade', 'upgrade'));

add_action('plugins_loaded', 'wpProQuiz_pluginLoaded');

if(is_admin()) {
	new WpProQuiz_Controller_Admin();
} else {
	new WpProQuiz_Controller_Front();
}

function wpProQuiz_autoload($class) {
	$c = explode('_', $class);

	if($c === false || count($c) != 3 || $c[0] !== 'WpProQuiz')
		return;

	$dir = '';

	switch ($c[1]) {
		case 'View':
			$dir = 'view';
			break;
		case 'Model':
			$dir = 'model';
			break;
		case 'Helper':
			$dir = 'helper';
			break;
		case 'Controller':
			$dir = 'controller';
			break;
		case 'Plugin':
			$dir = 'plugin';
			break;
		default:
			return;
	}

	if(file_exists(WPPROQUIZ_PATH.'/lib/'.$dir.'/'.$class.'.php'))
		include_once WPPROQUIZ_PATH.'/lib/'.$dir.'/'.$class.'.php';
}

function wpProQuiz_pluginLoaded() {
	
	if ( LEARNDASH_LMS_TEXT_DOMAIN !== LEARNDASH_WPPROQUIZ_TEXT_DOMAIN ) {
		if ((defined('LD_LANG_DIR')) && (LD_LANG_DIR)) {
			load_plugin_textdomain( LEARNDASH_WPPROQUIZ_TEXT_DOMAIN, false, LD_LANG_DIR );
		} else {
			load_plugin_textdomain( LEARNDASH_WPPROQUIZ_TEXT_DOMAIN, false, WPPROQUIZ_PPATH.'/languages');
		}
	}
	
	if(get_option('wpProQuiz_version') !== WPPROQUIZ_VERSION) {
		WpProQuiz_Helper_Upgrade::upgrade();
	}
	
	global $WpProQuiz_Answer_types_labels;
	$WpProQuiz_Answer_types_labels = array(
		'single' 				=> 	esc_html__('Single choice', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN),
		'multiple' 				=>	esc_html__('Multiple choice', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN),
		'free_answer'			=>	esc_html__('"Free" choice', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN),
		'sort_answer'			=>	esc_html__('"Sorting" choice', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN),
		'matrix_sort_answer' 	=>	esc_html__('"Matrix Sorting" choice', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN),
		'cloze_answer'			=>	esc_html__('Fill in the blank', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN),
		'assessment_answer' 	=>	esc_html__('Assessment', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN),
		'essay'					=>	esc_html__('Essay / Open Answer', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN)
	);
	
	
// 	//ACHIEVEMENTS Version 2.x.x
// 	if(defined('ACHIEVEMENTS_IS_INSTALLED') && ACHIEVEMENTS_IS_INSTALLED === 1 && defined('ACHIEVEMENTS_VERSION')) {
// 		$version = ACHIEVEMENTS_VERSION;
// 		if($version{0} == '2') {
// 			new WpProQuiz_Plugin_BpAchievementsV2();
// 		}
// 	}

	
}

function wpProQuiz_achievementsV3() {
	achievements()->extensions->wp_pro_quiz = new WpProQuiz_Plugin_BpAchievementsV3();

	do_action('wpProQuiz_achievementsV3');
}

add_action('dpa_ready', 'wpProQuiz_achievementsV3');

// //ACHIEVEMENTS Version 2.x.x
// $bpAchievementsV2_path = realpath(ABSPATH.PLUGINDIR.'/achievements/loader.php');

// if($bpAchievementsV2_path !== false) {
// 	register_deactivation_hook($bpAchievementsV2_path, array('WpProQuiz_Plugin_BpAchievementsV2', 'deinstall'));
// 	register_activation_hook($bpAchievementsV2_path, array('WpProQuiz_Plugin_BpAchievementsV2', 'install'));
// }


/**
 * Format the Quiz Cloze type answers into an array to be used when comparing responses. 
 * @ since 2.5
 * copied from WpProQuiz_View_FrontQuiz
 */
function fetchQuestionCloze( $answer_text, $convert_to_lower = true ) {
	preg_match_all( '#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', $answer_text, $matches, PREG_SET_ORDER );

	$data = array();

	foreach ( $matches as $k => $v ) {
		$text    = $v[1];
		$points  = ! empty( $v[2] ) ? (int) $v[2] : 1;
		$rowText = $multiTextData = array();
		$len     = array();

		if ( preg_match_all( '#\[(.*?)\]#im', $text, $multiTextMatches ) ) {
			foreach ( $multiTextMatches[1] as $multiText ) {
				$multiText_clean = trim( html_entity_decode( $multiText, ENT_QUOTES ) );
				
				if ( apply_filters('learndash_quiz_question_cloze_answers_to_lowercase', $convert_to_lower ) ) {
					if ( function_exists( 'mb_strtolower' ) )
						$x = mb_strtolower( $multiText_clean );
					else
						$x = strtolower( $multiText_clean );
				} else {
					$x = $multiText_clean;
				}
				
				$len[]           = strlen( $x );
				$multiTextData[] = $x;
				$rowText[]       = $multiText;
			}
		} else {
			$text_clean = trim( html_entity_decode( $text, ENT_QUOTES ) );
			if ( apply_filters('learndash_quiz_question_cloze_answers_to_lowercase', $convert_to_lower ) ) {
				if ( function_exists( 'mb_strtolower' ) )
					$x = mb_strtolower( trim( html_entity_decode( $text_clean, ENT_QUOTES ) ) );
				else
					$x = strtolower( trim( html_entity_decode( $text_clean, ENT_QUOTES ) ) );
			} else {
				$x = $text_clean;
			}
			
			$len[]           = strlen( $x );
			$multiTextData[] = $x;
			$rowText[]       = $text;
		}

		$a = '<span class="wpProQuiz_cloze"><input data-wordlen="' . max( $len ) . '" type="text" value=""> ';
		$a .= '<span class="wpProQuiz_clozeCorrect" style="display: none;"></span></span>';

		$data['correct'][] = $multiTextData;
		$data['points'][]  = $points;
		$data['data'][]    = $a;
	}

	$data['replace'] = preg_replace( '#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', '@@wpProQuizCloze@@', $answer_text );

	return $data;
}
