<?php

class AdminOrdersController extends AdminOrdersControllerCore {
  public function __construct() {
    parent::__construct();

		// $this->bulk_actions = [
		// 	'delete' => [
    //       'text' => $this->l('Delete'),
    //       'icon' => 'icon-power-off text-success'
    //   ],
		// ];

    // $this->_select .= ', address.company as company';
    $this->addRowAction('edit');
    $this->addRowAction('delete');
    unset($this->fields_list['cname']);
    unset($this->fields_list['new']);
    unset($this->fields_list['payment']);
    unset($this->fields_list['id_pdf']);
    $this->fields_list['total_paid_tax_incl']['title'] = 'Total TTC facture';
    $this->fields_list = $this->array_insert_after('company', $this->fields_list, 'total_paid_real', [
      'title' => $this->trans('Total paye', [], 'Admin.Global'),
      'type'=>'text',
      'callback' => 'getTotalPaidReal',
      'align' => 'text-right',
    ]);
    $this->fields_list = $this->array_insert_after('reference', $this->fields_list, 'titre', [
      'title' => $this->trans('Title', [], 'Admin.Global'),
      'type'=>'text'
    ]);
  }

  public static function getTotalPaidReal($echo, $tr){
    $order = new Order($tr['id_order']);
    $invoice = OrderInvoice::getInvoiceByNumber($order->invoice_number);
    // dump($invoice);
    // dump((float)$invoice->getTotalPaid());
    // dump((string)$invoice->getTotalPaid());
    if($invoice){
      return Tools::displayPrice((string)$invoice->getTotalPaid(), (int)$order->id_currency);
    }
    return '';
    // dump($echo);
    // die();
    // $payments = OrderPayment::getByInvoiceId($order->id_invoice);
    // dump($payments);die();
    // return Tools::displayPrice((string)$invoice->getTotalPaid(), (int)$order->id_currency);
    // return count($payments);
    // return $order->id_invoice;
  }

  public function initPageHeaderToolbar(){
    parent::initPageHeaderToolbar();
    if($this->display == 'view' && $this->action == 'view'){
      $this->page_header_toolbar_btn['new_order'] = array(
        'href' => self::$currentIndex.'&addorder&token='.$this->token,
        'desc' => $this->trans('Add new order', array(), 'Admin.Orderscustomers.Feature'),
        'icon' => 'process-icon-new'
      );
      $this->page_header_toolbar_btn['edit_order'] = array(
        'href' => self::$currentIndex.'&updateorder&token='.$this->token.'&'.$this->identifier.'='.$this->object->id,
        'desc' => $this->trans('Edit', array(), 'Admin.Global'),
        'icon' => 'process-icon-edit'
      );
    }
  }

	public function postProcess(){
		// if(!empty($_POST)){
		// 	dump($this->object);
		// 	if(Tools::isSubmit('submitAddOrder')){
		// 		$return = parent::postProcess();
		// 		dump($this->object);
		// 		die();
		// 		return $return;
		// 	}
		// 	dump($_POST);
		// 	dump($this->object);
		// 	die();
		// }
		parent::postProcess();
	}

  public function renderForm(){
    // si création
    if($this->action=='new' && $this->display=='add'){
      return parent::renderForm();
    }
    // si modification
    $order = $this->object;
    $customer = $order->getCustomer();
    $currency = new Currency($order->id_currency);
    $info = '<div class="panel">
      <div class="panel-heading"><i class="icon icon-info"></i> Informations</div>
      <p>';
    $info .= "Ref commande : {$order->reference}<br/>";
    $info .= "Client : {$customer->firstname} {$customer->lastname}<br/>";
    $info .= "Total : ".Tools::displayPrice($order->total_paid, $currency)."<br/>";
    $info .= "Paye : ".Tools::displayPrice($order->getTotalPaid(), $currency)."<br/>";
    $info .= "Paiement : {$order->payment}<br/>";
    $info .= "Etat : {$order->getCurrentOrderState()->name[$this->context->language->id]}<br/>";
    $info .= '</p></div>';
    $this->content .= $info;
    $this->fields_form = [
        'legend' => [
            'title' => $this->l('FC mods orders'),
            'icon' => 'icon-list-ul'
        ],
        'input' => [
            [
                'name' => 'titre',
                'type' => 'text',
                'label' => 'Titre',
            ],
            [
                'name' => 'date_add',
                'type' => 'datetime',
                'label' => $this->trans('Date added', [], 'Admin.Global'),
                'required' => true,
            ],
            [
                'name' => 'reference',
                'type' => 'text',
                'label' => $this->trans('Reference', [], 'Admin.Global'),
                'required' => true,
            ],

        ],
        'submit' => [
              'title' => $this->trans('Save', [], 'Admin.Actions'),
          ]
      ];
      return AdminController::renderForm();

  }

  function array_insert_after($key, array &$array, $new_key, $new_value) {
    if (array_key_exists($key, $array)) {
      $new = [];
      foreach ($array as $k => $value) {
        $new[$k] = $value;
        if ($k === $key) {
          $new[$new_key] = $new_value;
        }
      }
      return $new;
    }
    return FALSE;
  }

}
