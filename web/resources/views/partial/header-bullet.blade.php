<?php
switch ($color ?? null) {
	case 'dark':
		$color = '#413f41';
		break;
	case 'orange':
		$color = '#f26524';
		break;
	default: // default is orange
		$color = isset($color) ? $color : '#f26524';
		break;
}
?>
<div class="d-inline text-center step-main" style="width: 2rem; background-color: {{ $color }};"></div>
@if (isset($title))
	<div style="padding-left: 30px">
		<h4 style="padding-top: 8px; color: {{ $color }};">
			<span style="font-weight: bold; font-size: 24px;">{{ $title }}</span>
		</h4>
	</div>
@endif