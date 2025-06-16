<?php 
/**
 * @author Bernardo Fuentes
 * @since 10/10/2024
 */
class ItivosGoogleMaps extends modules
{
	public $html = "";
    public function __construct()
    {
        $this->name ='itivos_googlemaps';
        $this->displayName = "Google Maps";
        $this->description = $this->l('Agrega un mapa de tu dirección en el home del sitio web');
        $this->category  ='front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Bernardo Fuentes';
        $this->versions_compliancy = array('min'=>'1.0.0', 'max'=> __SYSTEM_VERSION__);
        $this->confirmUninstall = $this->l('Are you sure about removing these details?');
        $this->template_dir = __DIR_MODULES__."itivos_googlemaps/views/back/";
        $this->template_dir_front = __DIR_MODULES__."itivos_googlemaps/views/front/";
        parent::__construct();

        $this->key_module = "de34103f763d1cafc0dd87bf3c8ab91d";
        $this->crontLink = __URI__.__ADMIN__."/module/".$this->name."/crontab?key=".$this->key_module."";
    }
    public function install()
    {
    	 if(!$this->registerHook("displayFrontHead") ||
            !$this->registerHook("displayFrontBottom") ||
            !$this->registerHook("displayFrontBeforeFooterContact") ||
            !$this->defaultData() 
            ){
            return false;
        }
        return true;
    }
    public function uninstall()
    {
    	$return = true;
    	$return &= connect::execute("DELETE FROM ".__DB_PREFIX__. "configuration WHERE module = '".$this->name."'");
    	return $return;
    }
    public function defaultData()
    {
        $return = true;
        $return &= Configuration::updateValue('itivos_googlemaps_uri', 
                                   "1110x150",
                                   'itivos_googlemaps');
        
        return $return;
    }
    public function getConfig()
    {
    	if (isIsset('submit_action')) {
			Configuration::updateValue("itivos_googlemaps_uri", 
                                       getValue("itivos_googlemaps_uri"),
                                       'itivos_googlemaps');
            $_SESSION['message'] = "Mapa actualizado correctamente";
            $_SESSION['type_message'] = "success";
            header("Location: ".__URI__.__ADMIN__."/modules/config/".$this->name."");
    	}
    	$helper = new HelperForm();
        $helper->tpl_vars = array(
            'fields_values' => array("itivos_googlemaps_uri" =>Configuration::getValue("itivos_googlemaps_uri")),
            'languages' => language::getLangs($this->lang),
        );
        $helper->submit_action = "updateAction";
        return $this->html = $helper->renderForm(self::generateForm());
    }
    public function generateForm()
    {
        $form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Editar mi mapa'),
                        'icon' => 'icon-cogs',
                    ),
                    'inputs' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->l('Google Maps'),
                            'name' => 'itivos_googlemaps_uri',
                            'required' => true,
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Guardar configuración'),
                    ),
                ),
            );
        return $form;
    }
    public function hookDisplayFrontBeforeFooterContact($params = null)
    {
    	$uri_map = Configuration::getValue('itivos_googlemaps_uri');
        $this->view->assign("itivos_googlemaps_uri", $uri_map);
        $this->view->display($this->template_dir_front."displayFrontHomeTop.tpl");
    }
}