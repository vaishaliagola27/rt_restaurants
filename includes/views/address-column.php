<?php
//restaurant address column
foreach ($address as $key => $val) {
	?>
	<span itemprop="<?php echo $key ?>"> <?php echo $val ?>
	</span>
	<?php
}
