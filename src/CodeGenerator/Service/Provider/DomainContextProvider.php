<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Provider;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\DomainContext;

final class DomainContextProvider
{
    private readonly DomainContext $context;

    public function __construct(
        ?string $domain,
        ?string $entity,
    ) {
        $this->ensureDomainExists($domain);
        $this->ensureEntityExists($domain, $entity);

        $this->context = new DomainContext($domain, $entity);
    }

    public function getContext(): DomainContext
    {
        return $this->context;
    }

    private function ensureDomainExists(string $domain): void
    {
        // validation
    }

    private function ensureEntityExists(string $domain, string $entity): void
    {
        // validation
    }
}
