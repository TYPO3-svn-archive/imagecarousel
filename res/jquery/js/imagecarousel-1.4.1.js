var imagecarousel = {
	itemVisibleInCallback: function(carousel, item, i, state, evt) {
		var items = jQuery.data(carousel, 'items');
		var idx = carousel.index(i, items.length);
		carousel.add(i, items[idx - 1].html());
	},
	itemVisibleOutCallback: function(carousel, item, i, state, evt) {
		carousel.remove(i);
	},
	randomize: function(carousel, childElem) {
		var childes = childElem.split(',');
		var lastChild = childes.pop();
		var $this = jQuery(carousel + ' ' + childes.join(' '));
		var elems = $this.children(lastChild);
		elems.sort(function() { return (Math.round(Math.random())-0.5); });
		$this.remove(lastChild);
		for(var i=0; i < elems.length; i++) {
			$this.append(elems[i]);
		}
	},
	/**
	 * Normal initCallback
	 */
	initCallback: function(id, carousel, state) {
		var items = [];
		for (a=0; a<carousel.options.size;a++) {
			items[a] = carousel.get(a + 1);
		}
		jQuery.data(carousel, 'items', items);
		jQuery(id+' .jcarousel-control a').bind('click', function() {
			carousel.scroll(jQuery.jcarousel.intval(jQuery(this).attr('rev') ? jQuery(this).attr('rev') : jQuery(this).text()));
			return false;
		});
		carousel.buttonNext.bind('click', function() {
			carousel.startAuto(0);
		});
		carousel.buttonPrev.bind('click', function() {
			carousel.startAuto(0);
		});
	},
	/**
	 * Mouseover initCallback
	 */
	initCallbackMouseover: function(carousel, state) {
		carousel.clip.hover(function() {
			carousel.stopAuto();
		}, function() {
			carousel.startAuto();
		});
	}
};
