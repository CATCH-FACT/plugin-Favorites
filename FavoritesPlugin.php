<?php
/**
 * Basket Plugin
 * 
 * @copyright Copyright 2015 University of Twente
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Basket Plugin.
 * 
 * @package Omeka\Plugins\BasketPlugin
 */
class BasketPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array This plugin's hooks.
     */
    protected $_hooks = array('install', 
                                'uninstall', 
                                'initialize', 
                                'define_acl');
    
    /**
     * @var array This plugin's filters.
     */
    protected $_filters = array('admin_navigation_main');
    
    /**
     * Install this plugin.
     */
    public function hookInstall()
    {
        $db = get_db();
        $sql = "
        CREATE TABLE `{$db->BasketMap}` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `owner_id` INT UNSIGNED DEFAULT NULL,
            `title` VARCHAR(255) DEFAULT NULL,
            `public` TINYINT(1) DEFAULT 0,
            `added` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
            `modified` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
            `comments` text COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `element_id` (`element_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $db->query($sql);
        
        $sql = "
        CREATE TABLE `{$db->BasketItem}` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `item_id` int(10) unsigned NOT NULL,
            `added` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
            `modified` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
            `comments` text COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `element_id` (`element_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $db->query($sql);
    }
    
    /**
     * Uninstall this plugin.
     */
    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `{$db->BasketMap}`;";
        $db->query($sql);
        $sql = "DROP TABLE IF EXISTS `{$db->BasketItem}`;";
        $db->query($sql);

    }
    
    /**
     * Initialize this plugin.
     */
    public function hookInitialize()
    {
        // Register the select filter controller plugin.
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Basket_Controller_Plugin_SelectFilter);
        
        // Add translation.
        add_translation_source(dirname(__FILE__) . '/languages');
    }
    
    /**
     * Define this plugin's ACL.
     */
    public function hookDefineAcl($args)
    {
        // Restrict access to super and admin users.
        $args['acl']->addResource('Basket_Index');
    }
    
    /**
     * Add the Simple Vocab navigation link.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array('label' => __('Baskets'), 'uri' => url('basket'));
        return $nav;
    }
}
