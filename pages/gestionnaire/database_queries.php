<?php
// pages/gestionnaire/database_queries.php

function getMesEmpruntsStats($connexion, $userId) {
    $sql = "SELECT 
        DATE_FORMAT(e.date_debut, '%Y-%m') AS mois, 
        COUNT(*) AS nombre_emprunts
        FROM emprunt e
        WHERE e.id_adherent = ?
        AND e.statut = 'valide'
        GROUP BY DATE_FORMAT(e.date_debut, '%Y-%m')
        ORDER BY mois DESC
        LIMIT 12";
    
    $stmt = mysqli_prepare($connexion, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $stats = ['labels' => [], 'data' => []];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['labels'][] = $row['mois'];
        $stats['data'][] = $row['nombre_emprunts'];
    }
    
    return $stats;
}

function getTopLivresStats($connexion) {
    $sql = "SELECT 
        l.titre AS livre, 
        COUNT(*) AS nombre_emprunts
        FROM emprunt e
        JOIN exemplaire ex ON e.id_exemplaire = ex.id
        JOIN livre l ON ex.id_l = l.id
        WHERE e.statut = 'valide'
        GROUP BY l.id
        ORDER BY nombre_emprunts DESC
        LIMIT 10";
    
    $result = mysqli_query($connexion, $sql);
    
    $stats = ['labels' => [], 'data' => [], 'colors' => []];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['labels'][] = $row['livre'];
        $stats['data'][] = $row['nombre_emprunts'];
        $stats['colors'][] = sprintf(
            "rgba(%d, %d, %d, 0.7)", 
            rand(50, 200), 
            rand(50, 200), 
            rand(50, 200)
        );
    }
    
    return $stats;
}
?>