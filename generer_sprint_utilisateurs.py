# -*- coding: utf-8 -*-
"""
Génération du Sprint Backlog Utilisateurs (Admin + Étudiant)
Basé sur Product Backlog Utilisateurs - 19 User Stories
"""

# Début du HTML
html_start = """<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sprint Backlog - Utilisateurs</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6; color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
        }
        .container {
            max-width: 1600px; margin: 0 auto; background: white;
            border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; padding: 40px; text-align: center;
        }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2); }
        .content { padding: 40px; }
        table {
            width: 100%; border-collapse: collapse; margin-bottom: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); font-size: 0.9em;
        }
        thead { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        th {
            padding: 12px 10px; text-align: left; font-weight: 600;
            font-size: 0.9em; border-right: 1px solid rgba(255,255,255,0.2);
        }
        td {
            padding: 10px; border-bottom: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0; vertical-align: top;
        }
        tbody tr:hover { background-color: #f5f5f5; }
        .us-header { background: #f8f9fa; font-weight: bold; color: #667eea; font-size: 1.05em; }
        .task-row { background: white; }
        .done { color: #2ed573; font-weight: bold; margin-right: 8px; }
        .info-box {
            background: #fff3cd; border-left: 5px solid #ffc107;
            padding: 20px; margin-bottom: 30px; border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 SPRINT BACKLOG - UTILISATEURS</h1>
            <div class="subtitle">Module Gestion des Événements - Admin & Étudiant</div>
            <p style="margin-top: 15px; font-size: 1.1em;">Responsable: Amira NEFZI | 19 User Stories</p>
        </div>
        <div class="content">
            <div class="info-box">
                <h3>⚠️ Note Importante</h3>
                <p><strong>Ce Sprint Backlog est basé sur le Product Backlog Utilisateurs (19 User Stories).</strong></p>
                <p>Les validations système sont intégrées comme <strong>tâches techniques</strong>.</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">US ID</th>
                        <th style="width: 7%;">Tâche ID</th>
                        <th style="width: 40%;">Description</th>
                        <th style="width: 20%;">Fichier(s)</th>
                        <th style="width: 12%;">Responsable</th>
                        <th style="width: 8%;">Temps (h)</th>
                        <th style="width: 5%;">Priorité</th>
                    </tr>
                </thead>
                <tbody>
"""

# Données des User Stories
user_stories_data = [
