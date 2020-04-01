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

namespace Zikula\ContentModule\Entity\Repository\Base;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Doctrine\Paginator;
use Zikula\Bundle\CoreBundle\Doctrine\PaginatorInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Helper\CollectionFilterHelper;

/**
 * Repository class used to implement own convenience methods for performing certain DQL queries.
 *
 * This is the base repository class for page entities.
 */
abstract class AbstractPageRepository extends NestedTreeRepository
{
    /**
     * @var string The main entity class
     */
    protected $mainEntityClass = PageEntity::class;

    /**
     * @var string The default sorting field/expression
     */
    protected $defaultSortingField = 'title';

    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;

    /**
     * @var bool Whether translations are enabled or not
     */
    protected $translationsEnabled = true;

    /**
     * Retrieves an array with all fields which can be used for sorting instances.
     *
     * @return string[] List of sorting field names
     */
    public function getAllowedSortingFields(): array
    {
        return [
            'workflowState',
            'title',
            'views',
            'active',
            'activeFrom',
            'activeTo',
            'inMenu',
            'optionalString1',
            'optionalString2',
            'currentVersion',
            'createdBy',
            'createdDate',
            'updatedBy',
            'updatedDate',
        ];
    }
    
    public function getDefaultSortingField(): ?string
    {
        return $this->defaultSortingField;
    }
    
    public function setDefaultSortingField(string $defaultSortingField = null): void
    {
        if ($this->defaultSortingField !== $defaultSortingField) {
            $this->defaultSortingField = $defaultSortingField;
        }
    }
    
    public function getCollectionFilterHelper(): ?CollectionFilterHelper
    {
        return $this->collectionFilterHelper;
    }
    
    public function setCollectionFilterHelper(CollectionFilterHelper $collectionFilterHelper = null): void
    {
        if ($this->collectionFilterHelper !== $collectionFilterHelper) {
            $this->collectionFilterHelper = $collectionFilterHelper;
        }
    }
    
    public function getTranslationsEnabled(): ?bool
    {
        return $this->translationsEnabled;
    }
    
    public function setTranslationsEnabled(bool $translationsEnabled = null): void
    {
        if ($this->translationsEnabled !== $translationsEnabled) {
            $this->translationsEnabled = $translationsEnabled;
        }
    }
    
    /**
     * Updates the creator of all objects created by a certain user.
     *
     * @throws InvalidArgumentException Thrown if invalid parameters are received
     */
    public function updateCreator(
        int $userId,
        int $newUserId,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi
    ): void {
        if (0 === $userId || 0 === $newUserId) {
            throw new InvalidArgumentException($translator->trans('Invalid user identifier received.'));
        }
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->update($this->mainEntityClass, 'tbl')
           ->set('tbl.createdBy', $newUserId)
           ->where('tbl.createdBy = :creator')
           ->setParameter('creator', $userId);
        $query = $qb->getQuery();
        $query->execute();
    
        $logArgs = [
            'app' => 'ZikulaContentModule',
            'user' => $currentUserApi->get('uname'),
            'entities' => 'pages',
            'userid' => $userId
        ];
        $logger->debug('{app}: User {user} updated {entities} created by user id {userid}.', $logArgs);
    }
    
    /**
     * Updates the last editor of all objects updated by a certain user.
     *
     * @throws InvalidArgumentException Thrown if invalid parameters are received
     */
    public function updateLastEditor(
        int $userId,
        int $newUserId,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi
    ): void {
        if (0 === $userId || 0 === $newUserId) {
            throw new InvalidArgumentException($translator->trans('Invalid user identifier received.'));
        }
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->update($this->mainEntityClass, 'tbl')
           ->set('tbl.updatedBy', $newUserId)
           ->where('tbl.updatedBy = :editor')
           ->setParameter('editor', $userId);
        $query = $qb->getQuery();
        $query->execute();
    
        $logArgs = [
            'app' => 'ZikulaContentModule',
            'user' => $currentUserApi->get('uname'),
            'entities' => 'pages',
            'userid' => $userId
        ];
        $logger->debug('{app}: User {user} updated {entities} edited by user id {userid}.', $logArgs);
    }
    
    /**
     * Deletes all objects created by a certain user.
     *
     * @throws InvalidArgumentException Thrown if invalid parameters are received
     */
    public function deleteByCreator(
        int $userId,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi
    ): void {
        if (0 === $userId) {
            throw new InvalidArgumentException($translator->trans('Invalid user identifier received.'));
        }
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete($this->mainEntityClass, 'tbl')
           ->where('tbl.createdBy = :creator')
           ->setParameter('creator', $userId);
        $query = $qb->getQuery();
        $query->execute();
    
        $logArgs = [
            'app' => 'ZikulaContentModule',
            'user' => $currentUserApi->get('uname'),
            'entities' => 'pages',
            'userid' => $userId
        ];
        $logger->debug('{app}: User {user} deleted {entities} created by user id {userid}.', $logArgs);
    }
    
    /**
     * Deletes all objects updated by a certain user.
     *
     * @throws InvalidArgumentException Thrown if invalid parameters are received
     */
    public function deleteByLastEditor(
        int $userId,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi
    ): void {
        if (0 === $userId) {
            throw new InvalidArgumentException($translator->trans('Invalid user identifier received.'));
        }
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete($this->mainEntityClass, 'tbl')
           ->where('tbl.updatedBy = :editor')
           ->setParameter('editor', $userId);
        $query = $qb->getQuery();
        $query->execute();
    
        $logArgs = [
            'app' => 'ZikulaContentModule',
            'user' => $currentUserApi->get('uname'),
            'entities' => 'pages',
            'userid' => $userId
        ];
        $logger->debug('{app}: User {user} deleted {entities} edited by user id {userid}.', $logArgs);
    }

    /**
     * Adds an array of id filters to given query instance.
     *
     * @throws InvalidArgumentException Thrown if invalid parameters are received
     */
    protected function addIdListFilter(array $idList, QueryBuilder $qb): QueryBuilder
    {
        $orX = $qb->expr()->orX();
    
        foreach ($idList as $key => $id) {
            if (0 === $id) {
                throw new InvalidArgumentException('Invalid identifier received.');
            }
    
            $orX->add($qb->expr()->eq('tbl.id', ':idListFilter_' . $key));
            $qb->setParameter('idListFilter_' . $key, $id);
        }
    
        $qb->andWhere($orX);
    
        return $qb;
    }
    
    /**
     * Selects an object from the database.
     *
     * @param mixed $id The id (or array of ids) to use to retrieve the object (optional) (default=0)
     * @param bool $useJoins Whether to include joining related objects (optional) (default=true)
     * @param bool $slimMode If activated only some basic fields are selected without using any joins
     *                       (optional) (default=false)
     *
     * @return array|PageEntity Retrieved data array or pageEntity instance
     */
    public function selectById(
        $id = 0,
        bool $useJoins = true,
        bool $slimMode = false
    ) {
        $results = $this->selectByIdList(is_array($id) ? $id : [$id], $useJoins, $slimMode);
    
        return null !== $results && 0 < count($results) ? $results[0] : null;
    }
    
    /**
     * Selects a list of objects with an array of ids
     *
     * @param array $idList The array of ids to use to retrieve the objects (optional) (default=0)
     * @param bool $useJoins Whether to include joining related objects (optional) (default=true)
     * @param bool $slimMode If activated only some basic fields are selected without using any joins
     *                       (optional) (default=false)
     *
     * @return array Retrieved PageEntity instances
     */
    public function selectByIdList(
        array $idList = [0],
        bool $useJoins = true,
        bool $slimMode = false
    ): ?array {
        $qb = $this->genericBaseQuery('', '', $useJoins, $slimMode);
        $qb = $this->addIdListFilter($idList, $qb);
    
        if (!$slimMode && null !== $this->collectionFilterHelper) {
            $qb = $this->collectionFilterHelper->applyDefaultFilters('page', $qb);
        }
    
        $query = $this->getQueryFromBuilder($qb);
    
        $results = $query->getResult();
    
        return 0 < count($results) ? $results : null;
    }

    /**
     * Selects an object by slug field.
     *
     * @throws InvalidArgumentException Thrown if invalid parameters are received
     */
    public function selectBySlug(
        string $slugTitle = '',
        bool $useJoins = true,
        bool $slimMode = false,
        int $excludeId = 0
    ): ?PageEntity {
        if ('' === $slugTitle) {
            throw new InvalidArgumentException('Invalid slug title received.');
        }
    
        $qb = $this->genericBaseQuery('', '', $useJoins, $slimMode);
    
        $qb->andWhere('tbl.slug = :slug')
           ->setParameter('slug', $slugTitle);
    
        if ($excludeId > 0) {
            $qb = $this->addExclusion($qb, [$excludeId]);
        }
    
        if (!$slimMode && null !== $this->collectionFilterHelper) {
            $qb = $this->collectionFilterHelper->applyDefaultFilters('page', $qb);
        }
    
        $query = $this->getQueryFromBuilder($qb);
    
        $results = $query->getResult();
    
        return null !== $results && count($results) > 0 ? $results[0] : null;
    }

    /**
     * Adds where clauses excluding desired identifiers from selection.
     */
    protected function addExclusion(QueryBuilder $qb, array $exclusions = []): QueryBuilder
    {
        if (0 < count($exclusions)) {
            $qb->andWhere('tbl.id NOT IN (:excludedIdentifiers)')
               ->setParameter('excludedIdentifiers', $exclusions);
        }
    
        return $qb;
    }

    /**
     * Returns query builder for selecting a list of objects with a given where clause.
     */
    public function getListQueryBuilder(
        string $where = '',
        string $orderBy = '',
        bool $useJoins = true,
        bool $slimMode = false
    ): QueryBuilder {
        $qb = $this->genericBaseQuery($where, $orderBy, $useJoins, $slimMode);
        if (!$slimMode && null !== $this->collectionFilterHelper) {
            $qb = $this->collectionFilterHelper->addCommonViewFilters('page', $qb);
        }
    
        return $qb;
    }
    
    /**
     * Selects a list of objects with a given where clause.
     */
    public function selectWhere(
        string $where = '',
        string $orderBy = '',
        bool $useJoins = true,
        bool $slimMode = false
    ): array {
        $qb = $this->getListQueryBuilder($where, $orderBy, $useJoins, $slimMode);
    
        return $this->retrieveCollectionResult($qb);
    }

    /**
     * Selects a list of objects with a given where clause and pagination parameters.
     *
     * @return PaginatorInterface
     */
    public function selectWherePaginated(
        string $where = '',
        string $orderBy = '',
        int $currentPage = 1,
        int $resultsPerPage = 25,
        bool $useJoins = true,
        bool $slimMode = false
    ): PaginatorInterface {
        $qb = $this->getListQueryBuilder($where, $orderBy, $useJoins, $slimMode);
    
        return $this->retrieveCollectionResult($qb, true, $currentPage, $resultsPerPage);
    }

    /**
     * Selects entities by a given search fragment.
     *
     * @return array Retrieved collection and (for paginated queries) the amount of total records affected
     */
    public function selectSearch(
        string $fragment = '',
        array $exclude = [],
        string $orderBy = '',
        int $currentPage = 1,
        int $resultsPerPage = 25,
        bool $useJoins = true
    ): array {
        $qb = $this->getListQueryBuilder('', $orderBy, $useJoins);
        if (0 < count($exclude)) {
            $qb = $this->addExclusion($qb, $exclude);
        }
    
        if (null !== $this->collectionFilterHelper) {
            $qb = $this->collectionFilterHelper->addSearchFilter('page', $qb, $fragment);
        }
    
        return $this->retrieveCollectionResult($qb, true, $currentPage, $resultsPerPage);
    }

    /**
     * Performs a given database selection and post-processed the results.
     *
     * @return PaginatorInterface|array Paginator (for paginated queries) or retrieved collection
     */
    public function retrieveCollectionResult(
        QueryBuilder $qb,
        bool $isPaginated = false,
        int $currentPage = 1,
        int $resultsPerPage = 25
    ) {
        if (!$isPaginated) {
            $query = $this->getQueryFromBuilder($qb);
    
            return $query->getResult();
        }
    
        return (new Paginator($qb, $resultsPerPage))->paginate($currentPage);
    }

    /**
     * Returns query builder instance for a count query.
     */
    public function getCountQuery(string $where = '', bool $useJoins = false): QueryBuilder
    {
        $selection = 'COUNT(tbl.id) AS numPages';
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select($selection)
           ->from($this->mainEntityClass, 'tbl');
    
        if (true === $useJoins) {
            $this->addJoinsToFrom($qb);
        }
    
        if (!empty($where)) {
            $qb->andWhere($where);
        }
    
        return $qb;
    }

    /**
     * Selects entity count with a given where clause.
     */
    public function selectCount(string $where = '', bool $useJoins = false, array $parameters = []): int
    {
        $qb = $this->getCountQuery($where, $useJoins);
    
        if (null !== $this->collectionFilterHelper) {
            $qb = $this->collectionFilterHelper->applyDefaultFilters('page', $qb, $parameters);
        }
    
        $query = $qb->getQuery();
    
        return (int)$query->getSingleScalarResult();
    }
    
    /**
     * Selects tree of pages.
     */
    public function selectTree(int $rootId = 0, bool $useJoins = true): array
    {
        if (0 === $rootId) {
            // return all trees if no specific one has been asked for
            return $this->selectAllTrees($useJoins);
        }
    
        // fetch root node
        $rootNode = $this->selectById($rootId, $useJoins);
    
        // fetch children
        $children = $this->children($rootNode);
    
        return array_merge([$rootNode], $children);
    }
    
    /**
     * Selects all trees at once.
     */
    public function selectAllTrees(bool $useJoins = true): array
    {
        $trees = [];
    
        // get all root nodes
        $qb = $this->genericBaseQuery('tbl.lvl = 0', '', $useJoins);
        $query = $this->getQueryFromBuilder($qb);
        $rootNodes = $query->getResult();
    
        foreach ($rootNodes as $rootNode) {
            // fetch children
            $children = $this->children($rootNode);
            $trees[$rootNode->getId()] = array_merge([$rootNode], $children);
        }
    
        return $trees;
    }

    /**
     * Checks for unique values.
     */
    public function detectUniqueState(string $fieldName, string $fieldValue, int $excludeId = 0): bool
    {
        $qb = $this->getCountQuery();
        $qb->andWhere('tbl.' . $fieldName . ' = :' . $fieldName)
           ->setParameter($fieldName, $fieldValue);
    
        if ($excludeId > 0) {
            $qb = $this->addExclusion($qb, [$excludeId]);
        }
    
        $query = $qb->getQuery();
    
        $count = (int)$query->getSingleScalarResult();
    
        return 1 > $count;
    }

    /**
     * Builds a generic Doctrine query supporting WHERE and ORDER BY.
     */
    public function genericBaseQuery(
        string $where = '',
        string $orderBy = '',
        bool $useJoins = true,
        bool $slimMode = false
    ): QueryBuilder {
        // normally we select the whole table
        $selection = 'tbl';
    
        if (true === $slimMode) {
            // but for the slim version we select only the basic fields, and no joins
    
            $selection = 'tbl.id';
            $selection .= ', tbl.title';
            $selection .= ', tbl.slug';
            $useJoins = false;
        }
    
        if (true === $useJoins) {
            $selection .= $this->addJoinsToSelection();
        }
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select($selection)
           ->from($this->mainEntityClass, 'tbl');
    
        if (true === $useJoins) {
            $this->addJoinsToFrom($qb);
        }
    
        if (!empty($where)) {
            $qb->andWhere($where);
        }
    
        $this->genericBaseQueryAddOrderBy($qb, $orderBy);
    
        return $qb;
    }

    /**
     * Adds ORDER BY clause to given query builder.
     */
    protected function genericBaseQueryAddOrderBy(QueryBuilder $qb, string $orderBy = ''): QueryBuilder
    {
        if ('RAND()' === $orderBy) {
            // random selection
            $qb->addSelect('MOD(tbl.id, ' . random_int(2, 15) . ') AS HIDDEN randomIdentifiers')
               ->orderBy('randomIdentifiers');
    
            return $qb;
        }
    
        if (empty($orderBy)) {
            $orderBy = $this->defaultSortingField;
        }
    
        if (empty($orderBy)) {
            return $qb;
        }
    
        // add order by clause
        if (false === strpos($orderBy, '.')) {
            $orderBy = 'tbl.' . $orderBy;
        }
        if (false !== strpos($orderBy, 'tbl.createdBy')) {
            $qb->addSelect('tblCreator')
               ->leftJoin('tbl.createdBy', 'tblCreator');
            $orderBy = str_replace('tbl.createdBy', 'tblCreator.uname', $orderBy);
        }
        if (false !== strpos($orderBy, 'tbl.updatedBy')) {
            $qb->addSelect('tblUpdater')
               ->leftJoin('tbl.updatedBy', 'tblUpdater');
            $orderBy = str_replace('tbl.updatedBy', 'tblUpdater.uname', $orderBy);
        }
        $qb->add('orderBy', $orderBy);
    
        return $qb;
    }

    /**
     * Retrieves Doctrine query from query builder.
     */
    public function getQueryFromBuilder(QueryBuilder $qb): Query
    {
        $query = $qb->getQuery();
    
        if (true === $this->translationsEnabled) {
            // set the translation query hint
            $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class);
        }
    
        return $query;
    }

    /**
     * Helper method to add join selections.
     */
    protected function addJoinsToSelection(): string
    {
        $selection = ', tblContentItems';
    
        $selection .= ', tblCategories';
    
        return $selection;
    }
    
    /**
     * Helper method to add joins to from clause.
     */
    protected function addJoinsToFrom(QueryBuilder $qb): QueryBuilder
    {
        $qb->leftJoin('tbl.contentItems', 'tblContentItems');
    
        $qb->leftJoin('tbl.categories', 'tblCategories');
    
        return $qb;
    }
}
