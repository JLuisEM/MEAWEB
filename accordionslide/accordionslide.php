<?php

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class accordionslide extends Module
{
	/** @var max image size */
	protected $maxImageSize = 407200;

	public function __construct()
	{
		$this->name = 'accordionslide';
		$this->tab = 'others';
		$this->version = '1.0';
		$this->author = 'Prestashopic';
		$this->need_instance = 0;
				
		parent::__construct();

		$this->displayName = $this->l('Accordion Slider');
		$this->description = $this->l('Easily customizable Accordion slider');
	}

	function install()
	{
		if (!parent::install()
			OR !$this->registerHook('header')
			OR !$this->registerHook('footer')
			OR !$this->registerHook('home')
			OR !$this->registerHook('top')
			OR !Configuration::updateValue('TO_HOOK', 'top')
			OR !Configuration::updateValue('ACC_WIDTH', '980')
			OR !Configuration::updateValue('ACC_HEIGHT', '370')
			OR !Configuration::updateValue('ACC_CHANGE_SPEED', 800))
			return false;
		return true;
	}

	public function uninstall()
	{
	 	if (!parent::uninstall()
			OR !Configuration::deleteByName('TO_HOOK')
			OR !Configuration::deleteByName('ACC_WIDTH')
			OR !Configuration::deleteByName('ACC_CHANGE_SPEED')
			OR !Configuration::deleteByName('ACC_HEIGHT'))
	 		return false;
	 	return true;
	}
	public function putContent($xml_data, $key, $field, $forbidden, $section)
	{
		foreach ($forbidden AS $line)
			if ($key == $line)
				return 0;
		if (!preg_match('/^'.$section.'_/i', $key))
			return 0;
		$key = preg_replace('/^'.$section.'_/i', '', $key);
		$field = htmlspecialchars($field);
		if (!$field)
			return 0;
		return ("\n".'		<'.$key.'>'.$field.'</'.$key.'>');
	}

	public function getContent()
	{	
		/* display the module name */
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$errors = '';
		
		$TotalDestaques = 5;
		
		for ($i=0;$i<$TotalDestaques;$i++)
		{
			// Delete image
			if (Tools::isSubmit('deleteImage'.$i))
			{
				unlink(dirname(__FILE__).'/slider_'.$i.'.jpg');
				$this->_html .= $errors;
			}
		}

		/* update the destaques xml */
		if (isset($_POST['submitUpdate']))
		{
			Configuration::updateValue('TO_HOOK', Tools::getValue('hook_position'));
			Configuration::updateValue('ACC_WIDTH', Tools::getValue('accordion_width'));
			Configuration::updateValue('ACC_HEIGHT', Tools::getValue('accordion_height'));
			Configuration::updateValue('ACC_CHANGE_SPEED', Tools::getValue('accordion_speed'));

			$newXml = '<?xml version=\'1.0\' encoding=\'utf-8\' ?>'."\n";
			$newXml .= '<destaques>'."\n";
			$newXml .= '	<header>';

			foreach ($_POST AS $key => $field)
				if ($line = $this->putContent($newXml, $key, $field, $forbidden, 'header'))
					$newXml .= $line;
			$newXml .= "\n".'	</header>'."\n";
			$newXml .= '	<body>';

			foreach ($_POST AS $key => $field)
				if ($line = $this->putContent($newXml, $key, $field, $forbidden, 'body'))
					$newXml .= $line;
			$newXml .= "\n".'	</body>'."\n";
			$newXml .= '</destaques>'."\n";

			/* write it into the destaques xml file */
			if ($fd = @fopen(dirname(__FILE__).'/accordionslide.xml', 'w'))
			{
				if (!@fwrite($fd, $newXml))
					$errors .= $this->displayError($this->l('Unable to write to the editor file.'));
				if (!@fclose($fd))
					$errors .= $this->displayError($this->l('Can\'t close the editor file.'));
			}
			else
				$errors .= $this->displayError($this->l('Unable to update the editor file.<br />Please check the editor file\'s writing permissions.'));

			$totalDestaques = 5;
			
			for ($i=0; $i<=$totalDestaques; $i++){
				/* upload the image  i */
				if (isset($_FILES['body_slider_'.$i]) AND isset($_FILES['body_slider_'.$i]['tmp_name']) AND !empty($_FILES['body_slider_'.$i]['tmp_name']))
				{
					Configuration::set('PS_IMAGE_GENERATION_METHOD', 1);
					if ($error = checkImage($_FILES['body_slider_'.$i], $this->maxImageSize))
						$errors .= $error;
					elseif (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES['body_slider_'.$i]['tmp_name'], $tmpName))
						return false;
					elseif (!imageResize($tmpName, dirname(__FILE__).'/slider_'.$i.'.jpg'))
						$errors .= $this->displayError($this->l('An error occurred during the image upload.'));
					unlink($tmpName);
				}
			}
			$this->_html .= $errors == '' ? $this->displayConfirmation('Settings updated successfully') : $errors;
		}

		/* display the destaques form */
		$this->_displayForm();
	
		return $this->_html;
	}
	private function _displayForm()
	{
		global $cookie;
		/* Languages preliminaries */
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages();
		$iso = Language::getIsoById(intval($cookie->id_lang));
		$divLangName = 'title¤subheading¤cpara¤logo_subheading';

		/* xml loading */
		$xml = false;
		if (file_exists(dirname(__FILE__).'/accordionslide.xml'))
				if (!$xml = simplexml_load_file(dirname(__FILE__).'/accordionslide.xml'))
					$this->_html .= $this->displayError($this->l('Your editor file is empty.'));

		$carouselCurrentHook = Configuration::get('TO_HOOK'); 		
		$options = array ('home', 'top');
		$fields = '<select name="hook_position">';
		
		foreach ($options as $opt)
		{
		 	$fields .= '<option value="'.$opt.'"';
			
			if ($carouselCurrentHook == $opt)
			{
				$fields .= ' selected="selected"';
			}

			$fields .= '>'.$opt.'</option>';
		}
		
		$fields .= '</select>';
		
		$this->_html .= '
		<form method="post" action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data">
			<fieldset style="width: 900px;position:relative">
					<div style="position:absolute;top:30px;right:30px;width:300px;height:100px;color:red">*The default/max number of elements for this Slider is 5 but <strong>You can decrease the number of the elements by simply removing the image of the Item</strong>"</div> 
			
					<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->displayName.'</legend>';
		$this->_html .= '
					<h3 style="margin-left:100px;">Module basic configuration</h3>
					<label style="width:130px;">'.$this->l('Append to ').'</label>
					<div class="margin-form" style="padding-left:100px;">'
						. $fields . 
					'
						</select>
						<p class="clear">'.$this->l('This defines where your accordion slider should appear').'</p>
					</div>

					<label style="width:130px;">'.$this->l('Width').'</label>
					<div class="margin-form" style="padding-left:100px;">
						<input type="text" name="accordion_width" value="'.Configuration::get('ACC_WIDTH').'" />
					<p class="clear">'.$this->l('The Accordion slider Images Width').'</p>
					</div>
					
					<label style="width:130px;">'.$this->l('Height').'</label>
					<div class="margin-form" style="padding-left:100px;">
						<input type="text" name="accordion_height" value="'.Configuration::get('ACC_HEIGHT').'" />
					<p class="clear">'.$this->l('The Accordion slider Images Height').'</p>
					</div>
					
					<label style="width:130px;">'.$this->l('Duration Speed').'</label>
					<div class="margin-form" style="padding-left:100px;">
						<input type="text" name="accordion_speed" value="'.Configuration::get('ACC_CHANGE_SPEED').'" />
					<p class="clear">'.$this->l('The timing before going to the next Accordion slider Item (in miliseconds)').'</p>
					</div>
					
                    
					<div class="margin-form clear" style="padding-left:100px;"><input type="submit" name="submitUpdate" value="'.$this->l('Save').'" class="button" /></div><br/><br/>';
					
					$totalDestaques = 5;
					
					for ($i=0; $i<$totalDestaques; $i++){
						$item = $i + 1;
						/* Primeiro Destaque */
						$this->_html .='
							<h3 class="clear" style="margin-left:100px;">Accordion Item '.$item.'</h3>
							<label style="width:130px;">'.$this->l('Title').'</label>
							<div class="margin-form" style="padding-left:100px;">';
							
					$this->_html .= '
							<div id="title_'.$i.'">
								<input type="text" name="body_title_'.$i.'" id="body_title_'.$i.'" size="64" value="'.($xml ? stripslashes(htmlspecialchars($xml->body->{'title_'.$i})) : '').'" />
							</div>';
			
					$this->_html .= '
								<p class="clear">'.$this->l('Its the mouseover message that appears on the Image').'</p>
								<div class="clear"></div>
							</div>';
									
					$this->_html .= '
							<label style="width:130px;">'.$this->l('Link').'</label>
							<div class="margin-form" style="padding-left:100px;">
								<input type="text" name="body_home_logo_link_'.$i.'" size="64" value="'.($xml ? stripslashes(htmlspecialchars($xml->body->{'home_logo_link_'.$i})) : '').'" />
								<p style="clear: both">'.$this->l('Leave an "#" if you dont want this image to link to any page').'</p>
							</div>
							<div class="clear"></div>
							<label style="width:135px;">'.$this->l('Image').' </label>

							<div class="margin-form" style="padding-left:100px;">
									<img src="'.$this->_path.'slider_'.$i.'.jpg?t='.time().'" width="80%" />
									<br/>					
									<input type="file" name="body_slider_'.$i.'" />
									<a href="'.$_SERVER['REQUEST_URI'].'&deleteImage'.$i.'" onClick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');">
									<img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /> '.$this->l('Delete').'</a>
									<p style="clear: both">'.$this->l('The Image Size should be equal to the Widht and Height defined. Deleting the above image will remove this Highlight from the Carousel.').'</p>
								<div class="clear"></div>
							</div>
							<div class="margin-form clear" style="padding-left:100px;"><input type="submit" name="submitUpdate" value="'.$this->l('Update').'" class="button" /></div>
							<br/><br/>';
					}
                    $total= $item;
		/* Final dos destaques */
		$this->_html .= '
			<div class="clear pspace"></div>
			<div class="margin-form clear" style="padding-left:100px;"><input type="submit" name="submitUpdate" value="'.$this->l('Update All').'" class="button" /></div>
			</fieldset>
		</form>';
	}
	public function hookHeader($params)
	{
		Tools::addCSS(($this->_path).'accordionslide.css', 'all');
 	}
	

	
	public function hookHome($params)
	{
		$where = Configuration::get('TO_HOOK');
		
		switch ($where)
		{
			case 'home':
				
				return $this->showCarousel($params);
				
				break;
				
			case 'top':
				
				return;
				
				break;
		}
	}
	
	public function hookTop($params)
	{
		$where = Configuration::get('TO_HOOK');
		
		switch ($where)
		{
			case 'top':
				
				return $this->showCarousel($params);
				
				break;
				
			case 'home':
				
				return;
				
				break;
		}
	}
	
	public function showCarousel($params)
	{
		if (file_exists('modules/accordionslide/accordionslide.xml'))
		{
			if ($xml = simplexml_load_file('modules/accordionslide/accordionslide.xml'))
			{
				$destaques = array(
					0 => array (
						'logo' => file_exists('modules/accordionslide/slider_0.jpg'),
						'logo_link' => $xml->body->home_logo_link_0,
						'logo_title' => $xml->body->title_0
						),
					1 => array (
						'logo' => file_exists('modules/accordionslide/slider_1.jpg'),
						'logo_link' => $xml->body->home_logo_link_1,
						'logo_title' => $xml->body->title_1
						),
					2 => array (
						'logo' => file_exists('modules/accordionslide/slider_2.jpg'),
						'logo_link' => $xml->body->home_logo_link_2,
						'logo_title' => $xml->body->title_2
						),
					3 => array (
						'logo' => file_exists('modules/accordionslide/slider_3.jpg'),
						'logo_link' => $xml->body->home_logo_link_3,
						'logo_title' => $xml->body->title_3
						),
					4 => array (
						'logo' => file_exists('modules/accordionslide/slider_4.jpg'),
						'logo_link' => $xml->body->home_logo_link_4,
						'logo_title' => $xml->body->title_4
						),
				);
				
				global $cookie, $smarty;
				$smarty->assign(array(
					'width' => Configuration::get('ACC_WIDTH'),
					'height' => Configuration::get('ACC_HEIGHT'),
                    'hookslider' => Configuration::get('TO_HOOK'),
					'xml' => $xml,
      				'destaques' => $destaques,
					'changeSpeed' => Configuration::get('ACC_CHANGE_SPEED'),
					'this_path' => $this->_path
				));
				return $this->display(__FILE__, 'accordionslide.tpl');
			}
		}
		return false;
	}
}