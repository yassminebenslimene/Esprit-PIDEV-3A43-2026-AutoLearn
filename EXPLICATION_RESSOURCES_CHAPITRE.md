# Gestion des Ressources dans les Chapitres

## Vue d'ensemble

Le système d'importation de fichiers permet à l'administrateur d'ajouter des supports pédagogiques aux chapitres. Cette fonctionnalité permet l'upload sécurisé de différents types de fichiers depuis le poste local de l'administrateur.

Le champ "Ressources" des chapitres supporte maintenant 3 types de ressources :

1. **Liens externes** (Google Drive, YouTube, etc.)
2. **Fichiers** uploadés depuis le PC de l'admin (PDF, PPTX, DOCX, ZIP, Vidéos)
3. **Aucune ressource**

## Structure de la base de données

### Champs ajoutés à l'entité Chapitre

- `ressourceType` (string, nullable) : Type de ressource ('lien', 'fichier', ou null)
- `ressourceFichier` (string, nullable) : Nom du fichier uploadé
- `ressources` (text, nullable) : URL du lien externe

Les métadonnées sont enregistrées en base de données pour assurer la traçabilité et la gestion des ressources.

## Fonctionnement

### 1. Type "Lien"

Permet d'ajouter des liens vers :
- Google Drive
- YouTube
- Vimeo
- Tout autre lien externe

**Stockage** : L'URL est enregistrée dans le champ `ressources`

### 2. Type "Fichier"

Permet d'uploader des fichiers depuis le PC de l'admin.

**Formats acceptés** :
- Documents : PDF, PPTX, DOCX
- Archives : ZIP, RAR
- Vidéos : MP4, AVI, MOV

**Contraintes** :
- Taille maximale : 100 Mo
- Stockage : `public/uploads/chapitres/` (répertoire dédié du serveur)

**Sécurité** :
- Validation des types MIME
- Nom du fichier sécurisé (translitération et identifiant unique)
- Vérification de la taille maximale

### 3. Aucune ressource

Le chapitre n'a pas de ressource associée.

## Interface utilisateur

### Formulaire de création/modification

1. **Sélection du type** : Menu déroulant avec 3 options
   - Aucune ressource
   - Lien (Google Drive, YouTube, etc.)
   - Fichier (PDF, PPTX, ZIP, Vidéo, etc.)

2. **Champs conditionnels** : 
   - Si "Lien" est sélectionné → Affiche un champ texte pour l'URL
   - Si "Fichier" est sélectionné → Affiche un champ de téléchargement de fichier
   - Si "Aucune ressource" → Aucun champ supplémentaire

3. **JavaScript** : Toggle automatique entre les champs selon le type sélectionné
   - Fonction `toggleRessourceFields()` appelée au changement du select
   - Événement `onchange` sur le select pour réactivité immédiate

## Gestion des fichiers

### Upload
- Les fichiers sont uploadés dans `public/uploads/chapitres/`
- Le nom est sécurisé avec `transliterator_transliterate()` et rendu unique avec `uniqid()`
- Format du nom : `{nom-securise}-{uniqid}.{extension}`

### Modification
- Si un nouveau fichier est uploadé, l'ancien est automatiquement supprimé
- Si on change de "fichier" à "lien", le fichier est supprimé
- Si on change de "lien" à "fichier", le lien est effacé

### Suppression
- Lors de la suppression d'un chapitre, le fichier PDF associé est automatiquement supprimé grâce à la cascade de suppression

## Exemples d'utilisation

### Lien Google Drive
```
Type : Lien
URL : https://drive.google.com/file/d/1234567890/view
```

### Lien YouTube
```
Type : Lien
URL : https://www.youtube.com/watch?v=abcdefg
```

### Fichier PDF
```
Type : Fichier
Fichier : cours-chapitre1.pdf (sélectionné depuis le PC)
```

### Vidéo MP4
```
Type : Fichier
Fichier : presentation-cours.mp4 (sélectionné depuis le PC)
```

## Affichage dans le frontoffice

Pour afficher les ressources dans le frontoffice, vous pouvez utiliser :

```twig
{% if chapitre.ressourceType == 'lien' %}
    <a href="{{ chapitre.ressources }}" target="_blank" class="btn btn-primary">
        <i class="fa fa-external-link"></i> Voir la ressource
    </a>
{% elseif chapitre.ressourceType == 'fichier' %}
    {% set extension = chapitre.ressourceFichier|split('.')|last %}
    {% if extension in ['mp4', 'avi', 'mov'] %}
        {# Afficher la vidéo #}
        <video controls style="width: 100%; max-width: 800px;">
            <source src="{{ asset('uploads/chapitres/' ~ chapitre.ressourceFichier) }}" type="video/{{ extension }}">
            Votre navigateur ne supporte pas la lecture de vidéos.
        </video>
    {% else %}
        {# Télécharger le fichier #}
        <a href="{{ asset('uploads/chapitres/' ~ chapitre.ressourceFichier) }}" target="_blank" class="btn btn-primary" download>
            <i class="fa fa-download"></i> Télécharger {{ extension|upper }}
        </a>
    {% endif %}
{% endif %}
```

## Sécurité

### Validation des fichiers
- Vérification du type MIME côté serveur
- Limitation de la taille (100 Mo max)
- Nom de fichier sécurisé (suppression des caractères spéciaux)

### Stockage
- Fichiers stockés dans un répertoire dédié (`public/uploads/chapitres/`)
- Nom unique pour éviter les conflits
- Métadonnées en base de données pour la traçabilité

## Fichiers modifiés

1. `src/Entity/Chapitre.php` - Ajout des champs ressourceType et ressourceFichier
2. `src/Form/ChapitreType.php` - Ajout des champs de formulaire avec validation
3. `src/Controller/CoursController.php` - Gestion de l'upload et suppression des fichiers
4. `templates/backoffice/cours/chapitre_new.html.twig` - Interface de création avec JavaScript
5. `templates/backoffice/cours/chapitre_edit.html.twig` - Interface de modification avec JavaScript
6. `public/uploads/chapitres/` - Répertoire de stockage des fichiers

## Améliorations futures possibles

- Prévisualisation des fichiers avant upload
- Gestion de plusieurs fichiers par chapitre
- Compression automatique des vidéos
- Génération de miniatures pour les vidéos
- Statistiques de téléchargement des ressources
