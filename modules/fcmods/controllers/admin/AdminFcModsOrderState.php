<?php
class AdminFcModsOrderStateController extends ModuleAdminController{
  protected $kernel;

    public function __construct(){

      global $kernel;
      $this->kernel = $kernel;
      $this->bootstrap = true;
      $this->table = 'order_history';
      $this->className = 'OrderHistory';
      $this->allow_export = true;
      $this->_select = 'osl.name as state_name, o.reference, o.date_add as order_date,
        CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`
      ';
      $this->_join = '
        JOIN `'._DB_PREFIX_.'orders` o ON (a.`id_order` = o.`id_order`)
        JOIN `'._DB_PREFIX_.'customer` c ON (o.`id_customer` = c.`id_customer`)
		    JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (a.`id_order_state` = osl.`id_order_state`)
      ';

      parent::__construct();
    }

    public function renderList(){
      $this->orderBy = 'date_add';
      $this->_orderWay = 'DESC';

      $this->fields_list = [
          'id_order_history' => ['title' => $this->trans('ID', [], 'Admin.Global'),'class' => 'fixed-width-xs'],
          // 'module_name' => ['title' => $this->trans('Payment method', [], 'Admin.Global'),'class' => 'fixed-width-xs'],
          'date_add' => ['title' => $this->trans('Date', [], 'Admin.Global'), 'type'=>'datetime'],
          'state_name' => ['title' => $this->trans('State', [], 'Admin.Global')],
          // 'amount' => ['title' => $this->trans('Total', [], 'Admin.Global'),
          //   'align' => 'text-right',
          //   'type' => 'price',
          // 'badge_success' => true],

          'customer' => ['title' => $this->trans('Customer', [], 'Admin.Global')],
          'reference' => ['title' => $this->trans('Order', [], 'Admin.Global')],
          'order_date' => ['title' => $this->trans('Order', [], 'Admin.Global').' '.$this->trans('Date', [], 'Admin.Global'), 'type'=>'datetime'],
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
