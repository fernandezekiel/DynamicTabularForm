<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GetRowForm
 *
 * @author Web Developer
 */
class GetRowForm extends CAction{
    //put your code here
    public $view;
    public $modelClass;
    public function run() {
        $controller = $this->getController();
        $model = new $this->modelClass;
        $controller->renderPartial($this->view,array('key'=>$_GET['key'], 'model'=>$model));
    }
}

?>
