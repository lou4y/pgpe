<?php

namespace App\Repository;

use App\Entity\Absence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Absence>
 *
 * @method Absence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Absence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Absence[]    findAll()
 * @method Absence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbsenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Absence::class);
    }
    public function findByabsencesInMonth($value): array
    {
        $absences = $this->createQueryBuilder('a')
            ->where('a.present = :present')
            ->getQuery()
            ->setParameter('present', false)
            ->getResult();

        $absencesInMonth = [];
        $month = (int)$value;

        foreach ($absences as $absence) {
            if ((int)$absence->getDate()->format('n') === $month) {
                $absencesInMonth[] = $absence;
            }
        }

        return $absencesInMonth;
    }
    public function findByAbsencesOfGroupInMonth($value): array
    {
        $absences = $this->createQueryBuilder('a')
            ->leftJoin('a.etudiant', 's')  // Assuming the property for the student association is 'student'
            ->leftJoin('s.Groupe', 'g')    // Assuming the property for the group association is 'group'
            ->where('a.present = :present')
            ->getQuery()
            ->setParameter('present', false)
            ->getResult();

        $absencesInMonth = [];
        $month = (int)$value;

        foreach ($absences as $absence) {
            if ((int)$absence->getDate()->format('n') === $month) {
                $absencesInMonth[] = $absence;
            }
        }

        $groupedAbsences = [];
        foreach ($absencesInMonth as $absence) {
            $group = $absence->getEtudiant()->getGroupe() ; // Assuming you have a method to get the group from a student
            $groupId = $group->getId();  // Assuming the group entity has an ID property

            if (!isset($groupedAbsences[$groupId])) {
                $groupedAbsences[$groupId] = [
                    'group' => $group,
                    'absenceCount' => 1,
                ];
            } else {
                $groupedAbsences[$groupId]['absenceCount']++;
            }
        }

        return $groupedAbsences;
    }
    public function findByAbsencesOfGroupInTotal(): array
    {
        $absences = $this->createQueryBuilder('a')
            ->leftJoin('a.etudiant', 's')  // Assuming the property for the student association is 'student'
            ->leftJoin('s.Groupe', 'g')    // Assuming the property for the group association is 'group'
            ->where('a.present = :present')
            ->getQuery()
            ->setParameter('present', false)
            ->getResult();

        foreach ($absences as $absence) {
            $group = $absence->getEtudiant()->getGroupe() ; // Assuming you have a method to get the group from a student
            $groupId = $group->getId();  // Assuming the group entity has an ID property

            if (!isset($groupedAbsences[$groupId])) {
                $groupedAbsences[$groupId] = [
                    'group' => $group,
                    'absenceCount' => 1,
                ];
            } else {
                $groupedAbsences[$groupId]['absenceCount']++;
            }
        }

        return $groupedAbsences;
    }

//    /**
//     * @return Absence[] Returns an array of Absence objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Absence
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
