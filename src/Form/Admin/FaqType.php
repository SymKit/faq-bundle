<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symkit\FormBundle\Form\Type\FormSectionType;
use Symkit\FormBundle\Form\Type\SlugType;

final class FaqType extends AbstractType
{
    public function __construct(
        private readonly string $dataClass,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            $builder->create('general', FormSectionType::class, [
                'inherit_data' => true,
                'label' => 'form.faq.section.general',
                'section_icon' => 'heroicons:question-mark-circle-20-solid',
                'section_description' => 'form.faq.section.description',
            ])
                ->add('title', TextType::class, [
                    'label' => 'form.faq.title',
                    'attr' => ['placeholder' => 'form.faq.title_placeholder'],
                ])
                ->add('code', SlugType::class, [
                    'label' => 'form.faq.code',
                    'required' => false,
                    'target' => 'title',
                    'unique' => true,
                    'entity_class' => $this->dataClass,
                    'slug_field' => 'code',
                    'help' => 'form.faq.code_help',
                ]),
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->dataClass,
            'translation_domain' => 'SymkitFaqBundle',
        ]);
    }
}
