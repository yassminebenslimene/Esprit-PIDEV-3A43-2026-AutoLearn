<?php
// Déclaration du fichier PHP

// Définition du namespace pour les repositories
namespace App\Repository;

// Import de l'entité Quiz
use App\Entity\Quiz;
// Import de la classe de base pour les repositories Doctrine
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
// Import du ManagerRegistry pour accéder à l'EntityManager
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Quiz
 * 
 * Cette classe hérite de ServiceEntityRepository et fournit des méthodes
 * pour interroger la base de données concernant les quiz.
 * 
 * @extends ServiceEntityRepository<Quiz> Indique que ce repository gère l'entité Quiz
 */
class QuizRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository
     * 
     * @param ManagerRegistry $registry Le gestionnaire de registre Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        // Appelle le constructeur parent en spécifiant l'entité Quiz
        // Cela configure automatiquement le repository pour gérer les quiz
        parent::__construct($registry, Quiz::class);
    }

    // Exemples de méthodes personnalisées commentées (à décommenter si besoin)
    
//    /**
//     * Exemple de méthode pour trouver des quiz par un champ spécifique
//     * 
//     * @param mixed $value La valeur à rechercher
//     * @return Quiz[] Retourne un tableau d'objets Quiz
//     */
//    public function findByExampleField($value): array
//    {
//        // Crée un QueryBuilder avec l'alias 'q' pour Quiz
//        return $this->createQueryBuilder('q')
//            // Ajoute une condition WHERE sur un champ exemple
//            ->andWhere('q.exampleField = :val')
//            // Définit la valeur du paramètre :val
//            ->setParameter('val', $value)
//            // Trie les résultats par ID en ordre croissant
//            ->orderBy('q.id', 'ASC')
//            // Limite les résultats à 10 quiz maximum
//            ->setMaxResults(10)
//            // Récupère la requête SQL générée
//            ->getQuery()
//            // Exécute la requête et retourne les résultats
//            ->getResult()
//        ;
//    }

//    /**
//     * Exemple de méthode pour trouver un seul quiz par un champ spécifique
//     * 
//     * @param mixed $value La valeur à rechercher
//     * @return Quiz|null Retourne un objet Quiz ou null si non trouvé
//     */
//    public function findOneBySomeField($value): ?Quiz
//    {
//        // Crée un QueryBuilder avec l'alias 'q' pour Quiz
//        return $this->createQueryBuilder('q')
//            // Ajoute une condition WHERE sur un champ exemple
//            ->andWhere('q.exampleField = :val')
//            // Définit la valeur du paramètre :val
//            ->setParameter('val', $value)
//            // Récupère la requête SQL générée
//            ->getQuery()
//            // Exécute la requête et retourne un seul résultat (ou null)
//            ->getOneOrNullResult()
//        ;
//    }
}
