<?php

declare(strict_types=1);

/**
 * @var Core\DI\ContainerTracyPanel $this
 */

?>
<style>

	/* override Tracy's reset style that set `opacity: 1;` for `#tracy-debug *` */
	#tracy-debug .fa-secondary {
		opacity: .4;
	}

</style>
<span title="Core\DI\Container">
	<!-- source: https://fontawesome.com/v5.15/icons/project-diagram?style=duotone -->
	<svg
		aria-hidden="true"
		focusable="false"
		data-prefix="fad"
		data-icon="project-diagram"
		class="svg-inline--fa fa-project-diagram fa-w-20"
		role="img"
		xmlns="http://www.w3.org/2000/svg"
		viewBox="0 0 640 512"
	>
		<g class="fa-group">
			<path
				class="fa-secondary"
				fill="currentColor"
				d="M416 128H164.65l91.63 160H256a63.79 63.79 0 0 0-55.12 32L54.78 64H416z"
				opacity="0.4"
			></path>
			<path
				class="fa-primary"
				fill="currentColor"
				d="M384 320H256a32 32 0 0 0-32 32v128a32 32 0 0 0 32 32h128a32 32 0 0 0 32-32V352a32 32 0 0 0-32-32zM160 0H32A32 32 0 0 0 0 32v128a32 32 0 0 0 32 32h128a32 32 0 0 0 32-32V32a32 32 0 0 0-32-32zm448 0H480a32 32 0 0 0-32 32v128a32 32 0 0 0 32 32h128a32 32 0 0 0 32-32V32a32 32 0 0 0-32-32z"
			></path>
		</g>
	</svg>
	<span class="tracy-label">DI <?= $this->stats['numUniqueInstances'] ?></span>
</span>
