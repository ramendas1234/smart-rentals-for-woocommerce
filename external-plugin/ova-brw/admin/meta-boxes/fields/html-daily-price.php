<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label"><?php esc_html_e( 'Daily Pricing Plan', 'ova-brw' ); ?></h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-daily-price">
			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Days Of The Week', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
					    <td width="30%">
					     	<?php echo esc_html__( 'Monday', 'ova-brw' ); ?>
					    </td>
					    <td width="80%" class="ovabrw-input-price">
					    	<?php ovabrw_wp_text_input([
								'type' 			=> 'text',
								'name' 			=> $this->get_meta_name( 'daily_monday' ),
								'value' 		=> $this->get_meta_value( 'daily_monday' ),
								'data_type' 	=> 'price',
								'placeholder' 	=> '10.5'
							]); ?>
					    </td>
					</tr>
					<tr>
					    <td width="30%">
					     	<?php echo esc_html__( 'Tuesday', 'ova-brw' ); ?>
					    </td>
					    <td width="80%" class="ovabrw-input-price">
					    	<?php ovabrw_wp_text_input([
								'type' 			=> 'text',
								'name' 			=> $this->get_meta_name( 'daily_tuesday' ),
								'value' 		=> $this->get_meta_value( 'daily_tuesday' ),
								'data_type' 	=> 'price',
								'placeholder' 	=> '10.5'
							]); ?>
					    </td>
					</tr>
					<tr>
					    <td width="30%">
					     	<?php echo esc_html__( 'Wednesday', 'ova-brw' ); ?>
					    </td>
					    <td width="80%" class="ovabrw-input-price">
					    	<?php ovabrw_wp_text_input([
								'type' 			=> 'text',
								'name' 			=> $this->get_meta_name( 'daily_wednesday' ),
								'value' 		=> $this->get_meta_value( 'daily_wednesday' ),
								'data_type' 	=> 'price',
								'placeholder' 	=> '10.5'
							]); ?>
					    </td>
					</tr>
					<tr>
					    <td width="30%">
					     	<?php echo esc_html__( 'Thursday', 'ova-brw' ); ?>
					    </td>
					    <td width="80%" class="ovabrw-input-price">
					    	<?php ovabrw_wp_text_input([
								'type' 			=> 'text',
								'name' 			=> $this->get_meta_name( 'daily_thursday' ),
								'value' 		=> $this->get_meta_value( 'daily_thursday' ),
								'data_type' 	=> 'price',
								'placeholder' 	=> '10.5'
							]); ?>
					    </td>
					</tr>
					<tr>
					    <td width="30%">
					     	<?php echo esc_html__( 'Friday', 'ova-brw' ); ?>
					    </td>
					    <td width="80%" class="ovabrw-input-price">
					    	<?php ovabrw_wp_text_input([
								'type' 			=> 'text',
								'name' 			=> $this->get_meta_name( 'daily_friday' ),
								'value' 		=> $this->get_meta_value( 'daily_friday' ),
								'data_type' 	=> 'price',
								'placeholder' 	=> '10.5'
							]); ?>
					    </td>
					</tr>
					<tr>
					    <td width="30%">
					     	<?php echo esc_html__( 'Saturday', 'ova-brw' ); ?>
					    </td>
					    <td width="80%" class="ovabrw-input-price">
					    	<?php ovabrw_wp_text_input([
								'type' 			=> 'text',
								'name' 			=> $this->get_meta_name( 'daily_saturday' ),
								'value' 		=> $this->get_meta_value( 'daily_saturday' ),
								'data_type' 	=> 'price',
								'placeholder' 	=> '10.5'
							]); ?>
					    </td>
					</tr>
					<tr>
					    <td width="30%">
					     	<?php echo esc_html__( 'Sunday', 'ova-brw' ); ?>
					    </td>
					    <td width="80%" class="ovabrw-input-price">
					    	<?php ovabrw_wp_text_input([
								'type' 			=> 'text',
								'name' 			=> $this->get_meta_name( 'daily_sunday' ),
								'value' 		=> $this->get_meta_value( 'daily_sunday' ),
								'data_type' 	=> 'price',
								'placeholder' 	=> '10.5'
							]); ?>
					    </td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>