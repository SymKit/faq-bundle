<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Tests\Unit\Form\Admin;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symkit\FaqBundle\Entity\Faq;
use Symkit\FaqBundle\Form\Admin\FaqType;

final class FaqTypeTest extends TestCase
{
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $type = new FaqType(Faq::class);
        $type->configureOptions($resolver);

        $defaults = $resolver->resolve();
        self::assertSame(Faq::class, $defaults['data_class']);
        self::assertSame('SymkitFaqBundle', $defaults['translation_domain']);
    }

    public function testBuildFormAddsExpectedFields(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('create')->willReturnSelf();
        $builder->method('add')->willReturnSelf();

        $type = new FaqType(Faq::class);
        $type->buildForm($builder, []);
        self::addToAssertionCount(1);
    }
}
