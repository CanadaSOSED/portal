<?php

/**
 * Elements of Config sidebar UI
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Config_Elements {

	/**
	 * Display configuration options including add-ons.
	 *
	 * @param $kb_config
	 * @param $feature_specs
	 * @param array $args
	 * @param string $kb_page_layout
	 */
	public function option_group_filter( $kb_config, $feature_specs, $args = array(), $kb_page_layout='' ) {

		// let add-on adjust or replace KB Configuration
		if ( ! empty($args['class']) ) {
			$add_on_config = apply_filters( 'eckb_kb_config_option_group', $args, $kb_config, $kb_page_layout );
			$args = ( ! empty( $add_on_config ) && is_array( $add_on_config ) ) ? $add_on_config : $args;
		}

		$this->option_group( $feature_specs, $args );
	}

	/**
	 * Display configuration options
	 * @param $feature_specs
	 * @param array $args
	 */
	public function option_group( $feature_specs, $args = array() ) {

		$defaults = array(
            'info' => '',
	        'option-heading' => '',
            'class' => ' '
        );
		$args = array_merge( $defaults, $args );

		// there might be multiple classes
		$classes = explode(' ', $args['class']);
		$class_string = '';
		foreach( $classes as $class ) {
			$class_string .= $class . '-content ';
		}		?>

        <div class="config-option-group <?php echo $class_string; ?>">	        <?php
	        
            if ( $args['option-heading'] ) {    ?>
                <div class="config-option-heading">
                    <h4><?php echo __( $args['option-heading'], 'echo-knowledge-base' ); ?></h4>
                    <span class="ep_font_icon_info option-info-icon"></span>
                </div>            <?php

            } else {     ?>
                <div class="config-option-info">
                    <span class="ep_font_icon_info option-info-icon"></span>
                </div>            <?php

            }           ?>

            <div class="option-info-content hidden">
	            <h5 class="option-info-title"><?php _e( 'Help', 'echo-knowledge-base' ); ?></h5>                    <?php

                if ( $feature_specs ) {
                    if ( is_array( $args['info']) ) {
	                    foreach( $args['info'] as $item ) {
		                    if ( empty($feature_specs[$item]) ) {
			                    continue;
		                    }

		                    echo '<h6 style="padding-top:20px;">' . $feature_specs[$item]['label'] . '</h6>';
		                    echo '<p>' . $feature_specs[$item]['info'] . '</p>';
	                    }
                    } else {
	                    echo '<p>' .$args['info']. '</p>';
                    }
                }		            ?>

            </div>            <?php

            foreach ( $args['inputs'] as $input ) {
                echo $input;
            }   ?>

        </div><!-- config-option-group -->        <?php
	}

	/**
	 * Display configuration options
	 * @param $feature_specs
	 * @param array $args
	 */
	public function option_group_wizard( $feature_specs, $args = array() ) {

		$defaults = array(
			'info' => '',
			'option-heading' => '',
			'class' => ' ',
			'addition_info' => '',
		);
		$args = array_merge( $defaults, $args );

		// there might be multiple classes
		$classes = explode(' ', $args['class']);
		$class_string = '';
		foreach( $classes as $class ) {
			$class_string .= $class . '-content ';
		}

		$depends = '';
		
		if ( isset($args['depends']) ) {
			$depends = "data-depends='" . htmlspecialchars( json_encode( $args['depends'] ), ENT_QUOTES, 'UTF-8' ) . "'";
		}		?>

		<div class="<?php echo $class_string; ?>" <?php echo $depends; ?>>	        <?php

			if ( $args['option-heading'] ) {    ?>
				<div class="eckb-wizard-option-heading">
					<h4><?php echo __( $args['option-heading'], 'echo-knowledge-base' ); ?>
						<span class="epkbfa epkbfa-caret-right"></span>
						<span class="epkbfa epkbfa-caret-down"></span>
					</h4>
					<span class="ep_font_icon_info option-info-icon"></span>
				</div>            <?php

			} else {     ?>
				<div class="config-option-info">
					<span class="ep_font_icon_info option-info-icon"></span>
				</div>            <?php

			}           ?>

			<div class="option-info-content hidden">
				<h5 class="option-info-title"><?php _e( 'Help', 'echo-knowledge-base' ); ?></h5>                    <?php
				if ( $feature_specs ) {
					if ( is_array( $args['info']) ) {
						foreach( $args['info'] as $item ) {
							if ( empty($feature_specs[$item]) ) {
								continue;
							}
							echo '<h6>' . $feature_specs[$item]['label'] . '</h6>';
							echo '<p>' . $feature_specs[$item]['info'] . '</p>';
						}
					} else {
						echo '<p>' .$args['info']. '</p>';
					}
				}		            ?>
			</div>            <?php

			foreach ( $args['inputs'] as $input ) {
				echo $input;
			}

			// Add content after Settings
			if ( ! empty($args['addition_info']) ) {
				echo '<div class="eckb-wizard-default-note">' . $args['addition_info'] . '</div>';
			}		?>

		</div><!-- config-option-group -->        <?php
	}

	private function add_defaults( array $input_array, array $custom_defaults=array() ) {

		$defaults = array(
			'id'                => '',
			'name'              => 'text',
			'value'             => '',
			'label'             => '',
			'title'             => '',
			'class'             => '',
			'main_label_class'  => '',
			'label_class'       => '',
			'input_class'       => '',
			'input_group_class' => '',
			'radio_class'       => '',
			'action_class'      => '',
			'desc'              => '',
			'info'              => '',
			'placeholder'       => '',
			'readonly'          => false,  // will not be submitted
			'required'          => '',
			'autocomplete'      => false,
			'data'              => array(),
			'disabled'          => false,
			'size'              => 3,
			'max'               => 50,
			'current'           => null,
			'options'           => array()
		);
		$defaults = array_merge( $defaults, $custom_defaults );
		return array_merge( $defaults, $input_array );
	}

	private function add_common_defaults( array $input_array, array $custom_defaults=array() ) {
		$defaults = array(
			'id'                => '',
			'name'              => 'text',
			'value'             => '',
			'label'             => '',
			'title'             => '',
			'class'             => '',
			'main_label_class'  => '',
			'label_class'       => '',
			'input_class'       => '',
			'input_group_class' => '',
			'desc'              => '',
			'info'              => '',
			'placeholder'       => '',
			'readonly'          => false,  // will not be submitted
			'required'          => '',
			'autocomplete'      => false,
			'data'              => array(),
			'disabled'          => false,
			'size'              => 3,
			'max'               => 50,
			'current'           => null,
			'options'           => array()
		);
		$defaults = array_merge( $defaults, $custom_defaults );
		return array_merge( $defaults, $input_array );
	}

	/**
	 * Renders an HTML Text field
	 *
	 * @param array $args Arguments for the text field
	 * @return string Text field
	 */
	public function text( $args = array() ) {

		$args = $this->add_defaults( $args );

		$id             =  esc_attr( $args['name'] );
		$autocomplete   = ( $args['autocomplete'] ? 'on' : 'off' );
		$readonly       = $args['readonly'] ? ' readonly' : '';

		$data = '';
		foreach ( $args['data'] as $key => $value ) {
			$data .= 'data-' . $key . '="' . $value . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-single-text-example ';
		}

		ob_start();		?>

		<div class="config-input-group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo $id ?>_group" >			<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-single-dropdown-text__icon epkbfa epkbfa-eye"></div>';
			}   ?>

			<label class="<?php echo esc_html( $args['label_class'] )?>" for="<?php echo $id ?>">
				<?php echo esc_html( $args['label'] )?>
			</label>

			<div class="input_container <?php echo esc_html( $args['input_class'] ); ?>" id="">
				<input type="text"
				       name=        "<?php echo $id; ?>"
				       id=          "<?php echo $id; ?>"
				       autocomplete="<?php echo $autocomplete; ?>"
				       value=       "<?php echo esc_attr( $args['value'] ); ?>"
				       placeholder= "<?php echo esc_attr( $args['placeholder'] ); ?>"						<?php
						echo $data . ' ' . $readonly						?>
                       maxlength=   "<?php echo $args['max']; ?>"/>
			</div>

		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Renders an HTML textarea
	 *
	 * @param array $args Arguments for the textarea
	 * @return string textarea
	 */
	public function textarea( $args = array() ) {

		$defaults = array(
			'name'        => 'textarea',
			'class'       => 'large-text',
			'rows'        => 4
		);
		$args = $this->add_defaults( $args, $defaults );

		$disabled = '';
		if( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}

		$id =  esc_attr( $args['name'] );
		
		$data = '';
		foreach ( $args['data'] as $key => $value ) {
			$data .= 'data-' . $key . '="' . $value . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-single-text-example ';
		}
		
		ob_start();
		$inputText = trim($args['value']);  ?>

		<div class="config-input-group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo $id ?>_group" ><?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-single-dropdown-text__icon epkbfa epkbfa-eye"></div>';
			}   ?>

		<label class="<?php echo esc_html( $args['label_class'] )?>" for="<?php echo $id ?>">
			<?php echo esc_html( $args['label'] )?>
		</label>
			<div class="input_container <?php echo esc_html( $args['input_class'] )?>" id="">
				<textarea
					   rows="<?php echo esc_attr( $args['rows'] ); ?>"
				       name="<?php echo esc_attr( $args['name'] ); ?>"
				       id="<?php echo $id ?>"
				       placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
					<?php echo $data . ' ' . $disabled; ?> >
					<?php echo esc_attr( $inputText ); ?>
				</textarea>
			</div>

		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Renders an HTML Checkbox
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function checkbox( $args = array() ) {

		$defaults = array(
			'name'         => 'checkbox',
			'class'        => '',
			'disabled'     => false
		);
		$args = $this->add_defaults( $args, $defaults );
		$id             =  esc_attr( $args['name'] );
		$checked = checked( "on", $args['value'], false );
		
		if ( $args['disabled'] ) {
			$disabled = 'disabled="disabled"';
			$args['input_group_class'] .= ' eckb-wizard-single-checkbox-disabled ';
		} else {
			$disabled = '';
		}
		
		
		$data = '';
		foreach ( $args['data'] as $key => $value ) {
			$data .= 'data-' . $key . '="' . $value . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-single-checkbox-example ';
		}

		ob_start();		?>

		<div class="config-input-group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo $id; ?>_group">			<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-single-checkbox-example__icon epkbfa epkbfa-eye"></div>';
			}			?>

			<label class="<?php echo esc_html( $args['label_class'] )?>" for="<?php echo $id; ?>">
				<?php echo esc_html( $args['label'] ); ?>
			</label>

			<div class="input_container <?php echo esc_html( $args['input_class'] )?>" id="">
				<input type="checkbox"
				       name="<?php echo $id ?>"
				       id="<?php echo $id ?>"
				       value="on"
				       <?php echo $data . ' ' . $checked . ' ' . $disabled; ?> />
			</div>

		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Renders an HTML radio button
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function radio_button( $args = array() ) {

		$defaults = array(
			'name'         => 'radio-buttons',
			'class'        => '',
		);
		$args = $this->add_defaults( $args, $defaults );
		$id =  esc_attr( $args['id'] );
		$name =  esc_attr( $args['name'] );
		$checked = checked( 1, $args['value'], false );

		$data = '';
		foreach ( $args['data'] as $key => $value ) {
			$data .= 'data-' . $key . '="' . $value . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-single-radio-btn-example ';
		}

		ob_start();		?>

		<div class="config-input-group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo $id ?>_group">			<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-single-radio-btn-example__icon epkbfa epkbfa-eye"></div>';
			}			?>

			<input type="radio" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo esc_attr( $args['value'] ); ?>" <?php echo $data . ' ' . $checked; ?> />

			<label class="<?php echo esc_attr( $args['label_class'] )?>" for="<?php echo $id ?>"><?php echo esc_html( $args['label'] )?></label>

		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Renders an HTML drop-down box
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function dropdown( $args = array() ) {

		$defaults = array(
			'name'         => 'select',
		);
		$args = $this->add_defaults( $args, $defaults );

		$id =  esc_attr( $args['name'] );
		
		$data = '';
		foreach ( $args['data'] as $key => $value ) {
			$data .= 'data-' . $key . '="' . $value . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-single-dropdown-example ';
		}

		ob_start();		?>

		<div class="config-input-group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo $id ?>_group">			<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-single-dropdown-example__icon epkbfa epkbfa-eye"></div>';
			}			?>

			<label class="<?php echo esc_html( $args['label_class'] )?>" for="<?php echo $id ?>">
				<?php echo esc_html( $args['label'] )?>
			</label>

			<div class="input_container <?php echo esc_html( $args['input_class'] )?>" id="">

				<select name="<?php echo $id ?>" id="<?php echo $id ?>" <?php echo $data; ?>>     <?php
					foreach( $args['options'] as $key => $label ) {
						$selected = selected( $key, $args['current'], false );
						echo '<option value="' . esc_attr($key) . '"' . $selected . '>' . esc_html($label) . '</option>';
					}  ?>
				</select>
			</div>
		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Renders several HTML radio buttons in a row
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function radio_buttons_horizontal( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
			'main_label_class'  => '',
			'radio_class'       => '',
		);
		$args = $this->add_defaults( $args, $defaults );
		$id =  esc_attr( $args['name'] );
		$ix = 0;
		
		$data = '';
		foreach ( $args['data'] as $key => $value ) {
			$data .= 'data-' . $key . '="' . $value . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-radio-btn-horizontal-example ';
		}

        ob_start();        ?>

		<div class="config-input-group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo $id ?>_group" >		<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-radio-btn-horizontal-example__icon epkbfa epkbfa-eye"></div>';
			}			?>

			<span class="main_label <?php echo esc_html( $args['main_label_class'] )?>">
				<?php echo esc_html( $args['label'] ); ?>
			</span>

			<div class="radio-buttons-horizontal <?php echo esc_html( $args['input_class'] )?>" id="<?php echo $id ?>">					<?php

				foreach( $args['options'] as $key => $label ) {
					$checked = checked( $key, $args['current'], false );					?>

					<div class="<?php echo esc_html( $args['radio_class'] )?>">						<?php

						$checked_class ='';
						if ($args['current'] == $key ) {
							$checked_class ='checked-radio';
						}       ?>

						<div class="input_container <?php echo $checked_class; ?>">
							<input type="radio"
							       name="<?php echo esc_attr( $args['name'] ); ?>"
							       id="<?php echo $id.$ix; ?>"
							       value="<?php echo esc_attr( $key ); ?>"  <?php
									echo $data . ' ' . $checked;	?>/>
						</div>

						<label class="<?php echo esc_html( $args['label_class'] )?>" for="<?php echo $id.$ix ?>">
							<?php echo esc_html( $label )?>
						</label>
					</div>						<?php

					$ix++;
				} //foreach    	?>

			</div>

		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Renders several HTML radio buttons in a column
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function radio_buttons_vertical( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
			'main_label_class'  => '',
			'radio_class'       => '',
		);
		$args = $this->add_defaults( $args, $defaults );
		$id =  esc_attr( $args['name'] );
		$ix = 0;
		
		$data = '';
		foreach ( $args['data'] as $key => $value ) {
			$data .= 'data-' . $key . '="' . $value . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-radio-btn-vertical-example ';
		}

		ob_start();		?>

		<div class="config-input-group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo $id ?>_group" >		<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-radio-btn-vertical-example__icon epkbfa epkbfa-eye"></div>';
			}

	        if ( ! empty($args['label']) ) {     ?>
				<span class="main_label <?php echo esc_html( $args['main_label_class'] )?>">
					<?php echo esc_html( $args['label'] ); ?>
				</span>            <?php
	        }                       ?>

			<div class="radio-buttons-vertical <?php echo esc_html( $args['input_class'] )?>" id="<?php echo $id ?>">
                <ul>	                <?php

	                foreach( $args['options'] as $key => $label ) {
		                $checked = checked( $key, $args['current'], false );		                ?>

                        <li class="<?php echo esc_html( $args['radio_class'] )?>">			                <?php

			                $checked_class ='';
			                if ($args['current'] == $key ) {
				                $checked_class ='checked-radio';
			                } ?>

                            <div class="input_container config-col-1 <?php echo $checked_class; ?>">
                                <input type="radio"
                                       name="<?php echo esc_attr( $args['name'] ); ?>"
                                       id="<?php echo $id . $ix; ?>"
                                       value="<?php echo esc_attr( $key ); ?>"					                <?php
                                       echo $data . ' ' . $checked; ?> />
                            </div>
                            <label class="<?php echo esc_html( $args['label_class'] )?> config-col-10" for="<?php echo $id.$ix ?>">
				                <?php echo esc_html( $label )?>
                            </label>
                        </li>		                <?php

		                $ix++;
	                } //foreach	                ?>

                </ul>

			</div>

		</div>        <?php

		return ob_get_clean();
	}

	/**
	 * Display KB Settings Box
	 *
	 * @param array $args
	 */
	public function kb_setting_box( $args ) {		?>
		
		<div id="<?php echo $args[ 'box_id' ]; ?>" class="epkb-settings-box">
			<div class="epkb-sb-title">
				<h4><?php echo $args[ 'heading' ]; ?></h4>
				<span class="epkb-sb-info">
				    <a href="<?php echo $args[ 'link_url' ]; ?>" target="_blank"><span class="ep_font_icon_info"></span></a>
			    </span>
			</div>
			<div class="epkb-sb-content">
				<?php foreach( $args[ 'inputs' ] as $input ){
					echo $input;
				} ?>
			</div>
		</div>
		<?php
	}

	// deprecated ?
	public function radio_buttons_icons_list( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
			'main_label_class'  => '',
			'radio_class'       => '',
		);
		$args = $this->add_defaults( $args, $defaults );
		$id =  esc_attr( $args['name'] );
		$ix = 0;

		ob_start();		?>

		<div class="config-input-group <?php echo esc_html( $args['input_group_class'] );  ?>" id="<?php echo $id;  ?>_group" >            <?php

	        if ( ! empty($args['label']) ) {     ?>
				<span class="main_label <?php echo esc_html( $args['main_label_class'] );  ?>">
					<?php echo wp_kses( $args['label'], array('span' => array( 'class' => array() ) ) ); ?>
				</span>            <?php
	        }                       ?>

			<div class="radio-buttons-vertical <?php echo esc_html( $args['input_class'] )?>" id="<?php echo $id;  ?>">
                <ul>	                <?php

	                foreach( $args['options'] as $key => $label ) {
		                $checked = checked( $key, $args['current'], false );		                ?>

                        <li class="<?php echo esc_html( $args['radio_class'] );  ?>">			                <?php

			                $checked_class ='';
			                if ( $args['current'] == $key ) {
				                $checked_class ='checked-radio';
			                }   ?>

                            <div class="input_container<?php echo $checked_class; ?>">
                                <input type="radio"
                                       name="<?php echo esc_attr( $args['name'] ); ?>"
                                       id="<?php echo $id . $ix; ?>"
                                       value="<?php echo esc_attr( $key ); ?>"					                <?php
                                       echo $checked; ?> />
	                            <label class="<?php echo esc_html( $args['label_class'] )?> config-col-10" for="<?php echo $id.$ix; ?>">
		                            <i class="epkbfa <?php echo str_replace( ' fa-','epkbfa-' ,esc_html( $key ) );  ?>"></i>
		                            <span class="epkb-label"><?php echo EPKB_Icons::format_font_awesome_icon_name( $label );        ?></span>
	                            </label>
                            </div>

                        </li>		                <?php

		                $ix++;
	                } //foreach	                ?>

                </ul>
			</div>

		</div>        <?php

		return ob_get_clean();
	}

	/**
	 * Renders several HTML radio buttons in a column
	 *
	 * @param array $args
	 * @return string
	 */
	public function radio_buttons_vertical_v2( $args = array() ) {

		$defaults = array(
			'id'           => 'radio',
			'name'         => 'radio-buttons',
			'class'        => '',
		);
		$args = $this->add_defaults( $args, $defaults );

		$ix = 0;
		$id =  esc_attr( $args['name'] );
		
		
		$data = '';
		foreach ( $args['data'] as $key => $value ) {
			$data .= 'data-' . $key . '="' . $value . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-radio-btn-vertical-v2-example ';
		}
		
		if ( $args['return_html'] ) {
			ob_start();
		}		?>

		<li class="input_group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo $id ?>_group" >			<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-radio-btn-vertical-v2-example__icon epkbfa epkbfa-eye"></div>';
			}			?>

			<span class="main_label <?php echo esc_html( $args['main_label_class'] )?>"><?php echo esc_html( $args['label'] ); ?></span>

			<div class="radio-buttons-vertical <?php echo esc_html( $args['input_class'] )?>" id="<?php echo $id ?>">
				<ul>					<?php

					foreach( $args['options'] as $key => $label ) {
						$id = empty($args['name']) ? '' :  esc_attr($args['name'] ) . '_choice_' . $ix;
						$checked = checked( $key, $args['current'], false );
						$checked_list   = '';

						if( $args['current'] == $label ) {
							$checked_list = 'epkb-radio-checked';
						}						?>

						<li class="<?php echo esc_html( $args['radio_class'] ).' '.$checked_list; ?>">

							<input type="radio"
							       name="<?php echo esc_attr( $args['name'] ); ?>"
							       id="<?php echo $id; ?>"
							       value="<?php echo esc_attr( $key ); ?>"									<?php
								echo $data . ' ' . $checked;	?>
							/>

							<label class="<?php echo esc_html( $args['label_class'] )?>" for="<?php echo $id; ?>">
								<?php echo esc_html( $label )?>
							</label>
						</li>						<?php

						$ix++;
					}//foreach					?>

				</ul>
			</div>

		</li>		<?php
		if ( $args['return_html'] ) {
			return ob_get_clean();
		}
		return '';
	}

	public function multiple_number_inputs($args = array() , $inputs = array() ) {
		ob_start();
		
		$defaults = array(
			'name'         => 'text',
			'class'        => '',
		);
		$args = $this->add_common_defaults( $args, $defaults );
		
		$data = '';
		foreach ( $args['data'] as $key => $value ) {
			$data .= 'data-' . $key . '="' . $value . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-multiple-number-group-example ';
		}		?>

        <div class="config-input-group eckb-multiple-number-group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo $args['id']; ?>_group" >	        <?php

	        if ( ! empty($args['data']['example_image']) ) {
		        echo '<div class="eckb-wizard-multiple-number-group-example__icon epkbfa epkbfa-eye"></div>';
	        }	        ?>

	        <span class="main_label <?php echo esc_html( $args['main_label_class'] )?>"><?php echo esc_html( $args['label'] ); ?></span>
            <div class="number-inputs-container">                <?php

                foreach( $inputs as $input ) {
					
					// rewrite $data if need 
					if ( ! empty( $input['data'] ) ) {
						$input_data = '';
						foreach ( $input['data'] as $key => $value ) {
							$input_data .= 'data-' . $key . '="' . $value . '" ';
						}
					} else {
						$input_data = $data;
					}
					
                    echo '<div class="number-input">';
                        echo '<input type="number" name="'.esc_attr( $input['name'] ).'" id="'.esc_attr( $input['name'] ).'" value="'.esc_attr( $input['value'] ).'" ' . $input_data . '>';
                        echo '<label for="'.esc_attr( $input['name'] ).'">'.esc_html( $input['label'] ).'</label>';
                    echo '</div>';
                }                ?>

            </div>
        </div>		<?php

		return ob_get_clean();
	}

	/**
	 * Single Inputs for text_fields_horizontal function
	 *
	 * @param array $args
	 * @return string
	 */
	public function horizontal_text_input( $args = array() ){

		$args = $this->add_defaults( $args );

		//Set Values
		$id             =  esc_attr( $args[ 'name' ] );
		$autocomplete   = ( $args[ 'autocomplete' ] ? 'on' : 'off' );
		$disabled       = $args[ 'disabled' ] ? ' disabled="disabled"' : '';

		$data = '';
		foreach ( $args['data'] as $key => $value ) {
			$data .= 'data-' . $key . '="' . $value . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-horizontal-text-example ';
		}

		ob_start();		?>

		<div class="<?php echo esc_html( $args['text_class'] )?>">		<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-horizontal-text-example__icon epkbfa epkbfa-eye"></div>';
			}			?>

			<label class="<?php echo esc_html( $args['label_class'] )?>" for="<?php echo $id; ?>">
				<?php echo esc_html( $args['label'] )?>
			</label>
			<div class="input_container">
				<input type="text"
				       name="<?php echo $id; ?>"
				       id="<?php echo $id; ?>"
				       autocomplete='<?php echo $autocomplete; ?>'
				       value="<?php echo esc_attr( $args['value'] ); ?>"
				       placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
				       maxlength="<?php echo $args['max']; ?>"					<?php
						echo $data . $disabled;	?>	/>
			</div>

		</div>	<?php

		return ob_get_clean();
	}

	/**
	 * Renders two (three) text fields. The second text field depends in some way on the first one
	 *
	 * @param array $common - configuration for the main classes
	 * @param array $args1  - configuration for the first text field
	 * @param array $args2  - configuration for the second field
	 * @param array $args3  - configuration for the second field - not required 
	 *
	 * @return string
	 */
	public function text_fields_horizontal( $common = array(), $args1 = array(), $args2 = array(), $args3 = array() ) {

		$defaults = array(
			'name'         => 'text',
			'class'        => '',
		);

		$common = $this->add_common_defaults( $common, $defaults );

		$args1 = $this->add_defaults( $args1, $defaults );
		$args2 = $this->add_defaults( $args2, $defaults );
		
		if ( $args3 ) {
			$args3 = $this->add_defaults( $args3, $defaults );	
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-text-fields-horizontal-example ';
		}
		
		ob_start();		?>

		<div class="config-input-group <?php echo esc_html( $common['input_group_class'] )?>" id="<?php echo $common['id']; ?>_group" >			<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-text-fields-horizontal-example__icon epkbfa epkbfa-eye"></div>';
			}			?>

			<span class="main_label <?php echo esc_html( $common['main_label_class'] )?>"><?php echo esc_html( $common['label'] ); ?></span>
			<div class="text-fields-horizontal <?php echo esc_html( $common['input_class'] )?>">				  <?php

					echo $this->horizontal_text_input($args1);
					echo $this->horizontal_text_input($args2);
					
					if ($args3) {
						echo $this->horizontal_text_input($args3);
					}					?>

			</div>
		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Renders two text fields that related to each other. One field is text and other is select.
	 *
	 * @param array $common
	 * @param array $args1
	 * @param array $args2
	 *
	 * @return string
	 */
	public function text_and_select_fields_horizontal( $common = array(), $args1 = array(), $args2 = array() ) {

		$args1 = $this->add_defaults( $args1 );
		$args2 = $this->add_defaults( $args2 );
		$common = $this->add_common_defaults( $common );
		ob_start();

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-text-and-select-fields-horizontal-example ';
		}		?>

		<div class="config-input-group <?php echo esc_html( $common['input_group_class'] )?>" id="<?php echo $common['id']; ?>_group" >			<?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-text-and-select-fields-horizontal-example__icon epkbfa epkbfa-eye"></div>';
			}			?>

			<span class="main_label <?php echo esc_html( $common['main_label_class'] )?>"><?php echo esc_html( $common['label'] ); ?></span>
			<div class="text-select-fields-horizontal <?php echo esc_html( $common['input_class'] )?>">
				<ul>  <?php

					echo $this->text($args1);
					echo $this->dropdown($args2);						?>

				</ul>
			</div>
		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Renders several HTML checkboxes in several columns
	 *
	 * @param array $args
	 * @param $is_multi_select_not
	 * @return string
	 */
	public function checkboxes_multi_select( $args = array(), $is_multi_select_not ) {

		$defaults = array(
			'id'           => 'checkbox',
			'name'         => 'checkbox',
			'value'        => array(),
			'class'        => '',
			'main_class'   => '',
		);
		$args = $this->add_defaults( $args, $defaults );
		$id =  esc_attr( $args['name'] );
		$ix = 0;
		
		$data = '';
		foreach ( $args['data'] as $key => $value ) {
			$data .= 'data-' . $key . '="' . $value . '" ';
		}

		if ( ! empty($args['data']['example_image']) ) {
			$args['input_group_class'] =  $args['input_group_class'] . ' eckb-wizard-single-text-example ';
		}
		
		ob_start();		?>

		<div class="config-input-group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo $id ?>_group" ><?php

			if ( ! empty($args['data']['example_image']) ) {
				echo '<div class="eckb-wizard-single-dropdown-text__icon epkbfa epkbfa-eye"></div>';
			}   ?>

			<span class="main_label <?php echo esc_html( $args['main_label_class'] )?>"><?php echo esc_html( $args['label'] ); ?></span>

			<div class="checkboxes-vertical <?php echo esc_html( $args['input_class'] )?>" id="<?php echo $id ?>">
				<ul>  		<?php

					foreach( $args['options'] as $key => $label ) {

						$tmp_value = is_array($args['value']) ? $args['value'] : array();

						if ( $is_multi_select_not ) {
							$checked = in_array($key, array_keys($tmp_value)) ? '' : 'checked';
						} else {
							$checked = in_array($key, array_keys($tmp_value)) ? 'checked' : '';
						}

						$label = str_replace(',', '', $label);   			?>

						<div class="config-input-group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo $id; ?>_group">
							<?php
							if ( $is_multi_select_not ) { ?>
								<input type="hidden" value="<?php echo esc_attr( $key . '[[-HIDDEN-]]' . $label ); ?>" name="<?php echo esc_attr( $args['name'] ) . '_' . $ix; ?>" <?php echo $data; ?>>
							<?php }	?>

							<label class="<?php echo esc_html( $args['label_class'] )?>" for="<?php echo $id.$ix; ?>">
								<?php echo esc_html( $args['label'] ); ?>
							</label>

							<div class="input_container <?php echo esc_html( $args['input_class'] )?>" id="">
								<input type="checkbox"
								       name="<?php echo $id. '_' . $ix; ?>"
								       id="<?php echo $id.$ix; ?>"
								       value="<?php echo esc_attr( $key . '[[-,-]]' . $label ); ?>"
									<?php echo $data . ' ' . $checked; ?>	/>
							</div>
						</div>   	<?php

						$ix++;
					} //foreach   	?>

				</ul>
			</div>
		</div>   <?php

		return ob_get_clean();
	}

	/**
	 * Renders WordPress TinyMCE editor
	 *
	 * @param array $args Arguments for the text field
	 * @param $inside_dialog
	 * @return string Text field
	 */
	public function wp_editor( $args = array(), $inside_dialog=false ) {

		$args = $this->add_defaults( $args );

		ob_start();		?>

		<div class="config-input-group eckb-wp-editor-group <?php echo esc_html( $args['input_group_class'] )?>" id="<?php echo esc_attr( $args['name'] ) ?>_group" >

			<label class="<?php echo esc_html( $args['label_class'] )?>" for="<?php echo esc_attr( $args['name'] ) ?>">
				<?php echo esc_html( $args['label'] )?>
			</label>

			<div class="input_container <?php echo esc_html( $args['input_class'] ); ?>">     <?php
				// loading WP Editor with Ajax is problematic so it will be loaded ahead of time for Ajax situations
				if ( $inside_dialog ) {
					echo '<div id="eckb-wp-editor-update">' . __( 'Update', 'echo-knowledge-base' ) . '</div>';
				} else {
					self::get_wp_editor( $args );
				}       ?>
			</div>
			<textarea hidden id="<?php echo $args['id']; ?>" name="<?php echo $args['id']; ?>"><?php echo wp_kses_post( $args['value'] ); ?></textarea>

		</div>        <?php

		return ob_get_clean();
	}

	public static function get_wp_editor( $args ) {

		// Remove editor buttons for third parties
		remove_all_actions('media_buttons', 999999);
		remove_all_actions('media_buttons_context', 999999);
		remove_all_filters('mce_external_plugins', 999999);

		$settings =   array(
			'textarea_name' => $args['id'] . '_eckb_editor',// The name assigned to the generated textarea and passed parameter when the form is submitted.
			'media_buttons' => false, //Whether to display media insert/upload buttons
			'textarea_rows' => get_option('default_post_edit_rows', 15), // The number of rows to display for the textarea
			'editor_css' => '', // Additional CSS styling applied for both visual and HTML editors buttons, needs to include <style> tags, can use "scoped"
			'editor_class' => '', // Any extra CSS Classes to append to the Editor textarea
			'teeny' => false, // Whether to output the minimal editor configuration used in PressThis
			'dfw' => false, // Whether to replace the default fullscreen editor with DFW (needs specific DOM elements and CSS)
			'tinymce' => true, // Load TinyMCE, can be used to pass settings directly to TinyMCE using an array
			'quicktags' => true, // Load Quicktags, can be used to pass settings directly to Quicktags using an array. Set to false to remove your editor's Visual and Text tabs.
			'drag_drop_upload' => true //Enable Drag & Drop Upload Support (since WordPress 3.9)
		);

		wp_editor( wp_kses_post( $args['value'] ), $args['id'] . '_eckb_editor', $settings );
	}

	/**
	 * Output submit button
	 *
	 * @param array $args
	 * @param bool $return_html
	 * @return string
	 */
	public function submit_button( $args = array(), $return_html=false ) {
		$defaults = array(
			'label'        => __( 'Save', 'echo-knowledge-base' ),
			'id'           => '',
			'action'       => '',
			'input_class'  => '',
			'main_class'   => '',
		);
		$args = $this->add_defaults( $args, $defaults );
		
		if ( $return_html ) {
			ob_start();
		} 		?>

		<div class="config-input-group">
			<div class="submit <?php echo esc_html( $args['main_class'] ); ?>">
				<input type="hidden" id="_wpnonce_<?php echo esc_html( $args['action'] )?>" name="_wpnonce_<?php echo esc_html( $args['action'] ); ?>" value="<?php echo wp_create_nonce( "_wpnonce_" . esc_html( $args['action'] ) ); ?>"/>
				<input type="hidden" name="action" value="<?php echo esc_html( $args['action'] )?>"/>
				<input type="submit" id="<?php echo esc_html( $args['id'] ); ?>" class="<?php echo esc_html( $args['input_class'] ); ?>" value="<?php echo esc_html( $args['label'] ); ?>" />
			</div>
		</div>		<?php
		
		if ( $return_html ) {
			return ob_get_clean();
		}
		
		return '';
	}
}
