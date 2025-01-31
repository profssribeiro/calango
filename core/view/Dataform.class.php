<?php

class Dataform{
    private $form = array();
    private $elements = array();
    private $div = '';

    function __construct( $args = false ) {
		$defaults = array(
			'action'       => '#',
			'method'       => 'post',
			'enctype'      => '',
			'class'        => '',
			'id'           => '',
			'markup'       => 'html',
			'novalidate'   => false,
			'add_nonce'    => false,
			'add_honeypot' => false,
			'form_element' => true,
			'add_submit'   => true
		);
		// Merge with arguments, if present
		if ( $args ) {
			$settings = array_merge( $defaults, $args );
		} // Otherwise, use the defaults wholesale
		else {
			$settings = $defaults;
		}
		// Iterate through and save each option
		foreach ( $settings as $key => $val ) {
			$this->form[ $key ] = $val;
		}
	}

	function add($element,$args) {
        $this->elements[] = array($element,$args);
    }
	function div($class) {
        $this->div = $class;
    }
	function build_form() {
		$html = '';
		if ( $this->form['form_element'] ) {
			$html .= '<form method="' . $this->form['method'] . '"';
			if ( ! empty( $this->form['enctype'] ) )
				$html .= ' enctype="' . $this->form['enctype'] . '"';
			if ( ! empty( $this->form['action'] ) ) 
				$html .= ' action="' . $this->form['action'] . '"';
			if ( ! empty( $this->form['id'] ) ) 
				$html .= ' id="' . $this->form['id'] . '"';
			if ( ! empty( $this->form['class']) ) 
				$html .= ' class="'. $this->form['class'] . '"';
			if ( $this->form['novalidate'] ) 
				$html .= ' novalidate';
			$html .= '>';
		}
        foreach ( $this->elements as $element ) {
            switch ($element[0]) {
				case 'header': $html.=$this->header($element[1]); break;
				case 'input': $html.=$this->input($element[1]); break;
				case 'textarea': $html.=$this->textarea($element[1]); break;
				case 'select': $html.=$this->select($element[1]); break;
				case 'button': $html.=$this->button($element[1]); break;
				case 'img': $html.=$this->img($element[1]); break;
            }
		}
        if ( $this->form['form_element'] )
            $html .= '</form>';
        if (!empty($this->div))
            $html = '<div class="'.$this->div.'">'.$html.'</div>';
        return $html;
    }
    function input($arg){
        $html  = '';
        $label = '';
        if ( !empty($arg['label']) ) {
            $lbl_arg = $arg['label'];
            $label = '<label class="'.$lbl_arg['class'].'"';
            $label.= isset($arg['id']) ? ' for="'.$arg['id'].'"' : ' for="'.$arg['name'].'"';
            $label.= '>'.$lbl_arg['value'].'</label> ';
        }
        $input='<input ';
        if(isset($arg['type']))
            $input.= ' type="'.$arg['type'].'"';
        if(isset($arg['class']))
            $input.= ' class="'.$arg['class'] . '"';
        $input.= ' value="'.$arg['value'].'"';
        if(isset($arg['placeholder']))
            $input.= ' placeholder="'.$arg['placeholder'].'"';
        if(isset($arg['name']))
            $input.= ' name="'.$arg['name'].'"';
        if(isset($arg['id']))
            $input.= ' id="'.$arg['id'].'"';
        else
            $input.= ' id="'.$arg['name'].'"';
        $input.= $arg['checked'] ? ' checked' : '';
        $input.= $arg['disabled'] ? ' disabled' : '';
        $input.= $arg['readonly'] ? ' readonly' : '';
        $input.= $arg['required'] ? ' required' : '';
        $input.= '>';
        if (!empty( $arg['wrap_tag'])){
            $wrap_arg = $arg['wrap_tag'];
            $wrap = '<div ';
            $wrap.= ' class="'.$wrap_arg['class'].'">';
            if($arg['type']=='checkbox')
                $html.=$wrap.$input.$label.'</div>';
            else
                $html.=$wrap.$label.$input.'</div>';
        }else{
            if($arg['type']=='checkbox')
                $html.=$input.$label;
            else
                $html.=$label.$input;
        }
        return $html;
    }
    function textarea($arg){
        $html  = '';
        $label = '';
        if ( !empty( $arg['label'] ) ) {
            $lbl_arg = $arg['label'];
            $label = '<label class="'.$lbl_arg['class'].'"';
            $label.= isset($arg['id']) ? ' for="'.$arg['id'].'"' : ' for="'.$arg['name'].'"';
            $label.= '>'.$lbl_arg['value'].'</label> ';
        }
        $textarea='<textarea ';
        if(isset($arg['rows']))
            $textarea.= ' rows="'.$arg['rows'].'"';
        if(isset($arg['class']))
            $textarea.= ' class="'.$arg['class'] . '"';
        if(isset($arg['placeholder']))
            $textarea.= ' placeholder="'.$arg['placeholder'].'"';
        if(isset($arg['name']))
            $textarea.= ' name="'.$arg['name'].'"';
        if($arg['id'])
            $textarea.= ' id="'.$arg['id'].'"';
        else
            $textarea.= ' id="'.$arg['name'].'"';
        $textarea.= $arg['checked'] ? ' checked' : '';
        $textarea.= $arg['disabled'] ? ' disabled' : '';
        $textarea.= $arg['readonly'] ? ' readonly' : '';
        $textarea.= $arg['required'] ? ' required' : '';
        $textarea.= '>';
        $textarea.= $arg['value'];
        $textarea.= '</textarea>';
        if (!empty( $arg['wrap_tag'])){
            $wrap_arg = $arg['wrap_tag'];
            $wrap = '<div ';
            $wrap.= ' class="'.$wrap_arg['class'].'">';
            if($arg['type']=='checkbox')
                $html.=$wrap.$textarea.$label.'</div>';
            else
                $html.=$wrap.$label.$textarea.'</div>';
        }else{
            if($arg['type']=='checkbox')
                $html.=$textarea.$label;
            else
                $html.=$label.$textarea;
        }
        return $html;
    }
    function select($arg){
        $html  = '';
        $label = '';
        $options = $arg['options'];
        if ( !empty( $arg['label'] ) ) {
            $lbl_arg = $arg['label'];
            $label = '<label class="'.$lbl_arg['class'].'"';
            $label.= $arg['id'] ? ' for="'.$arg['id'].'"' : ' for="'.$arg['name'].'"';
            $label.= ' >'.$lbl_arg['value'].'</label>';
        }
        $select='<select class="'.$arg['class'].'"';
        $select.= ' name="' . $arg['name'] . '"';
        $select.= $arg['id'] ? ' id="'.$arg['id'].'"' : ' id="'.$arg['name'].'"';
        $select.= $arg['disabled'] ? ' disabled' : '';
        $select.= $arg['readonly'] ? ' readonly' : '';
        $select.= '>';
        foreach($options as $value => $description ):
            $selected = $value==$arg['option'] ? 'selected="selected"' : '';
            $select.= '<option value="'.$value.'" '.$selected.'>'.$description.'</option>';
        endforeach;
        $select.= '</select>';
        if (!empty( $arg['wrap_tag'])){
            $wrap_arg = $arg['wrap_tag'];
            $wrap = '<div ';
            $wrap.= ' class="'.$wrap_arg['class'].'">';
            $html.=$wrap.$label.$select.'</div>';
        }else{
            $html.=$label.$select;
        }
        return $html;
    }
    function header($arg){
        $html  = '';
        $h1='<h1 class="'.$arg['class'].'">';
        $h1.= $arg['value'].'</h1>';
        if (!empty( $arg['wrap_tag'])){
            $wrap_arg = $arg['wrap_tag'];
            $wrap = '<div ';
            $wrap.= ' class="'.$wrap_arg['class'].'">';
            $html.=$wrap.$h1.'</div>';
        }else{
            $html.=$h1;
        }
        return $html;
    }
    function button($arg){
        $html  = '';
        $button='<button class="'.$arg['class'].'"';
        $button.= ' type="' . $arg['type'].'"';
        $button.= ' onclick="' . $arg['onclick'].'"';
        $button.= '>'.$arg['value'].'</button>';
        if (!empty( $arg['wrap_tag'])){
            $wrap_arg = $arg['wrap_tag'];
            $wrap = '<div ';
            $wrap.= ' class="'.$wrap_arg['class'].'">';
            $html.=$wrap.$button.'</div>';
        }else{
            $html.=$button;
        }
        return $html;
    }
    function img($arg){
        $html  = '';
        $img='<img class="'.$arg['class'].'" ';
        $img.= ' src="'.$arg['src'].'" ';
        $img.= ' style="'.$arg['style'].'" ';
        $img.= ' alt="'.$arg['alt'].'" >';

        if (!empty( $arg['wrap_tag'])){
            $wrap_arg = $arg['wrap_tag'];
            $wrap = '<div ';
            $wrap.= ' class="'.$wrap_arg['class'].'">';
            $html.=$wrap.$img.'</div>';
        }else{
            $html.=$img;
        }
        return $html;
    }

}