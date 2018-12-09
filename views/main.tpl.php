<h1><?=$milestone['title']?></h1>

<div class="row headline">
	<?php $this->view('top-5',['title'=> 'Top 5 sumbitters', 'results'=> $top_submit]); ?>
	<?php $this->view('top-5',['title'=> 'Top 5 assigned', 'results'=> $top_assigned]); ?>
	<?php $this->view('top-5',['title'=> 'Top 5 labels', 'results'=> $top_labeled ,'link' => false]); ?>
</div>

<?php foreach ($groups as $name => $issues): ?>
	<div class="row">
		<div>
			<h3><?=$name?> (<?=sizeof($issues)?>)</h3>

			<?php foreach ($issues as $issue): ?>
				<a href="<?=$issue['url']?>"><strong>GH-<?=$issue['number']?>:</strong></a> <?=$issue['title'];?>

				<?php if (!empty($issue->assignee)): ?>
					<i>[<?=$issue['assignee']['login']?>]</i>
				<?php endif; ?>

				<br/>
			<?php endforeach;?>
		</div>
	</div>
<?php endforeach;?>


<h2>Raw data</h2>
<pre><?=print_r($allIssues, true)?></pre>