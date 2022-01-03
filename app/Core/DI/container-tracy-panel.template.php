<?php

declare(strict_types=1);

/**
 * @var Core\DI\ContainerTracyPanel $this
 */

?>
<h1>DI Container</h1>

<div class="tracy-inner">
	<div class="tracy-inner-container">

		<h2>Instances in the registry:</h2>

		<table class="tracy-sortable">
			<?php $totalCount = 0; ?>
			<thead>
				<tr>
					<th>Name</th>
					<th>Count</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->stats['instances'] as $type => $count): ?>
					<tr>
						<?php $totalCount += $count; ?>
						<td><?= htmlspecialchars($type) ?></td>
						<td><?= htmlspecialchars((string) $count) ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<th>Total count</th>
					<th><?= $totalCount ?></th>
				</tr>
				<tr>
					<th>Number of unique instances</th>
					<th><?= $this->stats['numUniqueInstances'] ?></th>
				</tr>
			</tfoot>
		</table>

		<h2>Factories:</h2>

		<table class="tracy-sortable">
			<thead>
				<tr>
					<th>Type</th>
					<th>Factory</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->stats['factories'] as $type => $factory): ?>
					<tr>
						<td><?= htmlspecialchars($type) ?></td>
						<td><?= htmlspecialchars($factory) ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	</div>
</div>
