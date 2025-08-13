<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Add Elementor Widget
 */
class Essential_Grid_Builders_Elementor_Widget extends \Elementor\Widget_Shortcode {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'essgrid';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return esc_html__( 'Essential Grid', 'essential-grid' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * @inheritDoc
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * function _register_controls() is deprecated since 3.1.0 of Elementor
	 *
	 * @return void
	 */
	protected function _register_controls() {
		$this->register_controls();
	}

	/**
	 * @inheritDoc
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => $this->get_title(),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'essgrid_title',
			array(
				'label'       => __( 'Selected Grid:', 'essential-grid' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'render_type' => 'none',
				'placeholder' => '',
				'default'     => '',
				'event'       => 'themepunch.esg.selectgrid',
			)
		);

		$this->add_control(
			'shortcode',
			array(
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label'       => __( 'Shortcode', 'essential-grid' ),
				'dynamic'     => [ 'active' => true ],
				'placeholder' => '',
				'default'     => '',
			)
		);

		$this->add_control(
			'select_grid',
			array(
				'type'        => \Elementor\Controls_Manager::BUTTON,
				'button_type' => 'default',
				'text'        => __( '<i type="button" class="material-icons">cached</i> Select Grid', 'essential-grid' ),
				'event'       => 'themepunch.esg.selectgrid',
			)
		);

		$this->add_control(
			'edit_grid',
			array(
				'type'        => \Elementor\Controls_Manager::BUTTON,
				'button_type' => 'default',
				'text'        => __( '<i type="button" class="material-icons">edit</i> Edit Grid', 'essential-grid' ),
				'event'       => 'themepunch.esg.editgrid',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$shortcode = $this->get_settings_for_display( 'shortcode' );
		$shortcode = do_shortcode( shortcode_unautop( $shortcode ) );

		// opens esg builder popup when user adds grid widget to page
		if ( empty( $shortcode ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<script>window.parent.essgridOpenPopup();</script>';
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="wp-block-themepunch-essgrid">' . $shortcode . '</div>';

	}

}
