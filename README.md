# Alerte Inondations Lomé (Laravel)

Les habitants s’inscrivent avec leur numéro et leur quartier. Un administrateur envoie une alerte à tous les inscrits d’un quartier par SMS ou Telegram. L’interface est pensée mobile et installable (PWA).

## Installation (Laravel 11 ou 12)

1. `composer create-project laravel/laravel alerte-inondations`
2. Copie le contenu de ce dossier par-dessus le projet (`app`, `config`, `database`, `public`, `resources`, `routes`).
3. Dans `bootstrap/app.php`, dans `withMiddleware`, ajoute :
   `$middleware->validateCsrfTokens(except: ['telegram/*']);`
4. Dans `.env` :
   ```
   APP_LOCALE=fr
   QUEUE_CONNECTION=database
   ADMIN_KEY=une-cle-longue-et-secrete
   TELEGRAM_TOKEN=...      # donné par @BotFather
   TELEGRAM_BOT=nom_du_bot # sans le @
   TELEGRAM_SECRET=une-chaine-aleatoire
   SMS_URL=...             # API de ton fournisseur SMS
   SMS_KEY=...
   SMS_FROM=Alerte
   ```
5. `php artisan migrate` puis `php artisan db:seed --class=QuartierSeeder`
6. Dans deux terminaux : `php artisan serve` et `php artisan queue:work`

## Utilisation

- Page publique : `/` (inscription, alertes des dernières 24 h)
- Envoi d’alertes : `/admin` (clé d’accès `ADMIN_KEY`)
- Installation sur mobile : ouvrir le site en HTTPS, puis « Ajouter à l’écran d’accueil »

## Telegram

Après déploiement en HTTPS, enregistre le webhook :
`https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://ton-domaine/telegram/<TELEGRAM_SECRET>`

## À adapter

- `app/Services/Notifier.php` : la méthode `sms()` suit un format générique, à adapter à ton fournisseur
- Vérifie les règles locales sur les SMS en masse (consentement, nom d’expéditeur)

## Prochaines étapes

- [ ] Désinscription (lien ou mot STOP)
- [ ] Carte des quartiers 
- [ ] Signalements des habitants avec confirmation avant envoi
- [ ] Tests automatisés et déploiement (Railway ou Render)
