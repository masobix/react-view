<?php

namespace FRZR\Admin;

if (!defined('WPTEST')) {
    defined('ABSPATH') or die("Direct access to files is prohibited");
}

class DevMode
{
    use \FRZR\SingletonTrait;

    private function __construct()
    {
        add_filter('graphql_show_admin', function () {
            return true;
        });

        if ( Admin::is_admin_page()) {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        }
    }

    public function enqueue_scripts()
    {
        //----- Scripts

        $path = 'src/Admin/assets/';
        $assets_path = FRZR_PATH . $path;
        $assets_uri = FRZR_URI .  $path;

        $scripts = [];
        if (FRZR_DEVMODE) {
            $js_files = glob($assets_path . '*.js');
            $scripts = array_map(function ($js_file) {
                return basename($js_file, '.js');
            }, $js_files);
        } else {
            $scripts = ['script1', 'script2'];
        }

        foreach ($scripts as $js_file_name) {
            wp_enqueue_script($js_file_name, $assets_uri . $js_file_name . '.js', array(), '1.2', true);
        }

        wp_localize_script(
            end($scripts),
            'fundrizer_admin',
            array(
                'endpoint' => esc_url(site_url()  . '/graphql' ),
                'pro' => is_plugin_active('fundrizer-pro/fundrizer-pro.php') ? 'active' : '',
            )
        );

        add_filter('script_loader_tag', function ($tag, $handle, $src) use ($scripts) {
            if (in_array($handle, $scripts)) {
                $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
            }
            return $tag;
        }, 10, 3);

        //----- Styles

        $styles = [];
        if (FRZR_DEVMODE) {
            $css_files = glob($assets_path . '*.css');
            $styles = array_map(function ($css_file) {
                return basename($css_file, '.css');
            }, $css_files);
        } else {
            $styles = ['style1', 'style2'];
        }

        foreach ($styles as $css_file_name) {
            wp_enqueue_style($css_file_name, $assets_uri . $css_file_name . '.css', array(), '1.0');
        }
    }
}
