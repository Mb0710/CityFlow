<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('/assets/logo2.png') }}">
    <title>Rapport des Actions Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        html,
        body {
            height: auto;
            overflow-y: scroll;
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
            box-sizing: border-box;
        }

        .box {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
            margin: 20px 0;
            position: relative;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.8);
            z-index: 5;
            overflow-y: auto;
        }

        .activity-trend {
            font-weight: bold;
        }

        .activity-trend.positive {
            color: green;
        }

        .activity-trend.negative {
            color: red;
        }

        .activity-trend.neutral {
            color: gray;
        }
    </style>
</head>

<body>
    <div class="wrapper2">
        <div class="box2">
            <h1>Rapport des Actions Utilisateurs</h1>

            <div class="city-features">
                <div class="feature">
                    <i class="bx bx-bar-chart"></i>
                    <span><strong>Total des actions :</strong> {{ $report['total_actions'] }}</span>
                </div>
                <div class="feature">
                    <i class="bx bx-user-check"></i>
                    <span><strong>Taux de connexion :</strong> {{ round($report['connection_rate'], 1) }}%
                        ({{ $report['active_users'] }}/{{ $report['total_users'] }} utilisateurs)</span>
                </div>
                <div class="feature">
                    <i class="bx bx-coin-stack"></i>
                    <span><strong>Points totaux distribués :</strong> {{ $report['total_points'] }}</span>
                </div>
                <div class="feature">
                    <i class="bx bx-trending-up"></i>
                    <span>
                        <strong>Tendance d'activité :</strong>
                        <span
                            class="activity-trend {{ $report['activity_trend'] > 0 ? 'positive' : ($report['activity_trend'] < 0 ? 'negative' : 'neutral') }}">
                            {{ $report['activity_trend'] > 0 ? '+' : '' }}{{ round($report['activity_trend'], 1) }}%
                        </span>
                    </span>
                </div>
                <div class="feature">
                    <i class="bx bx-time"></i>
                    <span><strong>Dernière action :</strong>
                        {{ $report['last_action_date'] ? date('d/m/Y H:i', strtotime($report['last_action_date'])) : 'Aucune' }}</span>
                </div>
                <div class="feature">
                    <i class="bx bx-medal"></i>
                    <span><strong>Moyenne points/action :</strong>
                        {{ round($report['average_points_per_action'], 1) }}</span>
                </div>
            </div>

            <div class="box">
                <h2 class="login-title">Activité par type d'action</h2>
                <ul class="list-group">
                    @foreach ($report['actions_by_type'] as $type)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ ucfirst($type->action_type) }}
                            <span class="badge bg-primary rounded-pill">{{ $type->count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="box">
                <h2 class="login-title">Répartition par niveau d'activité utilisateur</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Niveau d'activité</th>
                                <th>Nombre d'utilisateurs</th>
                                <th>Pourcentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report['user_activity_levels'] as $level => $count)
                                <tr>
                                    <td>{{ ucfirst($level) }}</td>
                                    <td>{{ $count }}</td>
                                    <td>{{ round(($count / $report['total_users']) * 100, 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box">
                <h2 class="login-title">Top 10 des utilisateurs les plus actifs</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Type</th>
                                <th>Nombre d'actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report['most_active_users'] as $user)
                                <tr>
                                    <td>{{ $user->user ? $user->user->name . ' ' . $user->user->firstname : 'Utilisateur inconnu' }}
                                    </td>
                                    <td>{{ $user->user ? ucfirst($user->user->member_type) : '-' }}</td>
                                    <td>{{ $user->action_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box">
                <h2 class="login-title">Top 10 des objets connectés les plus utilisés</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Objet</th>
                                <th>Type</th>
                                <th>Nombre d'interactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report['most_interacted_objects'] as $object)
                                <tr>
                                    <td>{{ $object->connectedObject ? $object->connectedObject->name : 'Objet inconnu' }}
                                    </td>
                                    <td>{{ $object->connectedObject ? $object->connectedObject->type : '-' }}</td>
                                    <td>{{ $object->interaction_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box">
                <h2 class="login-title">Évolution des actions (30 derniers jours)</h2>
                <canvas id="actionsChart" width="400" height="200"></canvas>
            </div>

            <div class="mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Retour au tableau de bord</a>
                <button onclick="window.print()" class="btn btn-primary">Telecharger</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Données pour le graphique
            var actionData = @json($report['actions_by_day']);

            var dates = actionData.map(function (item) {
                return item.date;
            });

            var counts = actionData.map(function (item) {
                return item.count;
            });

            // Créer le graphique
            var ctx = document.getElementById('actionsChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Nombre d\'actions par jour',
                        data: counts,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>