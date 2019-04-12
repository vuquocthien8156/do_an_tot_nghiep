<?php $currentStep = isset($currentStep) ? $currentStep - 1 : 0; ?>
@for ($i = 0; $i < $steps; $i++)
	<div class="d-inline orange text-center step-main" @if ($currentStep < $i) style="opacity: 0.5" @endif>
		<b>{{ $i + 1 }}</b>
	</div>
	@if ($i < $steps - 1)
		<div style="width: 20px;">
			<div class="step-small-center orange" @if ($currentStep < $i + 1) style="opacity: 0.5" @endif></div>
		</div>
	@endif
@endfor