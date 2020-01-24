<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Helper\Base;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\CategoriesModule\Api\ApiInterface\CategoryPermissionApiInterface;
use Zikula\CategoriesModule\Entity\RepositoryInterface\CategoryRegistryRepositoryInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;

/**
 * Category helper base class.
 */
abstract class AbstractCategoryHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var CategoryRegistryRepositoryInterface
     */
    protected $categoryRegistryRepository;
    
    /**
     * @var CategoryPermissionApiInterface
     */
    protected $categoryPermissionApi;
    
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        LoggerInterface $logger = null,
        CurrentUserApiInterface $currentUserApi,
        CategoryRegistryRepositoryInterface $categoryRegistryRepository,
        CategoryPermissionApiInterface $categoryPermissionApi
    ) {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->currentUserApi = $currentUserApi;
        $this->categoryRegistryRepository = $categoryRegistryRepository;
        $this->categoryPermissionApi = $categoryPermissionApi;
    }
    
    /**
     * Defines whether multiple selection is enabled for a given object type
     * or not. Subclass can override this method to apply a custom behaviour
     * to certain category registries for example.
     */
    public function hasMultipleSelection(string $objectType = '', string $registry = ''): bool
    {
        if (empty($objectType)) {
            throw new InvalidArgumentException($this->translator->trans('Invalid object type received.'));
        }
        if (empty($args['registry'])) {
            // default to the primary registry
            $registry = $this->getPrimaryProperty($objectType);
        }
    
        // we make no difference between different category registries here
        // if you need a custom behaviour you should override this method
    
        $result = false;
        switch ($objectType) {
            case 'page':
                $result = false;
                break;
        }
    
        return $result;
    }
    
    /**
     * Retrieves input data from POST for all registries.
     */
    public function retrieveCategoriesFromRequest(string $objectType = '', string $source = 'POST'): array
    {
        if (empty($objectType)) {
            throw new InvalidArgumentException($this->translator->trans('Invalid object type received.'));
        }
    
        $request = $this->requestStack->getCurrentRequest();
        $dataSource = 'GET' === $source ? $request->query : $request->request;
        $catIdsPerRegistry = [];
    
        $properties = $this->getAllProperties($objectType);
        $inputValues = null;
        $inputName = 'zikulacontentmodule_' . strtolower($objectType) . 'quicknav';
        if (!$dataSource->has($inputName)) {
            $inputName = 'zikulacontentmodule_' . strtolower($objectType) . 'finder';
        }
        if ($dataSource->has($inputName)) {
            $inputValues = $dataSource->get($inputName);
        }
        if (null === $inputValues) {
            return $catIdsPerRegistry;
        }
        $inputCategories = $inputValues['categories'] ?? [];
    
        if (!count($inputCategories)) {
            return $catIdsPerRegistry;
        }
    
        foreach ($properties as $propertyName => $propertyId) {
            $registryKey = 'registry_' . $propertyId;
            $inputValue = $inputCategories[$registryKey] ?? [];
            if (!is_array($inputValue)) {
                $inputValue = [$inputValue];
            }
    
            // prevent "All" option hiding all entries
            foreach ($inputValue as $k => $v) {
                if (0 === $v) {
                    unset($inputValue[$k]);
                }
            }
    
            $catIdsPerRegistry[$propertyName] = $inputValue;
        }
    
        return $catIdsPerRegistry;
    }
    
    /**
     * Adds a list of where clauses for a certain list of categories to a given query builder.
     */
    public function buildFilterClauses(
        QueryBuilder $queryBuilder,
        string $objectType = '',
        array $catIds = []
    ): QueryBuilder {
        $qb = $queryBuilder;
    
        $properties = $this->getAllProperties($objectType);
    
        $filtersPerRegistry = [];
        $filterParameters = [
            'values' => [],
            'registries' => []
        ];
    
        foreach ($properties as $propertyName => $propertyId) {
            if (!isset($catIds[$propertyName]) || !is_array($catIds[$propertyName]) || !count($catIds[$propertyName])) {
                continue;
            }
            $catIdsForProperty = [];
            foreach ($catIds[$propertyName] as $catId) {
                if (!$catId) {
                    continue;
                }
                $catIdsForProperty[] = $catId;
            }
            if (!count($catIdsForProperty)) {
                continue;
            }
    
            $propertyName = str_replace(' ', '', $propertyName);
            $filtersPerRegistry[] = '(
                tblCategories.categoryRegistryId = :propId' . $propertyName . '
                AND tblCategories.category IN (:categories' . $propertyName . ')
            )';
            $filterParameters['registries'][$propertyName] = $propertyId;
            $filterParameters['values'][$propertyName] = $catIdsForProperty;
        }
    
        if (0 < count($filtersPerRegistry)) {
            if (1 === count($filtersPerRegistry)) {
                $qb->andWhere($filtersPerRegistry[0]);
            } else {
                $qb->andWhere('(' . implode(' OR ', $filtersPerRegistry) . ')');
            }
            foreach ($filterParameters['values'] as $propertyName => $filterValue) {
                $qb->setParameter('propId' . $propertyName, $filterParameters['registries'][$propertyName])
                   ->setParameter('categories' . $propertyName, $filterValue);
            }
        }
    
        return $qb;
    }
    
    /**
     * Returns a list of all registries / properties for a given object type.
     */
    public function getAllProperties(string $objectType = ''): array
    {
        if (empty($objectType)) {
            throw new InvalidArgumentException($this->translator->trans('Invalid object type received.'));
        }
    
        $moduleRegistries = $this->categoryRegistryRepository->findBy([
            'modname' => 'ZikulaContentModule',
            'entityname' => ucfirst($objectType) . 'Entity'
        ]);
    
        $result = [];
        foreach ($moduleRegistries as $registry) {
            $result[$registry['property']] = $registry['id'];
        }
    
        return $result;
    }
    
    /**
     * Returns a list of all registries with main category for a given object type.
     */
    public function getAllPropertiesWithMainCat(string $objectType = '', string $arrayKey = 'property'): array
    {
        if (empty($objectType)) {
            throw new InvalidArgumentException($this->translator->trans('Invalid object type received.'));
        }
    
        $moduleRegistries = $this->categoryRegistryRepository->findBy([
            'modname' => 'ZikulaContentModule',
            'entityname' => ucfirst($objectType) . 'Entity'
        ], ['id' => 'ASC']);
    
        $result = [];
        foreach ($moduleRegistries as $registry) {
            $registry = $registry->toArray();
            $result[$registry[$arrayKey]] = $registry['category']->getId();
        }
    
        return $result;
    }
    
    /**
     * Returns the main category id for a given object type and a certain property name.
     */
    public function getMainCatForProperty(string $objectType = '', string $property = ''): ?int
    {
        if (empty($objectType)) {
            throw new InvalidArgumentException($this->translator->trans('Invalid object type received.'));
        }
    
        $registries = $this->getAllPropertiesWithMainCat($objectType);
        if ($registries && isset($registries[$property]) && $registries[$property]) {
            return $registries[$property];
        }
    
        return null;
    }
    
    /**
     * Returns the name of the primary registry.
     */
    public function getPrimaryProperty(string $objectType = ''): string
    {
        return 'Main';
    }
    
    /**
     * Checks whether permissions are granted to the given categories or not.
     */
    public function hasPermission(EntityAccess $entity): bool
    {
        $requireAccessForAll = $this->requireAccessForAll($entity);
    
        return $this->categoryPermissionApi->hasCategoryAccess(
            $entity->getCategories()->toArray(),
            ACCESS_OVERVIEW,
            $requireAccessForAll
        );
    }
    
    /**
     * Returns whether permissions are required for all categories
     * of a specific entity or for only one category.
     *
     * Returning false allows access if the user has access
     * to at least one selected category.
     * Returning true only allows access if the user has access
     * to all selected categories.
     */
    protected function requireAccessForAll(EntityAccess $entity): bool
    {
        return false;
    }
}
