<?php
/**
 * WilokeTaxonomy Class
 *
 * @category Taxonomy
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
	exit;
}

class WilokeTaxonomy
{
	public $aTaxes = array();
	public $aOptions;
	public function __construct()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('manage_users_custom_column', array($this, 'addCustomColumnToAjaxAddTag'), 10, 2);
		$this->taxonomy_settings();
	}

	public function addCustomColumnToAjaxAddTag(){

	}

	public function enqueue_scripts()
	{
		global $wiloke;

		if ( !isset($wiloke->aConfigs['taxonomy']) || !isset($_GET['taxonomy']) )
		{
			return false;
		}

		// Check Wiloke Post Format plugin is activating or not
		if ( !wp_script_is('wiloke_post_format', 'enqueued') )
		{
			wp_enqueue_media();
			wp_enqueue_script('wiloke_post_format_ui', WILOKE_AD_SOURCE_URI . 'js/wiloke_post_format.js', array('jquery'), false, true);
			wp_enqueue_style('wiloke_post_format_ui', WILOKE_AD_SOURCE_URI . 'css/wiloke_post_format.css');
			wp_enqueue_script('wiloke_taxonomy', WILOKE_AD_SOURCE_URI . 'js/taxonomy.js', array('jquery', 'wiloke_post_format_ui'), false, true);
			wp_enqueue_style('wiloke_taxonomy', WILOKE_AD_SOURCE_URI . 'css/taxonomy.css', array(), '1.0');
		}

		wp_enqueue_script('spectrum', WILOKE_AD_ASSET_URI . 'js/spectrum.js', array('jquery'), false, true);
		wp_enqueue_style('spectrum', WILOKE_AD_ASSET_URI . 'css/spectrum.css');
	}

	public function taxonomy_settings()
	{
		global $wiloke;

		if ( !isset($wiloke->aConfigs['taxonomy'])  )
		{
			if ( !isset($wiloke->aConfigs['taxonomy']) )
			{
				return false;
			}
		}

		if ( !$wiloke->kindofrequest('admin') )
		{
			return false;
		}

		$this->aTaxes = array_keys($wiloke->aConfigs['taxonomy']);

		foreach ( $wiloke->aConfigs['taxonomy'] as $key => $aValues )
		{
			add_action( $key.'_edit_form_fields', array($this, 'edit_form_field'), 30, 1);
			add_action( $key.'_add_form_fields', array($this, 'edit_form_field'), 30, 1);

			add_action( 'edited_'.$key, array($this, 'save_tax_settings'), 10, 2 );
			add_action( 'created_'.$key, array($this, 'save_tax_settings'), 10, 2 );

			add_filter('manage_edit-'.$key.'_columns', array($this, 'add_columns_head'));
			add_filter('manage_'.$key.'_custom_column', array($this, 'add_columns_content'), 10, 3);
//			add_filter('manage_edit-'.$key.'_sortable_columns', array($this, 'add_columns_content'), 10, 3);
		}
	}

	public function isInTax(){

		if ( (!isset($_GET['taxonomy']) || empty($_GET['taxonomy']) ) && (!isset($_POST['taxonomy']) || empty($_POST['taxonomy'])) ){
			return false;
		}

		$currentTax = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : $_POST['taxonomy'];

		if ( !in_array($currentTax, $this->aTaxes) ){
			return false;
		}

		global $wiloke;
		return $wiloke->aConfigs['taxonomy'][$currentTax];
	}

	public function add_columns_head($defaults){
		$aInfo = $this->isInTax();

		if ( !$aInfo ){
			return $defaults;
		}

		foreach ( $aInfo as $aTax ){
			if ( isset($aTax['is_add_to_column']) && $aTax['is_add_to_column'] ){
				$defaults[$aTax['id']] = $aTax['name'];
			}
		}

		return $defaults;
	}

	public function add_columns_content($c, $columnName, $termID){
		$aInfo = $this->isInTax();

		if ( !$aInfo ){
			return false;
		}

		foreach ( $aInfo as $aVal ){
			if ( $aVal['id'] === $columnName ){
				$aColumnInfo = $aVal;
				break;
			}
		}

		if ( !empty($aColumnInfo) ) {
			$aOptions = Wiloke::getTermOption($termID);
			$content = isset($aOptions[$aColumnInfo['id']]) ? $aOptions[$aColumnInfo['id']] : '';

			if ( !empty($content) ){
				if ( $aColumnInfo['type'] === 'media' ){

					if ( !isset($aColumnInfo['return']) || ($aColumnInfo['return'] !== 'url') ){
						$content = wp_get_attachment_image_url($content, 'thumbnail');
					}
					echo '<img width="50" height="50" src="'.esc_url($content).'">';
				}else if ($aColumnInfo['type'] == 'colorpicker'){
					echo '<span style="background-color: '.esc_attr($content).'; width: 50px; height: 50px; display: inline-block; text-align: center; border-radius: 50%;"></span>';
				}else{
					echo esc_html($content);
				}
			}else{
				echo '';
			}
		}
	}

	public function edit_form_field($term)
	{
		global $wiloke;
		$termID         = isset($term->term_id) ? $term->term_id : '';
		$aOptions       = Wiloke::getTermOption($termID);
		$name           = 'wiloke_cat_settings_'.$termID;
		$taxonomyType   = $_REQUEST['taxonomy'];

		foreach ( $wiloke->aConfigs['taxonomy'][$taxonomyType] as $aField )
		{
			if ( isset($aOptions[$aField['id']]) && !empty($aOptions[$aField['id']]) )
			{
				$value = $aOptions[$aField['id']];
			}else{
				if ( isset($aField['default']) && !empty($aField['default']) )
				{
					$value = $aField['default'];
				}else{
					$value = '';
				}
			}

			$aField['value'] = $value;
			$funcName       = 'wiloke_render_'.$aField['type'].'_field';
			$aField['id']   = $name . '[' . $aField['id'] . ']';
			WilokeHtmlHelper::$funcName($aField);
		}
	}

	public function save_tax_settings($termID)
	{

		if ( isset($_POST['wiloke_cat_settings_'.$termID]) && !empty($_POST['wiloke_cat_settings_'.$termID]) )
		{
			do_action('wiloke_before_update_tax', $termID, $_POST['wiloke_cat_settings_'.$termID]);
			Wiloke::updateOption('_wiloke_cat_settings_'.$termID, $_POST['wiloke_cat_settings_'.$termID]);
			$this->savePlaceID($termID, $_POST['wiloke_cat_settings_'.$termID]);
		}else if ( ($_POST['wiloke_cat_settings_']) && !empty($_POST['wiloke_cat_settings_']) ){
			do_action('wiloke_before_update_tax', $termID, $_POST['wiloke_cat_settings_']);
			Wiloke::updateOption('_wiloke_cat_settings_'.$termID, $_POST['wiloke_cat_settings_']);
			$this->savePlaceID($termID, $_POST['wiloke_cat_settings_']);
		}
	}

	public function savePlaceID($termID, $aData){
		if ( isset($aData['placeid']) ){
			update_term_meta($termID, 'wiloke_listing_location_place_id', $aData['placeid']);
		}
	}
}
