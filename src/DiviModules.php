<?php
namespace MRKWP\InstagramFeed;

/**
 * Register divi modules
 */
class DiviModules
{
    
    protected $container;


    public function __construct($container)
    {
        $this->container = $container;
    }



    /**
     * Register divi modules.
     */
    public function register()
    {
        new \MRKWP_Instagram_Divi_Modules\InstagramFeedModule\InstagramFeedModule($this->container);
    }

    public function register_extensions()
    {
        new \MRKWP_Instagram_Divi_Modules\InstagramFeedExtension($this->container);
    }


    public function wp_print_styles()
    {
        // divi frontend builder styles. 
        wp_dequeue_style('et_pb_df_instagram_feed-styles');
    }


}
