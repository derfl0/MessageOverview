<?php
require 'bootstrap.php';

/**
 * MessageconversationPlugin.class.php
 *
 * ...
 *
 * @author  Florian Bieringer <florian.bieringer@uni-passau.de>
 * @version 0.1a
 */
class MessageOverview extends StudIPPlugin implements SystemPlugin
{

    public function __construct()
    {
        parent::__construct();
        $navigation = new AutoNavigation(_('Übersicht'));
        $navigation->setURL(PluginEngine::GetURL($this, array(), 'show'));
        $navigation->setImage(Assets::image_path('blank.gif'));
        try {
            Navigation::insertItem('/messaging/messages/overview', $navigation, 0);
            Navigation::getItem('/messaging/messages')->setUrl(PluginEngine::GetURL($this, array(), 'show'));
        } finally {}
    }

    public function initialize()
    {
        self::addStylesheet('assets/messageoverview.less');
        PageLayout::addScript($this->getPluginURL().'/assets/messageoverview.js');
    }

    public function perform($unconsumed_path)
    {
        $this->setupAutoload();
        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'show'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }

    private function setupAutoload()
    {
        if (class_exists('StudipAutoloader')) {
            StudipAutoloader::addAutoloadPath(__DIR__ . '/models');
        } else {
            spl_autoload_register(function ($class) {
                include_once __DIR__ . $class . '.php';
            });
        }
    }
}
