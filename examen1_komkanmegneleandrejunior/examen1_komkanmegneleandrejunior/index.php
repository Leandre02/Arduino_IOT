<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examen 1 - leandre kanmegne</title>
    <link rel="stylesheet" href="/css/style.css"/>
</head>
<body>
    <h1>leandre kanmegne</h1>
 
    <?php
    $dsn  = "mysql:host=localhost;dbname=examen1;charset=utf8mb4";
    $user = "examen1";
    $pass = "examen1";

    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $enregistrements = $pdo->query(
            "SELECT etat_bouton_rouge, etat_bouton_jaune, adresse_ip, date_heure
             FROM boutons
             ORDER BY date_heure DESC
             LIMIT 5"
        )->fetchAll(PDO::FETCH_ASSOC);

        if (count($enregistrements) === 0) {
            echo "<p>Aucun enregistrement pour l'instant.</p>";
        } else {
            echo "<table>";
            echo "<thead><tr>
                    <th>#</th>
                    <th>Bouton rouge</th>
                    <th>Bouton jaune</th>
                    <th>Adresse IP</th>
                    <th>Date et heure</th>
                  </tr></thead>";
            echo "<tbody>";

            $numero = 1;
            foreach ($enregistrements as $ligne) {
                $classeRouge = $ligne['etat_bouton_rouge'] ? 'actif' : 'inactif';
                $classeJaune = $ligne['etat_bouton_jaune'] ? 'actif' : 'inactif';
                $labelRouge  = $ligne['etat_bouton_rouge'] ? 'Appuye' : 'Relache';
                $labelJaune  = $ligne['etat_bouton_jaune'] ? 'Appuye' : 'Relache';

                echo "<tr>
                        <td>{$numero}</td>
                        <td class='{$classeRouge}'>{$labelRouge}</td>
                        <td class='{$classeJaune}'>{$labelJaune}</td>
                        <td>{$ligne['adresse_ip']}</td>
                        <td>{$ligne['date_heure']}</td>
                      </tr>";
                $numero++;
            }

            echo "</tbody></table>";
        }

    } catch (Exception $exception) {
        echo "<p>Erreur BD : " . htmlspecialchars($exception->getMessage()) . "</p>";
    }
    ?>
</body>
</html>