<?php
namespace MRKWP_Instagram_Divi_Modules\InstagramFeedModule;

use ET_Builder_Module;

/**
 *
 */
class InstagramFeedModule extends ET_Builder_Module
{
    /**
     *
     * @var string
     */
    public $name = 'DF - Instagram Feed';
    /**
     *
     * @var string
     */
    public $slug = 'df_instagram_feed';
    /**
     *
     * @var mixed
     */
    public $fields;
    /**
     *
     * @var mixed
     */
    protected $container;

    protected $defaults;

    public $vb_support = 'partial';

    protected $module_credits = array(
        'module_uri' => 'https://www.diviframework.com',
        'author'     => 'Divi Framework',
        'author_uri' => 'https://www.diviframework.com',
    );

    public $custom_css_tab = false;

    /**
     *
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->is_pro = $this->container['plugins']->is_pro_version_active();
        $this->setDefaults();
        $this->initFields();

        $this->advanced_fields['border'] = false;
        $this->advanced_fields['borders'] = false;
        $this->advanced_fields['text'] = false;
        $this->advanced_fields['box_shadow'] = false;
        $this->advanced_fields['filters'] = false;
        $this->advanced_fields['animation'] = false;
        $this->advanced_fields['text_shadow'] = false;
        $this->advanced_fields['max_width'] = false;
        $this->advanced_fields['margin_padding'] = false;
        $this->advanced_fields['custom_margin_padding'] = false;
        $this->advanced_fields['background'] = false;
        $this->advanced_fields['fonts'] = false;
        $this->advanced_fields['link_options'] = false;

        $this->custom_css_fields = [];
        $this->_additional_fields_options = [];

        parent::__construct();
    }

    protected function setDefaults()
    {
        $instagram_feed_options = get_option('sb_instagram_settings', array());

        $this->defaults = array(
            'type' => 'id',
            'id' => isset($instagram_feed_options['sb_instagram_user_id']) ? $instagram_feed_options['sb_instagram_user_id'] : '',
            'num' => 10,
            'sortby' => 'none',
            'cols' => 5,
            'cols_mobile' => '',
            'cols_tablet' => '',
            'imageres' => 'auto',
            'widthunit' => '%',
            'heightunit' => '%',
            'width' => 100,
            'disablemobile' => 'on',
            'disablelightbox' => 'on',
            'imagepadding' => 10,
            'carousel' => 'off',
            'carouselarrows' => 'on',
            'carouselpag' => 'on',
            'carouselautoplay' => 'on',
            'carouseltime' => 8000,
            'showbutton' => 'on',
            'buttontext' => 'Load More Photos',
            'showfollow' => 'on',
            'followtext' => 'Follow me',
            'showcaption' => 'off',
            'captionlength' => 50,
            'captionsize' => 16,
            'showlikes' => 'off',
            'likessize' => 14,
            'hovercolor' => '',
            'hovertextcolor' => '',
            'excludewords' => '',
            'includewords' => '',
        );
    }

    protected function getDefault($name)
    {
        return isset($this->defaults[$name]) ? $this->defaults[$name] : '';
    }

    /**
     * Initialise the fields.
     */
    private function initFields()
    {
        $this->fields = array();

        $this->fields = array_merge($this->fields, $this->getConfigurationFields());
        $this->fields = array_merge($this->fields, $this->getPostFilteringFields());
        $this->fields = array_merge($this->fields, $this->getHeaderFields());
        $this->fields = array_merge($this->fields, $this->getStyleFields());
        $this->fields = array_merge($this->fields, $this->getPhotoFields());
        $this->fields = array_merge($this->fields, $this->getCarouselFields());
        $this->fields = array_merge($this->fields, $this->loadMoreButtonFields());
        $this->fields = array_merge($this->fields, $this->getFollowButtonFields());
        $this->fields = array_merge($this->fields, $this->getCaptionFields());
        $this->fields = array_merge($this->fields, $this->getLikesCommentsFields());



        $this->fields['admin_label'] = array(
            'label' => __('Admin Label', 'et_builder'),
            'type' => 'text',
            'description' => __('This will change the label of the module in the builder for easy identification.', 'et_builder'),
        );

        $this->fields = $this->reset_pro_fields($this->fields);
        unset($this->fields['module_id']);
        unset($this->fields['module_class']);
    }

    protected function reset_pro_fields($fields)
    {
        // dump($this->is_pro);
        if ($this->is_pro) {
            return $fields;
        }

        $new_fields = array();
        foreach ($fields as $name => $field) {
            if (isset($field['pro']) && $field['pro']) {
                continue;
            }

            $new_fields[$name] = $field;
        }

        return $new_fields;
    }


    protected function getPostFilteringFields()
    {
        $fields = array();

        $fields['excludewords'] = array(
            'label' => esc_html__('Exclude Words', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'general',
            'toggle_slug' => 'post_filter_options',
            'description' => esc_html__('Remove posts which contain certain words or hashtags in the caption.', 'et_builder'),
            'default' => $this->getDefault('excludewords'),
            'pro' => true,
        );

        $fields['includewords'] = array(
            'label' => esc_html__('Include Words', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'general',
            'toggle_slug' => 'post_filter_options',
            'description' => esc_html__('Only display posts which contain certain words or hashtags in the caption.', 'et_builder'),
            'default' => $this->getDefault('includewords'),
            'pro' => true,
        );

        return $fields;
    }

    protected function getLikesCommentsFields()
    {
        $fields = array();
        $fields['showlikes'] = array(
            'label' => esc_html__('Show Likes & Comments', 'et_builder'),
            'type' => 'yes_no_button',
            'options' => array(
                'off' => esc_html__('Off', 'et_builder'),
                'on' => esc_html__('On', 'et_builder'),
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'likes_comments_options',
            'description' => esc_html__('Whether to show the Likes & Comments', 'et_builder'),
            'affects' => array('likescolor', 'likessize'),
            'default' => $this->getDefault('showlikes'),
            'pro' => true,
        );

        $fields['likescolor'] = array(
            'label' => esc_html__('Text Color', 'et_builder'),
            'type' => 'color-alpha',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'likes_comments_options',
            'description' => esc_html__('The color of the Likes & Comments. Any hex color code.', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('likescolor'),
            'pro' => true,
        );

        $fields['likessize'] = array(
            'label' => esc_html__('Text Size', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'likes_comments_options',
            'description' => esc_html__('The size of the Likes & Comments. Any number.', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('likessize'),
            'pro' => true,
        );

        return $fields;
    }


    protected function getCaptionFields()
    {
        $fields = array();

        $fields['showcaption'] = array(
            'label' => esc_html__('Show Caption', 'et_builder'),
            'type' => 'yes_no_button',
            'options' => array(
                'off' => esc_html__('Off', 'et_builder'),
                'on' => esc_html__('On', 'et_builder'),
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'caption_options',
            'description' => esc_html__('Whether to show the photo caption', 'et_builder'),
            'affects' => array('captionlength', 'captioncolor', 'captionsize' ),
            
            'default' => $this->getDefault('showcaption'),
            'pro' => true,
        );

        $fields['captionlength'] = array(
            'label' => esc_html__('Number of characters', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'caption_options',
            'description' => esc_html__('The number of characters of the caption to display', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('captionlength'),
            'pro' => true,
        );

        $fields['captioncolor'] = array(
            'label' => esc_html__('Text Color', 'et_builder'),
            'type' => 'color-alpha',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'caption_options',
            'description' => esc_html__('The text color of the caption. Any hex color code.', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('captioncolor'),
            'pro' => true,
        );

        $fields['captionsize'] = array(
            'label' => esc_html__('Text Size', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'caption_options',
            'description' => esc_html__('The size of the caption text. Any number.', 'et_builder'),
            'affects' => array(),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('captionsize'),
            'pro' => true,
        );
        return $fields;
    }

    protected function getFollowButtonFields()
    {
        $fields = array();

        $fields['showfollow'] = array(
            'label' => esc_html__('Show Button', 'et_builder'),
            'type' => 'yes_no_button',
            'options' => array(
                'off' => esc_html__('Off', 'et_builder'),
                'on' => esc_html__('On', 'et_builder'),
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'follow_button_options',
            'description' => esc_html__("Whether to show the 'Follow on Instagram' button", 'et_builder'),
            'affects' => array('followcolor', 'followtextcolor', 'followtext'),
            'default' => $this->getDefault('showfollow'),
        );

        $fields['followcolor'] = array(
            'label' => esc_html__('Background Color', 'et_builder'),
            'type' => 'color-alpha',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'follow_button_options',
            'description' => esc_html__('The background color of the button. Any hex color code.', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('followcolor'),
        );

        $fields['followtextcolor'] = array(
            'label' => esc_html__('Text Color', 'et_builder'),
            'type' => 'color-alpha',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'follow_button_options',
            'description' => esc_html__('The text color of the button. Any hex color code.', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('followtextcolor'),
        );

        $fields['followtext'] = array(
            'label' => esc_html__('Button Text', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'follow_button_options',
            'description' => esc_html__('The text used for the button.', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('followtext'),
        );

        return $fields;
    }

    protected function getCarouselFields()
    {
        $fields = array();

        $fields['carousel'] = array(
            'label' => esc_html__('Show Carousel', 'et_builder'),
            'type' => 'yes_no_button',
            'options' => array(
                'off' => esc_html__('Off', 'et_builder'),
                'on' => esc_html__('On', 'et_builder'),
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'carousel_options',
            'description' => esc_html__('Display this feed as a carousel.', 'et_builder'),
            'default' => $this->getDefault('carousel'),
            'affects' => array(
                'carouselarrows',
                'carouselpag',
                'carouselautoplay',
                'carouseltime',
            ),
            'pro' => true,
        );


        $fields['carouselarrows'] = array(
            'label' => esc_html__('Display Directional Arrows', 'et_builder'),
            'type' => 'yes_no_button',
            'options' => array(
                'off' => esc_html__('Off', 'et_builder'),
                'on' => esc_html__('On', 'et_builder'),
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'carousel_options',
            'description' => esc_html__('Display directional arrows on the carousel ', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('carouselarrows'),
            'pro' => true,
        );

        $fields['carouselpag'] = array(
            'label' => esc_html__('Display Pagination', 'et_builder'),
            'type' => 'yes_no_button',
            'options' => array(
                'off' => esc_html__('Off', 'et_builder'),
                'on' => esc_html__('On', 'et_builder'),
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'carousel_options',
            'description' => esc_html__('Display pagination links below the carousel.', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('carouselpag'),
            'pro' => true,
        );

        $fields['carouselautoplay'] = array(
            'label' => esc_html__('Autoplay', 'et_builder'),
            'type' => 'yes_no_button',
            'options' => array(
                'off' => esc_html__('Off', 'et_builder'),
                'on' => esc_html__('On', 'et_builder'),
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'carousel_options',
            'description' => esc_html__('Make the carousel autoplay', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('carouselautoplay'),
            'pro' => true,
        );

        $fields['carouseltime'] = array(
            'label' => esc_html__('Interval Time', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'carousel_options',
            'description' => esc_html__('The interval time between slides for autoplay. Time in miliseconds.', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('carouseltime'),
            'pro' => true,
        );



        return $fields;
    }


    protected function getPhotoFields()
    {
        $fields = array();

        $fields['imagepadding'] = array(
            'label' => esc_html__('Padding', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'photo_options',
            'description' => esc_html__('The spacing around your photos', 'et_builder'),
            'default' => $this->getDefault('imagepadding'),
        );

        $fields['imagepaddingunit'] = array(
            'label' => esc_html__('Padding Unit', 'et_builder'),
            'type' => 'select',
            'options' => array(
                'px' => 'Pixel',
                '%' => 'Percentage',
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'photo_options',
            'description' => esc_html__("The unit of the padding. 'px' or '%'", 'et_builder'),
            'default' => $this->getDefault('imagepaddingunit'),
        );

        $fields['disablemobile'] = array(
            'label' => esc_html__('Disable Mobile Layout', 'et_builder'),
            'type' => 'yes_no_button',
            'options' => array(
                'off' => esc_html__('Off', 'et_builder'),
                'on' => esc_html__('On', 'et_builder'),
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'photo_options',
            'description' => esc_html__('Disable the mobile layout.', 'et_builder'),
            'default' => $this->getDefault('disablemobile'),
        );

        $fields['hovercolor'] = array(
            'label' => esc_html__('Hover Color', 'et_builder'),
            'type' => 'color-alpha',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'photo_options',
            'description' => esc_html__('The background color when hovering over a photo. Any hex color code. Available only on Pro Version', 'et_builder'),
            'default' => $this->getDefault('hovercolor'),
            'pro' => true,
        );

        $fields['hovertextcolor'] = array(
            'label' => esc_html__('Hover Text Color', 'et_builder'),
            'type' => 'color-alpha',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'photo_options',
            'description' => esc_html__('The text/icon color when hovering over a photo. Any hex color code. Available only on Pro Version.', 'et_builder'),
            'default' => $this->getDefault('hovertextcolor'),
            'pro' => true,
        );

        $fields['disablelightbox'] = array(
            'label' => esc_html__('Disable Lightbox', 'et_builder'),
            'type' => 'yes_no_button',
            'options' => array(
                'off' => esc_html__('Off', 'et_builder'),
                'on' => esc_html__('On', 'et_builder'),
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'photo_options',
            'description' => esc_html__('Whether to disable the photo Lightbox. It is enabled by default. Available only on Pro version', 'et_builder'),
            'default' => $this->getDefault('disablelightbox'),
            'pro' => true,
        );

        return $fields;
    }



    protected function getStyleFields()
    {
        $fields = array();

        $fields['width'] = array(
            'label' => esc_html__('Width', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'style_options',
            'description' => esc_html__('The width of your feed. Any number.', 'et_builder'),
            'default' => $this->getDefault('width'),
        );

        $fields['widthunit'] = array(
            'label' => esc_html__('Width Unit', 'et_builder'),
            'type' => 'select',
            'options' => array(
                'px' => 'Pixels',
                '%' => 'Percentage'
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'style_options',
            'description' => esc_html__("The unit of the width. 'px' or '%'", 'et_builder'),
            'default' => $this->getDefault('widthunit'),
        );

        $fields['height'] = array(
            'label' => esc_html__('Height', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'style_options',
            'description' => esc_html__('The height of your feed. Any number.', 'et_builder'),
            'default' => $this->getDefault('height'),
        );

        $fields['heightunit'] = array(
            'label' => esc_html__('Height Unit', 'et_builder'),
            'type' => 'select',
            'options' => array(
                'px' => 'Pixels',
                '%' => 'Percentage',
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'style_options',
            'description' => esc_html__("The unit of the height. 'px' or '%'", 'et_builder'),
            'default' => $this->getDefault('heightunit'),
        );

        $fields['background'] = array(
            'label' => esc_html__('Background Color', 'et_builder'),
            'type' => 'color-alpha',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'style_options',
            'description' => esc_html__('The background color of the feed. Any hex color code.', 'et_builder'),
            'default' => $this->getDefault('background'),
        );

        $fields['class'] = array(
            'label' => esc_html__('Custom CSS Class', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'style_options',
            'description' => esc_html__('Add a CSS class to the feed container', 'et_builder'),
            'default' => $this->getDefault('class'),
        );

        return $fields;
    }


    protected function getConfigurationFields()
    {
        $fields = array();

        $type_options = array(
            'id' => 'Instagram User ID',
        );

        if ($this->is_pro) {
            $type_options = array_merge(
                $type_options, array(
                'hashtag' => 'Hash Tag',
                'location' => 'Location',
                'coordinates' => 'Co-Ordinates',
                )
            );
        }

        $fields['type'] = array(
            'label' => esc_html__('Type', 'et_builder'),
            'type' => 'select',
            'options' => $type_options,
            'tab_slug' => 'general',
            'toggle_slug' => 'configuration_options',
            'description' => esc_html__('Select the feed type', 'et_builder'),
            'affects' => array(
                'id',
                'hashtag',
                'location',
                'coordinates',
            ),
            'default' => $this->getDefault('type'),
        );

        $fields['id'] = array(
            'label' => esc_html__('Instagram User ID', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'general',
            'toggle_slug' => 'configuration_options',
            'description' => esc_html__('An Instagram User ID. Separate multiple IDs by commas.', 'et_builder'),
            'affects' => array(),
            'depends_show_if' => 'id',
            'default' => $this->getDefault('id'),
        );

        $fields['hashtag'] = array(
            'label' => esc_html__('Hash Tag', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'general',
            'toggle_slug' => 'configuration_options',
            'description' => esc_html__('Any hashtag. Separate multiple hashtags by commas. Only available in Pro version', 'et_builder'),
            'affects' => array(),
            'depends_show_if' => 'hashtag',
            'default' => $this->getDefault('hashtag'),
        );

        $fields['location'] = array(
            'label' => esc_html__('Location', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'general',
            'toggle_slug' => 'configuration_options',
            'description' => esc_html__('The ID of the location. Separate multiple IDs by commas. Only available in Pro version', 'et_builder'),
            'affects' => array(),
            'depends_show_if' => 'location',
            'default' => $this->getDefault('location'),
        );

        $fields['coordinates'] = array(
            'label' => esc_html__('Co-Ordinates', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'general',
            'toggle_slug' => 'configuration_options',
            'description' => esc_html__(
                'The coordinates to display photos from. Separate multiple sets of coordinates by commas.
                The format is (latitude,longitude,distance). Only available in Pro version', 'et_builder'
            ),
            'affects' => array(),
            'depends_show_if' => 'coordinates',
            'default' => $this->getDefault('coordinates'),
        );


        $fields['num'] = array(
            'label' => esc_html__('Initial Number of Photos', 'et_builder'),
            'type' => 'range',
            'range_settings' => array(
                'min'  => 1,
                'max'  => 33,
                'step' => 1,
            ),
            'validate_unit'       => false,
            'fixed_unit'          => '',
            'fixed_range'         => true,

            'tab_slug' => 'general',
            'toggle_slug' => 'configuration_options',
            'description' => esc_html__('The number of photos to display initially. Maximum is 33', 'et_builder'),
            'default' => $this->getDefault('num'),
        );

        $fields['sortby'] = array(
            'label' => esc_html__('Sort By', 'et_builder'),
            'type' => 'select',
            'options' => array(
                'none' => 'Newest to Oldest',
                'random' => 'Random',
            ),
            'tab_slug' => 'general',
            'toggle_slug' => 'configuration_options',
            'description' => esc_html__('Sort the posts by Newest to Oldest or Random', 'et_builder'),
            'default' => $this->getDefault('sortby'),
        );

        $fields['cols'] = array(
            'label' => esc_html__('Columns', 'et_builder'),
            'type' => 'range',
            'range_settings' => array(
                'min'  => 1,
                'max'  => 10,
                'step' => 1,
            ),
            'validate_unit'       => false,
            'fixed_unit'          => '',
            'fixed_range'         => true,
            'tab_slug' => 'general',
            'toggle_slug' => 'configuration_options',
            'description' => esc_html__('The number of columns in your feed. 1 - 10.', 'et_builder'),
            'default' => $this->getDefault('cols'),
        );

        $fields['cols_tablet'] = array(
            'label' => esc_html__('Columns - Tablet', 'et_builder'),
            'type' => 'range',
            'range_settings' => array(
                'min'  => 1,
                'max'  => 10,
                'step' => 1,
            ),
            'validate_unit'       => false,
            'fixed_unit'          => '',
            'fixed_range'         => true,
            'tab_slug' => 'general',
            'toggle_slug' => 'configuration_options',
            'description' => esc_html__('The number of columns in your feed. 1 - 10 on screens between 480 and 768 pixels', 'et_builder'),
            'default' => $this->getDefault('cols_tablet'),
        );

        $fields['cols_mobile'] = array(
            'label' => esc_html__('Columns - Mobile', 'et_builder'),
            'type' => 'range',
            'range_settings' => array(
                'min'  => 1,
                'max'  => 10,
                'step' => 1,
            ),
            'validate_unit'       => false,
            'fixed_unit'          => '',
            'fixed_range'         => true,
            'tab_slug' => 'general',
            'toggle_slug' => 'configuration_options',
            'description' => esc_html__('The number of columns in your feed. 1 - 10 on screens below 480 pixels', 'et_builder'),
            'default' => $this->getDefault('cols_mobile'),
        );


        $fields['imageres'] = array(
            'label' => esc_html__('Image Resolution', 'et_builder'),
            'type' => 'select',
            'options' => array(
                'auto' => 'Auto',
                'full' => 'Full',
                'medium' => 'Medium',
                'thumb' => 'Thumb',
            ),
            'tab_slug' => 'general',
            'toggle_slug' => 'configuration_options',
            'description' => esc_html__("The resolution/size of the photos. 'auto', full', 'medium' or 'thumb'.", 'et_builder'),
            'default' => $this->getDefault('imageres'),
        );


        return $fields;
    }

    protected function getHeaderFields()
    {
        $fields = array();

        $fields['showheader'] = array(
            'label' => esc_html__('Show Header', 'et_builder'),
            'type' => 'yes_no_button',
            'options' => array(
                'off' => esc_html__('Off', 'et_builder'),
                'on' => esc_html__('On', 'et_builder'),
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'header_options',
            'description' => esc_html__('Enable/disable header.', 'et_builder'),
            'affects' => array('headercolor'),
            'default' => $this->getDefault('showheader'),
        );

        $fields['headercolor'] = array(
            'label' => esc_html__('Text Color', 'et_builder'),
            'type' => 'color-alpha',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'header_options',
            'description' => esc_html__('The color of the Header text. Any hex color code.', 'et_builder'),
            'default' => $this->getDefault('headercolor'),
            'depends_show_if' => 'on',
        );


        return $fields;
    }



    protected function loadMoreButtonFields()
    {
        $fields = array();

        $fields['showbutton'] = array(
            'label' => esc_html__('Show "Load More" button', 'et_builder'),
            'type' => 'yes_no_button',
            'options' => array(
                'off' => esc_html__('Off', 'et_builder'),
                'on' => esc_html__('On', 'et_builder'),
            ),
            'tab_slug' => 'advanced',
            'toggle_slug' => 'load_more_button_options',
            'description' => esc_html__("Whether to show the 'Load More' button.", 'et_builder'),
            'default' => $this->getDefault('showbutton'),
            'affects' => array(
                'buttoncolor',
                'buttontextcolor',
                'buttontext'
            ),
        );

        $fields['buttoncolor'] = array(
            'label' => esc_html__('Button Color', 'et_builder'),
            'type' => 'color-alpha',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'load_more_button_options',
            'description' => esc_html__('The background color of the button. Any hex color code.', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('buttoncolor'),
        );

        $fields['buttontextcolor'] = array(
            'label' => esc_html__('Button Text Color', 'et_builder'),
            'type' => 'color-alpha',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'load_more_button_options',
            'description' => esc_html__('The text color of the button. Any hex color code.', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('buttontextcolor'),
        );

        $fields['buttontext'] = array(
            'label' => esc_html__('Button Text', 'et_builder'),
            'type' => 'text',
            'tab_slug' => 'advanced',
            'toggle_slug' => 'load_more_button_options',
            'description' => esc_html__('The text used for the button.', 'et_builder'),
            'depends_show_if' => 'on',
            'default' => $this->getDefault('buttontext'),
        );

        return $fields;
    }

    /**
     * Init module.
     */
    public function init()
    {
       
        if (strpos($this->slug, 'et_pb_') !== 0) {
            $this->slug = 'et_pb_' . $this->slug;
        }

        $defaults = array();

        foreach ($this->fields as $field => $options) {
            if (isset($options['default'])) {
                $defaults[$field] = $options['default'];
            }
        }

        // dump($defaults);

        $this->field_defaults = $defaults;

        $this->setOptionsToggles();
    }

    protected function setOptionsToggles()
    {
        $this->settings_modal_toggles = array(
            'general' => array(
                'settings' => array(
                    'toggles_disabled' => true,
                ),
                'toggles' => array(
                    'configuration_options' => esc_html__('Configuration Options', 'et_builder'),
                    'post_filter_options' => esc_html__('Post Filtering Options - Pro Version', 'et_builder'),
                ),
            ),


            'advanced' => array(
                'settings' => array(
                    'toggles_disabled' => true,
                ),
                'toggles' => array(
                    'header_options' => esc_html__('Header Options', 'et_builder'),
                    'style_options' => esc_html__('Style Options', 'et_builder'),
                    'photo_options' => esc_html__('Photo Options', 'et_builder'),
                    'carousel_options' => esc_html__('Carousel Options - Pro Version', 'et_builder'),
                    'follow_button_options' => esc_html__("'Follow on Instagram' Button Options", 'et_builder'),
                    'caption_options' => esc_html__('Caption Options - Pro Version', 'et_builder'),
                    'load_more_button_options' => esc_html__('"Load More" Button Options', 'et_builder'),
                    'likes_comments_options' => esc_html__('Likes & Comments Options - Pro Version', 'et_builder'),
                    
                ),
            ),
        );
    }

    /**
     * Get Fields
     */
    public function get_fields()
    {
        return $this->fields;
    }

    /**
     * Shortcode render.
     */
    public function render($atts, $content = null, $function_name)
    {
        foreach ($this->props as $key => $value) {
            if (!$value and isset($this->defaults[$key])) {
                $this->props[$key] = $this->defaults[$key];
            }
        }
        
        $atts = wp_parse_args($this->props, $this->defaults);
        
        //
        if (is_array($atts['id']) and isset($atts['id'][0])) {
            $atts['id'] = $atts['id'][0];
        }

        if (is_array($atts['hashtag']) and isset($atts['hashtag'][0])) {
            $atts['hashtag'] = $atts['hashtag'][0];
        }

        if (is_array($atts['location']) and isset($atts['location'][0])) {
            $atts['location'] = $atts['location'][0];
        }

        if (is_array($atts['coordinates']) and isset($atts['coordinates'][0])) {
            $atts['coordinates'] = $atts['coordinates'][0];
        }

        $shortcode_tag = 'instagram-feed';

        $attributes = array();

        switch ($atts['type']) {
        case 'id':
            $attributes['id'] = $atts['id'];
            break;
            
        case 'hashtag':
            $attributes['hashtag'] = $atts['hashtag'];
            break;
            
        case 'location':
            $attributes['location'] = $atts['location'];
            break;
            
        case 'coordinates':
            $attributes['coordinates'] = $atts['coordinates'];
            break;
            
        default:
            // code...
            break;
        }

        $attributes['num'] = $atts['num'];
        $attributes['sortby'] = $atts['sortby'];
        $attributes['cols'] = $atts['cols'];

        $attributes['imageres'] = $atts['imageres'];

        $attributes['excludewords'] = $atts['excludewords'];
        $attributes['includewords'] = $atts['includewords'];

        if ($atts['showheader'] == 'on') {
            $attributes['showheader'] = 'true';
            $attributes['headercolor'] = $atts['headercolor'];
        } else {
            $attributes['showheader'] = 'false';
        }

        $attributes['width'] = $atts['width'];
        $attributes['widthunit'] = $atts['widthunit'];
        $attributes['height'] = $atts['height'];
        $attributes['heightunit'] = $atts['heightunit'];
        $attributes['background'] = $atts['background'];
        $attributes['class'] = $atts['class'];


        $attributes['imagepadding'] = $atts['imagepadding'];
        $attributes['imagepaddingunit'] = $atts['imagepaddingunit'];
        $attributes['disablelightbox'] = $atts['disablelightbox'] == 'on' ? 'true' : 'false';
        $attributes['disablemobile'] = $atts['disablemobile'] == 'on' ? 'true' : 'false';
        $attributes['hovercolor'] = $atts['hovercolor'];
        $attributes['hovertextcolor'] = $atts['hovertextcolor'];


        $attributes['carousel'] = $atts['carousel'] == 'on' ? 'true' : 'false';

        if ($attributes['carousel'] === 'true') {
            $attributes['carouselarrows'] = $atts['carouselarrows'] == 'on' ? 'true' : 'false';
            $attributes['carouselpag'] = $atts['carouselpag'] == 'on' ? 'true' : 'false';
            $attributes['carouselautoplay'] = $atts['carouselautoplay'] == 'on' ? 'true' : 'false';
            $attributes['carouseltime'] = $atts['carouseltime'];
        }

        $attributes['showheader'] = $atts['showheader'] == 'on' ? 'true' : 'false';
        if ($attributes['showheader'] === 'true') {
            $attributes['headercolor'] = $atts['headercolor'];
        }


        $attributes['showbutton'] = $atts['showbutton'] == 'on' ? 'true' : 'false';
        if ($attributes['showbutton'] === 'true') {
            $attributes['buttoncolor'] = $atts['buttoncolor'];
            $attributes['buttontextcolor'] = $atts['buttontextcolor'];
            $attributes['buttontext'] = $atts['buttontext'];
        }

        $attributes['showcaption'] = $atts['showcaption'] == 'on' ? 'true' : 'false';
        if ($attributes['showcaption'] === 'true') {
            $attributes['captionlength'] = $atts['captionlength'];
            $attributes['captioncolor'] = $atts['captioncolor'];
            $attributes['captionsize'] = $atts['captionsize'];
        }

        $attributes['showlikes'] = $atts['showlikes'] == 'on' ? 'true' : 'false';
        if ($attributes['showlikes'] === 'true') {
            $attributes['likescolor'] = $atts['likescolor'];
            $attributes['likessize'] = $atts['likessize'];
        }

        $attributes['showfollow'] = $atts['showfollow'] == 'on' ? 'true' : 'false';
        if ($attributes['showfollow'] === 'true') {
            $attributes['followcolor'] = $atts['followcolor'];
            $attributes['followtextcolor'] = $atts['followtextcolor'];
            $attributes['followtext'] = $atts['followtext'];
        }

        $marshalled_attributes = array();
        foreach ($attributes as $key => $value) {
            if (empty($value)) {
                continue;
            }

            $marshalled_attributes[] = sprintf("%s='%s'", $key, $value);
        }

        $inline_style = '';
        if ($atts['cols_tablet'] and $atts['cols_tablet']) {
            $sbi_item_tablet_width =  100 / $atts['cols_tablet'];
            
            $inline_style .= "@media all and (max-width: 768px){
  #sb_instagram.sbi_col_3 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_4 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_5 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_6 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_7 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_8 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_9 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_10 #sbi_images .sbi_item{
                width: {$sbi_item_tablet_width}% !important;
            }
        }";
        }

        if ($atts['cols_mobile'] and $atts['cols_mobile']) {
            $sbi_item_mobile_width = 100 / $atts['cols_mobile'];
            $inline_style .= "
        @media all and (max-width: 479px){
  #sb_instagram.sbi_col_3 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_4 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_5 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_6 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_7 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_8 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_9 #sbi_images .sbi_item,
  #sb_instagram.sbi_col_10 #sbi_images .sbi_item{
            width: {$sbi_item_mobile_width}% !important;
        }
    }";
        }

        // wp_die(var_dump($inline_style));

        if ($inline_style) {
            wp_register_style('df_instagram_styles', false);
            wp_enqueue_style('df_instagram_styles');
            wp_add_inline_style('df_instagram_styles', $inline_style);
        }

        $shortcode = sprintf("[%s %s/]", $shortcode_tag, implode(' ', $marshalled_attributes));


        return do_shortcode($shortcode);
    }
}
