<script type="application/javascript">
	$(document).ready(function() {
		$('body').on('click', function(e) {
			if (e.target.className && e.target.id && ~e.target.className.indexOf('nav-link') && ~e.target.id.indexOf('pills-')) {
				return;
			}

			var toggleEl = '.dropdown-toggle.header-dropdown-toggle';
			var dropdownEl =  toggleEl + ' + .dropdown-menu';
			if ($(toggleEl).is(e.target) || $(toggleEl).is($(e.target).parent())) {
				$(dropdownEl).toggleClass('show');
			} else if (!$(dropdownEl).is(e.target)
				&& $(dropdownEl).has(e.target).length === 0
				&& $('.open').has(e.target).length === 0
			) {
				$(dropdownEl).removeClass('show');
			}
		});

		let lang = document.documentElement.lang.substr(0, 2);
		bootbox.addLocale(lang, {
			OK: 'OK',
			CANCEL: '{{ __('common.cancel') }}',
			CONFIRM: '{{ __('common.confirm') }}',
		});
        bootbox.setLocale(lang);
        
        common.countMessageNotSeen();
        common.notifyAllPage();
	})
</script>