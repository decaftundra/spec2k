<?php

namespace App;

use Illuminate\Support\Facades\Gate;
use App\Interfaces\SegmentInterface;
use App\Exceptions\DataTypeException;
use App\Exceptions\InputTypeException;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\RequiredAttributesException;

class Spec2kInput extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    /**
     * Allowed input types.
     *
     * @var array
     */
    protected static $inputTypes = [
        'text',
        'select',
        'textarea',
        'date',
        'number',
        'float',
        'hidden',
        'radio',
        'checkbox',
        'radio-pp'
    ];
    
    /**
     * Allowed data types.
     *
     * @var array
     */
    protected static $dataTypes = [
        'string',
        'boolean',
        'integer',
        'float'
    ];
    
    /**
     * The minimum required attributes for an input.
     *
     * @var array
     */
    protected static $requiredAttributes = [
        'title',
        'input_type',
        'function'
    ];
    
    /**
     * The input key.
     *
     * @var string
     */
    protected $key;
    
    /**
     * The input title.
     *
     * @var string
     */
    protected $title;
    
    /**
     * Is the input required.
     *
     * @var boolean
     */
    protected $required = false;
    
    /**
     * The type of input.
     *
     * @var string
     */
    protected $input_type;
    
    /**
     * The type of data the input contains.
     *
     * @var string
     */
    protected $data_type;
    
    /**
     * Minimum length of the input data.
     *
     * @var integer
     */
    protected $min = 0;
    
    /**
     * Maximum length of the input data.
     *
     * @var integer
     */
    protected $max = 255;
    
    /**
     * Options, normally used for radio buttons and selects.
     *
     * @var array
     */
    protected $options = null;
    
    /**
     * The function called to retrieve the input value.
     *
     * @var string
     */
    protected $function;
    
    /**
     * The bootstrap classes to define input width.
     *
     * @var string
     */
    protected $input_width = 'col-sm-12';
    
    /**
     * The order in which the input should be displayed.
     *
     * @var integer
     */
    protected $order = 0;
    
    /**
     * Whether the input should be displayed by default.
     *
     * @var boolean
     */
    protected $display = false;
    
    /**
     * The input placeholder text.
     *
     * @var string
     */
    protected $placeholder;
    
    /**
     * The input default value.
     *
     * @var mixed
     */
    protected $default = null;
    
    /**
     * The input description text.
     *
     * @var string
     */
    protected $description;
    
    /**
     * Is the input admin only.
     *
     * @var boolean
     */
    protected $admin_only = false;
    
    /**
     * Set the step value.
     *
     * @var integer
     */
    protected $step = 'any';
    
    /**
     * Input classes.
     *
     * @var array
     */
    protected $input_classes = ['form-control'];
    
    /**
     * Label classes.
     *
     * @var array
     */
    protected $label_classes = ['control-label'];
    
    /**
     * Set the attributes.
     *
     * @param (string) $key
     * @param (array) $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // Check for required values.
        foreach (self::$requiredAttributes as $attribute) {
            if (!array_key_exists($attribute, $attributes)) {
                $message = "The input with key: {$this->get_key()} does not have all the required attributes. the $attribute attribute is missing.";
                throw new RequiredAttributesException($message);
            }
        }
        
        // Set the attributes.
        foreach ($attributes as $attribute => $value) {
            $method = 'set_' . $attribute;
            $this->$method($value);
        }
    }
    
    /**
     * Convert standard array to array of form objects.
     *
     * @param (array) $array
     * @return array
     */
    public static function convert(array $array)
    {
        $inputs = [];
        
        foreach ($array as $key => $attributes) {
            $attributes['key'] = $key; // Add key to attributes.
            
            $inputs[$key] = new self($attributes);
        }
        
        return $inputs;
    }
    
    /**
     * Set the input key.
     *
     * @return void
     */
    protected function set_key(string $value)
    {
        $this->key = $value;
    }
    
    /**
     * Set the input title.
     *
     * @return void
     */
    protected function set_title(string $value)
    {
        $this->title = $value;
    }
    
    /**
     * Set the required attribute.
     *
     * @return void
     */
    protected function set_required(bool $value)
    {
        $this->required = $value;
    }
    
    /**
     * Set the input_type attribute.
     *
     * @return void
     */
    protected function set_input_type(string $value)
    {
        if (!in_array($value, self::$inputTypes)) {
            throw new InputTypeException("The input_type value for key: {$this->get_key()} is invalid. Value given: $value.");
        }
        
        $this->input_type = $value;
    }
    
    /**
     * Set the data_type attribute.
     *
     * @return void
     */
    protected function set_data_type(string $value)
    {
        if (!in_array($value, self::$dataTypes)) {
            throw new DataTypeException("The data_type value for key: {$this->get_key()} is invalid. Value given: $value.");
        }
        
        $this->data_type = $value;
    }
    
    /**
     * Set the min attribute.
     *
     * @return void
     */
    protected function set_min(int $value)
    {
        $this->min = $value;
    }
    
    /**
     * Maximum length of the input data.
     *
     * @return void
     */
    protected function set_max(int $value)
    {
        $this->max = $value;
    }
    
    /**
     * Set the options array attribute.
     *
     * @return void
     */
    protected function set_options(array $value)
    {
        $this->options = $value;
    }
    
    /**
     * Set the function attribute.
     *
     * @return void
     */
    protected function set_function(string $value)
    {
        $this->function = $value;
    }
    
    /**
     * Set the input_width attribute.
     *
     * @return void
     */
    protected function set_input_width(string $value)
    {
        $this->input_width = $value;
    }
    
    /**
     * The order in which the input should be displayed.
     *
     * @return void
     */
    protected function set_order(int $value)
    {
        $this->order = $value;
    }
    
    /**
     * Whether the input should be displayed.
     *
     * @return void
     */
    protected function set_display(bool $value)
    {
        $this->display = $value;
    }
    
    /**
     * Set the placeholder attribute.
     *
     * @return void
     */
    protected function set_placeholder(string $value)
    {
        $this->placeholder = $value;
    }
    
    /**
     * Set the default attribute.
     *
     * @return void
     */
    protected function set_default($value)
    {
        $this->default = $value;
    }
    
    /**
     * Set the description attribute.
     *
     * @return void
     */
    protected function set_description(string $value)
    {
        $this->description = $value;
    }
    
    /**
     * Set the admin_only attribute.
     *
     * @return void
     */
    protected function set_admin_only(bool $value)
    {
        $this->admin_only = $value;
    }
    
    /**
     * Set the step attribute.
     *
     * @return void
     */
    protected function set_step(float $value)
    {
        $this->step = $value;
    }
    
    /**
     * Set the input_classes attribute.
     *
     * @return void
     */
    protected function set_input_classes(array $value)
    {
        $this->input_classes = $value;
    }
    
    /**
     * Set the label_classes attribute.
     *
     * @return void
     */
    protected function set_label_classes(array $value)
    {
        $this->label_classes = $value;
    }
    
    /**
     * Get the input key.
     *
     * @return string
     */
    public function get_key()
    {
        return (string) $this->key;
    }
    
    /**
     * Get the input title.
     *
     * @return string
     */
    public function get_title()
    {
        return (string) $this->title;
    }
    
    /**
     * Get the required attribute.
     *
     * @return boolean
     */
    public function get_required()
    {
        return (bool) $this->required;
    }
    
    /**
     * Get the type of input.
     *
     * @return string
     */
    public function get_input_type()
    {
        return (string) $this->input_type;
    }
    
    /**
     * Get the data_type attribute.
     *
     * @return string
     */
    public function get_data_type()
    {
        return (string) $this->data_type;
    }
    
    /**
     * Get the min attribute.
     *
     * @return integer
     */
    public function get_min()
    {
        return (int) $this->min;
    }
    
    /**
     * Get the max attribute.
     *
     * @return integer
     */
    public function get_max()
    {
        return (int) $this->max;
    }
    
    /**
     * Get the options attribute.
     *
     * @return array|null
     */
    public function get_options()
    {
        return is_array($this->options) ? (array) $this->options : null;
    }
    
    /**
     * Get the function attribute.
     *
     * @return string
     */
    public function get_function()
    {
        return (string) $this->function;
    }
    
    /**
     * Get the input_width attribute.
     *
     * @return string
     */
    public function get_input_width()
    {
        return (string) $this->input_width;
    }
    
    /**
     * Get the order attribute.
     *
     * @return integer
     */
    public function get_order()
    {
        return (int) $this->order;
    }
    
    /**
     * Get the display attribute.
     *
     * @return boolean
     */
    public function get_display()
    {
        return (bool) $this->display;
    }
    
    /**
     * Get the placeholder attribute.
     *
     * @return string
     */
    public function get_placeholder()
    {
        return (string) $this->placeholder;
    }
    
    /**
     * Get the default attribute.
     *
     * @return mixed
     */
    public function get_default()
    {
        return $this->default;
    }
    
    /**
     * Get the description attribute.
     *
     * @return string
     */
    public function get_description()
    {
        return (string) $this->description;
    }
    
    /**
     * Get the admin_only attribute.
     *
     * @return boolean
     */
    public function get_admin_only()
    {
        return (bool) $this->admin_only;
    }
    
    /**
     * Get the step attribute.
     *
     * @return integer
     */
    public function get_step()
    {
        return (float) $this->step;
    }
    
    /**
     * Get the input_classes attribute.
     *
     * @return array
     */
    public function get_input_classes()
    {
        return (array) $this->input_classes;
    }
    
    /**
     * Get the label_classes attribute.
     *
     * @return array
     */
    public function get_label_classes()
    {
        return (array) $this->label_classes;
    }
    
    /**
     * Is it a hidden input.
     *
     * @return boolean
     */
    public function is_hidden()
    {
        return $this->get_input_type() == 'hidden';
    }
    
    /**
     * Get the value of the input.
     *
     * @param \App\Interfaces\SegmentInterface $segment
     * @param (mixed) $old
     * @return string|null
     */
    public function get_value(SegmentInterface $segment, $old = false)
    {
        /*
        If form has been submitted with an empty value $old === null return submitted value
        
        If form has not been submitted $old === false return value if not empty or default value
        */
        
        if (is_null($old)) {
            return NULL;
        } else if (mb_strlen($old)) {
            // If old value is 0 return as a string.
            return $old ?: (string) $old;
        } else if (mb_strlen(call_user_func([$segment, $this->get_function()]))) {
            // If value is 0 return as a string.
            return call_user_func([$segment, $this->get_function()]) ?: (string) call_user_func([$segment, $this->get_function()]);
        }
        
        return (string) $this->get_default();
    }
    
    /**
     * Is the current user an admin user.
     *
     * @return boolean
     */
    private function isAdmin()
    {
        return auth()->check() && Gate::allows('edit-all-inputs');
    }
    
    /**
     * Output text input html.
     *
     * @param (mixed) $value
     * @return string $output
     */
    private function render_text_input($value = null)
    {
        $readOnly = !$this->isAdmin() && $this->get_admin_only() ? 'readonly' : '';
        
        $output = '<input id="'. $this->get_key() .'"
            type="text"
            class="' . implode(' ', $this->get_input_classes()) . '"
            name="'. $this->get_key() .'"
            autocomplete="off"
            maxlength="'. $this->get_max() .'"
            placeholder="'. $this->get_placeholder() .'"
            value="'. $value .'"
            aria-describedby="'. $this->get_key() .'-helpBlock" ' . $readOnly . '>';
        
        $output .= '<div id="wordcount-feedback-'. $this->get_key() .'" class="wordcount"></div>';
        
        if ($this->get_description()) {
            $output .= '<span id="' . $this->get_key() . '-helpBlock" class="help-block"><small>' . $this->get_description() . '</small></span>';
        }
        
        return $output;
    }
    
    /**
     * Output textarea input.
     *
     * @param (mixed) $value
     * @return string $output
     */
    private function render_textarea_input($value = null)
    {
        $readOnly = !$this->isAdmin() && $this->get_admin_only() ? 'readonly' : '';

        $output = '<textarea style="min-width:100%;max-width:100%;" id="' . $this->get_key() . '"
            class="' . implode(' ', $this->get_input_classes()) . '"
            name="'. $this->get_key() . '" rows="3"
            maxlength="' . $this->get_max() .'"
            placeholder="' . $this->get_placeholder() . '"
            aria-describedby="' . $this->get_key() . '-helpBlock" ' . $readOnly . '>'. $value .'</textarea>';
                  
        $output .= '<div id="wordcount-feedback-'. $this->get_key() .'" class="wordcount"></div>';

        if ($this->get_description()) {
            $output .= '<span id="' . $this->get_key() . '-helpBlock" class="help-block"><small>' . $this->get_description() . '</small></span>';
        }
        
        return $output;
    }
    
    /**
     * Output select input.
     *
     * @param (mixed) $value
     * @return string $output
     */
    private function render_select_input($value = null)
    {
        $adminOnly = !$this->isAdmin() && $this->get_admin_only();
        $disabled = $adminOnly ? 'disabled' : '';

        $output = '';
        
        if ($adminOnly) {
            $output .= '<input id="' . $this->get_key() . '"
               type="hidden"
               name="'. $this->get_key() . '"
               maxlength="'. $this->get_max() . '"
               value="' . $value . '">';
        }

        $output .= '<select id="' . $this->get_key() .'"
            class="' . implode(' ', $this->get_input_classes()) . '"
            name="'. $this->get_key() .'"
            aria-describedby="'. $this->get_key() .'-helpBlock"' . $disabled . '>';
            
    	foreach ($this->get_options() as $key => $val) {
        	$selected = $value == $key ? 'selected' : '';
        	$output .= '<option value="' . $key . '" ' . $selected . '>' . $val . '</option>';
    	}
            
        $output .= '</select>';

        if ($this->get_description()) {
            $output .= '<span id="' . $this->get_key() . '-helpBlock" class="help-block"><small>' . $this->get_description() . '</small></span>';
        }
        
        return $output;
    }
    
    /**
     * Output array of radio button inputs.
     *
     * @param (mixed) $value
     * @return string $output
     */
    private function render_radio_input($value = null)
    {
        $adminOnly = !$this->isAdmin() && $this->get_admin_only();
        $readOnly = $adminOnly ? 'readonly' : '';
        
        $count = 0;

        $output = '<br>';
        
        foreach ($this->get_options() as $k => $v) {
            $checked = (string) $value === (string) $k ? 'checked' : '';
            $count++;
            $output .= '<label class="radio-inline">
                <input type="radio"
                name="' . $this->get_key() . '"
                id="' . $this->get_key() . '-' .$count . '"
                value="'. $k . '" ' . $checked . ' ' . $readOnly . '>';
                
            $output .= $v . '</label>';
        }

        if ($this->get_description()) {
            $output .= '<span id="' . $this->get_key() . '-helpBlock" class="help-block"><small>' . $this->get_description() . '</small></span>';
        }
        
        return $output;
    }
    
    /**
     * Output number input.
     *
     * @param (mixed) $value
     * @return string $output
     */
    private function render_number_input($value = null)
    {
        $readOnly = !$this->isAdmin() && $this->get_admin_only() ? 'readonly' : '';
        $step = $this->get_data_type() == 'float' ? $this->get_step() : '';

        // We ignore the max value for this input, it is dealt with via server side validation.
        $output = '<input id="' . $this->get_key() . '"
            type="number"
            class="' . implode(' ', $this->get_input_classes()) . '"
            name="' . $this->get_key() . '"
            placeholder="'. $this->get_placeholder() . '"
            value="' . $value . '"
            step="' . $step . '"
            min="' . $this->get_min() . '"
            aria-describedby="' . $this->get_key() . '-helpBlock" ' . $readOnly . '>';
        
        if ($this->get_description()) {
            $output .= '<span id="' . $this->get_key() . '-helpBlock" class="help-block"><small>' . $this->get_description() . '</small></span>';
        }
        
        return $output;
    }
    
    /**
     * Output hidden input.
     *
     * @param (mixed) $value
     * @return string $output
     */
    private function render_hidden_input($value = null)
    {
        $output = '<input id="' . $this->get_key() . '"
            type="hidden"
            name="' . $this->get_key() . '"
            maxlength="'. $this->get_max() . '"
            placeholder="'. $this->get_placeholder() . '"
            value="' . $value . '">';
        
        return $output;
    }
    
    /**
     * Output date input.
     *
     * @param (mixed) $value
     * @return string $output
     */
    private function render_date_input($value = null)
    {
        $adminOnly = !$this->isAdmin() && $this->get_admin_only();
        $readOnly = $adminOnly ? 'readonly' : '';
        $datepickerClass = $adminOnly ? '' : 'datepicker';

        $output = '<div class="input-group">
            <div class="input-group-addon">
                <i class="fas fa-calendar-alt"></i>
            </div>';
            
        $output .= '<input id="' . $this->get_key() . '"
            autocomplete="off" type="text"
            class="' . $datepickerClass . ' ' . implode(' ', $this->get_input_classes()) . '"
            name="' . $this->get_key() . '"
            maxlength="'. $this->get_max() . '"
            placeholder="'. $this->get_placeholder() . '"
            value="' . $value . '"
            aria-describedby="' . $this->get_key() . '-helpBlock"' . $readOnly . '>';
        
        $output .= '</div>';

        if ($this->get_description()) {
            $output .= '<span id="' . $this->get_key() . '-helpBlock" class="help-block"><small>' . $this->get_description() . '</small></span>';
        }
        
        return $output;
    }
    
    /**
     * Output single checkbox input.
     *
     * @param (mixed) $value
     * @return string $output
     */
    private function render_checkbox_input($value = null)
    {
        $adminOnly = !$this->isAdmin() && $this->get_admin_only();
        $readOnly = $adminOnly ? 'readonly' : '';
        $checked = $value ? 'checked' : '';

        $output = '<div class="checkbox"><label>';
        
        $output .= '<input type="checkbox"
            id="' . $this->get_key() . '"
            name="' . $this->get_key() . '"
            value="1" ' . $checked . '
            aria-describedby="' . $this->get_key() . '-helpBlock" ' . $readOnly . '> ' . $this->get_title();
        
        $output .= '</label></div>';
        
        if ($this->get_description()) {
            $output .= '<span id="' . $this->get_key() . '-helpBlock" class="help-block"><small>' . $this->get_description() . '</small></span>';
        }
        
        return $output;
    }
    
    /**
     * Output array of radio button inputs without names for piece parts index.
     *
     * @param (mixed) $value
     * @return string $output
     */
    private function render_radio_pp_input($value = null)
    {
        $count = 0;
        $output = '';
        
        foreach ($this->get_options() as $k => $v) {
            if ($k) {
                $checked = (string) $value === (string) $k ? 'checked' : '';
                $count++;
                $output .= '<label class="radio-inline">
                    <input type="radio"
                    name="' . $this->get_key() . '"
                    id="' . $this->get_key() . '-' .$count . '"
                    value="'. $k . '" ' . $checked . '>&nbsp;</label>';
            }
        }
        
        return $output;
    }
    
    /**
     * Output the input label.
     *
     * @param (type) $name
     * @return
     */
    public function render_label()
    {
        $required = $this->get_required() ? ' <span class="required text-danger">*</span>' : '';
        
        $output = '<label for="' . $this->get_key() . '" class="' . implode(' ', $this->get_label_classes()) . '">';
        $output .= $this->get_title() . $required;
        $output .= '</label>';
        
        return $output;
    }
    
    /**
     * Render the input.
     *
     * @param (mixed) $value
     * @return string
     */
    public function render(SegmentInterface $segment, $old = false)
    {
        $value = $this->get_value($segment, $old);
        
        switch ($this->get_input_type()) {
            case 'text':
                return $this->render_text_input($value);
                break;
            case 'textarea':
                return $this->render_textarea_input($value);
                break;
            case 'select':
                return $this->render_select_input($value);
                break;
            case 'radio':
                return $this->render_radio_input($value);
                break;
            case 'number':
                return $this->render_number_input($value);
                break;
            case 'hidden':
                return $this->render_hidden_input($value);
                break;
            case 'date':
                return $this->render_date_input($value);
                break;
            case 'checkbox':
                return $this->render_checkbox_input($value);
                break;
            case 'radio-pp':
                return $this->render_radio_pp_input($value);
                break;
            default:
                return $this->render_text_input($value);
                break;
        }
    }
}