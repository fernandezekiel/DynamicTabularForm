DynamicTabularForm
==================
Allows us to Create dynamic tabular forms and also being able to use partial views for each 'row'

Example
==================

This is an example of the controller with actionCreate
this extension also uses a Custom action to load the each row of the tabular inputs via ajax
it also uses proccessOutput by default


Controller: SlaController.php

class SlaController extends Controller {
    public function loadModel($id){
        $model = Sla::model()->findbyPk($id);
        if($model == null)
            throw new CHttpException(404,"Page not found");
        return $model;
    }
    public function actions() {
        return array(
            'getRowForm' => array(
                'class' => 'ext.dynamictabularform.actions.GetRowForm',
                'view' => '_rowForm',
                'modelClass' => 'SlaDetail'
            ),
        );
    }

    /**
     * without relation extension
     */
    public function actionCreate() {
        /**
         * a typical setup... SLA is my header and its details is the SlaDetail model
         * this i like a regular receipt
         */
        $sla = new Sla();
        $sladetails = array(new SlaDetail);

        if (isset($_POST['Sla'])) {
            $sla->attributes = $_POST['Sla'];

            /**
             * creating an array of contact objects
             */
            if (isset($_POST['SlaDetail'])) {
                $sladetails = array();
                foreach ($_POST['SlaDetail'] as $key => $value) {
                    /*
                     * Contact needs a scenario wherein the fk customer_id
                     * is not required because the ID can only be
                     * linked after the customer has been saved
                     */
                    $sladetail = new SlaDetail('batchSave');
                    $sladetail->attributes = $value;
                    $sladetails[] = $sladetail;
                }
            }
            /**
             * validating the customer and array of contacts
             */
            $valid = $sla->validate();
            foreach ($sladetails as $sladetail) {
                $valid = $sladetail->validate() & $valid;
            }

            if ($valid) {
                $transaction = $sla->getDbConnection()->beginTransaction();
                try {
                    $sla->save();
                    $sla->refresh();

                    foreach ($sladetails as $sladetail) {
                        $sladetail->sla_id = $sla->id;
                        $sladetail->save();
                    }
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollback();
                }



                $this->redirect(array('/crm/sla/view', 'id' => $sla->id));
            }
        }
        $this->render('create', array(
            'sla' => $sla,
            'sladetails' => $sladetails
        ));
    }

}

View: create.php

