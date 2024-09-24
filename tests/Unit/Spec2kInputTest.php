<?php

namespace Tests\Unit;

use App\Notification;
use Tests\TestCase;
use App\Spec2kInput;
use App\PieceParts\WPS_Segment;
use App\ShopFindings\API_Segment;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\SAS_Segment;
use Illuminate\Foundation\Testing\WithFaker;
use App\Codes\PrimaryPiecePartFailureIndicator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Spec2kInputTest extends TestCase
{
    /**
     * Test the text input.
     *
     * @return void
     */
    public function testTextInput()
    {
        $segment = new EID_Segment;
        
        $textInput = factory(Spec2kInput::class)->states('text')->make();
        
        $this->assertEquals($textInput->get_key(), 'EMS');
        $this->assertEquals($textInput->get_title(), 'Engine/APU Module Serial Number');
        $this->assertEquals($textInput->get_required(), false);
        $this->assertEquals($textInput->get_input_type(), 'text');
        $this->assertEquals($textInput->get_data_type(), 'string');
        $this->assertEquals($textInput->get_min(), 1);
        $this->assertEquals($textInput->get_max(), 20);
        $this->assertEquals($textInput->get_placeholder(), '');
        $this->assertEquals($textInput->get_function(), 'get_EID_EMS');
        $this->assertEquals($textInput->get_input_width(), 'col-sm-6 col-md-4');
        $this->assertEquals($textInput->get_order(), 30);
        $this->assertEquals($textInput->get_default(), null);
        
        // Test the input name.
        $this->assertNotEquals(strpos($textInput->render($segment), 'name="EMS"'), false);
        
        // Test value is empty.
        $this->assertNotEquals(strpos($textInput->render($segment), 'value=""'), false);
        
        // Test old value is filled.
        $this->assertNotEquals(strpos($textInput->render($segment, 'old input'), 'value="old input"'), false);
        
        // Make input with default value.
        $textInputWithDefault = factory(Spec2kInput::class)->states('text')->make(['default' => 'test default value']);
        
        // Test value is default.
        $this->assertNotEquals(strpos($textInputWithDefault->render($segment), 'value="test default value"'), false);
        
        $segment = new EID_Segment;
        
        $textInput = factory(Spec2kInput::class)->states('text')->make();
        
        $this->assertEquals($textInput->get_key(), 'EMS');
        $this->assertEquals($textInput->get_title(), 'Engine/APU Module Serial Number');
        $this->assertEquals($textInput->get_required(), false);
        $this->assertEquals($textInput->get_input_type(), 'text');
        $this->assertEquals($textInput->get_data_type(), 'string');
        $this->assertEquals($textInput->get_min(), 1);
        $this->assertEquals($textInput->get_max(), 20);
        $this->assertEquals($textInput->get_placeholder(), '');
        $this->assertEquals($textInput->get_function(), 'get_EID_EMS');
        $this->assertEquals($textInput->get_input_width(), 'col-sm-6 col-md-4');
        $this->assertEquals($textInput->get_order(), 30);
        $this->assertEquals($textInput->get_default(), null);
        
        // Test the input name.
        $this->assertNotEquals(strpos($textInput->render($segment), 'name="EMS"'), false);
        
        // Test value is empty.
        $this->assertNotEquals(strpos($textInput->render($segment), 'value=""'), false);
        
        // Test old value is filled.
        $this->assertNotEquals(strpos($textInput->render($segment, 'old input'), 'value="old input"'), false);
        
        // Make input with default value.
        $textInputWithDefault = factory(Spec2kInput::class)->states('text')->make(['default' => 'test default value']);
        
        // Test value is default.
        $this->assertNotEquals(strpos($textInputWithDefault->render($segment), 'value="test default value"'), false);
    }
    
    /**
     * Test the textarea input.
     *
     * @return void
     */
    public function testTextareaInput()
    {
        $segment = new RCS_Segment;
        
        $input = factory(Spec2kInput::class)->states('textarea')->make();
        
        $this->assertEquals($input->get_key(), 'PML');
        $this->assertEquals($input->get_title(), 'Part Modification Level');
        $this->assertEquals($input->get_required(), false);
        $this->assertEquals($input->get_input_type(), 'textarea');
        $this->assertEquals($input->get_data_type(), 'string');
        $this->assertEquals($input->get_min(), 1);
        $this->assertEquals($input->get_max(), 1000);
        $this->assertEquals($input->get_function(), 'get_RCS_PML');
        $this->assertEquals($input->get_input_width(), 'col-sm-12 col-md-12');
        $this->assertEquals($input->get_order(), 300);
        $this->assertEquals($input->get_default(), null);
        
        // Test the input name.
        $this->assertNotEquals(strpos($input->render($segment), 'name="PML"'), false);
        
        // Test value is empty.
        $this->assertNotEquals(strpos($input->render($segment), '></textarea>'), false);
        
        // Test old value is filled.
        $this->assertNotEquals(strpos($input->render($segment, 'old input'), '>old input</textarea>'), false);
        
        // Make input with default value.
        $inputWithDefault = factory(Spec2kInput::class)->states('textarea')->make(['default' => 'test default value']);
        
        // Test value is default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment), '>test default value</textarea>'), false);
    }
    
    /**
     * Test the select input.
     *
     * @return void
     */
    public function testSelectInput()
    {
        $segment = new WPS_Segment;
        
        $options = PrimaryPiecePartFailureIndicator::getDropDownValues();
        
        $input = factory(Spec2kInput::class)->states('select')->make(['default' => null, 'options' => $options]);
        
        $this->assertEquals($input->get_key(), 'PFC');
        $this->assertEquals($input->get_title(), 'Primary Piece Part Failure Indicator');
        $this->assertEquals($input->get_required(), true);
        $this->assertEquals($input->get_input_type(), 'select');
        $this->assertEquals($input->get_data_type(), 'string');
        $this->assertEquals($input->get_options(), $options);
        $this->assertEquals($input->get_function(), 'get_WPS_PFC');
        $this->assertEquals($input->get_input_width(), 'col-sm-6 col-md-6');
        $this->assertEquals($input->get_order(), 30);
        $this->assertEquals($input->get_display(), true);
        $this->assertEquals($input->get_default(), null);
        
        // Test the input name.
        $this->assertNotEquals(strpos($input->render($segment), 'name="PFC"'), false);
        
        // Test value is empty.
        $this->assertNotEquals(strpos($input->render($segment), '<option value="" selected>Please select...</option>'), false);
        
        // Test old value is filled.
        $this->assertNotEquals(strpos($input->render($segment, 'N'), '<option value="N" selected>N - No</option>'), false);
        
        // Make input with default value.
        $inputWithDefault = factory(Spec2kInput::class)->states('select')->make(['default' => 'D']);
        
        $this->assertEquals($inputWithDefault->get_default(), 'D');
        
        // Test value is default. WHY IS THIS NOT FAILING????
        $this->assertNotEquals(strpos($inputWithDefault->render($segment), '<option value="D" selected>D - Does Not Apply</option>'), false);
        
        // Test value is displayed instead of default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment, 'Y'), '<option value="Y" selected>Y - Yes</option>'), false);
    }
    
    /**
     * Test number input.
     *
     * @return void
     */
    public function testNumberInput()
    {
        $segment = new API_Segment;
        
        $input = factory(Spec2kInput::class)->states('number')->make();
        
        $this->assertEquals($input->get_key(), 'ATC');
        $this->assertEquals($input->get_title(), 'APU Cumulative Total Cycles');
        $this->assertEquals($input->get_required(), false);
        $this->assertEquals($input->get_input_type(), 'number');
        $this->assertEquals($input->get_data_type(), 'integer');
        $this->assertEquals($input->get_min(), 1);
        $this->assertEquals($input->get_function(), 'get_API_ATC');
        $this->assertEquals($input->get_input_width(), 'col-sm-6 col-md-4');
        $this->assertEquals($input->get_order(), 60);
        $this->assertEquals($input->get_display(), false);
        $this->assertEquals($input->get_default(), null);
        
        // Test the input type.
        $this->assertNotEquals(strpos($input->render($segment), 'type="number"'), false);
        
        // Test the input name.
        $this->assertNotEquals(strpos($input->render($segment), 'name="ATC"'), false);
        
        // Test value is empty.
        $this->assertNotEquals(strpos($input->render($segment), 'value=""'), false);
        
        // Test old value is filled.
        $this->assertNotEquals(strpos($input->render($segment, 100), 'value="100"'), false);
        
        // Make input with default value.
        $inputWithDefault = factory(Spec2kInput::class)->states('number')->make(['default' => 10]);
        
        // Test value is default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment), 'value="10"'), false);
        
        // Test value is displayed instead of default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment, 20), 'value="20"'), false);
        
        $attributes = [
            'key'           => 'ATH',
            'title'         => 'APU Cumulative Total Hours',
            'required'      => false,
            'input_type'    => 'number',
            'data_type'     => 'float',
            'min'           => 0,
            'step'          => 0.01,
            'function'      => 'get_API_ATH',
            'input_width'   => 'col-sm-6 col-md-4',
            'order'         => 50,
            'description'   => 'Use period for decimal point.'
        ];
        
        $float = factory(Spec2kInput::class)->states('number')->make($attributes);
        
        $this->assertEquals($float->get_key(), $attributes['key']);
        $this->assertEquals($float->get_title(), $attributes['title']);
        $this->assertEquals($float->get_required(), $attributes['required']);
        $this->assertEquals($float->get_input_type(), $attributes['input_type']);
        $this->assertEquals($float->get_data_type(), $attributes['data_type']);
        $this->assertEquals($float->get_min(), $attributes['min']);
        $this->assertEquals($float->get_function(), $attributes['function']);
        $this->assertEquals($float->get_input_width(), $attributes['input_width']);
        $this->assertEquals($float->get_order(), $attributes['order']);
        $this->assertEquals($float->get_display(), false);
        $this->assertEquals($float->get_default(), null);
        
        // Test step is displayed correctly.
        $this->assertNotEquals(strpos($float->render($segment), 'step="0.01"'), false);
    }
    
    /**
     * Test hidden input.
     *
     * @return void
     */
    public function testHiddenInput()
    {
        $segment = new WPS_Segment;
        
        $input = factory(Spec2kInput::class)->states('hidden')->make();
        
        $this->assertEquals($input->get_key(), 'PPI');
        $this->assertEquals($input->get_title(), 'Piece Part Record Identifier');
        $this->assertEquals($input->get_required(), true);
        $this->assertEquals($input->get_input_type(), 'hidden');
        $this->assertEquals($input->get_data_type(), 'string');
        $this->assertEquals($input->get_min(), 1);
        $this->assertEquals($input->get_max(), 50);
        $this->assertEquals($input->get_function(), 'get_WPS_PPI');
        $this->assertEquals($input->get_input_width(), 'col-sm-6 col-md-4');
        $this->assertEquals($input->get_order(), 20);
        $this->assertEquals($input->get_display(), true);
        $this->assertEquals($input->get_default(), null);
        
        // Test the input type.
        $this->assertNotEquals(strpos($input->render($segment), 'type="hidden"'), false);
        
        // Test the input name.
        $this->assertNotEquals(strpos($input->render($segment), 'name="PPI"'), false);
        
        // Test old value is filled.
        $this->assertNotEquals(strpos($input->render($segment, '123abc'), 'value="123abc"'), false);
        
        // Test value is empty.
        $this->assertNotEquals(strpos($input->render($segment), 'value=""'), false);
        
        // Make input with default value.
        $inputWithDefault = factory(Spec2kInput::class)->states('hidden')->make(['default' => '123abc']);
        
        // Test value is default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment), 'value="123abc"'), false);
        
        // Test value is displayed instead of default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment, '321cba'), 'value="321cba"'), false);
    }
    
    /**
     * Test date input.
     *
     * @return void
     */
    public function testDateInput()
    {
        $segment = new WPS_Segment;
        
        $input = factory(Spec2kInput::class)->states('date')->make();
        
        $this->assertEquals($input->get_key(), 'MRD');
        $this->assertEquals($input->get_title(), 'Material Receipt Date');
        $this->assertEquals($input->get_required(), false);
        $this->assertEquals($input->get_input_type(), 'date');
        $this->assertEquals($input->get_data_type(), 'string');
        $this->assertEquals($input->get_function(), 'get_WPS_MRD');
        $this->assertEquals($input->get_input_width(), 'col-sm-6 col-md-4');
        $this->assertEquals($input->get_order(), 140);
        $this->assertEquals($input->get_display(), false);
        $this->assertEquals($input->get_default(), null);
        
        // Test the input type.
        $this->assertNotEquals(strpos($input->render($segment), 'type="text"'), false);
        
        // Check the datepicker class is rendered.
        $this->assertNotEquals(strpos($input->render($segment), 'class="datepicker '), false);
        
        // Test the input name.
        $this->assertNotEquals(strpos($input->render($segment), 'name="MRD"'), false);
        
        // Test value is empty.
        $this->assertNotEquals(strpos($input->render($segment), 'value=""'), false);
        
        // Test old value is filled.
        $this->assertNotEquals(strpos($input->render($segment, '17/02/1975'), 'value="17/02/1975"'), false);
        
        // Make input with default value.
        $inputWithDefault = factory(Spec2kInput::class)->states('date')->make(['default' => '31/01/2001']);
        
        // Test value is default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment), 'value="31/01/2001"'), false);
        
        // Test value is displayed instead of default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment, '25/02/2015'), 'value="25/02/2015"'), false);
    }
    
    /**
     * Test radio input.
     *
     * @return void
     */
    public function testRadioInput()
    {
        $segment = new SAS_Segment;
        
        $input = factory(Spec2kInput::class)->states('radio')->make();
        
        $this->assertEquals($input->get_key(), 'RFI');
        $this->assertEquals($input->get_title(), 'Repair Final Action Indicator');
        $this->assertEquals($input->get_required(), true);
        $this->assertEquals($input->get_input_type(), 'radio');
        $this->assertEquals($input->get_data_type(), 'boolean');
        $this->assertEquals($input->get_function(), 'get_SAS_RFI');
        $this->assertEquals($input->get_input_width(), 'col-sm-6 col-md-6');
        $this->assertEquals($input->get_order(), 60);
        $this->assertEquals($input->get_display(), true);
        $this->assertEquals($input->get_default(), null);
        
        // Test the input type.
        $this->assertNotEquals(strpos($input->render($segment), 'type="radio"'), false);
        
        // Test the input name.
        $this->assertNotEquals(strpos($input->render($segment), 'name="RFI"'), false);
        
        // Test value is empty.
        $this->assertNotEquals(strpos($input->render($segment), 'checked'), true);
        
        // Test old value is filled.
        $this->assertNotEquals(strpos($input->render($segment, 0), 'value="0" checked'), false);
        
        $segment->RFI = 0;
        
        // Check 'No' is checked.
        $this->assertNotEquals(strpos($input->render($segment), 'value="0" checked'), false);
        
        $segment->RFI = NULL;
        
        // Make input with default value.
        $inputWithDefault = factory(Spec2kInput::class)->states('radio')->make(['default' => 1]);
        
        // Test value is default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment), 'value="1" checked'), false);
        
        // Test value is displayed instead of default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment, 0), 'value="0" checked'), false);
    }
    
    /**
     * Test checkbox input.
     *
     * @return void
     */
    public function testCheckboxInput()
    {
        $segment = new SAS_Segment;
        
        $input = factory(Spec2kInput::class)->states('checkbox')->make();
        
        $this->assertEquals($input->get_key(), 'RFI');
        $this->assertEquals($input->get_title(), 'Repair Final Action Indicator');
        $this->assertEquals($input->get_required(), true);
        $this->assertEquals($input->get_input_type(), 'checkbox');
        $this->assertEquals($input->get_data_type(), 'boolean');
        $this->assertEquals($input->get_function(), 'get_SAS_RFI');
        $this->assertEquals($input->get_input_width(), 'col-sm-6 col-md-6');
        $this->assertEquals($input->get_order(), 60);
        $this->assertEquals($input->get_display(), true);
        $this->assertEquals($input->get_default(), null);
        
        // Test the input type.
        $this->assertNotEquals(strpos($input->render($segment), 'type="checkbox"'), false);
        
        // Test the input name.
        $this->assertNotEquals(strpos($input->render($segment), 'name="RFI"'), false);
        
        // Test value is empty.
        $this->assertNotEquals(strpos($input->render($segment), 'checked'), true);
        
        // Test old value is filled.
        $this->assertNotEquals(strpos($input->render($segment, 1), 'value="1" checked'), false);
        
        // Make input with default value.
        $inputWithDefault = factory(Spec2kInput::class)->states('checkbox')->make(['default' => 1]);
        
        // Test default value is checked.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment), 'value="1" checked'), false);
    }
    
    /**
     * Test the radio inputs on the piece part list page.
     *
     * @return void
     */
    public function testPiecePartListRadioInput()
    {
        $segment = new WPS_Segment;
        
        $input = factory(Spec2kInput::class)->states('radio-pp')->make(['default' => null]);
        
        $this->assertEquals($input->get_key(), 'PFC');
        $this->assertEquals($input->get_title(), 'Primary Piece Part Failure Indicator');
        $this->assertEquals($input->get_required(), true);
        $this->assertEquals($input->get_input_type(), 'radio-pp');
        $this->assertEquals($input->get_data_type(), 'string');
        $this->assertEquals($input->get_function(), 'get_WPS_PFC');
        $this->assertEquals($input->get_input_width(), 'col-sm-6 col-md-6');
        $this->assertEquals($input->get_order(), 30);
        $this->assertEquals($input->get_display(), true);
        $this->assertEquals($input->get_default(), null);
        
        // Test the input type.
        $this->assertNotEquals(strpos($input->render($segment), 'type="radio"'), false);
        
        // Test the input name.
        $this->assertNotEquals(strpos($input->render($segment), 'name="PFC"'), false);
        
        // Test value is empty.
        $this->assertNotEquals(strpos($input->render($segment), 'checked'), true);
        
        // Test old value is filled.
        $this->assertNotEquals(strpos($input->render($segment, 'N'), 'value="N" checked'), false);
        
        // Make input with default value.
        $inputWithDefault = factory(Spec2kInput::class)->states('radio-pp')->make(['default' => 'Y']);
        
        $this->assertEquals($inputWithDefault->get_default(), 'Y');
        
        // Test value is default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment), 'value="Y" checked'), false);
        
        // Test value is displayed instead of default.
        $this->assertNotEquals(strpos($inputWithDefault->render($segment, 'D'), 'value="D" checked'), false);
    }
    
    /**
     * Test the input label.
     *
     * @return void
     */
    public function testLabel()
    {
        $input = factory(Spec2kInput::class)->states('text')->make();
        
        $string1 = '<label for="EMS" class="control-label">Engine/APU Module Serial Number</label>';
        
        $this->assertEquals($input->render_label(), $string1);
        
        $inputRequired = factory(Spec2kInput::class)->states('text')->make(['required' => true]);
        
        $string2 = '<label for="EMS" class="control-label">Engine/APU Module Serial Number <span class="required text-danger">*</span></label>';
        
        $this->assertEquals($inputRequired->render_label(), $string2);
    }
}