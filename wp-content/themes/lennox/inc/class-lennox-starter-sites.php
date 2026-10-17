<?php
/**
 * Starter sites import service.
 *
 * @package lennox
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles bundled starter site data and imports.
 *
 * @since 1.0.0
 */
class Lennox_Starter_Sites {

	/**
	 * Starter ownership meta key.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const META_STARTER_SLUG = '_lennox_starter_site_slug';

	/**
	 * Starter page slug meta key.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const META_STARTER_PAGE_SLUG = '_lennox_starter_site_page_slug';

	/**
	 * Starter content type meta key.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const META_STARTER_CONTENT_TYPE = '_lennox_starter_site_content_type';

	/**
	 * Get available starter sites formatted for the dashboard.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_starters() {
		$starters = array();

		foreach ( self::get_manifest_files() as $starter_slug => $manifest_path ) {
			$manifest = self::read_manifest( $starter_slug, $manifest_path );

			if ( is_wp_error( $manifest ) ) {
				continue;
			}

			$preview_slug = 'default' === $starter_slug ? 'main' : $starter_slug;

			$starters[] = array(
				'slug'        => $starter_slug,
				'title'       => isset( $manifest['title'] ) && is_scalar( $manifest['title'] ) ? (string) $manifest['title'] : $starter_slug,
				'description' => isset( $manifest['description'] ) && is_scalar( $manifest['description'] ) ? (string) $manifest['description'] : '',
				'screenshot'  => self::get_starter_screenshot_url( $starter_slug, $manifest ),
				'preview'     => 'https://demo.emberwp.com/lennox-' . $preview_slug,
				'isPro'       => self::is_pro_starter( $manifest ),
			);
		}

		return $starters;
	}

	/**
	 * Import a bundled starter site.
	 *
	 * @since 1.0.0
	 *
	 * @param string $starter_slug Starter slug.
	 * @return array<string, mixed>|WP_Error Import result or error.
	 */
	public static function import( $starter_slug ) {
		$starter_slug = sanitize_key( $starter_slug );
		$manifest     = self::get_manifest( $starter_slug );

		if ( is_wp_error( $manifest ) ) {
			return $manifest;
		}

		if ( self::is_pro_starter( $manifest ) && ! self::is_pro_active() ) {
			return new WP_Error( 'lennox_pro_starter_site', __( 'This starter site requires Lennox Pro.', 'lennox' ) );
		}

		$pages = self::import_pages( $manifest );

		if ( is_wp_error( $pages ) ) {
			return $pages;
		}

		$front_page_id = self::set_front_page( $manifest, $pages );
		$navigation_id = self::import_navigation( $manifest, $pages );

		if ( is_wp_error( $navigation_id ) ) {
			return $navigation_id;
		}

		$template_part_id = self::import_header_template_part( $navigation_id );

		if ( is_wp_error( $template_part_id ) ) {
			return $template_part_id;
		}

		$template_id = self::import_front_page_template( $manifest );

		if ( is_wp_error( $template_id ) ) {
			return $template_id;
		}

		$global_styles_id = self::merge_global_styles( $manifest );

		if ( is_wp_error( $global_styles_id ) ) {
			return $global_styles_id;
		}

		return array(
			'starter'        => $starter_slug,
			'pages'          => array_values( $pages ),
			'frontPageId'    => $front_page_id,
			'navigationId'   => $navigation_id,
			'templatePartId' => $template_part_id,
			'templateId'     => $template_id,
			'globalStylesId' => $global_styles_id,
		);
	}

	/**
	 * Get manifest data for a starter slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $starter_slug Starter slug.
	 * @return array<string, mixed>|WP_Error Manifest data or error.
	 */
	private static function get_manifest( $starter_slug ) {
		$manifest_files = self::get_manifest_files();

		if ( ! isset( $manifest_files[ $starter_slug ] ) ) {
			return new WP_Error( 'lennox_invalid_starter_site', __( 'Invalid starter site.', 'lennox' ) );
		}

		return self::read_manifest( $starter_slug, $manifest_files[ $starter_slug ] );
	}

	/**
	 * Get bundled manifest files keyed by starter slug.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, string>
	 */
	private static function get_manifest_files() {
		$starter_root = get_theme_file_path( 'starter-sites' );

		if ( ! is_dir( $starter_root ) ) {
			return array();
		}

		$manifest_files = glob( $starter_root . '/*/manifest.json' );

		if ( false === $manifest_files ) {
			return array();
		}

		$files = array();

		foreach ( $manifest_files as $manifest_path ) {
			$starter_slug = sanitize_key( basename( dirname( $manifest_path ) ) );

			if ( '' === $starter_slug ) {
				continue;
			}

			$files[ $starter_slug ] = $manifest_path;
		}

		uksort(
			$files,
			static function ( $a, $b ) {
				if ( 'default' === $a ) {
					return -1;
				}

				if ( 'default' === $b ) {
					return 1;
				}

				return strnatcasecmp( $a, $b );
			}
		);

		return $files;
	}

	/**
	 * Get the starter screenshot URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string               $starter_slug Starter slug.
	 * @param array<string, mixed> $manifest Starter manifest.
	 * @return string Screenshot URL.
	 */
	private static function get_starter_screenshot_url( $starter_slug, $manifest ) {
		if ( ! isset( $manifest['screenshot'] ) || ! is_string( $manifest['screenshot'] ) ) {
			return '';
		}

		$screenshot_file = basename( $manifest['screenshot'] );
		$screenshot_path = get_theme_file_path( "starter-sites/$starter_slug/$screenshot_file" );

		if ( '' === $screenshot_file || ! file_exists( $screenshot_path ) ) {
			return '';
		}

		return get_theme_file_uri( "starter-sites/$starter_slug/$screenshot_file" );
	}

	/**
	 * Read and validate a manifest file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $starter_slug Starter slug.
	 * @param string $manifest_path Manifest path.
	 * @return array<string, mixed>|WP_Error Manifest data or error.
	 */
	private static function read_manifest( $starter_slug, $manifest_path ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading a bundled local JSON file.
		$contents = file_get_contents( $manifest_path );

		if ( false === $contents ) {
			return new WP_Error( 'lennox_manifest_unreadable', __( 'Starter site manifest could not be read.', 'lennox' ) );
		}

		$manifest = json_decode( $contents, true );

		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $manifest ) ) {
			return new WP_Error( 'lennox_manifest_invalid_json', __( 'Starter site manifest is invalid.', 'lennox' ) );
		}

		if ( ! isset( $manifest['slug'] ) || ! is_scalar( $manifest['slug'] ) || sanitize_key( (string) $manifest['slug'] ) !== $starter_slug ) {
			return new WP_Error( 'lennox_manifest_slug_mismatch', __( 'Starter site manifest slug is invalid.', 'lennox' ) );
		}

		if ( ! isset( $manifest['pages'] ) || ! is_array( $manifest['pages'] ) ) {
			return new WP_Error( 'lennox_manifest_missing_pages', __( 'Starter site manifest has no pages.', 'lennox' ) );
		}

		return $manifest;
	}

	/**
	 * Determine whether a starter site requires Lennox Pro.
	 *
	 * Set the `isPro` manifest property to true to restrict a starter site.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $manifest Starter manifest.
	 * @return bool Whether the starter site requires Lennox Pro.
	 */
	private static function is_pro_starter( $manifest ) {
		return isset( $manifest['isPro'] ) && true === $manifest['isPro'];
	}

	/**
	 * Determine whether Lennox Pro is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether Lennox Pro is active.
	 */
	private static function is_pro_active() {
		return defined( 'LENNOX_PRO_VERSION' );
	}

	/**
	 * Import starter pages.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $manifest Starter manifest.
	 * @return array<string, int>|WP_Error Imported page IDs keyed by manifest page slug.
	 */
	private static function import_pages( $manifest ) {
		if ( ! isset( $manifest['slug'], $manifest['pages'] ) || ! is_scalar( $manifest['slug'] ) || ! is_array( $manifest['pages'] ) ) {
			return new WP_Error( 'lennox_invalid_starter_site', __( 'Starter site manifest is invalid.', 'lennox' ) );
		}

		$starter_slug = sanitize_key( (string) $manifest['slug'] );
		$pages        = array();

		foreach ( $manifest['pages'] as $page ) {
			if ( ! is_array( $page ) || ! isset( $page['slug'], $page['title'], $page['patterns'] ) || ! is_array( $page['patterns'] ) ) {
				return new WP_Error( 'lennox_invalid_starter_page', __( 'Starter site page data is invalid.', 'lennox' ) );
			}

			$page_slug = is_scalar( $page['slug'] ) ? sanitize_title( (string) $page['slug'] ) : '';

			if ( '' === $page_slug ) {
				return new WP_Error( 'lennox_invalid_starter_page', __( 'Starter site page data is invalid.', 'lennox' ) );
			}

			$template_slug = '';

			if ( isset( $page['template'] ) ) {
				$template_slug = is_scalar( $page['template'] ) ? sanitize_key( (string) $page['template'] ) : '';

				if ( '' === $template_slug || ! file_exists( get_theme_file_path( "templates/$template_slug.html" ) ) ) {
					return new WP_Error( 'lennox_invalid_starter_page_template', __( 'Starter site page template is invalid.', 'lennox' ) );
				}
			}

			$content = self::render_page_patterns( array_values( $page['patterns'] ) );

			if ( is_wp_error( $content ) ) {
				return $content;
			}

			$meta_input = array(
				self::META_STARTER_SLUG         => $starter_slug,
				self::META_STARTER_PAGE_SLUG    => $page_slug,
				self::META_STARTER_CONTENT_TYPE => 'page',
			);

			if ( '' !== $template_slug ) {
				$meta_input['_wp_page_template'] = $template_slug;
			}

			$page_id = self::upsert_owned_post(
				'page',
				$page_slug,
				array(
					'post_title'   => sanitize_text_field( (string) $page['title'] ),
					'post_content' => $content,
					'post_status'  => 'publish',
					'post_name'    => $page_slug,
				),
				$meta_input
			);

			if ( is_wp_error( $page_id ) ) {
				return $page_id;
			}

			$pages[ $page_slug ] = $page_id;
		}

		return $pages;
	}

	/**
	 * Render a list of patterns and inline blocks to static block markup.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int, mixed> $pattern_items Pattern slugs or inline block definitions.
	 * @return string|WP_Error Rendered content or error.
	 */
	private static function render_page_patterns( $pattern_items ) {
		$content = '';

		foreach ( $pattern_items as $pattern_item ) {
			if ( is_array( $pattern_item ) ) {
				if ( isset( $pattern_item['pattern'] ) ) {
					$pattern_content = self::render_page_pattern( $pattern_item['pattern'] );

					if ( is_wp_error( $pattern_content ) ) {
						return $pattern_content;
					}

					if ( isset( $pattern_item['removeBackground'] ) ) {
						if ( ! is_bool( $pattern_item['removeBackground'] ) ) {
							return new WP_Error( 'lennox_invalid_pattern_override', __( 'Starter site pattern override is invalid.', 'lennox' ) );
						}

						if ( $pattern_item['removeBackground'] ) {
							$pattern_content = self::remove_pattern_root_background( $pattern_content );

							if ( is_wp_error( $pattern_content ) ) {
								return $pattern_content;
							}
						}
					}

					if ( isset( $pattern_item['removeLinkBlocks'] ) ) {
						if ( ! is_bool( $pattern_item['removeLinkBlocks'] ) ) {
							return new WP_Error( 'lennox_invalid_pattern_override', __( 'Starter site pattern override is invalid.', 'lennox' ) );
						}

						if ( $pattern_item['removeLinkBlocks'] ) {
							$pattern_content = self::remove_pattern_link_blocks( $pattern_content );

							if ( is_wp_error( $pattern_content ) ) {
								return $pattern_content;
							}
						}
					}

					if ( isset( $pattern_item['removeRootBlocksContaining'] ) ) {
						$search_texts = is_array( $pattern_item['removeRootBlocksContaining'] ) ? array_values( $pattern_item['removeRootBlocksContaining'] ) : array( $pattern_item['removeRootBlocksContaining'] );

						foreach ( $search_texts as $search_text ) {
							$search_text = is_scalar( $search_text ) ? trim( (string) $search_text ) : '';

							if ( '' === $search_text ) {
								return new WP_Error( 'lennox_invalid_pattern_override', __( 'Starter site pattern override is invalid.', 'lennox' ) );
							}

							$pattern_content = self::remove_pattern_root_blocks_containing( $pattern_content, $search_text );

							if ( is_wp_error( $pattern_content ) ) {
								return $pattern_content;
							}
						}
					}

					if ( isset( $pattern_item['removeLeafBlock'] ) ) {
						$leaf_block = $pattern_item['removeLeafBlock'];
						$block_name = is_array( $leaf_block ) && isset( $leaf_block['block'] ) && is_scalar( $leaf_block['block'] ) ? strtolower( (string) $leaf_block['block'] ) : '';
						$block_text = is_array( $leaf_block ) && isset( $leaf_block['text'] ) && is_scalar( $leaf_block['text'] ) ? trim( (string) $leaf_block['text'] ) : '';

						if ( ! preg_match( '/^[a-z0-9-]+\/[a-z0-9-]+$/', $block_name ) || '' === $block_text ) {
							return new WP_Error( 'lennox_invalid_pattern_override', __( 'Starter site pattern override is invalid.', 'lennox' ) );
						}

						$pattern_content = self::remove_pattern_leaf_block( $pattern_content, $block_name, $block_text );

						if ( is_wp_error( $pattern_content ) ) {
							return $pattern_content;
						}
					}

					if ( isset( $pattern_item['wrapper'] ) ) {
						$pattern_content = self::wrap_pattern_in_group( $pattern_content, $pattern_item['wrapper'] );

						if ( is_wp_error( $pattern_content ) ) {
							return $pattern_content;
						}
					}

					$content .= trim( $pattern_content ) . "\n\n";
					continue;
				}

				$block = self::prepare_page_block( $pattern_item );

				if ( is_wp_error( $block ) ) {
					return $block;
				}

				$content .= trim( serialize_block( $block ) ) . "\n\n";
				continue;
			}

			$pattern_content = self::render_page_pattern( $pattern_item );

			if ( is_wp_error( $pattern_content ) ) {
				return $pattern_content;
			}

			$content .= trim( $pattern_content ) . "\n\n";
		}

		return trim( $content );
	}

	/**
	 * Render a pattern slug to static block markup.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $pattern_slug Pattern slug.
	 * @return string|WP_Error Rendered pattern or error.
	 */
	private static function render_page_pattern( $pattern_slug ) {
		if ( ! is_scalar( $pattern_slug ) ) {
			return new WP_Error( 'lennox_invalid_pattern_slug', __( 'Starter site pattern slug is invalid.', 'lennox' ) );
		}

		$pattern_slug = sanitize_key( (string) $pattern_slug );
		$pattern_path = get_theme_file_path( "patterns/$pattern_slug.php" );

		if ( file_exists( $pattern_path ) ) {
			ob_start();
			include $pattern_path;

			return trim( (string) ob_get_clean() );
		}

		if ( class_exists( 'WP_Block_Patterns_Registry' ) ) {
			$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( "lennox/$pattern_slug" );

			if ( is_array( $pattern ) && isset( $pattern['content'] ) && is_string( $pattern['content'] ) ) {
				return trim( $pattern['content'] );
			}
		}

		return new WP_Error( 'lennox_missing_pattern', __( 'Starter site pattern file is missing.', 'lennox' ) );
	}

	/**
	 * Remove the preset background from a pattern's root Group block.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Pattern block markup.
	 * @return string|WP_Error Updated pattern content or error.
	 */
	private static function remove_pattern_root_background( $content ) {
		$blocks      = parse_blocks( $content );
		$block_index = null;

		foreach ( $blocks as $index => $block ) {
			if ( isset( $block['blockName'] ) && 'core/group' === $block['blockName'] ) {
				$block_index = $index;
				break;
			}
		}

		if ( null === $block_index || ! isset( $blocks[ $block_index ]['attrs']['backgroundColor'] ) || ! is_scalar( $blocks[ $block_index ]['attrs']['backgroundColor'] ) ) {
			return new WP_Error( 'lennox_invalid_pattern_override', __( 'Starter site pattern background could not be removed.', 'lennox' ) );
		}

		$background_slug  = sanitize_html_class( (string) $blocks[ $block_index ]['attrs']['backgroundColor'] );
		$background_class = 'has-' . $background_slug . '-background-color';

		unset( $blocks[ $block_index ]['attrs']['backgroundColor'] );

		$blocks[ $block_index ]['innerHTML'] = self::remove_pattern_background_classes(
			$blocks[ $block_index ]['innerHTML'],
			$background_class
		);

		foreach ( $blocks[ $block_index ]['innerContent'] as $index => $html ) {
			if ( is_string( $html ) ) {
				$blocks[ $block_index ]['innerContent'][ $index ] = self::remove_pattern_background_classes( $html, $background_class );
			}
		}

		return trim( serialize_blocks( $blocks ) );
	}

	/**
	 * Remove preset background classes from saved block HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html Block HTML fragment.
	 * @param string $background_class Preset background class.
	 * @return string Updated HTML fragment.
	 */
	private static function remove_pattern_background_classes( $html, $background_class ) {
		$processor = new WP_HTML_Tag_Processor( $html );

		while ( $processor->next_tag() ) {
			if ( $processor->has_class( $background_class ) ) {
				$processor->remove_class( $background_class );
				$processor->remove_class( 'has-background' );
				break;
			}
		}

		return $processor->get_updated_html();
	}

	/**
	 * Remove linked Paragraph blocks from pattern content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Pattern block markup.
	 * @return string|WP_Error Updated pattern content or error.
	 */
	private static function remove_pattern_link_blocks( $content ) {
		$result = self::filter_pattern_link_blocks( parse_blocks( $content ) );

		if ( 0 === $result['removed'] ) {
			return new WP_Error( 'lennox_invalid_pattern_override', __( 'Starter site pattern links could not be removed.', 'lennox' ) );
		}

		return trim( serialize_blocks( $result['blocks'] ) );
	}

	/**
	 * Recursively filter linked Paragraph blocks from a parsed block list.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int, array<string, mixed>> $blocks Parsed blocks.
	 * @return array{blocks: array<int, array<string, mixed>>, removed: int} Filtered blocks and removal count.
	 */
	private static function filter_pattern_link_blocks( $blocks ) {
		$filtered_blocks = array();
		$removed_count   = 0;

		foreach ( $blocks as $block ) {
			if ( self::is_linked_paragraph_block( $block ) ) {
				++$removed_count;
				continue;
			}

			if ( ! empty( $block['innerBlocks'] ) && ! empty( $block['innerContent'] ) ) {
				$original_inner_blocks = $block['innerBlocks'];
				$inner_block_index     = 0;
				$new_inner_blocks      = array();
				$new_inner_content     = array();

				foreach ( $block['innerContent'] as $html ) {
					if ( null !== $html ) {
						$new_inner_content[] = $html;
						continue;
					}

					if ( ! isset( $original_inner_blocks[ $inner_block_index ] ) ) {
						continue;
					}

					$inner_result   = self::filter_pattern_link_blocks( array( $original_inner_blocks[ $inner_block_index ] ) );
					$removed_count += $inner_result['removed'];
					++$inner_block_index;

					if ( ! empty( $inner_result['blocks'] ) ) {
						$new_inner_blocks[]  = $inner_result['blocks'][0];
						$new_inner_content[] = null;
					}
				}

				$block['innerBlocks']  = $new_inner_blocks;
				$block['innerContent'] = $new_inner_content;
			}

			$filtered_blocks[] = $block;
		}

		return array(
			'blocks'  => $filtered_blocks,
			'removed' => $removed_count,
		);
	}

	/**
	 * Determine whether a parsed block is a Paragraph containing a link.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $block Parsed block.
	 * @return bool Whether the block is a linked Paragraph.
	 */
	private static function is_linked_paragraph_block( $block ) {
		if ( ! isset( $block['blockName'], $block['innerHTML'] ) || 'core/paragraph' !== $block['blockName'] || ! is_string( $block['innerHTML'] ) ) {
			return false;
		}

		$processor = new WP_HTML_Tag_Processor( $block['innerHTML'] );

		return $processor->next_tag( array( 'tag_name' => 'A' ) );
	}

	/**
	 * Remove one matching leaf block from pattern content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Pattern block markup.
	 * @param string $block_name Block name to remove.
	 * @param string $block_text Exact block text to remove.
	 * @return string|WP_Error Updated pattern content or error.
	 */
	private static function remove_pattern_leaf_block( $content, $block_name, $block_text ) {
		$result = self::filter_pattern_leaf_blocks( parse_blocks( $content ), $block_name, $block_text );

		if ( 0 === $result['removed'] ) {
			return new WP_Error( 'lennox_invalid_pattern_override', __( 'Starter site pattern block could not be removed.', 'lennox' ) );
		}

		return trim( serialize_blocks( $result['blocks'] ) );
	}

	/**
	 * Recursively filter matching leaf blocks from a parsed block list.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int, array<string, mixed>> $blocks Parsed blocks.
	 * @param string                           $block_name Block name to remove.
	 * @param string                           $block_text Exact block text to remove.
	 * @return array{blocks: array<int, array<string, mixed>>, removed: int} Filtered blocks and removal count.
	 */
	private static function filter_pattern_leaf_blocks( $blocks, $block_name, $block_text ) {
		$filtered_blocks = array();
		$removed_count   = 0;

		foreach ( $blocks as $block ) {
			if ( self::is_matching_leaf_block( $block, $block_name, $block_text ) ) {
				++$removed_count;
				continue;
			}

			if ( ! empty( $block['innerBlocks'] ) && ! empty( $block['innerContent'] ) ) {
				$original_inner_blocks = $block['innerBlocks'];
				$inner_block_index     = 0;
				$new_inner_blocks      = array();
				$new_inner_content     = array();

				foreach ( $block['innerContent'] as $html ) {
					if ( null !== $html ) {
						$new_inner_content[] = $html;
						continue;
					}

					if ( ! isset( $original_inner_blocks[ $inner_block_index ] ) ) {
						continue;
					}

					$inner_result   = self::filter_pattern_leaf_blocks( array( $original_inner_blocks[ $inner_block_index ] ), $block_name, $block_text );
					$removed_count += $inner_result['removed'];
					++$inner_block_index;

					if ( ! empty( $inner_result['blocks'] ) ) {
						$new_inner_blocks[]  = $inner_result['blocks'][0];
						$new_inner_content[] = null;
					}
				}

				$block['innerBlocks']  = $new_inner_blocks;
				$block['innerContent'] = $new_inner_content;
			}

			$filtered_blocks[] = $block;
		}

		return array(
			'blocks'  => $filtered_blocks,
			'removed' => $removed_count,
		);
	}

	/**
	 * Determine whether a parsed leaf block has an exact text match.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $block Parsed block.
	 * @param string               $block_name Expected block name.
	 * @param string               $block_text Expected block text.
	 * @return bool Whether the block matches.
	 */
	private static function is_matching_leaf_block( $block, $block_name, $block_text ) {
		if ( ! isset( $block['blockName'], $block['innerHTML'] ) || $block_name !== $block['blockName'] || ! empty( $block['innerBlocks'] ) || ! is_string( $block['innerHTML'] ) ) {
			return false;
		}

		$parsed_text = html_entity_decode( wp_strip_all_tags( $block['innerHTML'] ), ENT_QUOTES, get_bloginfo( 'charset' ) );

		return trim( $parsed_text ) === $block_text;
	}

	/**
	 * Remove root-level pattern blocks containing the supplied text.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Pattern block markup.
	 * @param string $search_text Text that identifies blocks to remove.
	 * @return string|WP_Error Updated pattern content or error.
	 */
	private static function remove_pattern_root_blocks_containing( $content, $search_text ) {
		$filtered_blocks = array();
		$removed_count   = 0;

		foreach ( parse_blocks( $content ) as $block ) {
			$block_text = html_entity_decode( wp_strip_all_tags( serialize_block( $block ) ), ENT_QUOTES, get_bloginfo( 'charset' ) );

			if ( false !== strpos( $block_text, $search_text ) ) {
				++$removed_count;
				continue;
			}

			$filtered_blocks[] = $block;
		}

		if ( 0 === $removed_count ) {
			return new WP_Error( 'lennox_invalid_pattern_override', __( 'Starter site pattern blocks could not be removed.', 'lennox' ) );
		}

		return trim( serialize_blocks( $filtered_blocks ) );
	}

	/**
	 * Wrap a pattern's blocks in a Group block.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Pattern block markup.
	 * @param mixed  $definition Group block definition.
	 * @return string|WP_Error Wrapped pattern content or error.
	 */
	private static function wrap_pattern_in_group( $content, $definition ) {
		if ( ! is_array( $definition ) || ! isset( $definition['block'] ) || 'core/group' !== $definition['block'] ) {
			return new WP_Error( 'lennox_invalid_pattern_override', __( 'Starter site pattern wrapper is invalid.', 'lennox' ) );
		}

		$wrapper = self::prepare_page_block( $definition );

		if ( is_wp_error( $wrapper ) ) {
			return $wrapper;
		}

		$open_wrapper  = $wrapper['innerContent'][0];
		$close_wrapper = $wrapper['innerContent'][1];

		$wrapper['innerBlocks']  = parse_blocks( $content );
		$wrapper['innerContent'] = array( $open_wrapper );
		$block_count             = count( $wrapper['innerBlocks'] );

		for ( $index = 0; $index < $block_count; $index++ ) {
			$wrapper['innerContent'][] = null;

			if ( $index < $block_count - 1 ) {
				$wrapper['innerContent'][] = "\n\n";
			}
		}

		$wrapper['innerContent'][] = $close_wrapper;

		return trim( serialize_block( $wrapper ) );
	}

	/**
	 * Prepare an inline starter block for WordPress serialization.
	 *
	 * Inline block definitions support self-closing blocks and nested core Group
	 * blocks. This keeps dynamic blocks such as Post Title editable after import.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $definition Inline block definition.
	 * @return array<string, mixed>|WP_Error Prepared block or error.
	 */
	private static function prepare_page_block( $definition ) {
		if ( ! isset( $definition['block'] ) || ! is_scalar( $definition['block'] ) ) {
			return new WP_Error( 'lennox_invalid_starter_block', __( 'Starter site block definition is invalid.', 'lennox' ) );
		}

		$block_name = strtolower( (string) $definition['block'] );

		if ( ! preg_match( '/^[a-z0-9-]+\/[a-z0-9-]+$/', $block_name ) ) {
			return new WP_Error( 'lennox_invalid_starter_block', __( 'Starter site block definition is invalid.', 'lennox' ) );
		}

		$attributes = array();

		if ( isset( $definition['attrs'] ) ) {
			if ( ! is_array( $definition['attrs'] ) ) {
				return new WP_Error( 'lennox_invalid_starter_block', __( 'Starter site block attributes are invalid.', 'lennox' ) );
			}

			$attributes = $definition['attrs'];
		}

		$inner_blocks = array();

		if ( isset( $definition['innerBlocks'] ) ) {
			if ( ! is_array( $definition['innerBlocks'] ) ) {
				return new WP_Error( 'lennox_invalid_starter_block', __( 'Starter site inner blocks are invalid.', 'lennox' ) );
			}

			foreach ( array_values( $definition['innerBlocks'] ) as $inner_definition ) {
				if ( ! is_array( $inner_definition ) ) {
					return new WP_Error( 'lennox_invalid_starter_block', __( 'Starter site inner blocks are invalid.', 'lennox' ) );
				}

				$inner_block = self::prepare_page_block( $inner_definition );

				if ( is_wp_error( $inner_block ) ) {
					return $inner_block;
				}

				$inner_blocks[] = $inner_block;
			}
		}

		$inner_html    = '';
		$inner_content = array();

		if ( 'core/group' === $block_name ) {
			$wrapper = self::get_page_group_wrapper( $attributes );

			if ( is_wp_error( $wrapper ) ) {
				return $wrapper;
			}

			$inner_html    = $wrapper['open'] . $wrapper['close'];
			$inner_content = array( $wrapper['open'] );
			$block_count   = count( $inner_blocks );

			for ( $index = 0; $index < $block_count; $index++ ) {
				$inner_content[] = null;

				if ( $index < $block_count - 1 ) {
					$inner_content[] = "\n\n";
				}
			}

			$inner_content[] = $wrapper['close'];
		} elseif ( ! empty( $inner_blocks ) ) {
			return new WP_Error( 'lennox_unsupported_starter_block', __( 'Starter site block container is not supported.', 'lennox' ) );
		}

		return array(
			'blockName'    => $block_name,
			'attrs'        => $attributes,
			'innerBlocks'  => $inner_blocks,
			'innerHTML'    => $inner_html,
			'innerContent' => $inner_content,
		);
	}

	/**
	 * Build the saved wrapper markup for an inline core Group block.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $attributes Group block attributes.
	 * @return array{open: string, close: string}|WP_Error Wrapper markup or error.
	 */
	private static function get_page_group_wrapper( $attributes ) {
		$tag_name = 'div';

		if ( isset( $attributes['tagName'] ) ) {
			$allowed_tags = array( 'div', 'main', 'section', 'article', 'header', 'footer', 'aside', 'nav' );
			$tag_name     = is_scalar( $attributes['tagName'] ) ? strtolower( (string) $attributes['tagName'] ) : '';

			if ( ! in_array( $tag_name, $allowed_tags, true ) ) {
				return new WP_Error( 'lennox_invalid_starter_block', __( 'Starter site Group block tag is invalid.', 'lennox' ) );
			}
		}

		$classes = array( 'wp-block-group' );

		if ( isset( $attributes['align'] ) ) {
			$alignment = is_scalar( $attributes['align'] ) ? sanitize_key( (string) $attributes['align'] ) : '';

			if ( '' === $alignment ) {
				return new WP_Error( 'lennox_invalid_starter_block', __( 'Starter site Group block alignment is invalid.', 'lennox' ) );
			}

			$classes[] = 'align' . $alignment;
		}

		if ( isset( $attributes['backgroundColor'] ) ) {
			$background_color = is_scalar( $attributes['backgroundColor'] ) ? sanitize_html_class( (string) $attributes['backgroundColor'] ) : '';

			if ( '' === $background_color ) {
				return new WP_Error( 'lennox_invalid_starter_block', __( 'Starter site Group block background color is invalid.', 'lennox' ) );
			}

			$classes[] = 'has-' . $background_color . '-background-color';
			$classes[] = 'has-background';
		}

		if ( isset( $attributes['className'] ) ) {
			if ( ! is_scalar( $attributes['className'] ) ) {
				return new WP_Error( 'lennox_invalid_starter_block', __( 'Starter site Group block class is invalid.', 'lennox' ) );
			}

			$class_names = preg_split( '/\s+/', (string) $attributes['className'] );

			if ( false === $class_names ) {
				return new WP_Error( 'lennox_invalid_starter_block', __( 'Starter site Group block class is invalid.', 'lennox' ) );
			}

			foreach ( $class_names as $class_name ) {
				$class_name = sanitize_html_class( $class_name );

				if ( '' !== $class_name ) {
					$classes[] = $class_name;
				}
			}
		}

		$inline_style = '';

		if ( isset( $attributes['style'] ) ) {
			if ( ! is_array( $attributes['style'] ) ) {
				return new WP_Error( 'lennox_invalid_starter_block', __( 'Starter site Group block styles are invalid.', 'lennox' ) );
			}

			$style_data   = wp_style_engine_get_styles( $attributes['style'] );
			$declarations = isset( $style_data['declarations'] ) && is_array( $style_data['declarations'] ) ? $style_data['declarations'] : array();
			$style_order  = array(
				'margin-top',
				'margin-right',
				'margin-bottom',
				'margin-left',
				'padding-top',
				'padding-right',
				'padding-bottom',
				'padding-left',
			);
			$style_rules  = array();

			foreach ( $style_order as $property ) {
				if ( isset( $declarations[ $property ] ) && is_scalar( $declarations[ $property ] ) ) {
					$style_rules[] = $property . ':' . (string) $declarations[ $property ];
					unset( $declarations[ $property ] );
				}
			}

			foreach ( $declarations as $property => $value ) {
				if ( is_string( $property ) && is_scalar( $value ) ) {
					$style_rules[] = $property . ':' . (string) $value;
				}
			}

			$inline_style = implode( ';', $style_rules );
		}

		$opening_attributes = sprintf( ' class="%s"', esc_attr( implode( ' ', array_unique( $classes ) ) ) );

		if ( '' !== $inline_style ) {
			$opening_attributes .= sprintf( ' style="%s"', esc_attr( $inline_style ) );
		}

		return array(
			'open'  => sprintf( '<%1$s%2$s>', $tag_name, $opening_attributes ),
			'close' => sprintf( '</%s>', $tag_name ),
		);
	}

	/**
	 * Insert or update a Lennox-owned post by starter metadata.
	 *
	 * @since 1.0.0
	 *
	 * @param string               $post_type Post type.
	 * @param string               $object_slug Starter object slug.
	 * @param array<string, mixed> $post_data Post data.
	 * @param array<string, mixed> $meta_input Meta values.
	 * @return int|WP_Error Post ID or error.
	 */
	private static function upsert_owned_post( $post_type, $object_slug, $post_data, $meta_input ) {
		$post_id = self::get_owned_post_id( $post_type, $object_slug );

		$post_data['post_type']  = $post_type;
		$post_data['meta_input'] = $meta_input;

		if ( $post_id > 0 ) {
			if ( 'page' === $post_type ) {
				delete_post_meta( $post_id, '_wp_page_template' );
			}

			$post_data['ID'] = $post_id;
			$result          = wp_update_post( wp_slash( $post_data ), true );
		} else {
			$result = wp_insert_post( wp_slash( $post_data ), true );
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return (int) $result;
	}

	/**
	 * Get a Lennox-owned post ID by starter object slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type Post type.
	 * @param string $object_slug Starter object slug.
	 * @return int Post ID or 0.
	 */
	private static function get_owned_post_id( $post_type, $object_slug ) {
		$posts = get_posts(
			array(
				'fields'           => 'ids',
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Starter ownership is intentionally tracked by post meta.
				'meta_query'       => array(
					'relation' => 'AND',
					array(
						'key'   => self::META_STARTER_CONTENT_TYPE,
						'value' => $post_type,
					),
					array(
						'key'   => self::META_STARTER_PAGE_SLUG,
						'value' => $object_slug,
					),
				),
				'no_found_rows'    => true,
				'post_status'      => 'any',
				'post_type'        => $post_type,
				'posts_per_page'   => 1,
				'suppress_filters' => false,
			)
		);

		if ( empty( $posts ) ) {
			return 0;
		}

		return (int) $posts[0];
	}

	/**
	 * Set the imported front page.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $manifest Starter manifest.
	 * @param array<string, int>   $pages Imported page IDs.
	 * @return int Front page ID.
	 */
	private static function set_front_page( $manifest, $pages ) {
		if ( ! isset( $manifest['pages'] ) || ! is_array( $manifest['pages'] ) ) {
			return 0;
		}

		foreach ( $manifest['pages'] as $page ) {
			if ( ! is_array( $page ) || empty( $page['isFrontPage'] ) || ! isset( $page['slug'] ) ) {
				continue;
			}

			$page_slug = is_scalar( $page['slug'] ) ? sanitize_title( (string) $page['slug'] ) : '';

			if ( isset( $pages[ $page_slug ] ) ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $pages[ $page_slug ] );

				return $pages[ $page_slug ];
			}
		}

		return 0;
	}

	/**
	 * Create or update the starter front-page template override.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $manifest Starter manifest.
	 * @return int|WP_Error Template post ID or error.
	 */
	private static function import_front_page_template( $manifest ) {
		$stylesheet         = get_stylesheet();
		$has_header_overlay = ! isset( $manifest['headerOverlay'] ) || ! is_bool( $manifest['headerOverlay'] ) || $manifest['headerOverlay'];
		$header_class_name  = 'has-sticky-header';

		if ( $has_header_overlay ) {
			$header_class_name .= ' has-header-overlay has-transparent-header';
		}

		$content = sprintf(
			'<!-- wp:template-part {"slug":"header","tagName":"header","align":"wide","className":"%s"} /-->

<!-- wp:group {"tagName":"main","align":"full","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<main class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0"><!-- wp:post-content {"align":"full","layout":{"type":"default"}} /--></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->',
			$header_class_name
		);

		$post_id = self::upsert_owned_post(
			'wp_template',
			'front-page',
			array(
				'post_title'   => __( 'Front Page', 'lennox' ),
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_name'    => 'front-page',
				'tax_input'    => array(
					'wp_theme' => array( $stylesheet ),
				),
			),
			array(
				self::META_STARTER_CONTENT_TYPE => 'wp_template',
				self::META_STARTER_PAGE_SLUG    => 'front-page',
			)
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		return $post_id;
	}

	/**
	 * Create or update the starter navigation post.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $manifest Starter manifest.
	 * @param array<string, int>   $pages Imported page IDs.
	 * @return int|WP_Error Navigation post ID or error.
	 */
	private static function import_navigation( $manifest, $pages ) {
		if ( ! isset( $manifest['navigation'] ) || ! is_array( $manifest['navigation'] ) ) {
			return new WP_Error( 'lennox_invalid_navigation', __( 'Starter site navigation is invalid.', 'lennox' ) );
		}

		$navigation_blocks = array();

		foreach ( $manifest['navigation'] as $page_slug ) {
			if ( ! is_scalar( $page_slug ) ) {
				continue;
			}

			$page_slug = sanitize_title( (string) $page_slug );

			if ( ! isset( $pages[ $page_slug ] ) ) {
				continue;
			}

			$page_id = $pages[ $page_slug ];
			$title   = get_the_title( $page_id );

			$navigation_blocks[] = sprintf(
				'<!-- wp:navigation-link {"label":"%1$s","type":"page","id":%2$d,"url":"%3$s","kind":"post-type"} /-->',
				esc_attr( $title ),
				$page_id,
				esc_url( (string) get_permalink( $page_id ) )
			);
		}

		$post_id = self::upsert_owned_post(
			'wp_navigation',
			'primary-navigation',
			array(
				'post_title'   => __( 'Lennox Starter Navigation', 'lennox' ),
				'post_content' => implode( "\n\n", $navigation_blocks ),
				'post_status'  => 'publish',
				'post_name'    => 'lennox-starter-navigation',
			),
			array(
				self::META_STARTER_CONTENT_TYPE => 'wp_navigation',
				self::META_STARTER_PAGE_SLUG    => 'primary-navigation',
			)
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		return $post_id;
	}

	/**
	 * Create or update a user header template part override that references starter navigation.
	 *
	 * @since 1.0.0
	 *
	 * @param int $navigation_id Navigation post ID.
	 * @return int|WP_Error Template part post ID or error.
	 */
	private static function import_header_template_part( $navigation_id ) {
		$stylesheet = get_stylesheet();
		$content    = sprintf(
			'<!-- wp:group {"style":{"position":{"type":""},"color":{"background":"var:preset|color|custom-background"}},"layout":{"inherit":"true","type":"constrained"}} -->
<div class="wp-block-group has-background" style="background-color:var(--wp--preset--color--custom-background)"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"30px","top":"30px"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide" style="padding-top:30px;padding-bottom:30px"><!-- wp:group {"layout":{"type":"flex"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"width":64} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-group"><!-- wp:site-title /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:navigation {"ref":%d,"style":{"spacing":{"margin":{"top":"0"}}},"layout":{"type":"flex","setCascadingProperties":true,"orientation":"horizontal"}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
			$navigation_id
		);

		$post_id = self::upsert_owned_post(
			'wp_template_part',
			'header',
			array(
				'post_title'   => __( 'Header', 'lennox' ),
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_name'    => 'header',
				'tax_input'    => array(
					'wp_theme' => array( $stylesheet ),
				),
			),
			array(
				self::META_STARTER_CONTENT_TYPE => 'wp_template_part',
				self::META_STARTER_PAGE_SLUG    => 'header',
			)
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		return $post_id;
	}

	/**
	 * Merge starter style data into user global styles.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $manifest Starter manifest.
	 * @return int|WP_Error Global styles post ID or error.
	 */
	private static function merge_global_styles( $manifest ) {
		$global_styles_id = self::get_user_global_styles_post_id();

		if ( is_wp_error( $global_styles_id ) ) {
			return $global_styles_id;
		}

		$post_content = '';
		$post         = get_post( $global_styles_id );

		if ( $post instanceof WP_Post ) {
			$post_content = $post->post_content;
		}

		$user_styles = json_decode( $post_content, true );

		if ( ! is_array( $user_styles ) ) {
			$user_styles = array( 'version' => 3 );
		}

		$user_styles = self::merge_colors( $user_styles, $manifest );
		$user_styles = self::merge_typeset( $user_styles, isset( $manifest['typeset'] ) && is_scalar( $manifest['typeset'] ) ? sanitize_key( (string) $manifest['typeset'] ) : '' );

		$result = wp_update_post(
			wp_slash(
				array(
					'ID'           => $global_styles_id,
					'post_content' => wp_json_encode( $user_styles ),
				)
			),
			true
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( function_exists( 'wp_clean_theme_json_cache' ) ) {
			wp_clean_theme_json_cache();
		}

		return (int) $result;
	}

	/**
	 * Get or create the current theme user global styles post.
	 *
	 * @since 1.0.0
	 *
	 * @return int|WP_Error Post ID or error.
	 */
	private static function get_user_global_styles_post_id() {
		if ( class_exists( 'WP_Theme_JSON_Resolver' ) ) {
			$global_styles_id = (int) WP_Theme_JSON_Resolver::get_user_global_styles_post_id();

			if ( $global_styles_id > 0 ) {
				return $global_styles_id;
			}
		}

		$stylesheet = get_stylesheet();
		$post_id    = wp_insert_post(
			wp_slash(
				array(
					'post_content' => wp_json_encode( array( 'version' => 3 ) ),
					'post_name'    => 'wp-global-styles-' . $stylesheet,
					'post_status'  => 'publish',
					'post_title'   => __( 'Custom Styles', 'lennox' ),
					'post_type'    => 'wp_global_styles',
					'tax_input'    => array(
						'wp_theme' => array( $stylesheet ),
					),
				)
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		return (int) $post_id;
	}

	/**
	 * Merge starter colors into global styles.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $user_styles User styles.
	 * @param array<string, mixed> $manifest Starter manifest.
	 * @return array<string, mixed> Merged styles.
	 */
	private static function merge_colors( $user_styles, $manifest ) {
		if ( ! isset( $manifest['colors'] ) || ! is_array( $manifest['colors'] ) ) {
			return $user_styles;
		}

		$starter_colors = self::normalize_assoc_array( $manifest['colors'] );
		$palette        = self::get_theme_json_palette();
		$duotone        = self::get_theme_json_duotone();

		foreach ( $palette as $index => $color_item ) {
			if ( ! isset( $color_item['slug'] ) || ! is_string( $color_item['slug'] ) || ! isset( $starter_colors[ $color_item['slug'] ] ) ) {
				continue;
			}

			$palette[ $index ]['color'] = is_scalar( $starter_colors[ $color_item['slug'] ] ) ? sanitize_hex_color( (string) $starter_colors[ $color_item['slug'] ] ) : '';
		}

		foreach ( $duotone as $index => $duotone_item ) {
			if ( ! isset( $duotone_item['slug'], $duotone_item['colors'] ) || ! is_string( $duotone_item['slug'] ) || ! is_array( $duotone_item['colors'] ) ) {
				continue;
			}

			$colors = self::get_duotone_colors_for_slug( $duotone_item['slug'], $starter_colors );

			if ( ! empty( $colors ) ) {
				$duotone[ $index ]['colors'] = $colors;
			}
		}

		$starter_styles = array(
			'settings' => array(
				'color' => array(
					'palette' => array(
						'theme' => $palette,
					),
					'duotone' => array(
						'theme' => $duotone,
					),
				),
			),
			'styles'   => array(
				'color'    => array(
					'background' => 'var:preset|color|custom-background',
					'text'       => 'var:preset|color|custom-text',
				),
				'elements' => array(
					'button' => array(
						'color'  => array(
							'background' => 'var:preset|color|custom-accent',
							'text'       => '#ffffff',
						),
						':hover' => array(
							'color' => array(
								'background' => 'var:preset|color|custom-hover',
								'text'       => '#ffffff',
							),
						),
					),
					'link'   => array(
						':hover' => array(
							'color' => array(
								'text' => 'var:preset|color|custom-hover',
							),
						),
					),
				),
			),
		);

		return array_replace_recursive( $user_styles, $starter_styles );
	}

	/**
	 * Merge starter typography preset into global styles.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $user_styles User styles.
	 * @param string               $typeset_slug Typeset slug.
	 * @return array<string, mixed> Merged styles.
	 */
	private static function merge_typeset( $user_styles, $typeset_slug ) {
		if ( '' === $typeset_slug ) {
			return $user_styles;
		}

		$typeset_path = get_theme_file_path( "styles/typography/$typeset_slug.json" );

		if ( ! file_exists( $typeset_path ) ) {
			return $user_styles;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading a bundled local JSON file.
		$contents = file_get_contents( $typeset_path );

		if ( false === $contents ) {
			return $user_styles;
		}

		$typeset = json_decode( $contents, true );

		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $typeset ) ) {
			return $user_styles;
		}

		$starter_styles = array();
		$settings       = isset( $typeset['settings'] ) && is_array( $typeset['settings'] ) ? $typeset['settings'] : array();
		$typography     = isset( $settings['typography'] ) && is_array( $settings['typography'] ) ? $settings['typography'] : array();
		$styles         = isset( $typeset['styles'] ) && is_array( $typeset['styles'] ) ? $typeset['styles'] : array();
		$style_elements = isset( $styles['elements'] ) && is_array( $styles['elements'] ) ? $styles['elements'] : array();
		$heading        = isset( $style_elements['heading'] ) && is_array( $style_elements['heading'] ) ? $style_elements['heading'] : array();

		if ( isset( $typography['fontFamilies'] ) && is_array( $typography['fontFamilies'] ) ) {
			$starter_styles['settings']['typography']['fontFamilies']['theme'] = $typography['fontFamilies'];
		}

		if ( isset( $styles['typography'] ) && is_array( $styles['typography'] ) ) {
			$starter_styles['styles']['typography'] = $styles['typography'];
		}

		if ( isset( $heading['typography'] ) && is_array( $heading['typography'] ) ) {
			$starter_styles['styles']['elements']['heading']['typography'] = $heading['typography'];
		}

		return array_replace_recursive( $user_styles, $starter_styles );
	}

	/**
	 * Get the base theme palette.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private static function get_theme_json_palette() {
		$theme_json = self::read_theme_json();
		$settings   = isset( $theme_json['settings'] ) && is_array( $theme_json['settings'] ) ? $theme_json['settings'] : array();
		$color      = isset( $settings['color'] ) && is_array( $settings['color'] ) ? $settings['color'] : array();

		if ( isset( $color['palette'] ) && is_array( $color['palette'] ) ) {
			return self::normalize_list_of_arrays( $color['palette'] );
		}

		return array();
	}

	/**
	 * Get the base theme duotone presets.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private static function get_theme_json_duotone() {
		$theme_json = self::read_theme_json();
		$settings   = isset( $theme_json['settings'] ) && is_array( $theme_json['settings'] ) ? $theme_json['settings'] : array();
		$color      = isset( $settings['color'] ) && is_array( $settings['color'] ) ? $settings['color'] : array();

		if ( isset( $color['duotone'] ) && is_array( $color['duotone'] ) ) {
			return self::normalize_list_of_arrays( $color['duotone'] );
		}

		return array();
	}

	/**
	 * Read theme.json.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	private static function read_theme_json() {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading the bundled local theme.json file.
		$contents = file_get_contents( get_theme_file_path( 'theme.json' ) );

		if ( false === $contents ) {
			return array();
		}

		$theme_json = json_decode( $contents, true );

		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $theme_json ) ) {
			return array();
		}

		$normalized = array();

		foreach ( $theme_json as $key => $value ) {
			if ( is_string( $key ) ) {
				$normalized[ $key ] = $value;
			}
		}

		return $normalized;
	}

	/**
	 * Normalize a mixed list to an array list.
	 *
	 * @since 1.0.0
	 *
	 * @param array<mixed> $items Raw list items.
	 * @return array<int, array<string, mixed>>
	 */
	private static function normalize_list_of_arrays( $items ) {
		$normalized = array();

		foreach ( $items as $item ) {
			if ( is_array( $item ) ) {
				$normalized[] = $item;
			}
		}

		return $normalized;
	}

	/**
	 * Normalize an array to string keys.
	 *
	 * @since 1.0.0
	 *
	 * @param array<mixed> $items Raw array.
	 * @return array<string, mixed>
	 */
	private static function normalize_assoc_array( $items ) {
		$normalized = array();

		foreach ( $items as $key => $value ) {
			if ( is_string( $key ) ) {
				$normalized[ $key ] = $value;
			}
		}

		return $normalized;
	}

	/**
	 * Get starter duotone colors for a known preset slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string               $duotone_slug Duotone slug.
	 * @param array<string, mixed> $colors Starter colors.
	 * @return string[] Duotone color pair.
	 */
	private static function get_duotone_colors_for_slug( $duotone_slug, $colors ) {
		$duotone_map = array(
			'accent-dark'   => array( 'custom-accent', '#000000' ),
			'accent-light'  => array( 'custom-accent', '#FFFFFF' ),
			'hover-dark'    => array( 'custom-hover', '#000000' ),
			'hover-light'   => array( 'custom-hover', '#FFFFFF' ),
			'overlay-dark'  => array( 'custom-dark-background', '#000000' ),
			'overlay-light' => array( 'custom-dark-background', '#FFFFFF' ),
			'monochrome'    => array( '#000000', '#FFFFFF' ),
			'subtle-gray'   => array( 'custom-border', '#000000' ),
		);

		if ( ! isset( $duotone_map[ $duotone_slug ] ) ) {
			return array();
		}

		$duotone_colors = array();

		foreach ( $duotone_map[ $duotone_slug ] as $color ) {
			if ( isset( $colors[ $color ] ) && is_scalar( $colors[ $color ] ) ) {
				$duotone_colors[] = sanitize_hex_color( (string) $colors[ $color ] );
				continue;
			}

			$duotone_colors[] = sanitize_hex_color( $color );
		}

		return array_filter( $duotone_colors );
	}
}
