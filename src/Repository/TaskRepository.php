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

    public function edit(TodoList $list, Task $entity, bool $flush = false): void
    {
        $entity->setTodoList($list);
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
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
    public function findAllTasks($value): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.todoList = :value')
            ->setParameter('value', $value)
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findUncompletedTasks($value1, $value2): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.status = :value1')
            ->setParameter('value1', $value1)
            ->andWhere('t.todoList = :value2')
            ->setParameter('value2', $value2)
            ->getQuery()
            ->getResult()
            ;
    }

    public function search($value1, $value2): array
    {
        $tl = $this->createQueryBuilder('tl');
        $tl
            ->andWhere('tl.todoList = :value1')
            ->setParameter('value1', $value1)
            ->andWhere('lower(tl.name) LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.strtolower($value2).'%');
        return $tl->getQuery()->getResult();


    }



//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
