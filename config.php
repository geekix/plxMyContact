<?php if(!defined('PLX_ROOT')) exit;

$plugName = get_class($plxPlugin);

# Control du token du formulaire
plxToken::validateFormToken($_POST);

# Liste des langues disponibles et prises en charge par le plugin
$aLangs = array($plxAdmin->aConf['default_lang']);

# Si le plugin plxMyMultiLingue est installé on filtre sur les langues utilisées
# On garde par défaut le fr si aucune langue sélectionnée dans plxMyMultiLingue
if(defined('PLX_MYMULTILINGUE')) {
	$langs = plxMyMultiLingue::_Langs();
	$multiLangs = empty($langs) ? array() : explode(',', $langs);
	$aLangs = $multiLangs;
}

if(!empty($_POST)) {
	$plxPlugin->setParam('mnuDisplay', $_POST['mnuDisplay'], 'numeric');
	$plxPlugin->setParam('mnuPos', $_POST['mnuPos'], 'numeric');
	$plxPlugin->setParam('email', $_POST['email'], 'string');
	$plxPlugin->setParam('email_cc', $_POST['email_cc'], 'string');
	$plxPlugin->setParam('email_bcc', $_POST['email_bcc'], 'string');
	$plxPlugin->setParam('subject', $_POST['subject'], 'string');
	$plxPlugin->setParam('append_subject', $_POST['append_subject'], 'numeric');
	$plxPlugin->setParam('template', $_POST['template'], 'string');
	$plxPlugin->setParam('captcha', $_POST['captcha'], 'numeric');
	$plxPlugin->setParam('url', plxUtils::title2url($_POST['url']), 'string');
	$plxPlugin->setParam('label', $_POST['label'], 'numeric');
	$plxPlugin->setParam('placeholder', $_POST['placeholder'], 'numeric');
	foreach($aLangs as $lang) {
		$plxPlugin->setParam('mnuName_'.$lang, $_POST['mnuName_'.$lang], 'string');
		$plxPlugin->setParam('mnuText_'.$lang, $_POST['mnuText_'.$lang], 'string');
		$plxPlugin->setParam('thankyou_'.$lang, $_POST['thankyou_'.$lang], 'string');
	}
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=' . $plugName);
	exit;
}

$var = array();
# initialisation des variables propres à chaque lanque
$langs = array();
foreach($aLangs as $lang) {
	# chargement de chaque fichier de langue
	$langs[$lang] = $plxPlugin->loadLang(PLX_PLUGINS . $plugName . '/lang/'.$lang.'.php');
	$var[$lang]['mnuName'] =  $plxPlugin->getParam('mnuName_'.$lang)=='' ? $langs[$lang]['L_DEFAULT_MENU_NAME'] : $plxPlugin->getParam('mnuName_'.$lang);
	$var[$lang]['mnuText'] =  $plxPlugin->getParam('mnuText_'.$lang)=='' ? '' : $plxPlugin->getParam('mnuText_'.$lang);
	$var[$lang]['thankyou'] = $plxPlugin->getParam('thankyou_'.$lang)=='' ? $langs[$lang]['L_DEFAULT_THANKYOU'] : $plxPlugin->getParam('thankyou_'.$lang);
}
# initialisation des variables communes à chaque langue
$var['mnuDisplay'] =  $plxPlugin->getParam('mnuDisplay')=='' ? 1 : $plxPlugin->getParam('mnuDisplay');
$var['mnuPos'] =  $plxPlugin->getParam('mnuPos')=='' ? 2 : $plxPlugin->getParam('mnuPos');
$var['subject'] = $plxPlugin->getParam('subject')=='' ? $plxPlugin->getLang('L_DEFAULT_OBJECT') : $plxPlugin->getParam('subject');
$var['append_subject'] = $plxPlugin->getParam('append_subject')=='' ? 0 : $plxPlugin->getParam('append_subject');
$var['email'] = $plxPlugin->getParam('email')=='' ? '' : $plxPlugin->getParam('email');
$var['email_cc'] = $plxPlugin->getParam('email_cc')=='' ? '' : $plxPlugin->getParam('email_cc');
$var['email_bcc'] = $plxPlugin->getParam('email_bcc')=='' ? '' : $plxPlugin->getParam('email_bcc');
$var['template'] = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');
$var['captcha'] = $plxPlugin->getParam('captcha')=='' ? '1' : $plxPlugin->getParam('captcha');
$var['url'] = $plxPlugin->getParam('url')=='' ? 'contact' : $plxPlugin->getParam('url');
$var['label'] =  $plxPlugin->getParam('label')=='' ? 1 : $plxPlugin->getParam('label');
$var['placeholder'] =  $plxPlugin->getParam('placeholder')=='' ? 0 : $plxPlugin->getParam('placeholder');
	# On récupère les templates des pages statiques
	$glob = plxGlob::getInstance(PLX_ROOT . $plxAdmin->aConf['racine_themes'] . $plxAdmin->aConf['style'], false, true, '#^^static(?:-[\w-]+)?\.php$#');
	if (!empty($glob->aFiles)) {
		$aTemplates = array();
		foreach($glob->aFiles as $v)
			$aTemplates[$v] = basename($v, '.php');
	} else {
		$aTemplates = array('' => L_NONE1);
	}

if(function_exists('mail')) {
	echo '<p style="color:green"><strong>'.$plxPlugin->getLang('L_MAIL_AVAILABLE').'</strong></p>';
} else {
	echo '<p style="color:#ff0000"><strong>'.$plxPlugin->getLang('L_MAIL_NOT_AVAILABLE').'</strong></p>';
}
?>
<div id="tabContainer">
<form id="form_<?= $plugName ?>" action="parametres_plugin.php?p=<?= $plugName ?>" method="post">
	<div class="tabs">
		<ul>
			<li id="tabHeader_main"><?php $plxPlugin->lang('L_MAIN') ?></li>
			<?php
			foreach($aLangs as $lang) {
				echo '<li id="tabHeader_'.$lang.'">'.strtoupper($lang).'</li>';
			}
			?>
		</ul>
	</div>
	<div class="tabscontent">
		<div class="tabpage" id="tabpage_main">
			<fieldset>
				<p class="field"><label for="id_url"><?php $plxPlugin->lang('L_URL') ?>&nbsp;:</label></p>
				<?php plxUtils::printInput('url',$var['url'],'text','20-255') ?>
				<p class="field"><label for="id_mnuDisplay"><?= $plxPlugin->lang('L_MENU_DISPLAY') ?>&nbsp;:</label></p>
				<?php plxUtils::printSelect('mnuDisplay',array('1'=>L_YES,'0'=>L_NO),$var['mnuDisplay']); ?>
				<p class="field"><label for="id_mnuPos"><?php $plxPlugin->lang('L_MENU_POS') ?>&nbsp;:</label></p>
				<?php plxUtils::printInput('mnuPos',$var['mnuPos'],'text','2-5') ?>
				<p class="field"><label for="id_label"><?php $plxPlugin->lang('L_LABEL') ?>&nbsp;:</label></p>
				<?php plxUtils::printSelect('label',array('1'=>L_YES,'0'=>L_NO),$var['label']); ?>
				<p class="field"><label for="id_placeholder"><?php $plxPlugin->lang('L_PLACEHOLDER') ?>&nbsp;:</label></p>
				<?php plxUtils::printSelect('placeholder',array('1'=>L_YES,'0'=>L_NO),$var['placeholder']); ?>
				<p class="field"><label for="id_email"><?php $plxPlugin->lang('L_EMAIL') ?>&nbsp;:</label></p>
				<?php plxUtils::printInput('email',$var['email'],'text','50-120') ?>
				<p class="field"><label for="id_email_cc"><?php $plxPlugin->lang('L_EMAIL_CC') ?>&nbsp;:</label></p>
				<?php plxUtils::printInput('email_cc',$var['email_cc'],'text','50-120') ?>
				<p class="field"><label for="id_email_bcc"><?php $plxPlugin->lang('L_EMAIL_BCC') ?>&nbsp;:</label></p>
				<?php plxUtils::printInput('email_bcc',$var['email_bcc'],'text','50-120') ?>
				<p class="field"><label for="id_subject"><?php $plxPlugin->lang('L_EMAIL_SUBJECT') ?>&nbsp;:</label></p>
				<?php plxUtils::printInput('subject',$var['subject'],'text','100-120') ?>
				<p class="field"><label for="id_append_subject"><?php $plxPlugin->lang('L_APPEND_EMAIL_SUBJECT') ?>&nbsp;:</label></p>
				<?php plxUtils::printSelect('append_subject',array('1'=>L_YES,'0'=>L_NO),$var['append_subject']); ?>
				<p class="field"><label for="id_captcha"><?= $plxPlugin->lang('L_CAPTCHA') ?>&nbsp;:</label></p>
				<?php plxUtils::printSelect('captcha',array('1'=>L_YES,'0'=>L_NO),$var['captcha']); ?>
				<p class="field"><label for="id_template"><?php $plxPlugin->lang('L_TEMPLATE') ?>&nbsp;:</label></p>
				<?php plxUtils::printSelect('template', $aTemplates, $var['template']) ?>
			</fieldset>
			<p><?php $plxPlugin->lang('L_COMMA') ?></p>
		</div>
		<?php foreach($aLangs as $lang) : ?>
		<div class="tabpage" id="tabpage_<?= $lang ?>">
			<?php if(!file_exists(PLX_PLUGINS . $plugName . '/lang/'.$lang.'.php')) : ?>
			<p><?php printf($plxPlugin->getLang('L_LANG_UNAVAILABLE'), PLX_PLUGINS . $plugName . '/lang/'.$lang.'.php') ?></p>
			<?php else : ?>
			<fieldset>
				<p class="field"><label for="id_mnuName_<?= $lang ?>"><?php $plxPlugin->lang('L_MENU_TITLE') ?>&nbsp;:</label></p>
				<?php plxUtils::printInput('mnuName_'.$lang,$var[$lang]['mnuName'],'text','20-20') ?>
				<p class="field"><label for="id_mnuText_<?= $lang ?>"><?php $plxPlugin->lang('L_MENU_TEXT') ?>&nbsp;:</label></p>
				<?php plxUtils::printArea('mnuText_'.$lang,$var[$lang]['mnuText'],'80','6') ?>
				<p class="field"><label for="id_thankyou_<?= $lang ?>"><?php $plxPlugin->lang('L_THANKYOU_MESSAGE') ?>&nbsp;:</label></p>
				<?php plxUtils::printInput('thankyou_'.$lang,$var[$lang]['thankyou'],'text','100-120') ?>
			</fieldset>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
	</div>
	<fieldset>
		<p class="in-action-bar">
			<?= plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
		</p>
	</fieldset>
</form>
</div>
<script type="text/javascript" src="<?= PLX_PLUGINS . $plugName ?>/tabs/tabs.js"></script>
