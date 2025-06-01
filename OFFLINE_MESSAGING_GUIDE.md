# Guide de Messagerie Hors Ligne - Chatbot Gemini

## 🚀 Fonctionnalités Implémentées

### 1. **Détection Automatique du Statut Réseau**
- Surveillance en temps réel de la connexion réseau
- Détection automatique des changements de statut (en ligne/hors ligne)
- Vérifications périodiques de la santé du service IA

### 2. **Messagerie Hors Ligne**
- **Envoi de messages même hors ligne** : Les utilisateurs peuvent continuer à écrire et envoyer des messages
- **Stockage local** : Les messages sont automatiquement sauvegardés dans le localStorage
- **Réponses par défaut** : Le chatbot fournit des réponses prédéfinies en français quand hors ligne

### 3. **Réponses par Défaut en Français**
Le système utilise des réponses variées et contextuelles :
- "Je suis actuellement hors ligne, mais je serai de retour bientôt ! En attendant, vous pouvez consulter la section d'aide de la plateforme."
- "Service temporairement indisponible. Vos questions sont importantes pour moi, je vous répondrai dès que possible !"
- "Je ne peux pas vous répondre maintenant car je suis hors ligne. Essayez de redémarrer la conversation dans quelques minutes."
- "Connexion interrompue ! Pendant ce temps, vous pouvez explorer les fonctionnalités de la plateforme ou contacter votre tuteur directement."
- "Je rencontre des difficultés de connexion. Votre message a été enregistré et je vous répondrai dès mon retour en ligne !"

### 4. **Traitement Automatique des Messages en Attente**
- **Reconnexion automatique** : Détection automatique du retour en ligne
- **Traitement des messages** : Envoi automatique des messages stockés
- **Notifications de succès** : Confirmation du traitement des messages en attente

## 🎨 Indicateurs Visuels

### Statuts du Chatbot
- **🟢 En ligne** : Point vert avec animation de pulsation
- **🔴 Hors ligne** : Point rouge avec animation de pulsation
- **🟡 Vérification...** : Point jaune pendant les vérifications
- **🟢 Reconnexion...** : Point vert clignotant pendant la reconnexion
- **📊 En ligne (X en attente)** : Affichage du nombre de messages en attente

### Bannière Hors Ligne
- Bannière orange en haut du chat quand hors ligne
- Icône WiFi barrée avec message informatif
- Compteur de messages en attente

### Messages Spéciaux
- **Messages hors ligne** : Bulle orange avec icône de rotation
- **Messages de succès** : Bulle verte pour les confirmations
- **Bouton d'envoi** : Change de couleur selon le statut (bleu/orange)

## 🧪 Comment Tester

### Test 1: Simulation Hors Ligne
1. Ouvrez le chatbot sur http://localhost:5173/
2. Ouvrez les outils de développement (F12)
3. Allez dans l'onglet "Network" 
4. Cochez "Offline" pour simuler une perte de connexion
5. Essayez d'envoyer un message
6. **Résultat attendu** : Message envoyé avec réponse par défaut en français

### Test 2: Reconnexion Automatique
1. Avec le mode offline activé, envoyez plusieurs messages
2. Décochez "Offline" dans les outils de développement
3. **Résultat attendu** : 
   - Statut change à "Reconnexion..."
   - Messages en attente sont traités automatiquement
   - Notification de succès affichée

### Test 3: Persistance des Messages
1. Envoyez des messages en mode hors ligne
2. Fermez et rouvrez le navigateur
3. **Résultat attendu** : Les messages en attente sont toujours présents

## 🔧 Architecture Technique

### Fichiers Modifiés
- `front-end/src/components/GeminiChatbot.vue` : Interface utilisateur et logique
- `front-end/src/services/geminiService.js` : Service de gestion hors ligne

### Nouvelles Fonctionnalités du Service
- `storeOfflineMessage()` : Stockage des messages hors ligne
- `getOfflineResponse()` : Génération de réponses par défaut
- `processPendingMessages()` : Traitement des messages en attente
- `enhancedHealthCheck()` : Vérification avancée de la connectivité
- `isNetworkOnline()` : Détection du statut réseau

### Stockage Local
- Clé localStorage : `gemini_offline_messages`
- Format JSON avec métadonnées (timestamp, statut, historique)
- Nettoyage automatique après traitement réussi

## 🎯 Avantages Utilisateur

1. **Continuité d'expérience** : Pas d'interruption même sans connexion
2. **Feedback immédiat** : Réponses contextuelles en français
3. **Aucune perte de données** : Tous les messages sont sauvegardés
4. **Transparence** : Indicateurs visuels clairs du statut
5. **Traitement automatique** : Aucune action manuelle requise

## 🚀 Prochaines Améliorations Possibles

- Synchronisation avec le backend pour persistance serveur
- Notifications push pour les messages traités
- Historique détaillé des messages hors ligne
- Compression des données stockées localement
- Mode dégradé avec fonctionnalités limitées
