<?php
/**
 * Created by PhpStorm.
 * User: AINUL
 * Date: 4/19/2015
 * Time: 23:22
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class M_pdf {

    function m_pdf()
    {
        $CI = & get_instance();
        log_message('Debug', 'mPDF class is loaded.');
    }

    function load($param=NULL)
    {
        include_once APPPATH.'/third_party/mpdf/mpdf.php';

        if ($params == NULL)
        {
            $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        }

        return new mPDF($param);
    }
}