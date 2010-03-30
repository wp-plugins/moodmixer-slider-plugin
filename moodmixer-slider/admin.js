/**
 * extended Moodmixer Slider JS
 *
 * @author Fabian Wolf (http://fwolf.info/
 * @subpackage Moodmixer Slider
 * @license GNU GPL v2
 */

jQuery(function() {
	// base
	var Query = parseQuery(window.location.search);

	// navigation
	jQuery('.mxs-tabnav li:not(:last-child)').append('<span> |</span>');

	// overview

	// addedit
	if(Query.action == 'add' || Query.action == 'edit') {
		jQuery('#mxs-form-field-shortcode').change(function() {
			// check if shortcode does already exist
			jQuery

			// if so, disallow

			// if not, proceed
		});
	}


});

/**
 * function parseQuery
 *
 * @description Parses the GET query into an multidimensioal array
 * @parameter string Query
 * @return array ParsedQuery Consists of ParsedQuery[n] = array(0 => varName, 1 => varValue)
 */

function parseQuery(strUserQuery) {
	returnData = new Object();
	strQueryString = strUserQuery;

	if(strUserQuery.substr(0, 1) == '?') {
		strQueryString = strUserQuery.substr(1);
	}

	if(strQueryString != '') {
		arrX = strQueryString.split('&');

		for(n = 0; n < arrX.length; n++) {
			arrX2 = arrX[n].split('=');
// 			returnData[n] = new Array(arrX2[0], arrX2[1]);
			returnData[arrX2[0]] = arrX2[1];
		}

	}


	return returnData;
}