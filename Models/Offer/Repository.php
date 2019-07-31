<?php
/**
 * Created by PhpStorm.
 * User: jje
 * Date: 03/06/15
 * Time: 11:51
 */

namespace Shopware\CustomModels\Offer;
use         Shopware\Components\Model\ModelRepository;

class Repository extends ModelRepository {

    /**
     * This function returns list of offers with all associated data
     *
     * @param null $filter
     * @param null $orderBy
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getListQuery($filter = null, $orderBy = null) {
        $builder = $this->getEntityManager()->createQueryBuilder();

        $builder->select(array('o', 'op', 'od' ,'ob','os','oc','documents','documentType','states','offerBilling','dispatch','offerOrder','offerShipping'))
            ->from('Shopware\CustomModels\Offer\Offer', 'o')
            ->leftJoin('o.details', 'od')
            ->leftJoin('o.payment', 'op')
            ->leftJoin('o.billing', 'ob')
            ->leftJoin('o.shop', 'os')
            ->leftJoin('o.customer', 'oc')
            ->leftJoin('o.documents', 'documents')
            ->leftJoin('documents.type', 'documentType')
            ->leftJoin('o.states', 'states')
            ->leftJoin('o.offerBilling', 'offerBilling')
            ->leftJoin('o.dispatch', 'dispatch')
            ->leftJoin('o.offerOrder', 'offerOrder')
            ->leftJoin('o.offerShipping', 'offerShipping');
        if (!empty($filter)) {
            $builder = $this->filterListQuery($builder, $filter);
        }
        if (!empty($orderBy)) {
            //add order by path
            $builder->addOrderBy($orderBy);
        }
        return $builder;
    }

    /**
     * This function filters offer data for search function
     *
     * @param \Doctrine\ORM\QueryBuilder $builder
     * @param null $filters
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function filterListQuery(\Doctrine\ORM\QueryBuilder $builder, $filters=null)
    {
        $expr = Shopware()->Models()->getExpressionBuilder();

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                if (empty($filter['property']) || $filter['value'] === null || $filter['value'] === '') {
                    continue;
                }
                switch ($filter['property']) {
                    case "free":
                        $builder->andWhere(
                            $expr->orX(
                                $expr->like('o.number', '?1'),
                                $expr->like('offerOrder.number', '?1'),
                                $expr->like('offerBilling.number', '?1'),
                                $expr->like('o.offerTime', '?2'),
                                $expr->like('offerBilling.lastName', '?3'),
                                $expr->like('offerBilling.firstName', '?3')
                            )
                        );
                        $builder->setParameter(1,       $filter['value'] . '%');
                        $builder->setParameter(2, '%' . $filter['value']      );
                        $builder->setParameter(3, '%' . $filter['value'] . '%');
                        break;

                    default:
                        $builder->andWhere($expr->eq($filter['property'], $filter['value']));
                }
            }
        }
        return $builder;
    }
}