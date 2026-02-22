<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symkit\FormBundle\Form\Type\FormSectionType;

final class FaqItemType extends AbstractType
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
                'label' => 'form.faq_item.section.general',
                'section_icon' => 'heroicons:chat-bubble-bottom-center-text-20-solid',
                'section_description' => 'form.faq_item.section.description',
            ])
                ->add('question', TextType::class, [
                    'label' => 'form.faq_item.question',
                    'attr' => ['placeholder' => 'form.faq_item.question_placeholder'],
                    'help' => 'form.faq_item.question_help',
                ])
                ->add('answer', TextareaType::class, [
                    'label' => 'form.faq_item.answer',
                    'attr' => ['rows' => 5, 'placeholder' => 'form.faq_item.answer_placeholder'],
                    'help' => 'form.faq_item.answer_help',
                ])
                ->add('position', IntegerType::class, [
                    'label' => 'form.faq_item.position',
                    'attr' => ['min' => 0],
                    'help' => 'form.faq_item.position_help',
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
