<div>	
	<h3><?=$title?></h3>
	<ul>
		<?php foreach(array_slice($results,0, 5) as $name => $score):?>
			<li>
				<?php if(isset($link) && $link == false):?>
					<?=ucfirst($name)?>
				<?php else:?>
					<a href='https://github.com/<?=$name?>'><?=ucfirst($name)?></a> 
				<?php endif;?>
				<strong><?=$score?></strong>
			</li>
		<?php endforeach;?>
	</ul>
</div>