<?php

class ShowController extends StudipController
{

    //CREATE VIEW message_threads as SELECT message_id,MD5(GROUP_CONCAT(user_id ORDER BY user_id)) as group_id FROM message_user GROUP BY message_id;

    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;

    }

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (Request::isXhr()) {
            $this->set_content_type('text/html;Charset=windows-1252');
        } else {
            $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox.php'));
        }
//      PageLayout::setTitle('');
    }

    public function index_action()
    {
        if (Request::submitted('search')) {
            $query = DBManager::get()->quote('%'.Request::get('search').'%');
            $search = " HAVING topic LIKE $query";
        }

        $this->inbox = DBManager::get()->prepare("SELECT topic, message_id, MAX(mkdate) as mkdate, autor_id FROM (SELECT TRIM(LEADING 'RE: ' FROM subject) as topic,
message.message_id, MD5(GROUP_CONCAT(u.user_id ORDER BY u.user_id)) as groupid, message.mkdate as mkdate, autor_id
FROM message
JOIN message_user USING (message_id)
JOIN message_user u USING (message_id)
WHERE message_user.user_id = ?
GROUP BY message_id) as mails
GROUP BY topic, groupid
".$search."
ORDER BY mkdate DESC;");
        $this->inbox->execute(array(User::findCurrent()->id));
        $this->addSidebar();
    }

    public function message_action($id)
    {

        // We're still in overview mode
        Navigation::activateItem('/messaging/messages/overview');

        // Select topic and group
        $meta = DBManager::get()->fetchOne("SELECT TRIM(LEADING 'RE: ' FROM subject) as topic, MD5(GROUP_CONCAT(user_id ORDER BY user_id)) as group_id, autor_id FROM message JOIN message_user USING (message_id) WHERE message_id = ?", array($id));
        $this->topic = $meta['topic'];
        $this->system = $meta['autor_id'] == '____%system%____';


        $this->messages = DBManager::get()->prepare("SELECT * FROM message_user u1
JOIN message USING (message_id)
JOIN message_user u2 USING (message_id)
WHERE u1.user_id = ?
AND TRIM(LEADING 'RE: ' FROM subject) = ?
GROUP BY message_id
HAVING MD5(GROUP_CONCAT(u2.user_id ORDER BY u2.user_id)) = ?
ORDER BY message.mkdate DESC");
        $this->messages->execute(array(User::findCurrent()->id, $meta['topic'], $meta['group_id']));

        // If we have no replay id
        /*
        while (!$this->system && !$this->id && $msg = $this->message->fetch(PDO::FETCH_ASSOC)) {
            if ($msg['autor_id'] != User::findCurrent()->id) {
                $this->id = $msg['message_id'];
            }
        }*/
    }

    private function addSidebar() {
        $sidebar = Sidebar::get();
        $sidebar->setImage(Assets::image_path("sidebar/mail-sidebar.png"));

        $actions = new ActionsWidget();
        $actions->addLink(
            _("Neue Nachricht schreiben"),
            URLHelper::getLink('dispatch.php/messages/write'),
            'icons/16/blue/add/mail.png',
            array('data-dialog' => 'width=650;height=600')
        );
        if (Navigation::getItem('/messaging/messages/inbox')->isActive()) {
            $actions->addLink(
                _('Alle als gelesen markieren'),
                $this->url_for('messages/overview', array('read_all' => 1)),
                'icons/16/blue/accept.png'
            );
        }
        $sidebar->addWidget($actions);

        $search = new SearchWidget(URLHelper::getLink('?'));
        $search->addNeedle(_('Nachrichten durchsuchen'), 'search', true);
        //$search->addFilter(_('Betreff'), 'search_subject');
        //$search->addFilter(_('Inhalt'), 'search_content');
        //$search->addFilter(_('AutorIn'), 'search_autor');
        $sidebar->addWidget($search);
    }

    // customized #url_for for plugins
    function url_for($to)
    {
        $args = func_get_args();

        # find params
        $params = array();
        if (is_array(end($args))) {
            $params = array_pop($args);
        }

        # urlencode all but the first argument
        $args = array_map('urlencode', $args);
        $args[0] = $to;

        return PluginEngine::getURL($this->dispatcher->plugin, $params, join('/', $args));
    }
}
