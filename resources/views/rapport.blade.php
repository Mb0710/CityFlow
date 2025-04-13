<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('/assets/logo2.png') }}">
    <title>Rapport des Objets Connectés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        html,
        body {
            height: auto;
            overflow-y: scroll;
            /* Active le défilement vertical */
            margin: 0;
            padding: 0;
        }

        .wrapper2 {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            z-index: 5;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            /* Ajusté pour occuper toute la hauteur de l'écran */
            box-sizing: border-box;
        }

        .box {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            /* Étendre à toute la largeur */
            width: 100%;
            /* S'assurer que la largeur est de 100% */
            box-sizing: border-box;
            margin: 20px 0;
            /* Ajouter un espacement vertical */
            position: relative;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.8);
            z-index: 5;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="wrapper2">
        <div class="box2">
            <h1>Rapport des Objets Connectés</h1>

            <div class="city-features">
                <div class="feature">
                    <i class="bx bx-bar-chart"></i>
                    <span><strong>Total des objets connectés :</strong> {{ $report['total_objects'] }}</span>
                </div>
                <div class="feature">
                    <i class="bx bx-check-circle"></i>
                    <span><strong>Objets actifs :</strong> {{ $report['active_objects'] }}
                        ({{ round(($report['active_objects'] / $report['total_objects']) * 100, 1) }}%)</span>
                </div>
                <div class="feature">
                    <i class="bx bx-x-circle"></i>
                    <span><strong>Objets inactifs :</strong> {{ $report['inactive_objects'] }}
                        ({{ round(($report['inactive_objects'] / $report['total_objects']) * 100, 1) }}%)</span>
                </div>
                <div class="feature">
                    <i class="bx bx-battery"></i>
                    <span><strong>Niveau moyen de batterie :</strong>
                        {{ round($report['average_battery_level'], 1) }}%</span>
                </div>
                <div class="feature">
                    <i class="bx bx-low-battery"></i>
                    <span><strong>Objets avec batterie faible :</strong> {{ $report['low_battery_objects'] }} (moins de
                        20%)</span>
                </div>
                <div class="feature">
                    <i class="bx bx-time"></i>
                    <span><strong>Dernière interaction :</strong>
                        {{ $report['last_interaction_date'] ? date('d/m/Y H:i', strtotime($report['last_interaction_date'])) : 'Aucune' }}</span>
                </div>
            </div>

            <div class="box">
                <h2 class="login-title">Objets par type</h2>
                <ul class="list-group">
                    @foreach ($report['objects_by_type'] as $type)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $type->type }}
                            <span class="badge bg-primary rounded-pill">{{ $type->count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="box">
                <h2 class="login-title">Distribution par zone</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Zone</th>
                                <th>Nombre d'objets</th>
                                <th>Pourcentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report['objects_by_zone'] as $zone)
                                <tr>
                                    <td>{{ $zone->zone ? $zone->zone->name : 'Zone inconnue' }}</td>
                                    <td>{{ $zone->count }}</td>
                                    <td>{{ round(($zone->count / $report['total_objects']) * 100, 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Retour au tableau de bord</a>
            </div>
        </div>
    </div>
</body>

</html>