<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
$progress_value = get_post_meta(get_the_ID(), 'echo_progress', true);
$progress_value = !empty($progress_value) ? (int) $progress_value : 0;
$progress_value = $progress_value > 100 ? 100 : $progress_value;
?>
<div class="progress-bar-container" <?php echo esc_attr(get_block_wrapper_attributes()); ?>>
	<div class="progress-bar-visual">
		<div class="progress-bar-visual-element" style="width: <?php echo esc_attr($progress_value . '%'); ?>;"></div>
	</div>
</div>
