<?php

namespace JtcSolutions\CodeGenerator\Tests\Integration\Provider;

use JtcSolutions\CodeGenerator\CodeGenerator\Service\Provider\DomainContextProvider;
use PHPUnit\Framework\TestCase;

class DomainContextProviderTest extends TestCase
{
    public function testGetContextReturnsCorrectDomainAndEntity(): void
    {
        $domain = 'TestDomain';
        $entity = 'App\Entity\Test';

        $provider = new DomainContextProvider($domain, $entity);
        $context = $provider->getContext();

        $this->assertEquals($domain, $context->domain);
        $this->assertEquals($entity, $context->entity);
    }

    public function testConstructorHandlesEmptyValues(): void
    {
        $provider = new DomainContextProvider('', '');
        $context = $provider->getContext();

        $this->assertEquals('', $context->domain);
        $this->assertEquals('', $context->entity);
    }

    public function testGetContextReturnsSameObjectOnMultipleCalls(): void
    {
        $provider = new DomainContextProvider('Domain', 'Entity');

        $context1 = $provider->getContext();
        $context2 = $provider->getContext();

        $this->assertSame($context1, $context2);
    }
}