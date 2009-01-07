window.addEvent('domready', function() {
	var accordion = new Accordion($$('.toggler'),$$('.element'), {
		opacity: 0,
		onActive: function(toggler) { toggler.setStyle('color', '#ff3300'); },
		onBackground: function(toggler) { toggler.setStyle('color', '#000000'); }
	});
});
