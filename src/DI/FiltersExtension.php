<?php declare(strict_types = 1);

namespace Contributte\Latte\DI;

use Contributte\Latte\Exception\Runtime\LatteDefinitionNotFoundException;
use Contributte\Latte\Filters\FiltersProvider;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;

class FiltersExtension extends CompilerExtension
{

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		if ($builder->getByType(ILatteFactory::class) === null) {
			throw new LatteDefinitionNotFoundException();
		}

		$latte = $builder->getDefinitionByType(ILatteFactory::class);
		if ($latte instanceof FactoryDefinition) {
			$latte = $latte->getResultDefinition();
		}

		$filters = $builder->findByType(FiltersProvider::class);

		foreach ($filters as $definition) {
			$latte->addSetup(
				'foreach (?->getFilters() as $name => $callback) {
					?->addFilter($name, $callback);
				}',
				[$definition, '@self']
			);
		}
	}

}
