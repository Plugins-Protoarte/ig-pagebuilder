<?php
/**
 * @version    $Id$
 * @package    IG Pagebuilder
 * @author     InnoThemes Team <support@innothemes.com>
 * @copyright  Copyright (C) 2012 innothemes.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.innothemes.com
 * Technical Support:  Feedback - http://www.innothemes.com
 */
if ( ! class_exists( 'IG_Item_ButtonBar' ) ) {

	class IG_Item_ButtonBar extends IG_Pb_Child {

		public function __construct() {
			parent::__construct();
		}

		public function element_config() {
			$this->config['shortcode'] = strtolower( __CLASS__ );
			$this->config['exception'] = array(
				'require_js'       => array( 'ig-linktype.js' ),
				'data-modal-title' => __( 'ButtonBar Item', IGPBL )
			);
		}

		public function element_items() {
			$this->items = array(
				'Notab' => array(
					array(
						'name'    => __( 'Text', IGPBL ),
						'id'      => 'button_text',
						'type'    => 'text_field',
						'class'   => 'jsn-input-xxlarge-fluid',
						'std'     => __( ig_pb_add_placeholder( 'ButtonBar Item %s', 'index' ), IGPBL ),
						'role'    => 'title',
						'tooltip' => __( 'Text Button Description', IGPBL )
					),
					array(
						'name'       => __( 'Link Type', IGPBL ),
						'id'         => 'link_type',
						'type'       => 'select',
						'std'        => 'url',
						'options'    => IG_Pb_Helper_Type::get_link_types(),
						'tooltip'    => __( 'Link Type Description', IGPBL ),
						'has_depend' => '1',
					),
					array(
						'name'       => __( 'URL', IGPBL ),
						'id'         => 'button_type_url',
						'type'       => 'text_field',
						'class'      => 'jsn-input-xxlarge-fluid',
						'std'        => 'http://',
						'tooltip'    => __( 'URL Description', IGPBL ),
						'dependency' => array( 'link_type', '=', 'url' )
					),
					array(
						'name'  => __( 'Single Item', IGPBL ),
						'id'    => 'single_item',
						'type'  => 'type_group',
						'std'   => '',
						'items' => IG_Pb_Helper_Type::get_single_item_button_bar(
							'link_type',
							array(
								'type'         => 'items_list',
								'options_type' => 'select',
								'class'        => 'select2-select',
								'ul_wrap'      => false,
							 )
						),
					),
					array(
						'name'       => __( 'Open in', IGPBL ),
						'id'         => 'open_in',
						'type'       => 'select',
						'std'        => IG_Pb_Helper_Type::get_first_option( IG_Pb_Helper_Type::get_open_in_options() ),
						'options'    => IG_Pb_Helper_Type::get_open_in_options(),
						'tooltip'    => __( 'Open in Description', IGPBL ),
						'dependency' => array( 'link_type', '!=', 'no_link' )
					),
					array(
						'name'      => __( 'Icon', IGPBL ),
						'id'        => 'icon',
						'type'      => 'icons',
						'std'       => '',
						'role'      => 'title_prepend',
						'title_prepend_type' => 'icon',
						'tooltip'   => __( 'Icon Description', IGPBL )
					),
					array(
						'name'    => __( 'Size', IGPBL ),
						'id'      => 'button_size',
						'type'    => 'select',
						'std'     => IG_Pb_Helper_Type::get_first_option( IG_Pb_Helper_Type::get_button_size() ),
						'options' => IG_Pb_Helper_Type::get_button_size(),
						'tooltip' => __( 'Button Size Description', IGPBL )
					),
					array(
						'name'    => __( 'Color', IGPBL ),
						'id'      => 'button_color',
						'type'    => 'select',
						'std'     => IG_Pb_Helper_Type::get_first_option( IG_Pb_Helper_Type::get_button_color() ),
						'options' => IG_Pb_Helper_Type::get_button_color(),
						'tooltip' => __( 'Button Color Description', IGPBL ),
						'container_class'   => 'color_select2',
					),
				)
			);
		}

		public function element_shortcode( $atts = null, $content = null ) {
			$arr_params   = shortcode_atts( $this->config['params'], $atts );
			extract( $arr_params );
			$button_text  = ( ! $button_text ) ? '' : $button_text;
			$button_size  = ( ! $button_size || strtolower( $button_size ) == 'default' ) ? '' : $button_size;
			$button_color = ( ! $button_color || strtolower( $button_color ) == 'default' ) ? '' : $button_color;
			$button_icon  = ( ! $icon ) ? '' : "<i class='{$icon}'></i>";
			$tag          = 'a';
			$href         = '';
			$single_item  = explode( '__#__', $single_item );
			$single_item  = $single_item[0];
			if ( ! empty( $link_type ) ) {
				$taxonomies = IG_Pb_Helper_Type::get_public_taxonomies();
				$post_types = IG_Pb_Helper_Type::get_post_types();
				// single post
				if ( array_key_exists( $link_type, $post_types ) ) {
					$permalink = home_url() . "/?p=$single_item";
					$href      = ( ! $single_item ) ? ' href="#"' : " href='{$permalink}'";
				}
				// taxonomy
				else if ( array_key_exists( $link_type, $taxonomies ) ) {
					$permalink = get_term_link( intval( $single_item ), $link_type );
					if ( ! is_wp_error( $permalink ) )
						$href = ( ! $single_item ) ? ' href="#"' : " href='{$permalink}'";
				}
				else {
					switch ( $link_type ) {
						case 'no_link':
							$tag = 'button';
							break;
						case 'url':
							$href = ( ! $button_type_url ) ? ' href="#"' : " href='{$button_type_url}'";
							break;
					}
				}
			}
			$target = '';
			if ( $open_in ) {
				switch ( $open_in ) {
					case 'current_browser':
						$target = '';
						break;
					case 'new_browser':
						$target = ' target="_blank"';
						break;
					case 'lightbox':
						$cls_button_fancy = 'ig-button-fancy';
						$script = IG_Pb_Helper_Functions::fancybox( ".$cls_button_fancy", true );
						break;
				}
			}
			$button_type      = ( $tag == 'button' ) ? " type='button'" : '';
			$cls_button_fancy = ( ! isset( $cls_button_fancy ) ) ? '' : $cls_button_fancy;
			$script           = ( ! isset( $script ) ) ? '' : $script;

			$html_result      = "<{$tag} class='btn {$button_size} {$button_color} {$cls_button_fancy}'{$href}{$target}{$button_type}>[icon]{$button_icon}[/icon][title]{$button_text}[/title]</{$tag}>";

			return $html_result . $script . '<!--seperate-->';
		}

	}

}