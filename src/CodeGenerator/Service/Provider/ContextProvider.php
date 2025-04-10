<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Provider;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;

final class ContextProvider
{
    private readonly Context $context;

    /** @param class-string $entity */
    public function __construct(
        string $path,
        string $domain,
        string $entity,
    ) {
        $this->context = new Context($domain, $entity, $path);
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
