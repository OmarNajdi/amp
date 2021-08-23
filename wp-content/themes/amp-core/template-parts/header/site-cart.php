<div class="cart-container">
	<a class="cart-customlocation" href="<?php echo wc_get_cart_url(); ?>"
	   title="<?php _e( 'View your shopping cart' ); ?>"><?php echo WC()->cart->get_cart_contents_count(); ?></a>
	<a href="<?php echo wc_get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>">
		<img src="<?php echo amp_image_uri( 'icon-cart.svg' ); ?>" alt="Cart" class="cart-icon">
	</a>
</div>
