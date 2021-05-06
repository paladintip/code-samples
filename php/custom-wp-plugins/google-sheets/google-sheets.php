<?php
/**
 * @package Google Sheet Embed
 */
/*
Plugin Name: Google Sheets Embed
Description: Plugin for inserting Google Sheets into WordPress with full control of styling
Version 1.0.0
Author: Edward Parkhomovich
Author URI: https://edparko.com
License: NPOSLv3
Text Domain: google-sheets
 */
defined('ABSPATH') or die('This server will self-destruct.');
if(file_exists(dirname(__FILE__).'/vendor/autoload.php'))
{
    require_once dirname(__FILE__).'/vendor/autoload.php';
}

use Inc\Base\Activate;
use Inc\Base\Deactivate;
use Inc\Cache\Cache;


add_action( 'init', array ('GSEmbed', 'init' ) );


class GSEmbed
{

    public $plugin;

    public static function init()
    {
        new self;
    }

    function __construct()
    {
        $this->plugin = plugin_basename(__FILE__);;

    }

    function register()
    {

        $this->cache = new Cache();
        add_shortcode('google-sheet', array( $this, 'google_sheet_function'));

    }

    function deactivate()
    {

        Deactivate::deactivate();
    }


    function google_sheet_function ($atts = array())
    {

        extract(shortcode_atts(array(
            'url' => "https://docs.google.com/spreadsheets/d/e/2PACX-1vSgD7JpTbaEez2sbUj0Mfz-6rhP0vw3cLRh7xLrFg1ikpuwSf6F7LMrmohkVTXo0N9EDiu9lfHPv3Fs/pubhtml",
            'table' => 1
        ), $atts));

        $sheetsPage = $this->cache->get("gs-page4-$url");
        if($sheetsPage == false || empty($sheetsPage))
        {
            echo "<h3>Refreshing Cache . . .</h3><script>location.reload(true);</script>";
            $sheetsPage = $this->fetchGoogleSheet($url);
            $this->cache->set("gs-page4-$url", $sheetsPage, 300);

        }

        $dom = new DOMDocument;
        @$dom->loadHTML($sheetsPage);

        $sheet = $dom->getElementsByTagName('table')[$table];
        $style = $dom->getElementsByTagName('style')[0];
        $html =  $dom->saveHTML($style) . "<div class='table-wrapper ritz'>" . $dom->saveHTML($sheet) . "</div>";

        return $html;

    }

    function fetchGoogleSheet($url)
    {

        $page = file_get_contents($url);
        $dom = new DOMDocument;
        @$dom->loadHTML($page);

        $html = $dom->saveHTML();
        $html = preg_replace('@id="([^"]+)"@', "", $html);
        $html = preg_replace('@style="([^"]+)"@', "", $html);

        return $html;


    }


    function activate()
    {
        Activate::activate();
    }
}

if(class_exists('GSEmbed'))
{
    $gsEmbedPlugin = new GSEmbed();
    $gsEmbedPlugin->register();

}


register_activation_hook(__FILE__, array($gsEmbedPlugin, 'activate'));

register_deactivation_hook(__FILE__, array($gsEmbedPlugin, 'deactivate'));