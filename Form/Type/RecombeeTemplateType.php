<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecombeeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RecombeeTemplateType.
 */
class RecombeeTemplateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'header',
            'textarea',
            [
                'label'    => 'mautic.plugin.recombee.page.template',
                'required' => false,
                'attr'     => [
                    'class' => 'recombee-template',
                    'rows'  => 2,
                ],
            ]
        );

        $builder->add(
            'body',
            'textarea',
            [
                'label'       => 'mautic.plugin.recombee.page.template',
                'required'    => true,
                'attr'        => [
                    'class' => 'recombee-template',
                    'rows'  => 5,
                ],
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'mautic.core.value.required',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'footer',
            'textarea',
            [
                'label'    => 'mautic.plugin.recombee.page.template',
                'required' => false,
                'attr'     => [
                    'class' => 'recombee-template',
                    'rows'  => 2,
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'recombee_template';
    }
}
