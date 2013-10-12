<?php
class My_Controller extends BaseController
{
    /**
     * initialization
     */
    public function __construct()
    {
    }

    /**
     * $view->assign()を楽にするhelper関数
     * @param  String template name
     * @param  Array  Variables to use in templates
     * @return None
     */
    public function _display($template,$data)
    {
        //Smarty 初期化
        $view = new MySmarty();
        //変数をassignにバインド
        foreach ($data as $k => $v){
            $view->assign($k,$v);
        }
        //Extensions
        $EXT = Config::get('constants.view_ext');
        return $view->fetch($template . $EXT);
    }

    public function vd($dump)
    {
        echo "<pre>";
        var_dump($dump);
        echo "</pre>";
        exit(1);
    }
}
?>