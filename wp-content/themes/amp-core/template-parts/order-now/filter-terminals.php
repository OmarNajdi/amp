<?php
global $ordernow;
?>

<div class="filter-terminals">
		<span>
				<?php echo $ordernow->getFilterPretext() . "&nbsp" . $ordernow->getTerminalPickerLink(); ?>
		</span>

	<?php get_template_part( 'template-parts/order-now/filter-terminals', 'modal' ); ?>
</div>
