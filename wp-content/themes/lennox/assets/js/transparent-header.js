document.addEventListener('DOMContentLoaded', function () {
	const header = document.querySelector(
		'header.has-sticky-header.has-header-overlay'
	);
	const transparentClass = 'has-transparent-header';
	const smartStickyClass = 'lennox-pro-smart-sticky-header';
	const smartStickyActiveClass = 'lennox-pro-smart-sticky-header--active';

	if (!header || !header.classList.contains(transparentClass)) {
		return;
	}

	function toggleTransparentHeader() {
		const isSmartSticky = header.classList.contains(smartStickyClass);
		const shouldBeTransparent = isSmartSticky
			? !header.classList.contains(smartStickyActiveClass)
			: window.scrollY <= 10;

		header.classList.toggle(transparentClass, shouldBeTransparent);
	}

	toggleTransparentHeader();
	window.addEventListener('scroll', toggleTransparentHeader, {
		passive: true,
	});
});
