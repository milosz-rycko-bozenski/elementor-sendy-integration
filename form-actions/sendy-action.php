<?php
class Simply_Sendy_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {

	public function get_name() {
		return 'sendy';
	}

	public function get_label() {
		return esc_html__( 'Sendy', 'elementor-forms-sendy-action' );
	}

	public function register_settings_section( $widget ) {

		$widget->start_controls_section(
			'section_sendy',
			[
				'label' => esc_html__( 'Sendy', 'elementor-forms-sendy-action' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'sendy_url',
			[
				'label' => esc_html__( 'Sendy URL', 'elementor-forms-sendy-action' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'https://your_sendy_installation/',
				'description' => esc_html__( 'Enter the URL where you have Sendy installed.', 'elementor-forms-sendy-action' ),
				'default' => defined( 'SENDY_URL' ) ? constant( 'SENDY_URL' ) : '',
			]
		);
		
		$widget->add_control(
			'sendy_api_key',
			[
				'label' => 'Sendy API key',
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => defined( 'SENDY_API_KEY' ) ? constant( 'SENDY_API_KEY' ) : '',
			]
		);

		$widget->add_control(
			'sendy_list',
			[
				'label' => esc_html__( 'Sendy List ID', 'elementor-forms-sendy-action' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'The list ID you want to subscribe a user to. This encrypted & hashed ID can be found under "View all lists" section.', 'elementor-forms-sendy-action' ),
				'default' => defined( 'SENDY_DEFAULT_LIST' ) ? constant( 'SENDY_DEFAULT_LIST' ) : '',
			]
		);

		$widget->add_control(
			'sendy_email_field',
			[
				'label' => esc_html__( 'Email Field ID', 'elementor-forms-sendy-action' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$widget->add_control(
			'sendy_name_field',
			[
				'label' => esc_html__( 'Name Field ID', 'elementor-forms-sendy-action' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$widget->end_controls_section();

	}

	public function run( $record, $ajax_handler ) {

		$settings = $record->get( 'form_settings' );

		if ( empty( $settings[ 'sendy_url' ] ) ) {
			return;
		}

		if ( empty( $settings[ 'sendy_list' ] ) ) {
			return;
		}

		if ( empty( $settings[ 'sendy_email_field' ] ) ) {
			return;
		}

		$raw_fields = $record->get( 'fields' );

		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field[ 'value' ];
		}

		if ( empty( $fields[ $settings[ 'sendy_email_field' ] ] ) ) {
			return;
		}

		$sendy_data = [
			'email' => $fields[ $settings[ 'sendy_email_field' ] ],
			'name' => $fields[ $settings[ 'sendy_name_field' ] ],
			'api_key' => $settings[ 'sendy_api_key' ],
			'list' => $settings[ 'sendy_list' ],
			'ipaddress' => \ElementorPro\Core\Utils::get_client_ip(),
			'referrer' => isset( $_POST[ 'referrer' ] ) ? $_POST[ 'referrer' ] : '',
		];

		if ( empty( $fields[ $settings[ 'sendy_name_field' ] ] ) ) {
			$sendy_data['name'] = $fields[ $settings['sendy_name_field'] ];
		}

		$request = wp_remote_post(
			$settings[ 'sendy_url' ] . 'subscribe',
			[
				'body' => $sendy_data,
			]
		);
		
	}

	public function on_export( $element ) {

		unset(
			$element[ 'sendy_url' ],
			$element[ 'sendy_list' ],
			$element[ 'sendy_api_key' ],
			$element[ 'sendy_email_field' ],
			$element[ 'sendy_name_field' ],
		);

		return $element;

	}

}
