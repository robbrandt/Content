<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Entity\Repository\Base;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * Repository class used to implement own convenience methods for performing certain DQL queries.
 *
 * This is the base repository class for page log entry entities.
 */
abstract class AbstractPageLogEntryRepository extends LogEntryRepository
{
    /**
     * Selects all log entries for deletions to determine deleted page.
     *
     * @param integer $limit The maximum amount of items to fetch
     *
     * @return ArrayCollection Collection containing retrieved items
     */
    public function selectDeleted($limit = null)
    {
        $objectClass = str_replace('LogEntry', '', $this->_entityName);
    
        // avoid selecting logs for those entries which already had been undeleted
        $qbExisting = $this->getEntityManager()->createQueryBuilder();
        $qbExisting->select('tbl.id')
            ->from($objectClass, 'tbl');
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('log')
           ->from($this->_entityName, 'log')
           ->andWhere('log.objectClass = :objectClass')
           ->andWhere('log.action = :action')
           ->andWhere($qb->expr()->notIn('log.objectId', $qbExisting->getDQL()))
           ->orderBy('log.version', 'DESC');
    
        $qb->setParameter('objectClass', $objectClass)
           ->setParameter('action', 'remove');
    
        $query = $qb->getQuery();
    
        if (null !== $limit) {
            $query->setMaxResults($limit);
        }
    
        return $query->getResult();
    }
}