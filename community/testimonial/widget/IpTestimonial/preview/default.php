<?php if ($author) { ?>
	<blockquote class="ip_testimonial">
	  <p><?php echo $this->esc($comment); ?></p>
	</blockquote>
	<div class="arrow-down"></div>
	<p class="ip_testimonial-author"><?php echo $this->esc($author);  if ($extras) { ?> | <span><?php echo $this->esc($extras); ?></span><?php }?></p>
<?php }?>