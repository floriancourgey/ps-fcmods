<?php
class AdminCustomersController extends AdminCustomersControllerCore{
  public function __construct() {
    parent::__construct();

    unset($this->fields_list['title']);
    unset($this->fields_list['active']);
    unset($this->fields_list['newsletter']);
    unset($this->fields_list['optin']);
  }
  public function setMedia(){
    parent::setMedia();

  }
  public function renderForm(){
    $customer = $this->object;
    // dump($this->display.' '.$this->action);die();
    if(($this->display == 'add' && $this->action == 'new')
      || $this->display == 'edit' && empty($this->action)){
      $this->fields_form_override = [
        [
          'name'=>'note',
          'type'=>'textarea',
          'label'=>$this->trans('Note', [], 'Admin.Global'),
          'col'=>'9',
        ]
      ];
    }

    if(!$customer->id){
      // $customer->id_gender = 1;
    }
    return parent::renderForm();
  }
  public function postProcess() {
    if($this->display == 'add' && $this->action == 'new'){
      $this->addJS('https://cdn.jsdelivr.net/npm/random-string-generator@0.1.0/dist/random.js', false);
      $this->addJS('/js/fc-admin-customers.js', false);
    }
    return parent::postProcess();
    // TODO Hook https://github.com/PrestaShop/PrestaShop/blob/master/classes/controller/AdminController.php#L965
  }
}
