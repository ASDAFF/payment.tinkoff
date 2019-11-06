<?php use Bitrix\Main\Localization\Loc;

global $tinkoffSites;
?>
<form action="<?=$APPLICATION->GetCurPage()?>">
    <?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="hidden" name="id" value="rover.tinkoff">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="2">
	<?=\CAdminMessage::ShowNote(Loc::getMessage("rover-tp__install_title"))?>
	<p><?=Loc::getMessage("rover-tp__install-sites")?></p>
    <?php foreach ($tinkoffSites as $tSite): ?>
        <p><label><input type="checkbox" name="tsite[<?=$tSite['LID']?>]" value="Y" checked> [<?=$tSite['LID']?>] <?=$tSite['SITE_NAME']?></label></p>
    <?php endforeach; ?>
	<input type="submit" name="inst" value="<?=Loc::getMessage("rover-tp__install-continue")?>">
</form>