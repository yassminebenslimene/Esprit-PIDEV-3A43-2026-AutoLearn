-- DEBUG : Vérifier pourquoi l'étudiant n'est pas détecté

-- 1. Vérifier la valeur exacte de discr
SELECT userId, nom, prenom, discr, 
       CONCAT('[', discr, ']') as discr_avec_crochets,
       LENGTH(discr) as longueur_discr
FROM user 
WHERE userId = 2;

-- 2. Vérifier si l'étudiant répond aux critères
SELECT 
    userId,
    nom,
    prenom,
    discr,
    isSuspended,
    lastActivityAt,
    DATEDIFF(NOW(), lastActivityAt) as jours_inactivite,
    CASE 
        WHEN discr = 'etudiant' THEN 'OK: est étudiant'
        ELSE 'PROBLEME: pas étudiant'
    END as check_discr,
    CASE 
        WHEN isSuspended = 0 THEN 'OK: non suspendu'
        ELSE 'PROBLEME: suspendu'
    END as check_suspended,
    CASE 
        WHEN lastActivityAt < DATE_SUB(NOW(), INTERVAL 3 DAY) THEN 'OK: inactif depuis 3+ jours'
        WHEN lastActivityAt IS NULL THEN 'OK: jamais actif'
        ELSE 'PROBLEME: actif récemment'
    END as check_inactivity
FROM user 
WHERE userId = 2;

-- 3. Requête qui devrait fonctionner
SELECT userId, nom, prenom, email, lastActivityAt
FROM user
WHERE discr = 'etudiant'
  AND isSuspended = 0
  AND (lastActivityAt < DATE_SUB(NOW(), INTERVAL 3 DAY) OR lastActivityAt IS NULL);
