-- Script d'insertion du cours Python Programming avec 7 chapitres
-- À exécuter dans phpMyAdmin après avoir sélectionné la base autolearn_db

-- 1. Insérer le cours Python Programming
INSERT INTO cours (titre, description, matiere, niveau, duree, created_at) 
VALUES (
    'Python Programming',
    'Introduction complète à la programmation en Python. Ce cours couvre les fondamentaux du langage Python, de la syntaxe de base aux concepts avancés de programmation orientée objet.',
    'Informatique',
    'Débutant',
    40,
    NOW()
);

-- Récupérer l'ID du cours inséré
SET @cours_id = LAST_INSERT_ID();

-- 2. Insérer les 7 chapitres avec leur contenu

-- Chapitre 1: Introduction à Python
INSERT INTO chapitre (titre, contenu, ordre, cours_id, ressources, ressource_type) 
VALUES (
    'Introduction à Python',
    '<h2>Bienvenue dans le monde de Python</h2>
<p>Python est un langage de programmation polyvalent, puissant et facile à apprendre. Créé par Guido van Rossum en 1991, Python est devenu l''un des langages les plus populaires au monde.</p>

<h3>Pourquoi apprendre Python ?</h3>
<ul>
    <li><strong>Syntaxe claire et lisible</strong> : Python utilise une syntaxe proche du langage naturel</li>
    <li><strong>Polyvalent</strong> : Web, data science, IA, automatisation, etc.</li>
    <li><strong>Grande communauté</strong> : Des milliers de bibliothèques disponibles</li>
    <li><strong>Demandé sur le marché</strong> : Très recherché par les entreprises</li>
</ul>

<h3>Installation de Python</h3>
<p>Pour commencer, téléchargez Python depuis <a href="https://www.python.org" target="_blank">python.org</a>. Nous recommandons Python 3.10 ou supérieur.</p>

<h3>Votre premier programme</h3>
<pre><code>print("Hello, World!")
print("Bienvenue dans Python!")</code></pre>

<p>Ce simple programme affiche du texte à l''écran. La fonction <code>print()</code> est l''une des fonctions les plus utilisées en Python.</p>

<h3>L''interpréteur Python</h3>
<p>Python est un langage interprété. Vous pouvez exécuter du code ligne par ligne dans l''interpréteur interactif ou créer des fichiers .py.</p>',
    1,
    @cours_id,
    NULL,
    NULL
);

-- Chapitre 2: Variables et Types de Données
INSERT INTO chapitre (titre, contenu, ordre, cours_id, ressources, ressource_type) 
VALUES (
    'Variables et Types de Données',
    '<h2>Les Variables en Python</h2>
<p>Une variable est un conteneur qui stocke une valeur. En Python, vous n''avez pas besoin de déclarer le type de la variable.</p>

<h3>Créer des variables</h3>
<pre><code># Variables numériques
age = 25
prix = 19.99

# Variables textuelles
nom = "Alice"
message = ''Bonjour''

# Variables booléennes
est_etudiant = True
a_termine = False</code></pre>

<h3>Les types de données principaux</h3>
<ul>
    <li><strong>int</strong> : Nombres entiers (42, -10, 0)</li>
    <li><strong>float</strong> : Nombres décimaux (3.14, -0.5)</li>
    <li><strong>str</strong> : Chaînes de caractères ("texte")</li>
    <li><strong>bool</strong> : Booléens (True, False)</li>
    <li><strong>list</strong> : Listes [1, 2, 3]</li>
    <li><strong>dict</strong> : Dictionnaires {''clé'': ''valeur''}</li>
</ul>

<h3>Opérations sur les variables</h3>
<pre><code># Opérations mathématiques
a = 10
b = 3
somme = a + b        # 13
difference = a - b   # 7
produit = a * b      # 30
division = a / b     # 3.333...
puissance = a ** b   # 1000

# Concaténation de chaînes
prenom = "Jean"
nom = "Dupont"
nom_complet = prenom + " " + nom  # "Jean Dupont"</code></pre>

<h3>Conversion de types</h3>
<pre><code># Convertir en entier
nombre = int("42")

# Convertir en chaîne
texte = str(123)

# Convertir en décimal
decimal = float("3.14")</code></pre>',
    2,
    @cours_id,
    NULL,
    NULL
);

-- Chapitre 3: Structures Conditionnelles
INSERT INTO chapitre (titre, contenu, ordre, cours_id, ressources, ressource_type) 
VALUES (
    'Structures Conditionnelles',
    '<h2>Prendre des Décisions avec if, elif, else</h2>
<p>Les structures conditionnelles permettent à votre programme de prendre des décisions basées sur des conditions.</p>

<h3>La structure if</h3>
<pre><code>age = 18

if age >= 18:
    print("Vous êtes majeur")
    print("Vous pouvez voter")</code></pre>

<h3>if-else : deux chemins possibles</h3>
<pre><code>temperature = 25

if temperature > 30:
    print("Il fait chaud")
else:
    print("La température est agréable")</code></pre>

<h3>if-elif-else : plusieurs conditions</h3>
<pre><code>note = 15

if note >= 16:
    print("Excellent !")
elif note >= 14:
    print("Très bien")
elif note >= 12:
    print("Bien")
elif note >= 10:
    print("Assez bien")
else:
    print("Insuffisant")</code></pre>

<h3>Opérateurs de comparaison</h3>
<ul>
    <li><code>==</code> : égal à</li>
    <li><code>!=</code> : différent de</li>
    <li><code>&gt;</code> : supérieur à</li>
    <li><code>&lt;</code> : inférieur à</li>
    <li><code>&gt;=</code> : supérieur ou égal</li>
    <li><code>&lt;=</code> : inférieur ou égal</li>
</ul>

<h3>Opérateurs logiques</h3>
<pre><code>age = 20
a_permis = True

# AND : les deux conditions doivent être vraies
if age >= 18 and a_permis:
    print("Peut conduire")

# OR : au moins une condition doit être vraie
if age < 18 or not a_permis:
    print("Ne peut pas conduire")

# NOT : inverse la condition
if not a_permis:
    print("Doit passer le permis")</code></pre>',
    3,
    @cours_id,
    NULL,
    NULL
);

-- Chapitre 4: Boucles et Itérations
INSERT INTO chapitre (titre, contenu, ordre, cours_id, ressources, ressource_type) 
VALUES (
    'Boucles et Itérations',
    '<h2>Répéter des Actions avec les Boucles</h2>
<p>Les boucles permettent de répéter des instructions plusieurs fois sans réécrire le code.</p>

<h3>La boucle for</h3>
<p>Utilisée pour itérer sur une séquence (liste, chaîne, range).</p>
<pre><code># Boucle simple
for i in range(5):
    print(i)  # Affiche 0, 1, 2, 3, 4

# Parcourir une liste
fruits = ["pomme", "banane", "orange"]
for fruit in fruits:
    print(f"J''aime les {fruit}s")

# Range avec début et fin
for nombre in range(1, 11):
    print(nombre)  # Affiche de 1 à 10</code></pre>

<h3>La boucle while</h3>
<p>Continue tant qu''une condition est vraie.</p>
<pre><code>compteur = 0

while compteur < 5:
    print(f"Compteur: {compteur}")
    compteur += 1

# Boucle avec condition
reponse = ""
while reponse != "oui":
    reponse = input("Voulez-vous continuer ? (oui/non) ")</code></pre>

<h3>Contrôle de boucle</h3>
<pre><code># break : sortir de la boucle
for i in range(10):
    if i == 5:
        break  # Arrête la boucle
    print(i)

# continue : passer à l''itération suivante
for i in range(10):
    if i % 2 == 0:
        continue  # Saute les nombres pairs
    print(i)  # Affiche seulement les impairs

# else avec boucle
for i in range(5):
    print(i)
else:
    print("Boucle terminée normalement")</code></pre>

<h3>Boucles imbriquées</h3>
<pre><code># Table de multiplication
for i in range(1, 4):
    for j in range(1, 4):
        print(f"{i} x {j} = {i*j}")</code></pre>',
    4,
    @cours_id,
    NULL,
    NULL
);

-- Chapitre 5: Fonctions
INSERT INTO chapitre (titre, contenu, ordre, cours_id, ressources, ressource_type) 
VALUES (
    'Fonctions',
    '<h2>Créer et Utiliser des Fonctions</h2>
<p>Les fonctions sont des blocs de code réutilisables qui effectuent une tâche spécifique.</p>

<h3>Définir une fonction</h3>
<pre><code>def saluer():
    print("Bonjour !")
    print("Bienvenue")

# Appeler la fonction
saluer()</code></pre>

<h3>Fonctions avec paramètres</h3>
<pre><code>def saluer_personne(nom):
    print(f"Bonjour {nom} !")

saluer_personne("Alice")
saluer_personne("Bob")

# Plusieurs paramètres
def additionner(a, b):
    resultat = a + b
    print(f"{a} + {b} = {resultat}")

additionner(5, 3)</code></pre>

<h3>Valeurs de retour</h3>
<pre><code>def calculer_carre(nombre):
    return nombre ** 2

resultat = calculer_carre(5)
print(resultat)  # 25

# Retourner plusieurs valeurs
def calculer_operations(a, b):
    somme = a + b
    produit = a * b
    return somme, produit

s, p = calculer_operations(4, 5)
print(f"Somme: {s}, Produit: {p}")</code></pre>

<h3>Paramètres par défaut</h3>
<pre><code>def saluer(nom, message="Bonjour"):
    print(f"{message} {nom} !")

saluer("Alice")              # Bonjour Alice !
saluer("Bob", "Salut")       # Salut Bob !</code></pre>

<h3>Arguments variables</h3>
<pre><code># *args pour nombre variable d''arguments
def additionner_tout(*nombres):
    total = 0
    for nombre in nombres:
        total += nombre
    return total

print(additionner_tout(1, 2, 3))        # 6
print(additionner_tout(10, 20, 30, 40)) # 100

# **kwargs pour arguments nommés
def afficher_infos(**infos):
    for cle, valeur in infos.items():
        print(f"{cle}: {valeur}")

afficher_infos(nom="Alice", age=25, ville="Paris")</code></pre>

<h3>Portée des variables</h3>
<pre><code>x = 10  # Variable globale

def ma_fonction():
    y = 5  # Variable locale
    print(x)  # Peut accéder à x
    print(y)

ma_fonction()
# print(y)  # Erreur : y n''existe pas ici</code></pre>',
    5,
    @cours_id,
    NULL,
    NULL
);

-- Chapitre 6: Listes et Structures de Données
INSERT INTO chapitre (titre, contenu, ordre, cours_id, ressources, ressource_type) 
VALUES (
    'Listes et Structures de Données',
    '<h2>Travailler avec les Collections</h2>
<p>Python offre plusieurs structures pour stocker des collections de données.</p>

<h3>Les Listes</h3>
<p>Les listes sont des collections ordonnées et modifiables.</p>
<pre><code># Créer une liste
fruits = ["pomme", "banane", "orange"]
nombres = [1, 2, 3, 4, 5]
mixte = [1, "texte", True, 3.14]

# Accéder aux éléments
print(fruits[0])   # pomme
print(fruits[-1])  # orange (dernier élément)

# Modifier un élément
fruits[1] = "fraise"

# Ajouter des éléments
fruits.append("kiwi")        # Ajoute à la fin
fruits.insert(1, "mangue")   # Insère à l''index 1

# Supprimer des éléments
fruits.remove("pomme")       # Supprime par valeur
del fruits[0]                # Supprime par index
dernier = fruits.pop()       # Supprime et retourne le dernier</code></pre>

<h3>Opérations sur les listes</h3>
<pre><code>nombres = [3, 1, 4, 1, 5, 9, 2, 6]

# Trier
nombres.sort()               # Trie sur place
nombres_tries = sorted(nombres)  # Retourne une nouvelle liste

# Inverser
nombres.reverse()

# Longueur
taille = len(nombres)

# Vérifier la présence
if 5 in nombres:
    print("5 est dans la liste")

# Compter les occurrences
compte = nombres.count(1)

# Trouver l''index
position = nombres.index(4)</code></pre>

<h3>Slicing (découpage)</h3>
<pre><code>nombres = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]

# Extraire une portion
print(nombres[2:5])    # [2, 3, 4]
print(nombres[:3])     # [0, 1, 2]
print(nombres[7:])     # [7, 8, 9]
print(nombres[::2])    # [0, 2, 4, 6, 8] (pas de 2)
print(nombres[::-1])   # Inverse la liste</code></pre>

<h3>Les Tuples</h3>
<p>Comme les listes mais immuables (non modifiables).</p>
<pre><code>coordonnees = (10, 20)
point = (x, y, z) = (1, 2, 3)  # Unpacking

# Les tuples sont plus rapides que les listes
# Utilisés pour des données qui ne changent pas</code></pre>

<h3>Les Dictionnaires</h3>
<p>Collections de paires clé-valeur.</p>
<pre><code># Créer un dictionnaire
personne = {
    "nom": "Dupont",
    "prenom": "Jean",
    "age": 30,
    "ville": "Paris"
}

# Accéder aux valeurs
print(personne["nom"])
print(personne.get("age"))

# Modifier/Ajouter
personne["age"] = 31
personne["email"] = "jean@example.com"

# Parcourir un dictionnaire
for cle, valeur in personne.items():
    print(f"{cle}: {valeur}")</code></pre>

<h3>Les Sets (Ensembles)</h3>
<p>Collections non ordonnées sans doublons.</p>
<pre><code>nombres = {1, 2, 3, 4, 5}
nombres.add(6)
nombres.remove(3)

# Opérations d''ensembles
a = {1, 2, 3}
b = {3, 4, 5}
union = a | b          # {1, 2, 3, 4, 5}
intersection = a & b   # {3}
difference = a - b     # {1, 2}</code></pre>',
    6,
    @cours_id,
    NULL,
    NULL
);

-- Chapitre 7: Programmation Orientée Objet
INSERT INTO chapitre (titre, contenu, ordre, cours_id, ressources, ressource_type) 
VALUES (
    'Programmation Orientée Objet',
    '<h2>Introduction aux Classes et Objets</h2>
<p>La programmation orientée objet (POO) permet d''organiser le code en créant des objets qui combinent données et comportements.</p>

<h3>Créer une classe</h3>
<pre><code>class Personne:
    def __init__(self, nom, age):
        self.nom = nom
        self.age = age
    
    def se_presenter(self):
        print(f"Je m''appelle {self.nom} et j''ai {self.age} ans")

# Créer des objets
alice = Personne("Alice", 25)
bob = Personne("Bob", 30)

alice.se_presenter()
bob.se_presenter()</code></pre>

<h3>Attributs et Méthodes</h3>
<pre><code>class Voiture:
    # Attribut de classe (partagé par toutes les instances)
    nombre_roues = 4
    
    def __init__(self, marque, modele, annee):
        # Attributs d''instance (propres à chaque objet)
        self.marque = marque
        self.modele = modele
        self.annee = annee
        self.kilometrage = 0
    
    def rouler(self, distance):
        self.kilometrage += distance
        print(f"La voiture a roulé {distance} km")
    
    def afficher_info(self):
        print(f"{self.marque} {self.modele} ({self.annee})")
        print(f"Kilométrage: {self.kilometrage} km")

ma_voiture = Voiture("Toyota", "Corolla", 2020)
ma_voiture.rouler(100)
ma_voiture.afficher_info()</code></pre>

<h3>Encapsulation</h3>
<p>Protéger les données avec des attributs privés.</p>
<pre><code>class CompteBancaire:
    def __init__(self, titulaire, solde_initial):
        self.titulaire = titulaire
        self.__solde = solde_initial  # Attribut privé
    
    def deposer(self, montant):
        if montant > 0:
            self.__solde += montant
            print(f"Dépôt de {montant}€ effectué")
    
    def retirer(self, montant):
        if 0 < montant <= self.__solde:
            self.__solde -= montant
            print(f"Retrait de {montant}€ effectué")
        else:
            print("Solde insuffisant")
    
    def afficher_solde(self):
        print(f"Solde: {self.__solde}€")

compte = CompteBancaire("Alice", 1000)
compte.deposer(500)
compte.retirer(200)
compte.afficher_solde()</code></pre>

<h3>Héritage</h3>
<p>Créer des classes qui héritent d''autres classes.</p>
<pre><code>class Animal:
    def __init__(self, nom):
        self.nom = nom
    
    def faire_bruit(self):
        pass

class Chien(Animal):
    def faire_bruit(self):
        print(f"{self.nom} dit: Wouf!")

class Chat(Animal):
    def faire_bruit(self):
        print(f"{self.nom} dit: Miaou!")

rex = Chien("Rex")
felix = Chat("Felix")

rex.faire_bruit()
felix.faire_bruit()</code></pre>

<h3>Méthodes spéciales</h3>
<pre><code>class Point:
    def __init__(self, x, y):
        self.x = x
        self.y = y
    
    def __str__(self):
        return f"Point({self.x}, {self.y})"
    
    def __add__(self, other):
        return Point(self.x + other.x, self.y + other.y)
    
    def __eq__(self, other):
        return self.x == other.x and self.y == other.y

p1 = Point(1, 2)
p2 = Point(3, 4)
p3 = p1 + p2  # Utilise __add__
print(p3)     # Utilise __str__</code></pre>

<h3>Bonnes pratiques POO</h3>
<ul>
    <li>Une classe = une responsabilité</li>
    <li>Nommer les classes avec des noms (PascalCase)</li>
    <li>Nommer les méthodes avec des verbes (snake_case)</li>
    <li>Utiliser l''encapsulation pour protéger les données</li>
    <li>Favoriser la composition à l''héritage multiple</li>
</ul>',
    7,
    @cours_id,
    NULL,
    NULL
);

-- Afficher un message de confirmation
SELECT 'Cours Python Programming créé avec succès !' AS Message;
SELECT CONCAT('ID du cours: ', @cours_id) AS Info;
SELECT COUNT(*) AS 'Nombre de chapitres créés' FROM chapitre WHERE cours_id = @cours_id;
