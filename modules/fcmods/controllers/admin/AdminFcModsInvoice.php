<?php
class AdminFcModsInvoiceController extends ModuleAdminController{
  protected $kernel;

    public function __construct(){

      global $kernel;
      $this->kernel = $kernel;
      $this->bootstrap = true;
      $this->table = 'order_invoice';
      $this->className = 'OrderInvoice';
      $this->allow_export = true;
      $this->_select = 'o.reference, concat(c.firstname, " ", c.lastname) as customer ';
      $this->_join = '
        JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = a.`id_order`)
		    JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
      ';

      parent::__construct();

      $this->context->smarty->assign(array(
        'hello' => 'Hello World!!!',
  		));
    }

    public function renderList(){
      $this->orderBy = 'date_add';
      $this->_orderWay = 'DESC';

      $this->fields_list = [
          'id_order_invoice' => ['title' => $this->trans('ID', [], 'Admin.Global'),'class' => 'fixed-width-xs'],
          'number' => ['title' => $this->trans('Number', [], 'Admin.Global'),'class' => 'fixed-width-xs'],
          'date_add' => ['title' => $this->trans('Date', [], 'Admin.Global'), 'type'=>'datetime'],
          'customer' => ['title' => $this->trans('Customer', [], 'Admin.Global')],
          'total_paid_tax_incl' => ['title' => $this->trans('Total', [], 'Admin.Global'),
            'align' => 'text-right',
            'type' => 'price',
          'badge_success' => true],

          'reference' => ['title' => $this->trans('Order', [], 'Admin.Global')],
      ];

        $this->addRowAction('edit');
        $this->addRowAction('details');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm(){
      $invoice = $this->object;
      $order = $invoice->getOrder();
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
              'title' => $this->l('FC mods invoices'),
              'icon' => 'icon-list-ul'
          ],
          'input' => [
              [
                  'name' => 'date_add',
                  'type' => 'datetime',
                  'label' => $this->trans('Date', [], 'Admin.Global'),
                  'required' => true,
              ],
              [
                'name'=>'number',
                'type'=>'text',
                'required' => true,
                'label' => $this->trans('Number', [], 'Admin.Global'),
              ],
              [
                'name'=>'note',
                'type'=>'textarea',
                'label' => $this->trans('Note', [], 'Admin.Global'),
              ],
          ],
          'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ]
        ];
      return parent::renderForm();
    }

    public function viewAccess($disable = false){
      return true;
	  }
    public function formAccess($disable = false){
      return true;
	  }
    public function checkAccess($disable = false){
      return true;
	  }
}
