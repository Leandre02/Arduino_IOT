-- Nombre de lectures enregistrees ou le bouton rouge est appuye, par jour
-- By Léandre Kanmegne - examen1

SELECT DATE(date_heure) AS jour, COUNT(*) AS nombre_lectures_bouton_rouge 
FROM boutons 
WHERE etat_bouton_rouge = 1
GROUP BY DATE(date_heure)
ORDER BY jour DESC;