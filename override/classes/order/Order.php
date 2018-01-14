<?php
class Order extends OrderCore {
  public $titre;

  public function __construct($id = null, $id_lang = null){
    self::$definition['fields']['titre'] = [
      'type' => self::TYPE_STRING,
      'validate' => 'isGenericName',
      'size' => 255,
    ];
    parent::__construct($id, $id_lang);
  }
}
