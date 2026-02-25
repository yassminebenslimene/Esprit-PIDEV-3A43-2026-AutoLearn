#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour ajouter les ticks ✓ et les tâches de test dans le Sprint Backlog
"""

# Tâches à NE PAS cocher (sans tick)
TACHES_NON_REALISEES = [
    "T-5.8.3",
    "T-5.14.2",
    "T-5.20.3",
    "T-5.21.3",
    "T-5.26.2",
    "T-5.26.3",
    "T-5.30.1",
    "T-5.30.2"
]

# Lire le fichier HTML
with open('SPRINT_BACKLOG_COMPLET_FINAL.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Liste des verbes d'action à traiter
verbes = [
    "Créer", "Ajouter", "Générer", "Installer", "Configurer", "Implémenter",
    "Appeler", "Afficher", "Attacher", "Masquer", "Vérifier", "Tester",
    "Modifier", "Supprimer", "Définir", "Récupérer", "Envoyer", "Calculer",
    "Valider", "Exécuter", "Mettre", "Intégrer", "Développer", "Initialiser"
]

# Ajouter les ticks devant tous les verbes
for verbe in verbes:
    content = content.replace(
        f'<td>{verbe}',
        f'<td><span class="done">✓</span>{verbe}'
    )

# Retirer les ticks des tâches non réalisées
for tache_id in TACHES_NON_REALISEES:
    # Chercher toutes les occurrences de cette tâche et retirer le tick
    lines = content.split('\n')
    new_lines = []
    
    for line in lines:
        if tache_id in line and '<span class="done">✓</span>' in line:
            # Retirer le tick de cette ligne
            line = line.replace('<span class="done">✓</span>', '')
        new_lines.append(line)
    
    content = '\n'.join(new_lines)

# Ajouter les tâches de test après chaque US
# Trouver toutes les sections US et ajouter une ligne de test

import re

# Pattern pour trouver les sections US
us_pattern = r'(<!-- US-\d+\.\d+:.*?-->.*?(?=<!-- US-\d+\.\d+:|<!-- US-\d+\.\d+:|</tbody>))'

def ajouter_test_us(match):
    us_content = match.group(0)
    
    # Extraire l'ID de l'US
    us_id_match = re.search(r'US-(\d+\.\d+)', us_content)
    if not us_id_match:
        return us_content
    
    us_id = us_id_match.group(1)
    
    # Extraire la priorité de la première tâche
    priority_match = re.search(r'<td>(\d+)</td>\s*</tr>', us_content)
    priority = priority_match.group(1) if priority_match else "90"
    
    # Vérifier si une tâche TEST existe déjà
    if f'T-{us_id}.TEST' in us_content:
        return us_content
    
    # Créer la ligne de test
    test_row = f'''                    <tr class="task-row">
                        <td>US-{us_id}</td>
                        <td>T-{us_id}.TEST</td>
                        <td><span class="done">✓</span>Tests pour US-{us_id}</td>
                        <td>Tests manuels et automatiques</td>
                        <td>Amira NEFZI</td>
                        <td>1h</td>
                        <td>{priority}</td>
                    </tr>

'''
    
    # Ajouter la ligne de test à la fin de l'US (avant le prochain commentaire ou </tbody>)
    return us_content + test_row

# Appliquer la fonction à toutes les sections US
content = re.sub(us_pattern, ajouter_test_us, content, flags=re.DOTALL)

# Sauvegarder le fichier modifié
with open('SPRINT_BACKLOG_COMPLET_FINAL.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("✅ Ticks ajoutés avec succès!")
print(f"✅ {len(TACHES_NON_REALISEES)} tâches non réalisées sans tick")
print("✅ Tâches de test ajoutées pour chaque US")
print("\nFichier mis à jour: SPRINT_BACKLOG_COMPLET_FINAL.html")
