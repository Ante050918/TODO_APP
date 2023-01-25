<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\TodoList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Task[] Returns an array of Task objects
     */
    public function findAllTasks($todoList, $orderBy, $sort, $search): array
    {
        $t = $this->createQueryBuilder('t');
        $t
            ->andWhere('t.todoList = :todoList')
            ->setParameter('todoList', $todoList)
            ->orderBy('t.name', 'ASC');
        if($search){
            $t
                ->andWhere('lower(t.name) LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.strtolower($search).'%');
            return $t->getQuery()->getResult();
        }

        if($orderBy && $sort){
            $t
                ->orderBy('t' . '.' . $orderBy, $sort);
            return $t->getQuery()->getResult();
        }

            return $t->getQuery()->getResult()
        ;
    }

    public function findUncompletedTasks($status, $todoList): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.status = :status')
            ->setParameter('status', $status)
            ->andWhere('t.todoList = :todoList')
            ->setParameter('todoList', $todoList)
            ->getQuery()
            ->getResult()
            ;
    }
}
