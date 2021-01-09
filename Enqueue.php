<?php namespace App\Libraries;

/**
 * Basic Library for including styles and javascript in you CodeIgniter 4 project
 * You can set order for Css and Js,and also dependencies
 * CodeIgniter_Root/application/libraries/enqueue.php
 * Version: 1.0
 * (testing with styles, is not completed for js)
 */
class Enqueue
{
    public $scripts;
    public $styles;
    public $path_js;
    public $path_css;

    public function __construct($path_js = 'public/js/', $path_css = 'public/css/')
    {
        $this->scripts = array();
        $this->styles = array();
        $this->path_js = (string)$path_js;
        $this->path_css = (string)$path_css;
    }

    public function load(&$array, $sortBy = 'position', $what = 'all', $where = 'everywhere')
    {
        $keepInMind = $array; /* we keep a copy from original array */
        /* first we try to sort this array */
        $this->get_all($array, $sortBy);
        if ($what == 'parents') {
            /*
             * maybe you want just parents
             */
            $load = $this->get_parents($array, 'dependencies');

        } elseif ($what == 'childrens') {
            /*
            * or just children
            */
            $load = $this->get_childrens($array, 'dependencies');
        } else {
            /*
             * or everything
             */
            $load = $array;
        }
        /*
         * oh, but if you want just for header?
         * ok, let see..
         */
        if ($where == 'header') {
            $load = $this->enqueue_header($load);
        } elseif ($where == 'footer') {
            /* or footer ? */
            $load = $this->enqueue_footer($load);
        }
        foreach ($load as $style) {
            echo '<link rel="stylesheet" href="' . base_url() . '/' . $this->path_css . $style['url'] . '" />';
            /* check if dependency is found */
            if (isset($style['dependencies']) && strlen($style['dependencies']) > 0 && !isset($keepInMind[$style['dependencies']])) {
                $this->error(3, true);
            }
            echo "\n";
        }
    }

    /*
     * get all
     */
    public function get_all(&$array, $orderBy = null)
    {
        if ($orderBy == null) {
            return $this->sort_($array);
        } else {
            return $this->sort_($array, $orderBy);
        }

    }

    /*
     * get list of parents
     */
    public function get_parents($array, $depname, $where = null)
    {
        return $this->get_by_key($array, $depname, null);
    }

    /*
     * get list of childrens
     */
    public function get_childrens($array, $depname, $where = null)
    {
        //  print_r($array);
        return $this->get_by_key($array, $depname, 'childrens');
    }

    /* get list for  header */
    public function enqueue_header($array, $depname = 'where')
    {
        return $this->get_by_key($array, $depname, 'header');
    }

    /* get list for footer */
    public function enqueue_footer($array, $depname = 'where')
    {
        return $this->get_by_key($array, $depname, 'footer');
    }

    /*
     * in special looking for a key,we can use this function is diff ways
     */
    public function get_by_key(&$array, $depname, $condition = null)
    {
        //print_r($condition);
        if ($this->checkThisArray($array, $depname, true)) {

            foreach ($array as $key => $value) {
                /* return just if doesn't have dependencies, mean is parent */
                if ($condition == null && isset($value[$depname]) && ((strlen($value[$depname]) > 0))) {
                    unset($array[$key]);
                }
                /* return just if have dependencies, mean is children */
                if ($condition == 'childrens' && isset($value[$depname]) && (strlen($value[$depname]) == 0 || empty($value[$depname]))) {
                    unset($array[$key]);
                }
                /* return just if condition is equal with $depname */
                if ($condition == 'header' || $condition == 'footer') {
                    if (isset($value[$depname]) && ($value[$depname]) !== $condition) {
                        unset($array[$key]);
                    }
                }
            }
        }
        return $array;
    }

    /*
     * we try to sort
     */
    private function sort_(&$array, $key = null)
    {
        if ($key !== null && $this->checkThisArray($array, $key)) {
            usort($array, function ($a, $b) use (&$key) {
                if ($a[$key] === $b[$key]) {
                    return 0;
                }
                return ($a[$key] > $b[$key]) ? 1 : -1;
            });
        }
    }

    /*
     * check if array is OK
     */
    private function checkThisArray(&$array, $key = null, $dependencies = null)
    {
        if (!is_array($array) || empty($array)) {
            return $this->error(1);
        }
        if ($key !== null) {
            foreach ($array as $a) {
                if (!isset($a[$key])) {
                    return $this->error(2);
                }
                if ($dependencies !== null && strlen($a[$key]) > 0) {
                    return true;
                }
            }
        }
        return true;
    }

    /*
     * set error messages
     */
    private function error($error = NULL, $noExit = NULL)
    {

        $msg = '';
        switch ($error) {
            case 1:
                $msg = 'Required an array , or this array is empty !';
                break;
            case 2:
                $msg = 'This key not exist in this array (maybe you try to sort by a KEY which is not).';
                break;
            case 3:
                $msg = '<!-- Loaded , but dependency not found !-->';
                break;
        }
        if (strlen($msg) > 0) {
            echo $msg;
            if ($noExit == null) exit();
        }
    }
}
