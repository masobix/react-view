<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use FRZR\Helper\Utils;

$product_id = get_post_meta(get_the_ID(), 'frzr_bind_product_id', true);
$minimum_meta = get_post_meta(get_the_ID(), 'minimum', true);
$minimum = empty($minimum_meta) ? 1 : intval($minimum_meta);
?>
<div <?php echo esc_attr(get_block_wrapper_attributes()); ?>>
	<?php if (isset($attributes['showAmountBox']) && $attributes['showAmountBox']) : ?>
		<label for="amount-box"><?php esc_html_e('Amount box', 'fundrizer'); ?></label>
	<?php endif; ?>
	<input type="number" id="amount-box" class="frzr-input" min="<?php echo esc_attr($minimum); ?>" placeholder="<?php echo esc_attr(Utils::currency_format($minimum, ['no_symbol'])); ?>" data-product-id="<?php echo esc_attr($product_id); ?>">
	<?php if ($minimum_meta) : ?>
		<small><?php esc_html_e('Minimum', 'fundrizer'); ?> : <?php echo esc_html(Utils::currency_format($minimum)); ?></small>
	<?php endif; ?>
</div>
