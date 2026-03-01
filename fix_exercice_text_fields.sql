-- Fix: Augmenter la taille des colonnes question et reponse
-- Problème: Les colonnes étaient limitées à 30 caractères (VARCHAR(30))
-- Solution: Changer en TEXT pour permettre des réponses longues (jusqu'à 65,535 caractères)

-- Modifier la colonne question
ALTER TABLE exercice MODIFY COLUMN question TEXT NOT NULL;

-- Modifier la colonne reponse
ALTER TABLE exercice MODIFY COLUMN reponse TEXT NOT NULL;

-- Vérifier les changements
DESCRIBE exercice;

-- Afficher les 5 derniers exercices avec leur longueur
SELECT 
    id,
    LENGTH(question) as longueur_question,
    LENGTH(reponse) as longueur_reponse,
    LEFT(question, 50) as apercu_question,
    LEFT(reponse, 50) as apercu_reponse
FROM exercice
ORDER BY id DESC
LIMIT 5;
