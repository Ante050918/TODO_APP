<?php

namespace App\Repository;

use App\Entity\TodoList;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<TodoList>
 *
 * @method TodoList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoList[]    findAll()
 * @method TodoList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoList::class);
    }

    public function save(User $user, TodoList $entity, bool $flush = false): void
    {
        $createdAt = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Berlin'));
        $entity->setCreatedAt($createdAt);
        $entity->setUser($user);
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TodoList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return TodoList[] Returns an array of TodoList objects
     */
    public function findAllLists($value1, $orderBy, $sort, $search): array
    {
        $tl = $this->createQueryBuilder('tl');

        $tl
            ->andWhere('tl.user = :value1')
            ->setParameter('value1', $value1)
            ->leftJoin('tl.task', 'task')
            ->addSelect('task');
        if($search){
            $tl
                ->andWhere('lower(tl.name) LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.strtolower($search).'%');
            return $tl->getQuery()->getResult();
        }
        if($orderBy && $sort){
            $tl
                ->orderBy('tl' . '.' . $orderBy, $sort);
                return $tl->getQuery()->getResult();
        }
        return $tl->getQuery()->getResult();
    }

    public function search($value1, $value2): array
    {
        $tl = $this->createQueryBuilder('tl');
        $tl
            ->andWhere('tl.user = :value1')
            ->setParameter('value1', $value1)
            ->andWhere('lower(tl.name) LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.strtolower($value2).'%');
        return $tl->getQuery()->getResult();


    }
}
