#!/usr/bin/env python3
"""
Script pour ajouter le widget IA à tous les templates backoffice qui n'utilisent pas extends
"""

import os
import re

# Liste des fichiers à modifier
files_to_modify = [
    'templates/backoffice/communaute/index.html.twig',
    'templates/backoffice/communaute/show.html.twig',
    'templates/backoffice/communaute/edit.html.twig',
    'templates/backoffice/post/index.html.twig',
    'templates/backoffice/post/show.html.twig',
    'templates/backoffice/commentaire/index.html.twig',
    'templates/backoffice/commentaire/show.html.twig',
]

widget_code = """
    {# AI Chat Widget - Assistant Intelligent #}
    {% include 'ai_assistant/chat_widget.html.twig' %}
</body>"""

for file_path in files_to_modify:
    full_path = os.path.join(os.path.dirname(__file__), file_path)
    
    if not os.path.exists(full_path):
        print(f"❌ File not found: {file_path}")
        continue
    
    # Lire le fichier
    with open(full_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Vérifier si le widget est déjà présent
    if 'ai_assistant/chat_widget.html.twig' in content:
        print(f"✅ Widget already present in: {file_path}")
        continue
    
    # Remplacer </body> par le widget + </body>
    if '</body>' in content:
        content = content.replace('</body>', widget_code)
        
        # Écrire le fichier modifié
        with open(full_path, 'w', encoding='utf-8') as f:
            f.write(content)
        
        print(f"✅ Widget added to: {file_path}")
    else:
        print(f"⚠️  No </body> tag found in: {file_path}")

print("\n🎉 Done!")
