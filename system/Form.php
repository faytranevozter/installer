<?php 

/**
* Form Library
*/
class Form
{
	private $form_type = array('text', 
								'password',
								'hidden',
								'date',
								'number',
								'search',
								'color',
								'url',
								'reset',
								'datetime-local',
								'email',
								'file',
								'month',
								'range',
								'submit',
								'tel',
								'time',
								'week',
								'checkbox',
								'radio',
								'dropdown',
								'select',
								'textarea',
								'button');

	public $form_group_template = "<div>{label}{form}</div>";
	public $label_template = "<label for=\"{form_id}\">{content}</label>";

	
	function __construct()
	{
		$this->ins =& get_instance();
	}

	public function input($data, $value='', $extra='')
	{
		$defaults = array(
			'type' => 'text',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);
		
		if (is_array($data)) {
			if (!in_array($data['type'], $this->form_type)) {
				$data['type'] = 'text';
			}
		} else {
			$data = array();
			$data['name'] = $defaults['name'];
			$data['type'] = $defaults['type'];
			$data['value'] = $defaults['value'];
		}
		
		if ($data['type'] == 'textarea') {
			if ( ! is_array($data) OR ! isset($data['value'])) {
				$val = $value;
			} else {
				$val = $data['value'];
				unset($data['value']);
			}
			return '<textarea '.$this->_parse_form_attributes($data, $defaults).$this->_attributes_to_string($extra).'>'.$this->html_escape($val) ."</textarea>\n";
		} elseif ($data['type'] == 'radio' || $data['type'] == 'checkbox') {

			if (is_array($data) && array_key_exists('checked', $data)) {
				$checked = $data['checked'];

				if ($checked == FALSE) {
					unset($data['checked']);
				} else {
					$data['checked'] = 'checked';
				}
			}
			$check_radio = '';
			$check_radio_label = FALSE;
			if (isset($data['checkbox_label']) OR isset($data['radio_label'])) {
				$extra_label = isset($data['extra_label']) ? $this->_attributes_to_string($data['extra_label']) : '';
				$check_radio .= '<label'.$extra_label.'>';
				$label = isset($data['checkbox_label']) ? $data['checkbox_label'] : $data['radio_label'];
				unset($data['checkbox_label'], $data['radio_label'], $data['extra_label']);
				$check_radio_label = TRUE;
			}

			$check_radio .= '<input '.$this->_parse_form_attributes($data, $defaults).$this->_attributes_to_string($extra)." />\n";	
			
			if ($check_radio_label) {
				$check_radio .= $label.'</label>';
			}
			return $check_radio;

		} elseif ($data['type'] == 'dropdown' || $data['type'] == 'select') {
			$options = isset($data['options']) ? $data['options'] : array();
			$selected = isset($data['selected']) ? $data['selected'] : array();
			return $this->form_dropdown($data, $options, $selected, $extra);
		} else {
			return '<input '.$this->_parse_form_attributes($data, $defaults).$this->_attributes_to_string($extra).">\n";
		}
	}

	protected function _parse_form_attributes($attributes, $default)
	{
		if (is_array($attributes)) {
			foreach ($default as $key => $val) {
				if (isset($attributes[$key])) {
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}

			if (count($attributes) > 0) {
				$default = array_merge($default, $attributes);
			}
		}

		$att = '';

		foreach ($default as $key => $val) {
			if ($key === 'value') {
				$val = $this->html_escape($val);
			} elseif ($key === 'name' && ! strlen($default['name'])) {
				continue;
			}

			$att .= $key.'="'.$val.'" ';
		}

		return $att;
	}

	protected function _attributes_to_string($attributes)
	{
		if (empty($attributes)) {
			return '';
		}

		if (is_object($attributes)) {
			$attributes = (array) $attributes;
		}

		if (is_array($attributes)) {
			$atts = '';

			foreach ($attributes as $key => $val) {
				$atts .= ' '.$key.'="'.$val.'"';
			}

			return $atts;
		}

		if (is_string($attributes)) {
			return ' '.$attributes;
		}

		return FALSE;
	}

	public function html_escape($var, $double_encode = TRUE)
	{
		if (empty($var)) {
			return $var;
		}

		if (is_array($var)) {
			foreach (array_keys($var) as $key) {
				$var[$key] = $this->$this->html_escape($var[$key], $double_encode);
			}

			return $var;
		}

		return htmlspecialchars($var, ENT_QUOTES, 'UTF-8', $double_encode);
	}

	protected function form_dropdown($data = '', $options = array(), $selected = array(), $extra = '')
	{
		$defaults = array();

		if (is_array($data)) {
			if (isset($data['selected'])) {
				$selected = $data['selected'];
				unset($data['selected']); // select tags don't have a selected attribute
			}

			if (isset($data['options'])) {
				$options = $data['options'];
				unset($data['options']); // select tags don't use an options attribute
			}
		} else {
			$defaults = array('name' => $data);
		}

		is_array($selected) OR $selected = array($selected);
		is_array($options) OR $options = array($options);

		// If no selected state was submitted we will attempt to set it automatically
		if (empty($selected)) {
			if (is_array($data)) {
				if (isset($data['name'], $_POST[$data['name']])) {
					$selected = array($_POST[$data['name']]);
				}
			} elseif (isset($_POST[$data])) {
				$selected = array($_POST[$data]);
			}
		}

		$extra = $this->_attributes_to_string($extra);

		$multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		$form = '<select '.rtrim($this->_parse_form_attributes($data, $defaults)).$extra.$multiple.">\n";

		foreach ($options as $key => $val) {
			$key = (string) $key;

			if (is_array($val)) {
				if (empty($val)) {
					continue;
				}

				$form .= '<optgroup label="'.$key."\">\n";

				foreach ($val as $optgroup_key => $optgroup_val) {
					$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="'.$this->html_escape($optgroup_key).'"'.$sel.'>'
						.(string) $optgroup_val."</option>\n";
				}

				$form .= "</optgroup>\n";
			} else {
				$form .= '<option value="'.$this->html_escape($key).'"'
					.(in_array($key, $selected) ? ' selected="selected"' : '').'>'
					.(string) $val."</option>\n";
			}
		}

		return $form."</select>\n";
	}

	public function set_label($label='', $label_id='')
	{
		if (!empty($label)) {
			$html_label = str_replace(array('{content}', '{form_id}'), array($label, $label_id), $this->label_template);
			$html_label .= "\n";
		} else {
			$html_label = '';
		}
		return $html_label;
	}

	public function form_group($arr=array())
	{
		$label = isset($arr['label']) ? $arr['label'] : '';
		$id = isset($arr['id']) ? $arr['id'] : '';
		$extra = isset($arr['extra']) ? $arr['extra'] : '';
		$html_label = !empty($label) ? $this->set_label($label, $id) : '';
		unset($arr['label'], $arr['extra']); // useless in form input

		$html_form = $this->input($arr);
		
		$html_group = str_replace(array('{label}', '{form}', '{extra}'), array($html_label, $html_form, $extra."\n"), $this->form_group_template);
		return $html_group."\n";
	}

	public function multiple_group($data=array())
	{
		$groups = '';
		foreach ($data as $name => $form_group_config) {
			$form_group_config['name'] = $name; // overriding input name
			$groups .= $this->form_group($form_group_config);
		}
		return $groups;
	}

}