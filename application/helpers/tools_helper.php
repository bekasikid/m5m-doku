<?php
/**
 * Created by PhpStorm.
 * User: Ainul Yaqin
 * Date: 7/3/2015
 * Time: 15:51
 */

function js_url($uri = '')
{
    $CI = &get_instance();
    return $CI->config->slash_item('js') . $uri;
}

function js_load($uri = '')
{
    return "<script src='" . js_url($uri) . "'></script>";
}

function plugin_url($uri = '')
{
    $CI = &get_instance();
    return $CI->config->slash_item('plugin') . $uri;
}

function plugin_load($uri = '')
{
    return "<script src='" . plugin_url($uri) . "'></script>";
}

function css_url($uri = '')
{
    $CI = &get_instance();
    return $CI->config->slash_item('css') . $uri;
}

function img_url($uri = '')
{
    $CI = &get_instance();
    return $CI->config->slash_item('img_url') . $uri;
}

function css_load($uri, $isjs = 0)
{
    if ($isjs == 1)//=1
        return "<link rel='stylesheet' type='text/css' href='" . js_url($uri) . "'>";
    elseif ($isjs == 2)//=1
        return "<link rel='stylesheet' type='text/css' href='" . plugin_url($uri) . "'>";
    else
        return "<link rel='stylesheet' type='text/css' href='" . css_url($uri) . "'>";
}

function rand_string( $length ) {

    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars),0,$length);

}