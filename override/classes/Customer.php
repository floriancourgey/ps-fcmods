<?php
class Customer extends CustomerCore {
  public $id_category;

  public function __construct($id = null, $id_lang = null){
    self::$definition['fields']['id_category'] = [
      'type' => self::TYPE_INT,
      // 'validate' => 'isGenericName',
      // 'size' => 255,
    ];
    parent::__construct($id, $id_lang);
  }
}
