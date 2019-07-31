<?php
/**
 * Shopware 4
 * Copyright © shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace   Shopware\CustomModels\Offer\Document;
use         Shopware\Components\Model\ModelRepository;

/**
 * Repository for the offer document model (Shopware\Models\Offer\Document\Document).
 *
 * The offer document model repository is responsible to load all document data.
 * It supports the standard functions like findAll or findBy and extends the standard repository for
 * some specific functions to return the model data as array.
 */
class Repository extends ModelRepository
{
    /**
     * Returns an instance of the \Doctrine\ORM\Query object which contains
     * all required fields for the backend offer document list.
     * The filtering is performed on all columns.
     * The passed limit parameters for the list paging are placed directly into the query object.
     * To determine the total number of records, use the following syntax:
     * Shopware()->Models()->getQueryCount($query);
     *
     * @param       $offerId
     * @param null  $filter
     * @param null  $offerBy
     * @param  null $limit
     * @param  null $offset
     * @return \Doctrine\ORM\Query
     */
    public function getListQuery($offerId, $filter = null,$offerBy = null, $limit = null, $offset = null)
    {
        /** @var $builder \Doctrine\ORM\QueryBuilder*/
        $builder = $this->getEntityManager()->createQueryBuilder();
        $builder = $this->selectListQuery($builder);

        $builder = $this->filterListQuery($builder,$filter);
        $this->addOfferBy($builder, $offerBy);

        $builder->andWhere($builder->expr()->eq('documents.offerId', $offerId));

        if ($limit !== null) {
            $builder->setFirstResult($offset)
                    ->setMaxResults($limit);
        }
        return $builder->getQuery();
    }

    /**
     * Helper function which sets the fromPath and the selectPath for the offer list query.
     * @param \Doctrine\ORM\QueryBuilder $builder
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function selectListQuery(\Doctrine\ORM\QueryBuilder $builder)
    {
        //select the different entities
        $builder->select(array(
            'documents.id as id',
            'documents.date as date',
            'documents.typeId as typeId',
            'documents.customerId as customerId',
            'documents.offerId as offerId',
            'documents.amount as amount',
            'documents.documentId as documentId',
            'documents.hash as hash',
            'type.name as typeName'
        ));

        //join the required tables for the offer list
        $builder->from('Shopware\CustomModels\Offer\Document\Document', 'documents')
                ->join('documents.type', 'type');

        return $builder;
    }

    protected function filterListQuery(\Doctrine\ORM\QueryBuilder $builder, $filter=null)
    {
        return $builder;
    }
    /**
     * Returns a list of all defined config entries as array
     *
     * @return \Doctrine\ORM\Query
     */
    public function getConfigQuery() {
        $builder = $this->getConfigQueryBuilder();
        return $builder->getQuery();
    }

    /**
     * Helper function to create the query builder for the "getConfigQuery" function.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getConfigQueryBuilder() {
        $builder = $this->getEntityManager()->createQueryBuilder();
        return $builder->select(array('name', 'value'))
            ->from('Shopware\CustomModels\Offer\Document\Config', 'config')
            ->orderBy('config.name', 'ASC');
    }
}
