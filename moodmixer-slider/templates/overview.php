<?php
/**
 * Template: Moodmixer Slider Overview
 * List all available sliders
 *
 * Fields in row: (id), title, shortcode, content (excerpt), size (width x height)
 */
?>

<div id="mxs-overview" class="mxs-tab-container">
	<form method="post" action="?page=<?php echo $page; ?>">
		<div class="tablenav">
			<div class="actions">
				<select name="action">
					<option value="-1" selected="selected">select Action</option>
					<option value="remove">Löschen / delete</option>
				</select>
				<button id="doaction" type="submit" class="button button-secondary action">Ausführen/ execute</button>
			</div>
		</div>

<?php if(isset($arrSliders) != false && sizeof($arrSliders) > 0) { ?>
		<table class="widefat post mxs-table">
			<thead>
				<tr>
					<th scope="col" id="cb" class="manage-column column-cb check-column"><input type="checkbox" name="select_all" class="mxs-select-all" /></th>
					<th scope="col" class="manage-column column-actions">Action:</th>
					<th scope="col" class="manage-column column-title">Name:</th>
					<th scope="col" class="manage-column column-shortcode">Shortcode:</th>
					<th scope="col" class="manage-column column-content">Content (Auszug):</th>
				</tr>
			</thead>
			<tbody>


<?php 	foreach($arrSliders as $slider) { ?>
				<tr>
					<th scope="row" class="check-column"><input type="checkbox" name="id[]" value="<?php echo $slider->slider_ID; ?>" /></td>
					<td class="mxs-table-field-action">
						<ul>
							<li class="mxs-table-action-edit"><a href="?page=<?php echo $page; ?>&amp;action=edit&amp;id=<?php echo $slider->slider_ID; ?>"><img src="<?php echo $strImagePath; ?>application_edit.png" alt="Bearbeiten" title="Slider bearbeiten / edit slider (ID <?php echo $slider->slider_ID; ?>)" /></a></li>
							<li class="mxs-table-action-remove"><a href="?page=<?php echo $page; ?>&amp;action=remove&amp;id=<?php echo $slider->slider_ID; ?>"><img src="<?php echo $strImagePath; ?>bin.png" alt="Löschen" title="Slider löschen/ delete slider (ID <?php echo $slider->slider_ID; ?>)" /></a></li>
						</ul>
					</td>

					<td class="mxs-table-field-name"><?php echo $slider->slider_title; ?></td>
					<td class="mxs-table-field-shortcode"><?php echo $slider->slider_shortcode; ?></td>
					<td class="mxs-table-field-content"><?php echo htmlentities($this->generate_excerpt($slider->slider_content, ' ..')); ?></td>
				</tr>
<?php
	}
?>
			</tbody>
		</table>
<?php
} else { // no sliders added (yet)
?>
		<p>Keine Einträge vorhanden. No entry avaialble<a href="?page=<?php echo $page; ?>&amp;action=add">Fügen Sie doch einen neuen hinzu! Add a new one!</p>
<?php
}
?>

	</form>
</div>