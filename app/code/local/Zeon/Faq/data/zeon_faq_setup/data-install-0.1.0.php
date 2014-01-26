<?php
/**
 * Zeon Solutions,Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Zeon Solutions License
 * that is bundled with this package in the file LICENSE_ZE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.zeonsolutions.com/license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web,please send an email
 * to license@zeonsolutions.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.zeonsolutions.com for more information.
 *
 * @category    Zeon
 * @package     Zeon_Faq
 * @copyright   Copyright (c) 2012 Zeon Solutions,Inc. All Rights Reserved.(http://www.zeonsolutions.com)
 * @license     http://www.zeonsolutions.com/license/
 */

$faqsCategory = array(
    array(
        'title'         => 'Magento',
        'identifier'    => 'magento',
        'sort_order'        => 1,
        'status'        => 1,
        'creation_time' => now(),
        'update_time'   => now()
    )
);


$faqs = array(
    array(
        'title'              => 'What is Magento?',
        'category_id'        => 1,
        'status'             => 1,
        'is_most_frequently' => 1,
        'description'        => "Magento is a feature-rich eCommerce platform built on open-source technology that provides online merchants with unprecedented flexibility and control over the look, content and functionality of their eCommerce store. Magento’s intuitive administration interface features powerful marketing, search engine optimization and catalog-management tools to give merchants the power to create sites that are tailored to their unique business needs. Designed to be completely scalable and backed by Varien's support network, Magento offers companies the ultimate eCommerce solution.
        Magento was launched on March 31, 2008. It was developed by Varien (now Magento Inc) with help from the programmers within the open source community but is owned solely by Magento Inc.. Magento was built using the Zend Framework.[1][2] It uses the entity-attribute-value (EAV) database model to store data.
        ",
        'sort_order'         => 1,
        'creation_time'      => now(),
        'update_time'        => now(),
        'store_ids'          => array(0)
    ),
    array(
        'title'              => 'Where can I see a demo?',
        'category_id'        => 1,
        'status'             => 1,
        'is_most_frequently' => 1,
        'description'        => "A demo is available at <a href=\"http://www.magentocommerce.com/demo\">http://www.magentocommerce.com/demo</a>",
        'sort_order'         => 2,
        'creation_time'      => now(),
        'update_time'        => now(),
        'store_ids'          => array(0)
    ),
    array(
        'title'              => 'What licenses does Magento use?',
        'category_id'        => 1,
        'status'             => 1,
        'is_most_frequently' => 1,
        'description'        => "The Magento Community Edition is licensed under the Open Software License (OSL) v3.0, an open source certified license. More information about Magento's license can be found at <a href=\"http://www.magentocommerce.com/license/\">http://www.magentocommerce.com/license/</a>. The premium, Magento Enterprise Edition product is licensed under a commercial license.",
        'sort_order'         => 3,
        'creation_time'      => now(),
        'update_time'        => now(),
        'store_ids'          => array(0)
    ),
    array(
        'title'              => 'Where can I get themes, extensions and other add-ons?',
        'category_id'        => 1,
        'status'             => 1,
        'is_most_frequently' => 1,
        'description'        => "Extensions (themes, payment integrations, shipping, etc.) to the Magento platform can be found on the Magento Connect marketplace at <a href=\"http://www.magentocommerce.com/magento-connect\">http://www.magentocommerce.com/magento-connect</a>.",
        'sort_order'         => 4,
        'creation_time'      => now(),
        'update_time'        => now(),
        'store_ids'          => array(0)
    ),
    array(
        'title'              => 'What features does Magento Support?',
        'category_id'        => 1,
        'status'             => 1,
        'is_most_frequently' => 1,
        'description'        => "Magento's feature list can be seen at <a href=\"http://www.magentocommerce.com/product/features\">http://www.magentocommerce.com/product/features</a>.",
        'sort_order'         => 5,
        'creation_time'      => now(),
        'update_time'        => now(),
        'store_ids'          => array(0)
    ),
    array(
        'title'              => 'What does Magento cost?',
        'category_id'        => 1,
        'status'             => 1,
        'is_most_frequently' => 1,
        'description'        => "The Magento Community Edition is available as a free download under the open source OSL 3.0 license. The premium, Magento Enterprise Edition solution is available based on an annual subscription.",
        'sort_order'         => 6,
        'creation_time'      => now(),
        'update_time'        => now(),
        'store_ids'          => array(0)
    ),
    array(
        'title'              => 'Why Open Source?',
        'category_id'        => 1,
        'status'             => 1,
        'is_most_frequently' => 1,
        'description'        => "A recent report by O'Reilly Research on usage of Open Source in the enterprise, provides a number of reasons for the rise and meteoric growth of Open Source products, including: Agility and scale, reduced vendor lock-in, quality and security, cost, and innovation.",
        'sort_order'         => 7,
        'creation_time'      => now(),
        'update_time'        => now(),
        'store_ids'          => array(0)
    ),
    array(
        'title'              => 'What is Varien\'s relationship to Magento?',
        'category_id'        => 1,
        'status'             => 1,
        'is_most_frequently' => 1,
        'description'        => "Varien is the company behind Magento, responsible for the development and on-going maintenance of the platform. The company provides a full suite of services for Magento including world-class support, professional services, and more. Please visit the services page for additional information.",
        'sort_order'         => 8,
        'creation_time'      => now(),
        'update_time'        => now(),
        'store_ids'          => array(0)
    ),
    array(
        'title'              => 'How can I get support?',
        'category_id'        => 1,
        'status'             => 1,
        'is_most_frequently' => 1,
        'description'        => "The Magento Community Edition is supported by the community (no official product support is available) through the forums, wiki, and chat. Full core product support is available to Enteprise Edition subscribers.",
        'sort_order'         => 9,
        'creation_time'      => now(),
        'update_time'        => now(),
        'store_ids'          => array(0)
    )
    
);

/**
 * Insert sample faq category
 */
foreach ($faqsCategory as $data) {
    Mage::getModel('zeon_faq/category')->setData($data)->save();
}

/**
 * Insert sample faqs
 */
foreach ($faqs as $data1) {
    Mage::getModel('zeon_faq/faq')->setData($data1)->save();
}