<?php
class NovaWorks_ThemeOptions_Model_Source_Slideshow_Effects
{
    public function toOptionArray()
    {
        return array(
					array('value' => 'random',			'label' => Mage::helper('themeoptions')->__('Random')),
					array('value' => 'sliceDown',			'label' => Mage::helper('themeoptions')->__('SliceDown')),
					array('value' => 'sliceDownLeft',			'label' => Mage::helper('themeoptions')->__('SliceDownLeft')),
					array('value' => 'sliceUpDown',			'label' => Mage::helper('themeoptions')->__('SliceUpDown')),
					array('value' => 'sliceUpDownLeft',			'label' => Mage::helper('themeoptions')->__('SliceUpDownLeft')),
					array('value' => 'fold',			'label' => Mage::helper('themeoptions')->__('Fold')),
					array('value' => 'fade',			'label' => Mage::helper('themeoptions')->__('Fade')),
					array('value' => 'slideInRight',			'label' => Mage::helper('themeoptions')->__('SlideInRight')),
					array('value' => 'slideInLeft',			'label' => Mage::helper('themeoptions')->__('SlideInLeft')),
					array('value' => 'boxRandom',			'label' => Mage::helper('themeoptions')->__('BoxRandom')),
					array('value' => 'boxRain',			'label' => Mage::helper('themeoptions')->__('BoxRain')),
					array('value' => 'boxRainReverse',			'label' => Mage::helper('themeoptions')->__('BoxRainReverse')),
					array('value' => 'boxRainGrow',			'label' => Mage::helper('themeoptions')->__('BoxRainGrow')),
					array('value' => 'boxRainGrowReverse',			'label' => Mage::helper('themeoptions')->__('BoxRainGrowReverse'))
        );
    }
}