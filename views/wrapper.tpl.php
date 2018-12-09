<!DOCTYPE html>
<html>
<head>
	<title>GH Milestone report</title>
	
	<style>
		html, body {
			background: #f4f4f4;
			font-size: 16px;
			padding:0;margin: 0;
			font-family: arial;
		}

		h1,h2,h3 {
			margin-top:0;
		}
		a {
			color: #000;
			text-decoration: none
		}
		a:hover {
			text-decoration: underline;
		}
		
		main {
			background: #fff;
			max-width: 960px;
			position: relative;
			margin: 1rem auto;
			padding: 2rem;
			box-shadow: 0 2px 2px rgba(0,0,0,0.06 );
		}

		.row div {
			margin-bottom:2rem;
		}

		@media (min-width: 990px) {
			main {
				margin-left: auto;
				margin-right: auto;
			}
			.row {
				display:flex;
			}
			.row + .row {
				margin-bottom:1rem;
			}
			.row > div {
				flex: 1;
				margin: 10px;
			}
			.row > div:first-child {
				margin-left: 0px;
			}
			.row > div:last-child {
				margin-right: 0px;
			}
		}

		.headline {
	   		
	    	margin-bottom:2rem;
		}
		.headline div {
			padding: 1rem 1.5rem;
			background: #f4f4f4;
	    	border-radius: 6px;
	    	box-shadow: 0 2px 2px rgba(0,0,0,0.06 );
		}
		.headline li strong {
			float: right;
		}

		ul {
			padding-left: 1rem;
			margin-bottom: 0;
		}

		pre { background: #f4f4f4; border: solid 1px #efefef; padding:20px;  overflow: auto; font-size:.8rem;}


		.bar {
			background: #444; padding:.8rem 1rem ;
		}
		.bar form {float:right; }
		.bar:after {
			content: "";
			display: table;
			clear: both;
		}
		.bar h1 {
			color: #fff;
			padding:0;margin:0;
			font-size:1.1rem;	
			float:left;
			font-weight: normal;
			line-height: 1.82rem;
		}
		.bar select, .bar input {padding: .3rem;}
	</style>
		
	</head>
	<body>
		<div class='bar'>
			<h1>Milestone report</h1>
			<form method="GET">
				<select placeholder="Repo.." name='repo'>

					<?php foreach($repos as $r): ?>
						<option value="<?=$r['name']?>" <?php if($r['name']==$repo_name): ?>selected="selected"<?php endif;?>> <?=$r['name']?></option>
					<?php endforeach; ?>
				</select>	
				<select placeholder="Milestone..." name='milestone'>
					<?php foreach($milestones as $milestone): ?>
						<option value="<?=$milestone['number']?>" style="<?=$milestone['state']=='closed'?'color:grey':''?>" <?php if($milestone['number']==$milestone_id): ?>selected="selected"<?php endif;?>><?=$milestone['title']?></option>
					<?php endforeach; ?>
				</select>
				<input type='submit' value='View'>
			</form>
		</div>
		<main>
			<?php $this->view($content, $data); ?>
		</main>
	</body>
</html>