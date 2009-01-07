//when the dom is ready
window.addEvent('domready', function() {
	
	
	//store titles and text
	$$('a.Tips2').each(function(element,index) {
		var content = element.get('title').split('::');
		element.store('tip:title', content[0]);
		element.store('tip:text', content[1]);
	});
	
	//create the tooltips
	var Tips2 = new Tips('.Tips2',{
		className: 'Tips2',
		fixed: true,
		hideDelay: 300,
		showDelay: 200
	});

	Tips2.addEvents({
	'show': function(tip) {
		tip.fade('in');
	},
	'hide': function(tip) {
		tip.fade('out');
	}
	});
	
});
