<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Rapport des Actions Utilisateurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }

        .summary div {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
    </style>
</head>

<body>
    <h1>Rapport des Actions Utilisateurs</h1>

    <div class="summary">
        <div><strong>Total des actions :</strong> {{ $report['total_actions'] }}</div>
        <div><strong>Taux de connexion :</strong> {{ round($report['connection_rate'], 1) }}%
            ({{ $report['active_users'] }}/{{ $report['total_users'] }} utilisateurs)</div>
        <div><strong>Points totaux distribués :</strong> {{ $report['total_points'] }}</div>
        <div>
            <strong>Tendance d'activité :</strong>
            {{ $report['activity_trend'] > 0 ? '+' : '' }}{{ round($report['activity_trend'], 1) }}%
        </div>
        <div><strong>Dernière action :</strong>
            {{ $report['last_action_date'] ? date('d/m/Y H:i', strtotime($report['last_action_date'])) : 'Aucune' }}
        </div>
        <div><strong>Moyenne points/action :</strong> {{ round($report['average_points_per_action'], 1) }}</div>
    </div>

    <div class="section">
        <h2>Activité par type d'action</h2>
        <table>
            <thead>
                <tr>
                    <th>Type d'action</th>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report['actions_by_type'] as $type)
                    <tr>
                        <td>{{ ucfirst($type->action_type) }}</td>
                        <td>{{ $type->count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Répartition par niveau d'activité utilisateur</h2>
        <table>
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

    <div class="section">
        <h2>Top 10 des utilisateurs les plus actifs</h2>
        <table>
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

    <div class="section">
        <h2>Top 10 des objets connectés les plus utilisés</h2>
        <table>
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
                        <td>{{ $object->connectedObject ? $object->connectedObject->name : 'Objet inconnu' }}</td>
                        <td>{{ $object->connectedObject ? $object->connectedObject->type : '-' }}</td>
                        <td>{{ $object->interaction_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>

</html>