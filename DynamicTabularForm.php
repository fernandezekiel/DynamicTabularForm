<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DynamicTabularForm
 *
 * @author Web Developer
 */
class DynamicTabularForm extends CActiveForm {

    //put your code here
    /*
     * the url of the ajax path
     */
    public $rowUrl;
    public $rowView = '_rowForm';

    public function checkBox($model, $attribute, $htmlOptions = array()) {
        parent::checkBox($model, $attribute, $htmlOptions);
    }

    public function init() {
        parent::init();
        if ($this->rowUrl == null)
            $this->rowUrl = $this->controller->createUrl('getRowForm');
    }

    public function rowForm($models = array(), $htmlOptions = array()) {
        $cs = Yii::app()->clientScript;
        $cs->registerScript("DynamicForm", "
            var counter = " . sizeof($models) . ";
            function addRow(){
                counter = counter + 1;
                $.ajax({
                    url:'" . $this->rowUrl . "',
                    data:{
                        key:counter,
                    },
                    success:function(data){appendRow(data)},
                });
            }
            function appendRow(html){
               $('#row-form').append(html);
            }
            $('#addButton').click(function(e){addRow()});
        ");
        $htmlOptions = array_merge(array('id'=>'row-form'),$htmlOptions);
        echo CHtml::openTag('div', $htmlOptions);
        foreach ($models as $key => $model) {
            $this->controller->renderPartial($this->rowView, array('key' => $key, 'model' => $model));
        }

        echo "</div>";
        echo CHtml::button('+', array(
            'id' => 'addButton'
        ));
    }

}

?>
