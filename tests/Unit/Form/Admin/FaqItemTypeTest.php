<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Tests\Unit\Form\Admin;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symkit\FaqBundle\Entity\FaqItem;
use Symkit\FaqBundle\Form\Admin\FaqItemType;
use Symkit\FormBundle\Form\Type\FormSectionType;

final class FaqItemTypeTest extends TestCase
{
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $type = new FaqItemType(FaqItem::class);
        $type->configureOptions($resolver);

        $defaults = $resolver->resolve();
        self::assertSame(FaqItem::class, $defaults['data_class']);
        self::assertSame('SymkitFaqBundle', $defaults['translation_domain']);
    }

    public function testBuildFormAddsExpectedFields(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('create')->willReturnSelf();
        $builder->method('add')->willReturnSelf();

        $type = new FaqItemType(FaqItem::class);
        $type->buildForm($builder, []);
        self::addToAssertionCount(1);
    }

    public function testBuildFormSectionAndFieldsOptions(): void
    {
        $calls = [];
        $sectionBuilder = $this->createMock(FormBuilderInterface::class);
        $sectionBuilder->method('add')->willReturnCallback(function (string $name, string $type, array $options = []) use ($sectionBuilder, &$calls): FormBuilderInterface {
            $calls['add'][] = ['name' => $name, 'type' => $type, 'options' => $options];

            return $sectionBuilder;
        });

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('create')->willReturnCallback(function (string $name, string $type, array $options = []) use ($sectionBuilder, &$calls): FormBuilderInterface {
            $calls['create'] = ['name' => $name, 'type' => $type, 'options' => $options];

            return $sectionBuilder;
        });
        $builder->method('add')->willReturnSelf();

        $type = new FaqItemType(FaqItem::class);
        $type->buildForm($builder, []);

        self::assertArrayHasKey('create', $calls);
        self::assertSame('general', $calls['create']['name']);
        self::assertSame(FormSectionType::class, $calls['create']['type']);
        self::assertTrue($calls['create']['options']['inherit_data'] ?? false);
        self::assertSame('form.faq_item.section.general', $calls['create']['options']['label'] ?? '');
        self::assertSame('form.faq_item.section.description', $calls['create']['options']['section_description'] ?? '');

        self::assertArrayHasKey('add', $calls);
        self::assertCount(3, $calls['add']);

        self::assertSame('question', $calls['add'][0]['name']);
        self::assertSame(TextType::class, $calls['add'][0]['type']);
        self::assertSame('form.faq_item.question', $calls['add'][0]['options']['label'] ?? '');
        self::assertSame(['placeholder' => 'form.faq_item.question_placeholder'], $calls['add'][0]['options']['attr'] ?? []);
        self::assertSame('form.faq_item.question_help', $calls['add'][0]['options']['help'] ?? '');

        self::assertSame('answer', $calls['add'][1]['name']);
        self::assertSame(TextareaType::class, $calls['add'][1]['type']);
        self::assertSame('form.faq_item.answer', $calls['add'][1]['options']['label'] ?? '');
        self::assertSame(5, $calls['add'][1]['options']['attr']['rows'] ?? 0);
        self::assertSame('form.faq_item.answer_placeholder', $calls['add'][1]['options']['attr']['placeholder'] ?? '');
        self::assertSame('form.faq_item.answer_help', $calls['add'][1]['options']['help'] ?? '');

        self::assertSame('position', $calls['add'][2]['name']);
        self::assertSame(IntegerType::class, $calls['add'][2]['type']);
        self::assertSame('form.faq_item.position', $calls['add'][2]['options']['label'] ?? '');
        self::assertSame(0, $calls['add'][2]['options']['attr']['min'] ?? -1);
        self::assertSame('form.faq_item.position_help', $calls['add'][2]['options']['help'] ?? '');
    }
}
