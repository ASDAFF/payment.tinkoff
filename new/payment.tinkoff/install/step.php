<?php use Bitrix\Main\Localization\Loc;
/**
 * Copyright (c) 7/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

global $tinkoffSites;
?>
<form action="<?=$APPLICATION->GetCurPage()?>">
    <?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="hidden" name="id" value="payment.tinkoff">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="2">
	<?=\CAdminMessage::ShowNote(Loc::getMessage("payment-tp__install_title"))?>
	<p><?=Loc::getMessage("payment-tp__install-sites")?></p>
    <?php foreach ($tinkoffSites as $tSite): ?>
        <p><label><input type="checkbox" name="tsite[<?=$tSite['LID']?>]" value="Y" checked> [<?=$tSite['LID']?>] <?=$tSite['SITE_NAME']?></label></p>
    <?php endforeach; ?>
	<input type="submit" name="inst" value="<?=Loc::getMessage("payment-tp__install-continue")?>">
</form>