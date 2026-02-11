<?php 

/**********************Size Chart Individual Product************/

add_action('woocommerce_product_options_general_product_data', 'product_checkbox_fields_sizechart');
function product_checkbox_fields_sizechart(){
    global $post;

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => 'tall_tees',
        'desc'      => __('Tall Tees size cart button', 'woocommerce'),
        'label'     => __('Tall Tees', 'woocommerce'),
        'desc_tip'  => 'true'
    ));

    echo '</div>';
	echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => 'tall_hoodies',
        'desc'      => __('Tall Hoodies size cart button', 'woocommerce'),
        'label'     => __('Tall Hoodies', 'woocommerce'),
        'desc_tip'  => 'true'
    ));

    echo '</div>';
	echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => 'button_ups',
        'desc'      => __('Button UPS size cart button', 'woocommerce'),
        'label'     => __('Button UPS', 'woocommerce'),
        'desc_tip'  => 'true'
    ));

    echo '</div>';
	
	echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => 'tackpants',
        'desc'      => __('Trackpants size cart button', 'woocommerce'),
        'label'     => __('Trackpants', 'woocommerce'),
        'desc_tip'  => 'true'
    ));

    echo '</div>';
	
	echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => 'crewneck_jumpers',
        'desc'      => __('Crewneck Jumpers size cart button', 'woocommerce'),
        'label'     => __('Jumpers', 'woocommerce'),
        'desc_tip'  => 'true'
    ));

    echo '</div>';
	
	echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => 'semi_tall',
        'desc'      => __('Semi Tall size cart button', 'woocommerce'),
        'label'     => __('Semi Tall', 'woocommerce'),
        'desc_tip'  => 'true'
    ));

    echo '</div>';
	
	echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => 'work_shirt',
        'desc'      => __('Work Shirt size cart button', 'woocommerce'),
        'label'     => __('Work Shirt', 'woocommerce'),
        'desc_tip'  => 'true'
    ));

    echo '</div>';
	
	echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => 'basketball_shorts',
        'desc'      => __('Basketball Shorts size cart button', 'woocommerce'),
        'label'     => __('Basketball Shorts', 'woocommerce'),
        'desc_tip'  => 'true'
    ));

    echo '</div>';
	
	echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => 'polo_shirt',
        'desc'      => __('Polo Shirt size cart button', 'woocommerce'),
        'label'     => __('Polo Shirt', 'woocommerce'),
        'desc_tip'  => 'true'
    ));

    echo '</div>';
	
	echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => 'jogger_pants',
        'desc'      => __('Jogger Pants size cart button', 'woocommerce'),
        'label'     => __('Jogger Pants', 'woocommerce'),
        'desc_tip'  => 'true'
    ));

    echo '</div>';

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'jacket',
            'desc'     => __('Jacket size cart button', 'woocommerce'),
            'label'    => __('Jacket', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';
	
	
	echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => 'singlet',
        'desc'      => __('Singlet size cart button', 'woocommerce'),
        'label'     => __('Singlet', 'woocommerce'),
        'desc_tip'  => 'true'
    ));

    echo '</div>';
	
	
}

// Save Fields
add_action('woocommerce_process_product_meta', 'product_checkbox_fields_sizechart_save');
function product_checkbox_fields_sizechart_save($post_id){
    $tall_tees_product = isset( $_POST['tall_tees'] ) ? 'yes' : 'no';
    update_post_meta($post_id, 'tall_tees', esc_attr( $tall_tees_product ));
	
	$tall_hoodies_product = isset( $_POST['tall_hoodies'] ) ? 'yes' : 'no';
    update_post_meta($post_id, 'tall_hoodies', esc_attr( $tall_hoodies_product ));
	
	$button_ups_product = isset( $_POST['button_ups'] ) ? 'yes' : 'no';
    update_post_meta($post_id, 'button_ups', esc_attr( $button_ups_product ));
	
	$trackpants_product = isset( $_POST['tackpants'] ) ? 'yes' : 'no';
    update_post_meta($post_id, 'tackpants', esc_attr( $trackpants_product ));
	
	$crewneck_jumpers = isset( $_POST['crewneck_jumpers'] ) ? 'yes' : 'no';
    update_post_meta($post_id, 'crewneck_jumpers', esc_attr( $crewneck_jumpers ));
	
	$semi_tall = isset( $_POST['semi_tall'] ) ? 'yes' : 'no';
    update_post_meta($post_id, 'semi_tall', esc_attr( $semi_tall ));
	
	$work_shirt = isset( $_POST['work_shirt'] ) ? 'yes' : 'no';
    update_post_meta($post_id, 'work_shirt', esc_attr( $work_shirt ));
	
	$basketball_shorts = isset( $_POST['basketball_shorts'] ) ? 'yes' : 'no';
    update_post_meta($post_id, 'basketball_shorts', esc_attr( $basketball_shorts ));
	
	$polo_shirt = isset( $_POST['polo_shirt'] ) ? 'yes' : 'no';
    update_post_meta($post_id, 'polo_shirt', esc_attr( $polo_shirt ));
	
	$jogger_pants = isset( $_POST['jogger_pants'] ) ? 'yes' : 'no';
    update_post_meta($post_id, 'jogger_pants', esc_attr( $jogger_pants ));

    $jacket = isset($_POST['jacket']) ? 'yes' : 'no';
    update_post_meta($post_id, 'jacket', esc_attr($jacket));
	
	$singlet = isset($_POST['singlet']) ? 'yes' : 'no';
    update_post_meta($post_id, 'singlet', esc_attr($singlet));
	 
}
/**********************Size Chart Individual Product************/


/**********************Size Chart Category************/
add_action('product_cat_add_form_fields', 'wh_taxonomy_add_new_meta_field', 10, 1);
add_action('product_cat_edit_form_fields', 'wh_taxonomy_edit_meta_field', 10, 1);
//Product Cat Create page
function wh_taxonomy_add_new_meta_field() {
    ?>   
    <div class="form-field">
        <label for="wh_meta_tees"><?php _e('Tall Tees', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_tees" id="wh_meta_tees" />
    </div>
    <div class="form-field">
        <label for="wh_meta_hoodies"><?php _e('Tall Hoodies', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_hoodies" id="wh_meta_hoodies" />
    </div>
	<div class="form-field">
        <label for="wh_meta_buttons"><?php _e('Buttons UPS', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_buttons" id="wh_meta_buttons" />
    </div>
	<div class="form-field">
        <label for="wh_meta_trackpants"><?php _e('Track Pants', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_trackpants" id="wh_meta_trackpants" />
    </div>
	<div class="form-field">
        <label for="wh_meta_crewneck_jumpers"><?php _e('Jumpers', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_crewneck_jumpers" id="wh_meta_crewneck_jumpers" />
    </div>

    	<div class="form-field">
        <label for="wh_meta_semi_tall"><?php _e('Semi Tall', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_semi_tall" id="wh_meta_semi_tall" />
    </div>

    	<div class="form-field">
        <label for="wh_meta_work_shirt"><?php _e('Work Shirt', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_work_shirt" id="wh_meta_work_shirt" />
    </div>

    	<div class="form-field">
        <label for="wh_meta_basketball_shorts"><?php _e('Basketball Shorts', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_basketball_shorts" id="wh_meta_basketball_shorts" />
    </div>

    	<div class="form-field">
        <label for="wh_meta_polo_shirt"><?php _e('Polo Shirt', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_polo_shirt" id="wh_meta_polo_shirt" />
    </div>
    <div class="form-field">
        <label for="wh_meta_jogger_pants"><?php _e('Jogger Pants', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_jogger_pants" id="wh_meta_jogger_pants" />
    </div>
     <div class="form-field">
        <label for="wh_meta_jacket"><?php _e('Jacket', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_jacket" id="wh_meta_jacket" />
    </div>
	
	<div class="form-field">
        <label for="wh_meta_singlet"><?php _e('Singlet', 'wh'); ?></label>
        <input type="checkbox" name="wh_meta_singlet" id="wh_meta_singlet" />
    </div>
	
    <?php
}
//Product Cat Edit page
function wh_taxonomy_edit_meta_field($term) {
    //getting term ID
    $term_id = $term->term_id;
    // retrieve the existing value(s) for this meta field.
    $wh_meta_tees = get_term_meta($term_id, 'wh_meta_tees', true);
    $wh_meta_hoodies = get_term_meta($term_id, 'wh_meta_hoodies', true);
	$wh_meta_buttons = get_term_meta($term_id, 'wh_meta_buttons', true);
	$wh_meta_trackpants = get_term_meta($term_id, 'wh_meta_trackpants', true);
	$wh_meta_crewneck_jumpers = get_term_meta($term_id, 'wh_meta_crewneck_jumpers', true);
	
	$wh_meta_semi_tall = get_term_meta($term_id, 'wh_meta_semi_tall', true);
	$wh_meta_work_shirt = get_term_meta($term_id, 'wh_meta_work_shirt', true);
	$wh_meta_basketball_shorts = get_term_meta($term_id, 'wh_meta_basketball_shorts', true);
	$wh_meta_polo_shirt = get_term_meta($term_id, 'wh_meta_polo_shirt', true);
	$wh_meta_jogger_pants = get_term_meta($term_id, 'wh_meta_jogger_pants', true);
	$wh_meta_jacket = get_term_meta($term_id, 'wh_meta_jacket', true);
	$wh_meta_singlet = get_term_meta($term_id, 'wh_meta_singlet', true);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_tees"><?php _e('Tall Tees', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_tees" id="wh_meta_tees" value="yes" <?php echo ( $wh_meta_tees ) ? checked( $wh_meta_tees, 'yes' ) : ''; ?> />
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_hoodies"><?php _e('Tall Hoodies', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_hoodies" id="wh_meta_hoodies" value="yes" <?php echo ( $wh_meta_hoodies ) ? checked( $wh_meta_hoodies, 'yes' ) : ''; ?> />
        </td>
    </tr>
	<tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_buttons"><?php _e('Button UPS', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_buttons" id="wh_meta_buttons" value="yes" <?php echo ( $wh_meta_buttons ) ? checked( $wh_meta_buttons, 'yes' ) : ''; ?> />
        </td>
    </tr>
	<tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_trackpants"><?php _e('Trackpants', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_trackpants" id="wh_meta_trackpants" value="yes" <?php echo ( $wh_meta_trackpants ) ? checked( $wh_meta_trackpants, 'yes' ) : ''; ?> />
        </td>
    </tr>
	<tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_crewneck_jumpers"><?php _e('Jumpers', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_crewneck_jumpers" id="wh_meta_crewneck_jumpers" value="yes" <?php echo ( $wh_meta_crewneck_jumpers ) ? checked( $wh_meta_crewneck_jumpers, 'yes' ) : ''; ?> />
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_semi_tall"><?php _e('Semi Tall', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_semi_tall" id="wh_meta_semi_tall" value="yes" <?php echo ( $wh_meta_semi_tall ) ? checked( $wh_meta_semi_tall, 'yes' ) : ''; ?> />
        </td>
    </tr>


    <tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_work_shirt"><?php _e('Work Shirt', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_work_shirt" id="wh_meta_work_shirt" value="yes" <?php echo ( $wh_meta_work_shirt ) ? checked( $wh_meta_work_shirt, 'yes' ) : ''; ?> />
        </td>
    </tr>


    <tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_basketball_shorts"><?php _e('Basketball Shorts', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_basketball_shorts" id="wh_meta_basketball_shorts" value="yes" <?php echo ( $wh_meta_basketball_shorts ) ? checked( $wh_meta_basketball_shorts, 'yes' ) : ''; ?> />
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_polo_shirt"><?php _e('Polo Shirt', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_polo_shirt" id="wh_meta_polo_shirt" value="yes" <?php echo ( $wh_meta_polo_shirt ) ? checked( $wh_meta_polo_shirt, 'yes' ) : ''; ?> />
        </td>
    </tr>
 <tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_jogger_pants"><?php _e('Jogger Pants', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_jogger_pants" id="wh_meta_jogger_pants" value="yes" <?php echo ( $wh_meta_jogger_pants ) ? checked( $wh_meta_jogger_pants, 'yes' ) : ''; ?> />
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_jacket"><?php _e('Jacket', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_jacket" id="wh_meta_jacket" value="yes" <?php echo ( $wh_meta_jacket ) ? checked( $wh_meta_jacket, 'yes' ) : ''; ?> />
        </td>
    </tr>

   <tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_singlet"><?php _e('Singlet', 'wh'); ?></label></th>
        <td>
            <input type="checkbox" name="wh_meta_singlet" id="wh_meta_singlet" value="yes" <?php echo ( $wh_meta_singlet ) ? checked( $wh_meta_singlet, 'yes' ) : ''; ?> />
        </td>
    </tr>

    <?php
}


add_action('edited_product_cat', 'wh_save_taxonomy_custom_meta', 10, 1);
add_action('create_product_cat', 'wh_save_taxonomy_custom_meta', 10, 1);
// Save extra taxonomy fields callback function.
function wh_save_taxonomy_custom_meta($term_id) {
	if ( isset( $_POST[ 'wh_meta_hoodies' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_hoodies', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_hoodies', '' );
    }
	
	if ( isset( $_POST[ 'wh_meta_buttons' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_buttons', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_buttons', '' );
    }
	
	if ( isset( $_POST[ 'wh_meta_tees' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_tees', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_tees', '' );
    }
	
	if ( isset( $_POST[ 'wh_meta_trackpants' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_trackpants', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_trackpants', '' );
    }
	
	if ( isset( $_POST[ 'wh_meta_crewneck_jumpers' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_crewneck_jumpers', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_crewneck_jumpers', '' );
    }
	
	
	
	if ( isset( $_POST[ 'wh_meta_semi_tall' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_semi_tall', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_semi_tall', '' );
    }
	
	if ( isset( $_POST[ 'wh_meta_work_shirt' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_work_shirt', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_work_shirt', '' );
    }
	
	
	if ( isset( $_POST[ 'wh_meta_basketball_shorts' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_basketball_shorts', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_basketball_shorts', '' );
    }
	
	
	if ( isset( $_POST[ 'wh_meta_polo_shirt' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_polo_shirt', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_polo_shirt', '' );
    }
	
	if ( isset( $_POST[ 'wh_meta_jogger_pants' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_jogger_pants', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_jogger_pants', '' );
    }
	
	if ( isset( $_POST[ 'wh_meta_jacket' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_jacket', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_jacket', '' );
    }
	
	if ( isset( $_POST[ 'wh_meta_singlet' ] ) ) {
        update_term_meta( $term_id, 'wh_meta_singlet', 'yes' );
    } else {
        update_term_meta( $term_id, 'wh_meta_singlet', '' );
    }
	
	
	
	
	
	
	
	
}
/**********************Size Chart Category************/
?>