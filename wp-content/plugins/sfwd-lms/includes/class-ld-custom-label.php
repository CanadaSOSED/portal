<?php
/**
* LearnDash Custom Label class.
*/
class LearnDash_Custom_Label {
	/**
	 * Construct
	 */
	public function __construct() {
	}

	/**
	 * Get label based on key name
	 *
	 * @param  string $key Key name of setting field.
	 * @return string Label entered on settings page.
	 */
	public static function get_label( $key ) {
		$labels = array();
		$key    = strtolower( $key );

		// The Setting logic for custom labels moved to includes/settings/class-ld-settings-section-custom-labels.php as of V2.4.
		$labels[ $key ] = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_Custom_Labels', $key );

		switch ( $key ) {
			case 'course':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Course', 'learndash' );
				break;

			case 'courses':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Courses', 'learndash' );
				break;

			case 'lesson':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Lesson', 'learndash' );
				break;

			case 'lessons':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Lessons', 'learndash' );
				break;

			case 'topic':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Topic', 'learndash' );
				break;

			case 'topics':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Topics', 'learndash' );
				break;

			case 'quiz':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Quiz', 'learndash' );
				break;

			case 'quizzes':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Quizzes', 'learndash' );
				break;

			case 'question':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Question', 'learndash' );
				break;

			case 'questions':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Questions', 'learndash' );
				break;

			case 'button_take_this_course':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Take this Course', 'learndash' );
				break;

			case 'button_mark_complete':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Mark Complete', 'learndash' );
				break;

			case 'button_click_here_to_continue':
				$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Click Here to Continue', 'learndash' );
				break;

			default:
				$label = '';
		}

		return $label;
	}

	/**
	 * Get slug-ready string
	 *
	 * @param  string $key Key name of setting field.
	 * @return string      Lowercase string
	 */
	public static function label_to_lower( $key ) {
		$label = strtolower( self::get_label( $key ) );
		return $label;
	}

	/**
	 * Get slug-ready string
	 *
	 * @param  string $key Key name of setting field.
	 * @return string      Slug-ready string
	 */
	public static function label_to_slug( $key ) {
		$label = sanitize_title( self::get_label( $key ) );
		return $label;
	}
}

add_action( 'plugins_loaded', function() {
	new LearnDash_Custom_Label();
} );

/**
 * Utility function to get a custom field label.
 *
 * @since 2.6.0
 * @param string $field Field label to retreive.
 * @return string Field label. Empty of none found.
 */
function learndash_get_custom_label( $field = '' ) {
	return LearnDash_Custom_Label::get_label( $field );
}

/**
 * Utility function to get a custom field label lowercase.
 *
 * @since 2.6.0
 * @param string $field Field label to retreive.
 * @return string Field label. Empty of none found.
 */
function learndash_get_custom_label_lower( $field = '' ) {
	return LearnDash_Custom_Label::label_to_lower( $field );
}

/**
 * Utility function to get a custom field label slug.
 *
 * @since 2.6.0
 * @param string $field Field label to retreive.
 * @return string Field label. Empty of none found.
 */
function learndash_get_custom_label_slug( $field = '' ) {
	return LearnDash_Custom_Label::label_to_slug( $field );
}
