<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use \Bitrix\Main\SystemException;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if($params['READY_TO_PAY']): ?>
	<FORM ACTION="<?=$params['FORM_PARAMS']['url']; ?>" METHOD="GET" target="_blank">
		<?php foreach($params['FORM_PARAMS']['params'] as $name => $value): ?>
			<INPUT TYPE="HIDDEN" NAME="<?=$name; ?>" VALUE="<?=$value; ?>">
		<?php endforeach; ?>

		<INPUT TYPE="SUBMIT" VALUE="<?=Loc::getMessage("SALE_TINKOFF_PAYBUTTON_NAME")?>">
	</FORM>

	<p align="justify">
		<b><?=Loc::getMessage("PAYMENT_DESCRIPTION"); ?></b>
	</p>
<?php else: ?>
	<b><?=Loc::getMessage("SALE_TINKOFF_UNAVAILABLE"); ?></b>
<?php endif; ?>

<?php if(isset($params['MESSAGE'])): ?>
	<br>
	<b><?=$params['MESSAGE']?></b>
<?php endif; ?>
