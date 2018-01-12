<?php
class AdminFcModsOrderController extends ModuleAdminController{
  protected $kernel;

    public function __construct(){

      global $kernel;
      $this->kernel = $kernel;
      $this->bootstrap = true;
      $this->table = 'order';
      $this->className = 'Order';
      $this->allow_export = true;
      $this->_select = 'concat(c.firstname, " ", c.lastname) as customer ';
      $this->_join = '
		    JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
      ';

      parent::__construct();
    }

    public function renderList(){
      $this->orderBy = 'date_add';
      $this->_orderWay = 'DESC';

      $this->fields_list = [
          'id_order' => ['title' => $this->trans('ID', [], 'Admin.Global'),'class' => 'fixed-width-xs'],
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
