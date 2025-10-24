<div class="sessionTest index">
	<h2>CakePHP 2 Redis Session Test</h2>
	
	<div class="content">
		<h3>Session Information</h3>
		<table>
			<tr>
				<th>Session ID:</th>
				<td><?php echo h($session_id); ?></td>
			</tr>
			<tr>
				<th>Visit Count:</th>
				<td><?php echo h($visit_count); ?></td>
			</tr>
			<tr>
				<th>Last Visit:</th>
				<td><?php echo h($last_visit); ?></td>
			</tr>
		</table>
		
		<p>
			Refresh this page to increment the visit counter. 
			The session data is stored in Redis.
		</p>
		
		<div class="actions">
			<?php echo $this->Html->link('Refresh Page', array('action' => 'index')); ?> | 
			<?php echo $this->Html->link('Clear Session', array('action' => 'clear')); ?>
		</div>
	</div>
</div>

<style>
	.sessionTest {
		padding: 20px;
		font-family: Arial, sans-serif;
	}
	
	h2 {
		color: #333;
	}
	
	table {
		border-collapse: collapse;
		margin: 20px 0;
	}
	
	th, td {
		padding: 10px;
		border: 1px solid #ddd;
		text-align: left;
	}
	
	th {
		background-color: #f5f5f5;
		font-weight: bold;
	}
	
	.actions {
		margin-top: 20px;
	}
	
	.actions a {
		padding: 8px 15px;
		background-color: #0066cc;
		color: white;
		text-decoration: none;
		border-radius: 4px;
		margin-right: 10px;
		display: inline-block;
	}
	
	.actions a:hover {
		background-color: #0052a3;
	}
</style>
