<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Provider;

use Doctrine\ORM\EntityManagerInterface;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\InvalidArgumentException;
use JtcSolutions\Helpers\Helper\FQCNHelper;

class EntityMapProvider
{
    /**
     * @var array<string, string[]> where key is domain name and value are entity names
     */
    private readonly array $map;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        $this->map = $this->createEntityMap();
    }

    /**
     * @return string[]
     */
    public function getDomainEntities(string $domain): array
    {
        return $this->map[$domain];
    }

    /**
     * @return array<string, string[]>
     */
    private function createEntityMap(): array
    {
        $metas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $map = [];

        foreach ($metas as $meta) {
            $data = FQCNHelper::extractDomainAndEntity($meta->name);
            if ($data['domain'] === null) {
                throw InvalidArgumentException::unableToExtractDomainName($meta->name);
            }
            if ($data['entity'] === null) {
                throw InvalidArgumentException::unableToExtractEntityName($meta->name);
            }
            if (! isset($map[$data['domain']])) {
                $map[$data['domain']] = [];
            }
            $map[$data['domain']][] = $data['entity'];
        }

        return $map;
    }
}
