<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');

$email=Format::input($_POST['lemail']?$_POST['lemail']:$_GET['e']);
$ticketid=Format::input($_POST['lticket']?$_POST['lticket']:$_GET['t']);

if ($cfg->isClientEmailVerificationRequired())
    $button = "Enlace de Acceso vía Email";
else
    $button = "Ver Ticket";
?>
<h1>Revisar el estado de Ticket</h1>
<p>Por favor indíquenos su dirección de correo electrónico y un número de ticket, y le enviaremos un enlace de acceso por correo electrónico.</p>
<form action="login.php" method="post" id="clientLogin">
    <?php csrf_token(); ?>
<div style="display:table-row">
    <div style="width:40%;display:table-cell;box-shadow: 12px 0 15px -15px rgba(0,0,0,0.4);padding-right: 2em;">
    <strong><?php echo Format::htmlchars($errors['login']); ?></strong>
    <br>
    <div>
        <label for="email">Email:
        <input id="email" placeholder="ej. john.doe@osticket.com" type="text"
            name="lemail" size="30" value="<?php echo $email; ?>"></label>
    </div>
    <div>
        <label for="ticketno">Número de Ticket:</label><br/>
        <input id="ticketno" type="text" name="lticket" placeholder="ej. 1243"
            size="30" value="<?php echo $ticketid; ?>"></td>
    </div>
    <p>
        <input class="btn" type="submit" value="<?php echo $button; ?>">
    </p>
    </div>
    <div style="display:table-cell;padding-left: 2em;padding-right:90px;">
<?php if ($cfg && $cfg->getClientRegistrationMode() !== 'disabled') { ?>
        Tiene una cuenta con nosotros?
        <a href="login.php">Inicie Sesión</a> <?php
    if ($cfg->isClientRegistrationEnabled()) { ?>
        or <a href="account.php?do=create">registrar para una cuenta</a> <?php
    } ?> para acceder a todos sus tickets.
<?php
} ?>
    </div>
</div>
</form>
<br>
<p>
Si esta es tu primera vez en contacto con nosotros o que haya perdido el número de Ticket, por favor <a href="open.php">Abrir un nuevo Ticket</a>.
</p>
