# Diagrammes UML — Projet Dar Anawat

Ce dossier documente les parcours réellement présents dans l’application Laravel. Le livrable principal est [`Diagrammes_UML_Dar_Anawat.pdf`](./Diagrammes_UML_Dar_Anawat.pdf), composé d’une couverture, d’un sommaire et d’une page par diagramme.

## Contenu

1. **Cas d’utilisation** — visite des chambres, consultation d’un détail, changement de langue et dépôt d’une demande de réservation par un visiteur.
2. **Classes** — contrôleur et requête de réservation, modèles `Reservation` et `User`, middleware de langue et formateur de devise, avec leurs dépendances Laravel et le schéma SQL.
3. **Séquence : consultation des chambres** — affichage des données statiques de la liste, puis chargement d’une fiche chambre.
4. **Séquence : demande de réservation** — formulaire, validation, création d’une demande en attente et retour avec une référence.
5. **Activité : demande de réservation** — branchements réellement exécutés pour la chambre présélectionnée, la limitation de débit, les validations et l’enregistrement.
6. **États-transitions : réservation** — seul l’état `pending` est défini dans le code et la migration ; aucune transition métier n’est implémentée.

## Limites fonctionnelles représentées

- La liste des chambres utilise des tableaux PHP statiques ; il n’existe pas de modèle Eloquent `Room`.
- Une réservation est une **demande**, pas une allocation confirmée. Le contrôleur enregistre son état à `pending`; aucun contrôle de disponibilité ou de confirmation n’est exécuté.
- Aucun paiement, passerelle de paiement, administrateur ou parcours d’administration n’est présent dans les routes et classes examinées. Ils ne sont donc pas représentés comme fonctionnalités.
- `user_id` est nullable. Si une requête possède déjà un utilisateur authentifié, son identifiant est associé à la demande ; le parcours d’authentification lui-même n’est pas défini par les routes de l’application.

## Sources et édition

Le fichier [`diagrammes.puml`](./diagrammes.puml) contient les six diagrammes PlantUML, chacun dans son propre bloc `@startuml` / `@enduml`. Il peut être modifié avec PlantUML dans VS Code ou un éditeur compatible.

Pour générer des images SVG avec PlantUML installé localement :

```sh
plantuml -tsvg diagrammes.puml
```

Pour régénérer le PDF mis en page, ouvrez [`livre-uml.html`](./livre-uml.html) dans Microsoft Edge ou Chromium, puis utilisez **Imprimer → Enregistrer au format PDF**, en activant les graphiques d’arrière-plan. Choisissez le format A4 paysage. Le PDF inclus a été généré à partir de cette maquette et vérifié après export.

## Sources d’implémentation consultées

- `routes/web.php`
- `app/Http/Controllers/ReservationController.php`
- `app/Http/Requests/StoreReservationRequest.php`
- `app/Models/Reservation.php` et `app/Models/User.php`
- `app/Http/Middleware/SetLocale.php` et `app/Support/Currency.php`
- `database/migrations/2026_10_19_000000_create_reservations_table.php`
- `database/migrations/0001_01_01_000000_create_users_table.php`
- Vues `chambres`, `chambre-details`, `reservations/create` et `components/room-card`
- `tests/Feature/ReservationTest.php`
