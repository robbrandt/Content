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

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\CategoriesModule\Entity\CategoryEntity;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\PageCategoryEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Helper\WorkflowHelper;

/**
 * Example data helper base class.
 */
abstract class AbstractExampleDataHelper
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
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;
    
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        LoggerInterface $logger,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper
    ) {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->entityFactory = $entityFactory;
        $this->workflowHelper = $workflowHelper;
    }
    
    /**
     * Create the default data for ZikulaContentModule.
     */
    public function createDefaultData(): void
    {
        $dtNow = date('Y-m-d H:i:s');
        $entityManager = $this->entityFactory->getEntityManager();
        // example category
        $categoryId = 41; // Business and work
        /** @var CategoryEntity $category */
        $category = $entityManager->find('ZikulaCategoriesModule:CategoryEntity', $categoryId);
    
        // determine category registry identifiers
        $registryRepository = $entityManager->getRepository('ZikulaCategoriesModule:CategoryRegistryEntity');
        $categoryRegistries = $registryRepository->findBy(['modname' => 'ZikulaContentModule']);
    
    
        $page1 = new PageEntity();
        
        $contentItem1 = new ContentItemEntity();
        
        $categoryRegistry = null;
        foreach ($categoryRegistries as $registry) {
            if ('PageEntity' === $registry->getEntityname()) {
                $categoryRegistry = $registry;
                break;
            }
        }
        $page1->setWorkflowState('initial');
        $page1->setTitle('Page title 1');
        $page1->setShowTitle(true);
        $page1->setMetaDescription('Page meta description 1');
        $page1->setSkipHookSubscribers(false);
        $page1->setLayout([]);
        $page1->setViews(1);
        $page1->setActive(true);
        $page1->setActiveFrom($dtNow);
        $page1->setActiveTo($dtNow);
        $page1->setScope('###0###');
        $page1->setInMenu(true);
        $page1->setOptionalString1('Page optional string 1 1');
        $page1->setOptionalString2('Page optional string 2 1');
        $page1->setOptionalText('Page optional text 1');
        $page1->setStylingClasses([]);
        $page1->setCurrentVersion(1);
        $page1->setContentData([]);
        $page1->setTranslationData([]);
        
        $page1->setParent(null);
        $page1->setRoot(1);
        // create category assignment
        $page1->getCategories()->add(new PageCategoryEntity($categoryRegistry->getId(), $category, $page1));
        
        $contentItem1->setWorkflowState('initial');
        $contentItem1->setOwningType('Content item owning type 1');
        $contentItem1->setContentData([]);
        $contentItem1->setActive(true);
        $contentItem1->setActiveFrom($dtNow);
        $contentItem1->setActiveTo($dtNow);
        $contentItem1->setScope('###0###');
        $contentItem1->setStylingClasses([]);
        $contentItem1->setSearchText('Content item search text 1');
        $contentItem1->setAdditionalSearchText('Content item additional search text 1');
        
        $contentItem1->setPage($page1);
        
        // execute the workflow action for each entity
        $action = 'submit';
        try {
            $entityManager = $this->entityFactory->getEntityManager();
            $entityManager->persist($page1);
            $entityManager->persist($contentItem1);
            $success = $this->workflowHelper->executeAction($page1, $action);
            $success = $this->workflowHelper->executeAction($contentItem1, $action);
        } catch (Exception $exception) {
            $flashBag = $this->requestStack->getCurrentRequest()->getSession()->getFlashBag();
            $flashBag->add(
                'error',
                $this->translator->__('Exception during example data creation')
                    . ': ' . $exception->getMessage()
            );
            $this->logger->error(
                '{app}: Could not completely create example data after installation. Error details: {errorMessage}.',
                ['app' => 'ZikulaContentModule', 'errorMessage' => $exception->getMessage()]
            );
        }
    }
}
