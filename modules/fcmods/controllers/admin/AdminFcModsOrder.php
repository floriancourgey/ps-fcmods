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

      $this->bulk_actions = [
  			'delete' => [
            'text' => $this->l('Delete'),
            'icon' => 'icon-power-off text-success'
        ],
  		];
    }

    public function renderList(){
      $this->orderBy = 'date_add';
      $this->_orderWay = 'DESC';

      $this->fields_list = [
          'id_order' => ['title' => $this->trans('ID', [], 'Admin.Global'),'class' => 'fixed-width-xs'],
          'reference' => ['title' => $this->trans('Order', [], 'Admin.Global'),'class' => 'fixed-width-sm'],
          'titre' => ['title' => $this->trans('Title', [], 'Admin.Global'), 'type'=>'datetime'],
          'date_add' => ['title' => $this->trans('Date', [], 'Admin.Global'), 'type'=>'datetime'],
          'customer' => ['title' => $this->trans('Customer', [], 'Admin.Global')],
          'total_paid_tax_incl' => ['title' => $this->trans('Total', [], 'Admin.Global'),
            'align' => 'text-right',
            'type' => 'price',
          'badge_success' => true,
          'class' => 'fixed-width-sm'],
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
