#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import matplotlib.pyplot as plt
import matplotlib.patches as mpatches
from matplotlib.patches import FancyBboxPatch, Rectangle
import networkx as nx
from PIL import Image, ImageDraw, ImageFont
import numpy as np

# Configuration matplotlib pour un meilleur rendu
plt.rcParams['font.family'] = 'sans-serif'
plt.rcParams['font.sans-serif'] = ['Arial', 'DejaVu Sans']
plt.rcParams['font.size'] = 10

# 1. CRÉER LE SCHÉMA D'ARBORESCENCE
def create_sitemap():
    fig, ax = plt.subplots(figsize=(14, 10))
    ax.set_xlim(0, 14)
    ax.set_ylim(0, 10)
    ax.axis('off')
    
    # Couleurs
    color_home = '#667eea'
    color_main = '#764ba2'
    color_sub = '#f093fb'
    color_page = '#4facfe'
    
    # Titre
    ax.text(7, 9.5, 'Culture Radar - Arborescence du Site', 
            fontsize=20, fontweight='bold', ha='center')
    
    # Accueil (niveau 0)
    home = FancyBboxPatch((5.5, 8), 3, 0.8, 
                          boxstyle="round,pad=0.1", 
                          facecolor=color_home, 
                          edgecolor='black', 
                          linewidth=2)
    ax.add_patch(home)
    ax.text(7, 8.4, 'ACCUEIL', color='white', fontweight='bold', 
            ha='center', va='center', fontsize=12)
    
    # Niveau 1 - Pages principales
    main_pages = [
        ('Découvrir', 2, 6.5),
        ('Mon Espace', 7, 6.5),
        ('Pages Annexes', 12, 6.5)
    ]
    
    for title, x, y in main_pages:
        box = FancyBboxPatch((x-1.2, y-0.3), 2.4, 0.6,
                             boxstyle="round,pad=0.05",
                             facecolor=color_main,
                             edgecolor='black',
                             linewidth=1.5)
        ax.add_patch(box)
        ax.text(x, y, title, color='white', fontweight='bold',
                ha='center', va='center')
        # Lignes de connexion
        ax.plot([7, x], [8, y+0.3], 'k-', linewidth=1)
    
    # Niveau 2 - Sous-pages Découvrir
    discover_pages = [
        'Tous les\névénements',
        'Par\ncatégorie',
        'Par\nlocalisation',
        'Événements\ngratuits'
    ]
    
    for i, title in enumerate(discover_pages):
        x = 0.5 + i * 1
        y = 4.5
        box = FancyBboxPatch((x-0.35, y-0.3), 0.7, 0.6,
                             boxstyle="round,pad=0.02",
                             facecolor=color_sub,
                             edgecolor='gray',
                             linewidth=1)
        ax.add_patch(box)
        ax.text(x, y, title, fontsize=8, ha='center', va='center')
        ax.plot([2, x], [6.2, y+0.3], 'gray', linewidth=0.5)
    
    # Catégories (niveau 3)
    categories = ['Concerts', 'Théâtre', 'Expos', 'Cinéma', 'Ateliers']
    for i, cat in enumerate(categories):
        x = 0.8 + i * 0.4
        y = 3
        box = FancyBboxPatch((x-0.15, y-0.15), 0.3, 0.3,
                             boxstyle="round,pad=0.01",
                             facecolor=color_page,
                             edgecolor='lightgray',
                             linewidth=0.5)
        ax.add_patch(box)
        ax.text(x, y, cat, fontsize=6, ha='center', va='center')
        ax.plot([1.5, x], [4.2, y+0.15], 'lightgray', linewidth=0.3)
    
    # Niveau 2 - Sous-pages Mon Espace
    espace_pages = [
        'Dashboard',
        'Mes favoris',
        'Calendrier',
        'Notifications',
        'Paramètres'
    ]
    
    for i, title in enumerate(espace_pages):
        x = 5.5 + i * 0.7
        y = 4.5
        box = FancyBboxPatch((x-0.3, y-0.25), 0.6, 0.5,
                             boxstyle="round,pad=0.02",
                             facecolor=color_sub,
                             edgecolor='gray',
                             linewidth=1)
        ax.add_patch(box)
        ax.text(x, y, title, fontsize=7, ha='center', va='center')
        ax.plot([7, x], [6.2, y+0.25], 'gray', linewidth=0.5)
    
    # Niveau 2 - Pages Annexes
    annexe_pages = [
        'À propos',
        'Contact',
        'Mentions\nlégales',
        'CGU'
    ]
    
    for i, title in enumerate(annexe_pages):
        x = 10.5 + i * 0.8
        y = 4.5
        box = FancyBboxPatch((x-0.35, y-0.25), 0.7, 0.5,
                             boxstyle="round,pad=0.02",
                             facecolor=color_sub,
                             edgecolor='gray',
                             linewidth=1)
        ax.add_patch(box)
        ax.text(x, y, title, fontsize=7, ha='center', va='center')
        ax.plot([12, x], [6.2, y+0.25], 'gray', linewidth=0.5)
    
    # Légende
    ax.text(0.5, 1.5, 'Légende:', fontweight='bold', fontsize=10)
    legends = [
        (color_home, 'Page d\'accueil'),
        (color_main, 'Sections principales'),
        (color_sub, 'Sous-pages'),
        (color_page, 'Catégories')
    ]
    
    for i, (color, label) in enumerate(legends):
        rect = Rectangle((0.5, 1 - i*0.3), 0.3, 0.2, facecolor=color)
        ax.add_patch(rect)
        ax.text(0.9, 1.1 - i*0.3, label, fontsize=8, va='center')
    
    plt.tight_layout()
    plt.savefig('/root/culture-radar/arborescence_schema.png', dpi=150, bbox_inches='tight')
    plt.close()
    print("✅ Schéma d'arborescence créé : arborescence_schema.png")

# 2. CRÉER LE SCHÉMA DES TRIS DE CARTES
def create_card_sorting():
    fig = plt.figure(figsize=(14, 10))
    
    # Créer 2 sous-graphiques
    ax1 = plt.subplot(2, 2, 1)
    ax2 = plt.subplot(2, 2, 2)
    ax3 = plt.subplot(2, 1, 2)
    
    # 1. Tri ouvert - Dendrogramme
    ax1.set_title('Tri Ouvert - Regroupements des participants', fontweight='bold')
    
    # Simuler un dendrogramme
    categories = ['Découverte', 'Mon Espace', 'Social', 'Admin']
    y_pos = np.arange(len(categories))
    consensus = [87, 92, 78, 65]
    
    bars = ax1.barh(y_pos, consensus, color=['#667eea', '#764ba2', '#f093fb', '#4facfe'])
    ax1.set_yticks(y_pos)
    ax1.set_yticklabels(categories)
    ax1.set_xlabel('Consensus (%)')
    ax1.set_xlim(0, 100)
    
    # Ajouter les valeurs sur les barres
    for i, (bar, val) in enumerate(zip(bars, consensus)):
        ax1.text(val + 2, bar.get_y() + bar.get_height()/2, 
                f'{val}%', va='center')
    
    # 2. Matrice de similarité
    ax2.set_title('Matrice de Similarité - Tri Fermé', fontweight='bold')
    
    # Créer une matrice de similarité
    features = ['Explorer', 'Filtres', 'Favoris', 'Calendrier', 'Partage']
    matrix = np.array([
        [100, 85, 45, 40, 55],
        [85, 100, 35, 30, 60],
        [45, 35, 100, 95, 70],
        [40, 30, 95, 100, 65],
        [55, 60, 70, 65, 100]
    ])
    
    im = ax2.imshow(matrix, cmap='RdYlGn', vmin=0, vmax=100)
    ax2.set_xticks(np.arange(len(features)))
    ax2.set_yticks(np.arange(len(features)))
    ax2.set_xticklabels(features, rotation=45, ha='right')
    ax2.set_yticklabels(features)
    
    # Ajouter les valeurs dans les cellules
    for i in range(len(features)):
        for j in range(len(features)):
            text = ax2.text(j, i, matrix[i, j],
                          ha="center", va="center", color="black", fontsize=8)
    
    # Colorbar
    plt.colorbar(im, ax=ax2, fraction=0.046, pad=0.04)
    
    # 3. Résultats consolidés
    ax3.set_title('Catégorisation Finale - Résultats Consolidés', fontweight='bold', pad=20)
    ax3.axis('off')
    
    # Créer 3 colonnes pour les catégories finales
    categories_final = {
        'DÉCOUVERTE\n(87% consensus)': [
            '• Explorer événements',
            '• Filtres par ville',
            '• Recherche avancée',
            '• Carte interactive',
            '• Recommandations'
        ],
        'MON ESPACE\n(92% consensus)': [
            '• Mes favoris',
            '• Calendrier perso',
            '• Notifications',
            '• Historique',
            '• Préférences'
        ],
        'SOCIAL\n(78% consensus)': [
            '• Partage événements',
            '• Avis & commentaires',
            '• Groupes d\'intérêt',
            '• Amis',
            '• Messagerie'
        ]
    }
    
    x_positions = [0.15, 0.45, 0.75]
    colors = ['#667eea', '#764ba2', '#f093fb']
    
    for i, (cat, items) in enumerate(categories_final.items()):
        # Titre de catégorie
        rect = FancyBboxPatch((x_positions[i]-0.12, 0.8), 0.24, 0.15,
                              boxstyle="round,pad=0.02",
                              facecolor=colors[i],
                              edgecolor='black',
                              linewidth=2,
                              transform=ax3.transAxes)
        ax3.add_patch(rect)
        ax3.text(x_positions[i], 0.875, cat, 
                transform=ax3.transAxes,
                ha='center', va='center',
                fontweight='bold', color='white',
                fontsize=10)
        
        # Items
        for j, item in enumerate(items):
            ax3.text(x_positions[i], 0.65 - j*0.12, item,
                    transform=ax3.transAxes,
                    ha='center', va='top',
                    fontsize=9)
    
    # Note méthodologique
    ax3.text(0.5, 0.05, 
            'Méthodologie: 15 participants (tri ouvert) + 10 participants (tri fermé) | 40 cartes fonctionnalités',
            transform=ax3.transAxes,
            ha='center', fontsize=8, style='italic',
            bbox=dict(boxstyle="round,pad=0.3", facecolor='lightgray', alpha=0.3))
    
    plt.tight_layout()
    plt.savefig('/root/culture-radar/tri_cartes_schema.png', dpi=150, bbox_inches='tight')
    plt.close()
    print("✅ Schéma des tris de cartes créé : tri_cartes_schema.png")

# 3. CRÉER UN SCHÉMA DE SESSION DE TRI
def create_sorting_session():
    fig, ax = plt.subplots(figsize=(12, 8))
    ax.set_xlim(0, 12)
    ax.set_ylim(0, 8)
    ax.axis('off')
    
    # Titre
    ax.text(6, 7.5, 'Session de Tri de Cartes - Culture Radar', 
            fontsize=18, fontweight='bold', ha='center')
    ax.text(6, 7, 'Exemple de regroupement par un participant', 
            fontsize=10, ha='center', style='italic')
    
    # Cartes non triées (gauche)
    ax.text(1.5, 6, 'Cartes à trier', fontweight='bold', fontsize=12)
    
    unsorted_cards = [
        'Recherche', 'Favoris', 'Carte', 'Filtres',
        'Calendrier', 'Partage', 'Notifications', 'Profil'
    ]
    
    for i, card in enumerate(unsorted_cards):
        y = 5.5 - (i % 4) * 0.8
        x = 0.5 + (i // 4) * 1.5
        rect = FancyBboxPatch((x, y-0.25), 1.2, 0.5,
                              boxstyle="round,pad=0.02",
                              facecolor='lightgray',
                              edgecolor='darkgray',
                              linewidth=1)
        ax.add_patch(rect)
        ax.text(x+0.6, y, card, ha='center', va='center', fontsize=9)
    
    # Flèche
    ax.arrow(3.5, 3.5, 1.5, 0, head_width=0.2, head_length=0.2, 
             fc='black', ec='black')
    ax.text(4.25, 3, 'Regroupement', ha='center', fontsize=10)
    
    # Cartes triées (droite)
    groups = {
        'Exploration': ['Recherche', 'Carte', 'Filtres'],
        'Personnel': ['Favoris', 'Calendrier', 'Profil'],
        'Social': ['Partage', 'Notifications']
    }
    
    colors_group = ['#667eea', '#764ba2', '#f093fb']
    x_start = 6
    
    for g, (group_name, cards) in enumerate(groups.items()):
        # Conteneur du groupe
        group_rect = FancyBboxPatch((x_start + g*2 - 0.7, 1.5), 1.8, 3.5,
                                    boxstyle="round,pad=0.05",
                                    facecolor=colors_group[g],
                                    alpha=0.2,
                                    edgecolor=colors_group[g],
                                    linewidth=2)
        ax.add_patch(group_rect)
        
        # Titre du groupe
        ax.text(x_start + g*2 + 0.2, 5.2, group_name, 
                fontweight='bold', fontsize=11, ha='center')
        
        # Cartes dans le groupe
        for i, card in enumerate(cards):
            y = 4.5 - i * 0.8
            rect = FancyBboxPatch((x_start + g*2 - 0.5, y-0.25), 1.4, 0.5,
                                  boxstyle="round,pad=0.02",
                                  facecolor='white',
                                  edgecolor=colors_group[g],
                                  linewidth=1)
            ax.add_patch(rect)
            ax.text(x_start + g*2 + 0.2, y, card, ha='center', va='center', fontsize=9)
    
    # Statistiques
    ax.text(6, 0.8, 'Temps moyen: 12 min | Accord inter-juges: 0.82 | n=15 participants',
            ha='center', fontsize=9, 
            bbox=dict(boxstyle="round,pad=0.3", facecolor='lightyellow'))
    
    plt.tight_layout()
    plt.savefig('/root/culture-radar/session_tri_cartes.png', dpi=150, bbox_inches='tight')
    plt.close()
    print("✅ Schéma de session créé : session_tri_cartes.png")

# Exécuter toutes les fonctions
if __name__ == "__main__":
    print("\n🎨 Création des schémas visuels pour Culture Radar...\n")
    create_sitemap()
    create_card_sorting()
    create_sorting_session()
    print("\n✅ Tous les schémas ont été créés avec succès !")
    print("\nFichiers générés:")
    print("- arborescence_schema.png")
    print("- tri_cartes_schema.png")
    print("- session_tri_cartes.png")