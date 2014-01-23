<?php
/**
 *  New work center group entry form
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 21, 2012, 8:24:36 AM
 */
class Application_Form_WorkCenterGroup_Add extends Zend_Form
{
    //----------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {
        /**********************************************************************/
        // Group ID
        $groupID = new Zend_Form_Element_Text('groupID');
        $groupID->setAttrib('maxLength', 1)
                ->setDecorators(array(
                    'ViewHelper',
                    array('Label', 
                        array(
                            'placement' => 'prepend'
                    )),
                    array('Description', 
                        array(
                            'escape' => false, 
                            //'placement' => 'prepend'
                    )),
                    array('HtmlTag', 
                        array(
                            'tag' => 'div'
                    ))
                ))
                //->setDescription('A ltter to identify the group.')
                ->setLabel('Group ID');
        
        /**********************************************************************/
        // Description
        $description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 1)
                    ->setDecorators(array(
                        'ViewHelper',
                        array('Label', 
                            array(
                                'escape' => false
                            )),
                        array('Htmltag', 
                            array(
                                'tag' => 'div'
                            ))
                    ))
                    ->setLabel('Description');
        
        /**********************************************************************/
        // Work Centers
        $workCenters = new Zend_Form_Element_Text('workCenters');
        $workCenters->setDecorators(array('ViewHelper'))
                    ->setLabel('Work Centers');
        
        /**********************************************************************/
        // Staffing
        $staffing = new Zend_Form_Element_Text('staffing');
        $staffing->setAttrib('maxLength', 4)
                 ->setDecorators(array(
                    'ViewHelper',
                     array('Label',       array('escape' => false)),
                     array('Description', array('escape' => false)),
                     array('Htmltag',     array('tag' => 'div'))
                 ))
                 ->setLabel('Staffing');
        /**********************************************************************/
        
        // Place all the elements into one array
        $elements = array(
            $groupID,
            $description,
            $workCenters,
            $staffing
        );
        
        /**********************************************************************/
        
        // Merge all the elements into the form
        $this->addElements($elements);
        
        /**********************************************************************/
    }
    
    //----------------------------------------------------------------------
}
/* End of file Add.php */
/* Location: application/forms/WorkCenterGroup/Add.php */