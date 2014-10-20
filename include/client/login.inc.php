<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');

$email=Format::input($_POST['luser']?:$_GET['e']);
$passwd=Format::input($_POST['lpasswd']?:$_GET['t']);

$content = Page::lookup(Page::getIdByType('banner-client'));

if ($content) {
    list($title, $body) = $ost->replaceTemplateVariables(
        array($content->getName(), $content->getBody()));
} else {
    $title = 'Iniciar Sesión';
    $body = 'Para servirle mejor, animamos a nuestros clientes a registrarse para una cuenta y verificar la dirección de correo electrónico que tenemos grabada.';
}

?>
<h1><?php echo Format::display($title); ?></h1>
<p><?php echo Format::display($body); ?></p>
<form action="login.php" method="post" id="clientLogin">
    <?php csrf_token(); ?>
<div style="display:table-row">
    <div style="width:40%;display:table-cell;box-shadow: 12px 0 15px -15px rgba(0,0,0,0.4);padding:15px;">
    <strong><?php echo Format::htmlchars($errors['login']); ?></strong>
    <div>
        <input id="username" placeholder="Email o Nombre de Usuario" type="text" name="luser" size="30" value="<?php echo $email; ?>">
    </div>
    <div>
        <input id="passwd" placeholder="Contraseña" type="password" name="lpasswd" size="30" value="<?php echo $passwd; ?>"></td>
    </div>
    <p>
        <input class="btn" type="submit" value="Iniciar Sesión">
<?php if ($suggest_pwreset) { ?>
        <a style="padding-top:4px;display:inline-block;" href="pwreset.php">Olvidé mi Contraseña</a>
<?php } ?>
    </p>
    </div>
    <div style="display:table-cell;padding: 15px;vertical-align:top">
<?php

$ext_bks = array();
foreach (UserAuthenticationBackend::allRegistered() as $bk)
    if ($bk instanceof ExternalAuthentication)
        $ext_bks[] = $bk;

if (count($ext_bks)) {
    foreach ($ext_bks as $bk) { ?>
<div class="external-auth"><?php $bk->renderExternalLink(); ?></div><?php
    }
}
if ($cfg && $cfg->isClientRegistrationEnabled()) {
    if (count($ext_bks)) echo '<hr style="width:70%"/>'; ?>
    Todavía no estas registrado? <a href="account.php?do=create">Crear una Cuenta</a>
    <br/>
    <div style="margin-top: 5px;">
    <b>Soy un agente</b> —
    <a href="<?php echo ROOT_PATH; ?>scp">Iniciar Sesión aquí</a>
    </div>
<?php } ?>
    </div>
</div>
</form>
<br>
<p>
<?php if ($cfg && !$cfg->isClientLoginRequired()) { ?>
Si esta es tu primera vez en contacto con nosotros o que haya perdido el número de Ticket, por favor <a href="open.php">Abrir un nuevo Ticket</a>.
<?php } ?>
</p>
