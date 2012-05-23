<?php

// 	STOP! DO NOT MODIFY THIS FILE!
//	If you wish to customize the output, you can safely do so by COPYING this file
//	into a new folder called 'oscommerce-templates' in your 'wp-content' directory
//	and then making your changes there. When in place, that file will load in place of this one.
// the loader looks here
// 1) Child theme directory
// 2) Parent theme directory
// 3) wp-content directory
// 4) Default template directory
//
// all data is in object $record !!!!!!!
?>
<div class="special-offer-box">
	<div class="special-offer-box-img">
		<a href="<?php echo $record->products_link ?>"><img class="special-offer-img"
			title="<?php echo $record->products_name ?>" alt="<?php $record->products_name ?>"
			src="<?php echo $record->image_url ?>"> </a>
	</div>
	<div class="special-offer-box-text">
		<a href="<?php echo $record->products_link ?>"> <span class="special-offer-box-format"><?php echo $record->products_format ?>
		</span> <span class="special-offer-box-name"><?php echo $record->products_name ?> </span>
		</a> <br /> <br/><span class="special-offer-box-price"><?php echo $record->products_price_gross ?> </span>
	</div>
</div>
<br class="clear" />
