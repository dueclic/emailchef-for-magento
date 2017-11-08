<?php

class Dueclic_Emailchef_Block_Adminhtml_System_Config_Edit extends
    Mage_Adminhtml_Block_System_Config_Edit
{

    protected function _prepareLayout()
    {

        parent::_prepareLayout();

        if ($this->getRequest()->getModuleName() == 'admin'
            && $this->getRequest()->getControllerName() == 'system_config'
            && $this->getRequest()->getActionName() == 'edit'
            && $this->getRequest()->getParam('section') == 'emailchef'
        ) {

            $this->setChild(
                'save_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(
                        array(
                            'label' => Mage::helper('adminhtml')->__(
                                'Save Config'
                            ),
                            'class' => 'save',
                            'id'    => 'emailchef_save_wizard',
                        )
                    )
            );

        } else {
            $this->setChild(
                'save_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(
                        array(
                            'label' => Mage::helper('adminhtml')->__(
                                'Save Config'
                            ),
                            'onclick' => 'configForm.submit()',
                            'class' => 'save',
                        )
                    )
            );
        }
    }

}
