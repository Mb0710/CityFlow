<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard des Objets Connectés</title>
    <link rel="icon" href="{{ asset('/assets/logo2.png') }}">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color:rgb(37, 150, 190)
        }

        h1 {
            text-align: center;
            padding: 20px;
            color: white;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* 2 graphes par ligne */
            gap: 20px;
            padding: 20px;
            max-width: 100vw;
            box-sizing: border-box;
        }
        .button {
            width: 100%;
            background-color: #1976d2;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .chart-card {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            max-height: 400px; /*  limite la hauteur du bloc */
            overflow: hidden; /*  empêche le contenu de déborder */
        }

        canvas { /* Ca vient de chart.js*/
            width: 100% !important;
            max-width: 100%;
            height: 250px !important; /* hauteur fixe raisonnable */
            display: block;
        }

        h2 {
            margin-top: 0;
            font-size: 18px;
            color: #333;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1> Dashboard des Objets Connectés</h1>

    <div class="grid">
        <!-- 1. Objets par zone -->
        <div class="chart-card">
            <h2> Objets par zone</h2>
            <canvas id="zoneChart" width="400" height="300"></canvas>
        </div>

        <!-- 2. Par type -->
        <div class="chart-card">
            <h2>Répartition par type</h2>
            <canvas id="typeChart" width="400" height="300"></canvas>
        </div>

        <!-- 3. Moyenne de batterie -->
        <div class="chart-card">
            <h2>🔋 Moyenne batterie</h2>
            <canvas id="batteryChart" width="400" height="300"></canvas>
        </div>

        <!-- 4. Par statut -->
        <div class="chart-card">
            <h2>Répartition par statut</h2>
            <canvas id="statusChart" width="400" height="300"></canvas>
        </div>
    </div>            
    <a href="{{ route('dashboard') }}" class="button">Retour au tableau de bord</a>
    

    <script> //SCRIPT CHART.JS
        //  Objets par zone
        new Chart(document.getElementById('zoneChart'), {
            type: 'bar',
            data: {
                labels: @json($byZone->pluck('zone.name')),
                datasets: [{
                    label: 'Objets connectés',
                    data: @json($byZone->pluck('total')),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true, //graph s'adapte à la taille du parent(duv,canva..).
                maintainAspectRatio: false, // Désactive le ratio largeur/hauteur par défaut
                plugins: {
                    legend: { display: true }, //Active ou désactive la légende du graphique
                    tooltip: {
                        enabled: true, //gère les infobulle une bulle d'information qui s’affiche quand tu survoles un élément, sans cliquer. True = actif
                        mode: 'index', //montre les données du meme label
                        intersect: false //Affiche la tooltip même si ta souris est juste proche, pas obligé d’être pile sur l’élément
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true //Config axe y pour commencer à 0.
                    }
                }
            }
        });

        // 🧁 Répartition par type
        new Chart(document.getElementById('typeChart'), {
            type: 'pie',
            data: {
                labels: @json($byType->pluck('type')),
                datasets: [{
                    data: @json($byType->pluck('total')),
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#8e5ea2', '#4bc0c0'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 🔋 Moyenne batterie
        new Chart(document.getElementById('batteryChart'), {
            type: 'bar',
            data: {
                labels: @json($batteryAvg->pluck('type')),
                datasets: [{
                    label: 'Batterie moyenne (%)',
                    data: @json($batteryAvg->pluck('average')),
                    backgroundColor: 'rgba(255, 206, 86, 0.6)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // ⚙️ Répartition par statut
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: @json($byStatus->pluck('status')),
                datasets: [{
                    label: 'Statuts',
                    data: @json($byStatus->pluck('total')),
                    backgroundColor: ['#4caf50', '#f44336', '#ffc107'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>