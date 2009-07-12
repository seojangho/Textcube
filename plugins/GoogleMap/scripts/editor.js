// Google Map Plugin WYSISYG Helper
// - depends on EAF4.js, Google Map API v2, and jQuery 1.3.2 or higher included in Textcube 1.8 or higher.

function initializeGoogleMap() {
	// nothing to do.
}

(function($) {
	$(document).ready(function() {
		$('#editor-form').append('<input type="hidden" name="latitude" value="" /><input type="hidden" name="longitude" value="" />');	
	});
})(jQuery);

function GMapTool_insertMap() {
	window.open(blogURL + '/plugin/GMapCustomInsert/', 'GMapTool_Insert', 'menubar=no,toolbar=no,width=550,height=680,scrollbars=yes');
}

function GMapTool_getLocation() {
	window.open(blogURL + '/plugin/GMapGetLocation/', 'GMapTool_GetLocation', 'menubar=no,toolbar=no,width=550,height=600,scrollbars=no');
}

STD.addUnloadEventListener(function() { GUnload(); });
