<?php

namespace Liuggio\HelpDeskBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TicketRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TicketRepository extends EntityRepository
{

    public function findTicketsByStatesAndCustomer($user, $states, $filter = '', $onlyQuery = false)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('t')
            ->from('LiuggioHelpDeskBundle:Ticket', 't')
            ->leftjoin('t.state', 'st')
            ->leftJoin('t.category', 'c')
            ->where('t.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('t.updatedAt','DESC')
        ;

        if (!is_array($states) || count($states) <= 0) {
            throw new \Liuggio\HelpDeskBundle\Exception('Impossible to read state');
        }
        //$qb->andWhereIn('st.code', $statesOr);
        $qb->andWhere($qb->expr()->in('st.code', $states));

        if (!empty($filter)) {
            $qb->andWhere(
                $qb->expr()->orx($qb->expr()->like('t.subject', ':pattern'),
                    $qb->expr()->like('t.body', ':pattern'),
                    $qb->expr()->like('c.name', ':pattern'))
            )
                ->setParameter('pattern', "%" . $filter . "%");
        }
        if($onlyQuery){
            return $qb->getQuery();
        }
        return $qb->getQuery()->getResult();
    }


    public function findTicketsByStatesAndOperator($operator, $states, $filter = '', $onlyQuery = false)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('t')
            ->from('LiuggioHelpDeskBundle:Ticket', 't')
            ->leftjoin('t.state', 'st')
            ->leftjoin('t.category', 'ct')
            ->leftjoin('ct.operators', 'opr')
            ->leftJoin('opr.operator','user_operator')
            ->where('user_operator = :user')
            ->orderBy('t.updatedAt','DESC')
            ->setParameter('user', $operator)
        ;

        if (!is_array($states) || count($states) <= 0) {
            throw new \Liuggio\HelpDeskBundle\Exception('Impossible to read state');
        }
        //$qb->andWhereIn('st.code', $statesOr);
        $qb->andWhere($qb->expr()->in('st.code', $states));

        if (!empty($filter)) {
            $qb->andWhere(
                $qb->expr()->orx($qb->expr()->like('t.subject', ':pattern'), $qb->expr()->like('t.body', ':pattern'))
            )
                ->setParameter('pattern', "%" . $filter . "%");
        }
        if($onlyQuery){
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }


}