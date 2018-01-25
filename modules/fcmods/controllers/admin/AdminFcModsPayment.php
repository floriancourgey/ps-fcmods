<?php
class AdminFcModsPaymentController extends ModuleAdminController{
  protected $kernel;

    public function __construct(){

      global $kernel;
      $this->kernel = $kernel;
      $this->bootstrap = true;
      $this->table = 'order_payment';
      $this->className = 'OrderPayment';
      $this->allow_export = true;
      $this->_select = 'o.titre, concat(c.firstname, " ", c.lastname) as customer';
      $this->_join = '
        LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`reference` = a.`order_reference`)
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
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
          'id_order_payment' => ['title' => $this->trans('ID', [], 'Admin.Global'),'class' => 'fixed-width-xs'],
          'payment_method' => ['title' => $this->trans('Payment method', [], 'Admin.Global'),'class' => 'fixed-width-xs'],
          'date_add' => ['title' => $this->trans('Date', [], 'Admin.Global'), 'type'=>'datetime'],
          // 'customer' => ['title' => $this->trans('Customer', [], 'Admin.Global')],
          'amount' => ['title' => $this->trans('Amount', [], 'Admin.Global'),
            'align' => 'text-right',
            'type' => 'price',
          'badge_success' => true],
          'customer' => ['title' => $this->trans('Customer', [], 'Admin.Global')],
          'order_reference' => ['title' => $this->trans('Order', [], 'Admin.Global')],
          'titre' => ['title' => $this->trans('Title', [], 'Admin.Global')],
      ];

        $this->addRowAction('edit');
        $this->addRowAction('details');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm(){
      $payment = $this->object;
      // $invoice = $payment->getOrderInvoice();
      $orders = Order::getByReference($payment->order_reference);
      if(count($orders) != 1){
        $this->errors[] = ("None or too many orders found for this Payment. You shouldn't edit it. (".count($orders)." orders found with reference = '".$payment->order_reference."')");
        // $link = $this->context->link->getAdminLink('AdminFcModsPayment');
        // Tools::redirectAdmin($link);
        $order = new Order();
        $currency = new Currency($this->context->currency->id);
        $customer = new Customer();
      } else {
        $order = $orders[0];
        $currency = new Currency($order->id_currency);
        $customer = $order->getCustomer();
      }
      $info = '<div class="panel">
	      <div class="panel-heading"><i class="icon icon-info"></i> Informations</div>
	      <p>';
      $info .= "Ref commande : {$order->reference}<br/>";
      $info .= "Client : {$customer->firstname} {$customer->lastname}<br/>";
      $info .= "Total : ".Tools::displayPrice($order->total_paid, $currency)."<br/>";
      $info .= "Paye : ".Tools::displayPrice($order->getTotalPaid(), $currency)."<br/>";
      $info .= "Paiement : {$order->payment}<br/>";
      if($order->getCurrentOrderState()){
        $info .= "Etat : {$order->getCurrentOrderState()->name[$this->context->language->id]}<br/>";
      }
      $info .= '</p></div>';
      $this->content .= $info;

      $payment_methods = array(); // taken from https://github.com/PrestaShop/PrestaShop/blob/develop/controllers/admin/AdminOrdersController.php#L1742
      foreach (PaymentModule::getInstalledPaymentModules() as $payment) {
        $module = Module::getInstanceByName($payment['name']);
        if (Validate::isLoadedObject($module) && $module->active) {
          $payment_methods[] = ['key'=>$module->displayName, 'name'=>$module->displayName];
        }
      }

      $currencies = [];
      foreach(Currency::getCurrenciesByIdShop(Context::getContext()->shop->id) as $c){
        // dump($c)
        $currencies[] = ['key'=>$c['id_currency'], 'name'=>"{$c['name']} ({$c['iso_code']})"];
        // dump($c);die();
      }

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
                  'name' => 'order_reference',
                  'type' => 'text',
                  'label' => $this->trans('Reference', [], 'Admin.Global').' '.$this->trans('Order', [], 'Admin.Global'),
                  'required' => true,
              ],
              [
                  'name' => 'payment_method',
                  'type' => 'select',
                  'options' => [
                    'query'=> $payment_methods,
                    'id' => 'key',
                    'name' => 'name',
                  ],
                  'label' => $this->trans('Method', [], 'Admin.Global').' '.$this->trans('Payment', [], 'Admin.Global'),
                  'required' => true,
              ],
              [
                  'name' => 'amount',
                  'suffix' => $currency->sign,
                  'type' => 'text',
                  'label' => $this->trans('Amount', [], 'Admin.Global'),
                  'required' => true,
              ],
              [
                  'name' => 'id_currency',
                  'type' => 'select',
                  'options' => [
                    'query'=> $currencies,
                    'id' => 'key',
                    'name' => 'name',
                  ],
                  'label' => $this->trans('Currency', [], 'Admin.Global'),
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
