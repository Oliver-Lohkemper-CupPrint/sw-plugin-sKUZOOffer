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

/**
 * Shopware LastOffersWidget Plugin - Backend Controller
 */
class Shopware_Controllers_Backend_LastOffersWidget extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * Return the last registered users with an offset if it is defined
     */
    public function getLastOffersAction()
    {
        $filter = $this->Request()->getParam('filter', null);
        $sort = $this->Request()->getParam('sort', null);
        $limit = $this->Request()->getParam('limit', 10);
        $offset = $this->Request()->getParam('start', 0);
        if (empty($sort)) {
            $sort = array(array('property' => 'o.offerTime', 'direction' => 'DESC'));
        } else {
            $sort[0]['property'] = 'o.' . $sort[0]['property'];
        }
        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Offer');
        $builder = $repository->getListQuery($filter, $sort);
        $builder = $builder->setFirstResult($offset)
                            ->setMaxResults($limit);
        $query = $builder->getQuery();
        $query->setHydrationMode(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        $paginator = $this->getModelManager()->createPaginator($query);
        $total = $paginator->count();

        $data = $query->getArrayResult();
        if(!empty($data)) {
            foreach ($data as $key =>$offer) {
                foreach ($offer["details"] as $detailKey => &$offerDetail) {
                    $articleRepository = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail');
                    $article = $articleRepository->findOneBy(array('number' => $offerDetail["articleNumber"]));
                    if ($article instanceof \Shopware\Models\Article\Detail) {
                        $data[$key]['details'][$detailKey]['inStock'] = $article->getInStock();
						if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
	                        $data[$key]['details'][$detailKey]['articleId'] = $article->getarticleId();
						} else {
	                        $data[$key]['details'][$detailKey]['articleId'] = $article->getArticle()->getId();
						}
                    }
                }
                if($offer['orderId']){
                    $order = Shopware()->Models()->find(
                        'Shopware\Models\Order\Order',
                        $offer['orderId']
                    );
                    $data[$key]['orderNumber'] = $order->getNumber();
                }
            }
        }
        // sends the out put to the view of the HBox
        $this->View()->assign(array(
            'success' => true,
            'data'    => $data,
            'total' => $total
        ));
    }

    public function makeDiscountAction(){
        try{
            $id = $this->Request()->getParam('positionId');
            $offerId = $this->Request()->getParam('offerId');
            $originalPrice = $this->Request()->getParam('originalPrice');
            $discountPercentage = $this->Request()->getParam('discount');

            $price = ($originalPrice -($originalPrice * ($discountPercentage)/100));

            $position = Shopware()->Models()->find(
                'Shopware\CustomModels\Offer\Detail',
                $id
            );
            $position->setPrice($price);
            Shopware()->Models()->flush();

            $offer = Shopware()->Models()->find(
                'Shopware\CustomModels\Offer\Offer',
                $offerId
            );
            $offer->calculateInvoiceAmount();
            Shopware()->Models()->flush();
            $this->View()->assign(array(
                'success' => true,
                'offerId' => $offerId
            ));
        } catch (Exception $e) {

            $this->View()->assign(array(
                'success' => false,
                'message' => $e->getMessage()
            ));
            return;
        }
    }

    public function positionListAction(){
        $id = $this->Request()->getParam('positionId');
        $expr = Shopware()->Models()->getExpressionBuilder();
        $builder = Shopware()->Models()->createQueryBuilder();
        $query = $builder->select(array('position'))
            ->from('Shopware\CustomModels\Offer\Detail', 'position')
            ->getQuery();


        $positions = $query->getArrayResult();

        $this->View()->assign(array('success' => true, 'data' => $positions));
    }

}