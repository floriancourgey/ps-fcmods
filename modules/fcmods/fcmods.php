<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Fcmods extends Module
{
    protected $config_form = false;
    protected $tabs = [
      'order'=>'AdminFcModsOrder',
      'invoice'=>'AdminFcModsInvoice',
      'payment'=>'AdminFcModsPayment',
      'order_state'=>'AdminFcModsOrderState',
    ];

    public function __construct()
    {
        $this->name = 'fcmods';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Florian Courgey';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('FC mods');
        $this->description = $this->l('Provides overrides, shortcuts and helpers for Prestashop front and back office');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install(){
      Configuration::updateValue('FCMODS_LIVE_MODE', false);

      parent::install();

      $languages = Language::getLanguages(false);
/*
      $tabRoot = new Tab();
      $tabRoot->id_parent = Tab::getIdFromClassName('CONFIGURE');
      $tabRoot->module = $this->name;
      $tabRoot->class_name = 'AdminFcMods';
      foreach ($languages as $lang){
        $tabRoot->name[$lang['id_lang']] = $this->l('FC mods');
      }
      $tabRoot->save();

      foreach ($this->tabs as $title => $class) {
        $tab = new Tab();
        $tab->id_parent = $tabRoot->id;
        $tab->module = $this->name;
        $tab->class_name = $class;
        foreach ($languages as $lang){
          $tab->name[$lang['id_lang']] = $this->l('FC mods').' '.$this->l($title);
        }
        $tab->save();
        Tab::initAccess($tab->id);
      }
*/
      // $tabRoot = new Tab();
      // $tabRoot->active = 1;
      // $tabRoot->class_name = $this->tabs['order'];
      // $tabRoot->id_parent = Tab::getIdFromClassName('CONFIGURE');
      // $tabRoot->module = $this->name;
      // foreach ($languages as $lang){
      //   $tabRoot->name[$lang['id_lang']] = $this->l('FC mods');
      // }
      // $tabRoot->save();

      // $tabsParentId = $tabRoot->id;
      $tabsParentId = Tab::getIdFromClassName('CONFIGURE');

      $tab = new Tab();
      $tab->active = 1;
      $tab->class_name = $this->tabs['order'];
      $tab->id_parent = $tabsParentId;
      $tab->module = $this->name;
      foreach ($languages as $lang){
        $tab->name[$lang['id_lang']] = $this->l('FC mods').' '.$this->l('orders');
      }
      $tab->save();

      $tab1 = new Tab();
      $tab1->active = 1;
      $tab1->class_name = $this->tabs['invoice'];
      $tab1->id_parent = $tabsParentId;
      $tab1->module = $this->name;
      foreach ($languages as $lang){
        $tab1->name[$lang['id_lang']] = $this->l('FC mods invoices');
      }
      $tab1->save();

      $tab = new Tab();
      $tab->active = 1;
      $tab->class_name = $this->tabs['payment'];
      $tab->id_parent = $tabsParentId;
      $tab->module = $this->name;
      foreach ($languages as $lang){
        $tab->name[$lang['id_lang']] = $this->l('FC mods payments');
      }
      $tab->save();

      $tab = new Tab();
      $tab->active = 1;
      $tab->class_name = $this->tabs['order_state'];
      $tab->id_parent = $tabsParentId;
      $tab->module = $this->name;
      foreach ($languages as $lang){
        $tab->name[$lang['id_lang']] = $this->l('FC mods states');
      }
      $tab->save();

      return $this->registerHook('header') &&
        $this->registerHook('backOfficeHeader') &&
        $this->registerHook('displayBackOfficeHeader') &&
        $this->registerHook('displayAdminProductsExtra')
      ;
    }

    public function uninstall()
    {
        // config
        Configuration::deleteByName('FCMODS_LIVE_MODE');
        // tabs
        foreach ($this->tabs as $tabClass) {
          $id_tab = Tab::getIdFromClassName($tabClass);
         if ($id_tab) {
           $tab = new Tab($id_tab);
           $tab->delete();
         }
       }

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitFcmodsModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFcmodsModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'FCMODS_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'FCMODS_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'FCMODS_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'FCMODS_LIVE_MODE' => Configuration::get('FCMODS_LIVE_MODE', true),
            'FCMODS_ACCOUNT_EMAIL' => Configuration::get('FCMODS_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'FCMODS_ACCOUNT_PASSWORD' => Configuration::get('FCMODS_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
    public function hookDisplayAdminProductsExtra(){
      echo "hookDisplayAdminProductsExtra";
    }


}
