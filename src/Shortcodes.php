<?php

namespace MRKWP\InstagramFeed;

/**
 * Class to register WordPress shortcodes.
 */
class Shortcodes
{
    
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Register shortcodes.
     */
    public function add()
    {
        // Add shortcodes here
    }
}
