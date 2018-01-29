<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class TCB_Testimonial_Element
 */
class TCB_Testimonial_Element extends TCB_Columns_Element {

	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Testimonial', 'thrive-cb' );
	}

	/**
	 * Return icon class needed for display in menu
	 *
	 * @return string
	 */
	public function icon() {
		return 'testimonials';
	}

	/**
	 * Testimonial element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.thrv_testimonial_shortcode, .thrv-testimonial';
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		$original_components = TCB_Columns_Element::own_components();
		$texts = array(
			' p',
			' li',
			' blockquote',
			' address',
		);
		return array_merge(
			$original_components,
			array(
				'testimonial' => array(
					'config' => array(
						'templates' => array(
							'template_1' => array(
								'name'         => 'Template 1',
								'thumbnail'    => tve_editor_url() . '/editor/css/images/template-1-thumbnail.png',
								'image'        => tve_editor_url() . '/editor/css/images/photo-1-rounded.png',
								'custom-class' => 'thrv-testimonial-template-one',
								'text'         => 'Template 1 Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
								'title'        => 'position',
							),
							'template_2' => array(
								'name'         => 'Template 2',
								'thumbnail'    => tve_editor_url() . '/editor/css/images/template-2-thumbnail.png',
								'image'        => tve_editor_url() . '/editor/css/images/photo1.png',
								'custom-class' => 'thrv-testimonial-template-two',
								'text'         => 'Template 2 Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
								'title'        => 'Position',
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Element category that will be displayed in the sidebar
	 * @return string
	 */
	public function category() {
		return $this->get_thrive_advanced_label();
	}
}
