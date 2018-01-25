<?php
class IndexController extends IndexControllerCore{
  public function initContent(){
    parent::initContent();
    $category = new Category($this->context->customer->id_category);dump($category);die();
    $this->context->smarty->assign(array(
      'seances' => [
        ['id'=>1, 'type'=>'Yoga', 'de'=>'19h30', 'a'=>'20h30'],
        ['id'=>5, 'type'=>'Cross Training', 'de'=>'10h30', 'a'=>'12h00'],
        ['id'=>10, 'type'=>'Yoga', 'de'=>'15h00', 'a'=>'16h30'],
      ],
      'context' => $this->context,
      'category' => $category,
    ));
  }
}
