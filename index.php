<?php
/*********************************************************************
    index.php

    Helpdesk landing page. Please customize it to fit your needs.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('client.inc.php');
$section = 'home';
require(CLIENTINC_DIR.'header.inc.php');
?>
<div id="landing_page">
    <?php
    if($cfg && ($page = $cfg->getLandingPage()))
        echo $page->getBodyWithImages();
    else
        echo  '<h1>Bienvenido al Centro de Soporte de Software Fácil</h1>';
    ?>
    <div id="new_ticket">
        <h3>Abrir un nuevo Ticket</h3>
        <br>
        <div>Por favor, proporcione tantos detalles como sea posible, para poderlo ayudar. Para actualizar un ticket presentado anteriormente, por favor regístrese.</div>
        <p>
            <a href="open.php" class="green button">Abrir un nuevo Ticket</a>
        </p>
    </div>

    <div id="check_status">
        <h3>Revisar el estado de Ticket</h3>
        <br>
        <div>Proporcionamos los archivos y el registro de todas sus solicitudes de soporte actuales y pasadas ​​con las respuestas.</div>
        <p>
            <a href="view.php" class="blue button">Revisar el estado de ticket</a>
        </p>
    </div>
</div>
<div class="clear"></div>
<?php
if($cfg && $cfg->isKnowledgebaseEnabled()){
    //FIXME: provide ability to feature or select random FAQs ??
?>
<p>Asegúsere de ingresar a nuestras <a href="kb/index.php">Preguntas Frecuentes</a>, antes de abrir un ticket.</p>
</div>
<?php
} ?>
<?php require(CLIENTINC_DIR.'footer.inc.php'); ?>
