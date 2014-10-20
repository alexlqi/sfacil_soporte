<?php
/*********************************************************************
    class.nav.php

    Navigation helper classes. Pointless BUT helps keep navigation clean and free from errors.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require_once(INCLUDE_DIR.'class.app.php');

class StaffNav {
    var $tabs=array();
    var $submenus=array();

    var $activetab;
    var $activemenu;
    var $panel;

    var $staff;

    function StaffNav($staff, $panel='staff'){
        $this->staff=$staff;
        $this->panel=strtolower($panel);
        $this->tabs=$this->getTabs();
        $this->submenus=$this->getSubMenus();
    }

    function getPanel(){
        return $this->panel;
    }

    function isAdminPanel(){
        return (!strcasecmp($this->getPanel(),'admin'));
    }

    function isStaffPanel() {
        return (!$this->isAdminPanel());
    }

    function getRegisteredApps() {
        return Application::getStaffApps();
    }

    function setTabActive($tab, $menu=''){

        if($this->tabs[$tab]){
            $this->tabs[$tab]['active']=true;
            if($this->activetab && $this->activetab!=$tab && $this->tabs[$this->activetab])
                 $this->tabs[$this->activetab]['active']=false;

            $this->activetab=$tab;
            if($menu) $this->setActiveSubMenu($menu, $tab);

            return true;
        }

        return false;
    }

    function setActiveTab($tab, $menu=''){
        return $this->setTabActive($tab, $menu);
    }

    function getActiveTab(){
        return $this->activetab;
    }

    function setActiveSubMenu($mid, $tab='') {
        if(is_numeric($mid))
            $this->activeMenu = $mid;
        elseif($mid && $tab && ($subNav=$this->getSubNav($tab))) {
            foreach($subNav as $k => $menu) {
                if(strcasecmp($mid, $menu['href'])) continue;

                $this->activeMenu = $k+1;
                break;
            }
        }
    }

    function getActiveMenu() {
        return $this->activeMenu;
    }

    function addSubMenu($item,$active=false){

        $this->submenus[$this->getPanel().'.'.$this->activetab][]=$item;
        if($active)
            $this->activeMenu=sizeof($this->submenus[$this->getPanel().'.'.$this->activetab]);
    }


    function getTabs(){

        if(!$this->tabs) {
            $this->tabs=array();
            $this->tabs['dashboard'] = array('desc'=>'Tablero','href'=>'dashboard.php','title'=>'Staff Dashboard');
            $this->tabs['users'] = array('desc' => 'Usuarios', 'href' => 'users.php', 'title' => 'User Directory');
            $this->tabs['tickets'] = array('desc'=>'Tickets','href'=>'tickets.php','title'=>'Ticket Queue');
            $this->tabs['kbase'] = array('desc'=>'Base Con.','href'=>'kb.php','title'=>'Knowledgebase');
            if (count($this->getRegisteredApps()))
                $this->tabs['apps']=array('desc'=>'Aplicaciones','href'=>'apps.php','title'=>'Applications');
        }

        return $this->tabs;
    }

    function getSubMenus(){ //Private.

        $staff = $this->staff;
        $submenus=array();
        foreach($this->getTabs() as $k=>$tab){
            $subnav=array();
            switch(strtolower($k)){
                case 'tickets':
                    $subnav[]=array('desc'=>'Tickets','href'=>'tickets.php','iconclass'=>'Ticket', 'droponly'=>true);
                    if($staff) {
                        if(($assigned=$staff->getNumAssignedTickets()))
                            $subnav[]=array('desc'=>"Mis&nbsp;Tickets ($assigned)",
                                            'href'=>'tickets.php?status=assigned',
                                            'iconclass'=>'assignedTickets',
                                            'droponly'=>true);

                        if($staff->canCreateTickets())
                            $subnav[]=array('desc'=>'Nuevo&nbsp;Ticket',
                                            'title' => 'Open New Ticket',
                                            'href'=>'tickets.php?a=open',
                                            'iconclass'=>'newTicket',
                                            'id' => 'new-ticket',
                                            'droponly'=>true);
                    }
                    break;
                case 'dashboard':
                    $subnav[]=array('desc'=>'Tablero','href'=>'dashboard.php','iconclass'=>'logs');
                    $subnav[]=array('desc'=>'Directorio&nbsp;de&nbsp;Staff','href'=>'directory.php','iconclass'=>'teams');
                    $subnav[]=array('desc'=>'Mi&nbsp;Perfil','href'=>'profile.php','iconclass'=>'users');
                    break;
                case 'users':
                    $subnav[] = array('desc' => 'Directorio&nbsp;de&nbsp;Usuarios', 'href' => 'users.php', 'iconclass' => 'teams');
                    $subnav[] = array('desc' => 'Organizaciones', 'href' => 'orgs.php', 'iconclass' => 'departments');
                    break;
                case 'kbase':
                    $subnav[]=array('desc'=>'FAQs','href'=>'kb.php', 'urls'=>array('faq.php'), 'iconclass'=>'kb');
                    if($staff) {
                        if($staff->canManageFAQ())
                            $subnav[]=array('desc'=>'Categorías','href'=>'categories.php','iconclass'=>'faq-categories');
                        if($staff->canManageCannedResponses())
                            $subnav[]=array('desc'=>'Respuestas&nbsp;Precargadas','href'=>'canned.php','iconclass'=>'canned');
                    }
                   break;
                case 'apps':
                    foreach ($this->getRegisteredApps() as $app)
                        $subnav[] = $app;
                    break;
            }
            if($subnav)
                $submenus[$this->getPanel().'.'.strtolower($k)]=$subnav;
        }

        return $submenus;
    }

    function getSubMenu($tab=null){
        $tab=$tab?$tab:$this->activetab;
        return $this->submenus[$this->getPanel().'.'.$tab];
    }

    function getSubNav($tab=null){
        return $this->getSubMenu($tab);
    }

}

class AdminNav extends StaffNav{

    function AdminNav($staff){
        parent::StaffNav($staff, 'admin');
    }

    function getRegisteredApps() {
        return Application::getAdminApps();
    }

    function getTabs(){


        if(!$this->tabs){

            $tabs=array();
            $tabs['dashboard']=array('desc'=>'Tablero','href'=>'logs.php','title'=>'Admin Dashboard');
            $tabs['settings']=array('desc'=>'Ajustes','href'=>'settings.php','title'=>'System Settings');
            $tabs['manage']=array('desc'=>'Administrar','href'=>'helptopics.php','title'=>'Manage Options');
            $tabs['emails']=array('desc'=>'Emails','href'=>'emails.php','title'=>'Email Settings');
            $tabs['staff']=array('desc'=>'Staff','href'=>'staff.php','title'=>'Manage Staff');
            if (count($this->getRegisteredApps()))
                $tabs['apps']=array('desc'=>'Aplicaciones','href'=>'apps.php','title'=>'Applications');
            $this->tabs=$tabs;
        }

        return $this->tabs;
    }

    function getSubMenus(){

        $submenus=array();
        foreach($this->getTabs() as $k=>$tab){
            $subnav=array();
            switch(strtolower($k)){
                case 'dashboard':
                    $subnav[]=array('desc'=>'System&nbsp;Logs','href'=>'logs.php','iconclass'=>'logs');
                    $subnav[]=array('desc'=>'Información','href'=>'system.php','iconclass'=>'preferences');
                    break;
                case 'settings':
                    $subnav[]=array('desc'=>'Compañía','href'=>'settings.php?t=pages','iconclass'=>'pages');
                    $subnav[]=array('desc'=>'Sistema','href'=>'settings.php?t=system','iconclass'=>'preferences');
                    $subnav[]=array('desc'=>'Tickets','href'=>'settings.php?t=tickets','iconclass'=>'ticket-settings');
                    $subnav[]=array('desc'=>'Emails','href'=>'settings.php?t=emails','iconclass'=>'email-settings');
                    $subnav[]=array('desc'=>'Acceso','href'=>'settings.php?t=access','iconclass'=>'users');
                    $subnav[]=array('desc'=>'Base de Conocimiento','href'=>'settings.php?t=kb','iconclass'=>'kb-settings');
                    $subnav[]=array('desc'=>'Autoresponder','href'=>'settings.php?t=autoresp','iconclass'=>'email-autoresponders');
                    $subnav[]=array('desc'=>'Alertas&nbsp;y&nbsp;Noticias','href'=>'settings.php?t=alerts','iconclass'=>'alert-settings');
                    break;
                case 'manage':
                    $subnav[]=array('desc'=>'Temas&nbsp;de&nbsp;Ayuda','href'=>'helptopics.php','iconclass'=>'helpTopics');
                    $subnav[]=array('desc'=>'Ticket&nbsp;Filtros','href'=>'filters.php',
                                        'title'=>'Ticket&nbsp;Filters','iconclass'=>'ticketFilters');
                    $subnav[]=array('desc'=>'ANS&nbsp;Plans','href'=>'slas.php','iconclass'=>'sla');
                    $subnav[]=array('desc'=>'API&nbsp;Keys','href'=>'apikeys.php','iconclass'=>'api');
                    $subnav[]=array('desc'=>'Páginas', 'href'=>'pages.php','title'=>'Pages','iconclass'=>'pages');
                    $subnav[]=array('desc'=>'Formularios','href'=>'forms.php','iconclass'=>'forms');
                    $subnav[]=array('desc'=>'Listas','href'=>'lists.php','iconclass'=>'lists');
                    $subnav[]=array('desc'=>'Plugins','href'=>'plugins.php','iconclass'=>'api');
                    break;
                case 'emails':
                    $subnav[]=array('desc'=>'Emails','href'=>'emails.php', 'title'=>'Email Addresses', 'iconclass'=>'emailSettings');
                    $subnav[]=array('desc'=>'Lista Prohib.','href'=>'banlist.php',
                                        'title'=>'Banned&nbsp;Emails','iconclass'=>'emailDiagnostic');
                    $subnav[]=array('desc'=>'Plantillas','href'=>'templates.php','title'=>'Email Templates','iconclass'=>'emailTemplates');
                    $subnav[]=array('desc'=>'Diagnostico','href'=>'emailtest.php', 'title'=>'Email Diagnostic', 'iconclass'=>'emailDiagnostic');
                    break;
                case 'staff':
                    $subnav[]=array('desc'=>'Miembros&nbsp;Staff','href'=>'staff.php','iconclass'=>'users');
                    $subnav[]=array('desc'=>'Equipos','href'=>'teams.php','iconclass'=>'teams');
                    $subnav[]=array('desc'=>'Grupos','href'=>'groups.php','iconclass'=>'groups');
                    $subnav[]=array('desc'=>'Departamentos','href'=>'departments.php','iconclass'=>'departments');
                    break;
                case 'apps':
                    foreach ($this->getRegisteredApps() as $app)
                        $subnav[] = $app;
                    break;
            }
            if($subnav)
                $submenus[$this->getPanel().'.'.strtolower($k)]=$subnav;
        }

        return $submenus;
    }
}

class UserNav {

    var $navs=array();
    var $activenav;

    var $user;

    function UserNav($user=null, $active=''){

        $this->user=$user;
        $this->navs=$this->getNavs();
        if($active)
            $this->setActiveNav($active);
    }

    function getRegisteredApps() {
        return Application::getClientApps();
    }

    function setActiveNav($nav){

        if($nav && $this->navs[$nav]){
            $this->navs[$nav]['active']=true;
            if($this->activenav && $this->activenav!=$nav && $this->navs[$this->activenav])
                 $this->navs[$this->activenav]['active']=false;

            $this->activenav=$nav;

            return true;
        }

        return false;
    }

    function getNavLinks(){
        global $cfg;

        //Paths are based on the root dir.
        if(!$this->navs){

            $navs = array();
            $user = $this->user;
            $navs['home']=array('desc'=>'Inicio&nbsp;Centro&nbsp;de&nbsp;Soporte','href'=>'index.php','title'=>'');
            if($cfg && $cfg->isKnowledgebaseEnabled())
                $navs['kb']=array('desc'=>'Knowledgebase','href'=>'kb/index.php','title'=>'');

            // Show the "Open New Ticket" link unless BOTH client
            // registration is disabled and client login is required for new
            // tickets. In such a case, creating a ticket would not be
            // possible for web clients.
            if ($cfg->getClientRegistrationMode() != 'disabled'
                    || !$cfg->isClientLoginRequired())
                $navs['new']=array('desc'=>'Abrir&nbsp;Nuevo&nbsp;Ticket','href'=>'open.php','title'=>'');
            if($user && $user->isValid()) {
                if(!$user->isGuest()) {
                    $navs['tickets']=array('desc'=>sprintf('Tickets&nbsp;(%d)',$user->getNumTickets()),
                                           'href'=>'tickets.php',
                                            'title'=>'Show all tickets');
                } else {
                    $navs['tickets']=array('desc'=>'Ver&nbsp;Seguimiento&nbsp;de&nbsp;Ticket',
                                           'href'=>sprintf('tickets.php?id=%d',$user->getTicketId()),
                                           'title'=>'View ticket status');
                }
            } else {
                $navs['status']=array('desc'=>'Revisar el Estado de Ticket','href'=>'view.php','title'=>'');
            }
            $this->navs=$navs;
        }

        return $this->navs;
    }

    function getNavs(){
        return $this->getNavLinks();
    }

}

?>
