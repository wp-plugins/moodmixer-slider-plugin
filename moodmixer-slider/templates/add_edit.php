<?php
/**
 * Template: Moodmixer Slider Add / Edit
 * Adds a news slider or edits an existing one, dependend of the current action
 *
 * Fields: (id), title, shortcode, content (excerpt), size (width x height)
 */

$strSubmitButtonText = 'Speichern/ save';

// prepare shortcodes
if(isset($arrShortcodes) != false && sizeof($arrShortcodes) > 0) {
	foreach($arrShortcodes as $iItemKey => $strShortcode) {
		if($strShortcode != $slider->slider_shortcode) {
			$arrPreparedShortcodes[] = '"' . $strShortcode . '"';
		}
	}

	if(isset($arrPreparedShortcodes) != false) {
		$strPreparedShortcodes = implode(', ', $arrPreparedShortcodes);
	}

}
?>

<div id="mxs-<?php echo $strAction; ?>" class="mxs-tab-container" style="clear: left">
	<form method="post" action="?page=<?php echo $page; ?>" id="mxsFormAddEdit">
		<input type="hidden" name="action" value="save" />

<?php if($strAction == 'edit') {
	$strSubmitButtonText = '&Auml;nderungen/ changes ' . strtolower($strSubmitButtonText); ?>
		<input type="hidden" name="id" value="<?php echo $slider->slider_ID; ?>" />
<?php } else {
 	$strSubmitButtonText = 'Eintrag /entry ' . strtolower($strSubmitButtonText);
} ?>

		<table class="form-table mxs-form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="mxs-form-field-title">Name</label></td>
					<td><input type="text" name="slider_title" id="mxs-form-field-title" value="<?php echo $slider->slider_title; ?>" size="40" /></td>
					<td class="help">Short description / Kurze Bezeichnung oder Beschreibung, z.B. Slider #1 oder Jeans-Slider.</td>
				</tr>

				<tr>
					<th scope="row"><label for="mxs-form-field-shortcode">Shortcode</label></th>
					<td><input type="text" name="slider_shortcode" id="mxs-form-field-shortcode" value="<?php echo $slider->slider_shortcode; ?>" size="40" /></td>
					<td class="help">Code for this entry/ Code für diesen Eintrag, welcher nachher im Artikel eingetragen wird.</td>
				</tr>

				<tr>
					<th scope="row"><label for="mxs-form-field-content">Content/<br />
					  Inhalt
				  </label></th>
					<td><textarea name="slider_content" id="mxs-form-field-content" rows="10" cols="38"><?php echo $slider->slider_content; ?></textarea></td>
					<td class="help">Die Daten, die später ausgegeben werden sollen, also z.B. Javascript-Quelltext.<br />
					  Enter the Slider code here</td>
				</tr>
			</tbody>
		</table>

		<p class="submit"><button type="submit" class="button button-primary"><?php echo $strSubmitButtonText; ?></button></p>
	</form>
</div>

<script type="text/javascript">
	jQuery(function() {

		jQuery('button.button-primary').after(' &nbsp; <button type="button" class="button-abort button">Abbrechen/abort</button>');
		jQuery('button.button-abort').click(function() {
			window.history.back();
		});


		mxsShortcodes = [<?php echo $strPreparedShortcodes; ?>];

		// form checks
		jQuery('#mxs-form-field-shortcode').change(function() {
			if(jQuery.inArray(jQuery(this).val(), mxsShortcodes) > -1) { // is already reserved
				jQuery(this).addClass('form-field-error');
			} else if(jQuery.inArray(jQuery(this).val(), mxsShortcodes) < 0) { // not in array
				jQuery(this).removeClass('form-field-error');
			}

		});

		jQuery('#mxsFormAddEdit').submit(function() {
			if(jQuery.inArray(jQuery('#mxs-form-field-shortcode').val(), mxsShortcodes) > -1) { // is already reserved
				jQuery('.wrap h2').after('<div id="message" class="error fade"><p><strong>Fehler: Shortcode ist bereits vergeben!</strong></p></div>');
				document.getElementById('mxs-form-field-shortcode').focus();
				return false;
			}
		});
	});
</script>
