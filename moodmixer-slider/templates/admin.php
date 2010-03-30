<?php
/**
 * Admin template
 */

$arrActivePageClass = array('default' => '', 'add_edit' => '');
$strActivePageClass = 'current';

switch($strAction) {
	default:
		$strActionInclude = 'overview.php';
		$strActionHeadline = '';
		$arrActivePageClass['default'] = $strActivePageClass;
		break;
	case 'add':
		$strActionInclude = 'add_edit.php';
		$strActionHeadline = 'Neuen Slider anlegen / add new slider';
		$arrActivePageClass['add_edit'] = $strActivePageClass;
		break;
	case 'edit':
		$strActionInclude = 'add_edit.php';
		$strActionHeadline = 'Slider bearbeiten / edit';
		break;
}
?>
<div class="wrap">
	<?php if (isset($updated_message) != false) : ?>
	<div id="message" class="updated fade"><p><strong><?php echo $updated_message; ?></strong></p></div>
	<?php endif; ?>

	<h2>Moodmixer Slider<?php
if( !empty($strActionHeadline) ) {
	echo ' &raquo; ' . $strActionHeadline;
} ?></h2>

	<div class="xms-tabnav">
		<ul class="subsubsub">
			<li><a class="<?php echo $arrActivePageClass['default']; ?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $page; ?>">Übersicht/ Overview</a> | </li>
			<li><a class="<?php echo $arrActivePageClass['add_edit']; ?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $page; ?>&amp;action=add">Neuen Slider anlegen/New</a></li>
		</ul>
	</div>
<?php

require_once MXSLIDER_TEMPLATE_PATH . $strActionInclude;

?>
</div>
