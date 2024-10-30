<?php
namespace MRKWP\InstagramFeed;

/**
 * Plugins Helper class.
 */
class Plugins
{

    //container.
    protected $container;

    /**
     * Constructor.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Check Dependancies
     */
    public function checkDependancies()
    {
        if (is_admin()) {
            if (!$this->is_free_version_active() && !$this->is_pro_version_active()) {
                $container = $this->container;

                add_action(
                    'admin_notices', function () use ($container) {
                        $class = 'notice notice-error is-dismissible';
                        $message = sprintf('<b>%s</b> requires <b>%s</b> plugin to be installed and activated.', $container['plugin_name'], 'Smash Balloon Social Photo Feed');

                        printf('<div class="%1$s"><p>%2$s</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', $class, $message);
                    }
                );
            }
        }
    }


    public function is_free_version_active()
    {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        return is_plugin_active('instagram-feed/instagram-feed.php');
    }

    public function is_pro_version_active()
    {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        return is_plugin_active('instagram-feed-pro/instagram-feed.php');
    }
}
