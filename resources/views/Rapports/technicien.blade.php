<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Technicien — {{ $technicien->user->prenom }} {{ $technicien->user->nom }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a2e; }

        .header {
            background: linear-gradient(135deg, #0F1E35, #22C55E);
            color: #fff; padding: 28px 36px;
            display: flex; justify-content: space-between; align-items: flex-start;
        }
        .header-logo  { font-size: 22px; font-weight: 900; }
        .header-sub   { font-size: 11px; opacity: 0.7; margin-top: 3px; }
        .header-title {
            background: #F0A500; color: #0F1E35;
            font-size: 13px; font-weight: 700;
            padding: 4px 14px; border-radius: 20px;
            display: inline-block; margin-top: 6px;
        }
        .header-right { text-align: right; font-size: 11px; opacity: 0.8; }

        .content { padding: 28px 36px; }

        .section-title {
            font-size: 13px; font-weight: 700; color: #1D6FA4;
            text-transform: uppercase; letter-spacing: 0.5px;
            border-bottom: 2px solid #1D6FA4;
            padding-bottom: 5px; margin: 22px 0 12px;
        }

        .info-grid  { display: table; width: 100%; }
        .info-row   { display: table-row; }
        .info-label {
            display: table-cell; width: 35%;
            color: #6B84AA; font-size: 11px; font-weight: 600;
            text-transform: uppercase; padding: 5px 0; vertical-align: top;
        }
        .info-value { display: table-cell; font-size: 12px; padding: 5px 0; }

        .periode-box {
            background: #f0f4f8; border: 1px solid #dde3ed;
            border-radius: 8px; padding: 12px 16px;
            margin-bottom: 16px; font-size: 12px; color: #1D6FA4; font-weight: 700;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #0F1E35; color: #fff; }
        thead th { padding: 8px 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; text-align: left; }
        tbody tr:nth-child(even) { background: #f5f7fa; }
        tbody td { padding: 7px 10px; font-size: 11px; border-bottom: 1px solid #e8ecf2; }

        .badge { padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; }
        .badge-green  { background: #d1fae5; color: #065f46; }
        .badge-blue   { background: #dbeafe; color: #1e40af; }
        .badge-orange { background: #fef3c7; color: #92400e; }
        .badge-red    { background: #fee2e2; color: #991b1b; }

        .stat-row td {
            background: #f0f4f8; border-radius: 8px;
            padding: 12px; text-align: center;
            border: 1px solid #dde3ed !important;
        }
        .stat-value { font-size: 24px; font-weight: 900; color: #1D6FA4; }
        .stat-label { font-size: 10px; color: #6B84AA; margin-top: 3px; text-transform: uppercase; }

        .footer {
            margin-top: 36px; border-top: 1px solid #dde3ed;
            padding: 14px 36px; display: flex;
            justify-content: space-between; font-size: 10px; color: #9ba8b8;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div>
            <div class="header-logo">FGCL SARL</div>
            <div class="header-sub">Services Informatiques · Douala, Cameroun</div>
            <div class="header-title">Rapport Technicien</div>
        </div>
        <div class="header-right">
            <div>Généré le {{ now()->format('d/m/Y à H:i') }}</div>
            <div style="margin-top:4px; font-size:14px; font-weight:700; color:#F0A500;">
                {{ $technicien->user->prenom }} {{ $technicien->user->nom }}
            </div>
        </div>
    </div>

    <div class="content">

        <!-- PÉRIODE -->
        <div class="periode-box">
            📅 Période analysée :
            {{ \Carbon\Carbon::parse($request->debut)->format('d/m/Y') }}
            →
            {{ \Carbon\Carbon::parse($request->fin)->format('d/m/Y') }}
        </div>

        <!-- PROFIL TECHNICIEN -->
        <div class="section-title">Profil du technicien</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom & Prénom</div>
                <div class="info-value"><strong>{{ $technicien->user->prenom }} {{ $technicien->user->nom }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $technicien->user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone</div>
                <div class="info-value">{{ $technicien->user->telephone ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Type de contrat</div>
                <div class="info-value">{{ $technicien->type_contrat_travail }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Zone d'intervention</div>
                <div class="info-value">{{ $technicien->zone_intervention }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Spécialités</div>
                <div class="info-value">
                    {{ $technicien->specialites->pluck('nom')->join(', ') ?: '—' }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Disponibilité</div>
                <div class="info-value">
                    <span class="badge {{ $technicien->disponible ? 'badge-green' : 'badge-red' }}">
                        {{ $technicien->disponible ? 'DISPONIBLE' : 'INDISPONIBLE' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- STATISTIQUES PÉRIODE -->
        <div class="section-title">Statistiques sur la période</div>
        @php
            $total     = $interventions->count();
            $terminees = $interventions->where('statut', 'terminee')->count();
            $dureeTotal= $interventions->sum('duree_minutes');
            $coutTotal = $interventions->sum('cout');
        @endphp
        <table style="border-collapse:separate; border-spacing:8px;">
            <tr class="stat-row">
                <td>
                    <div class="stat-value">{{ $total }}</div>
                    <div class="stat-label">Interventions</div>
                </td>
                <td>
                    <div class="stat-value" style="color:#22C55E;">{{ $terminees }}</div>
                    <div class="stat-label">Terminées</div>
                </td>
                <td>
                    <div class="stat-value" style="color:#F59E0B;">
                        {{ $dureeTotal ? floor($dureeTotal/60).'h '.($dureeTotal%60).'m' : '0h' }}
                    </div>
                    <div class="stat-label">Durée totale</div>
                </td>
                <td>
                    <div class="stat-value" style="color:#A855F7;">
                        {{ $coutTotal ? number_format($coutTotal, 0, ',', ' ') : '0' }}
                    </div>
                    <div class="stat-label">Coût total (FCFA)</div>
                </td>
            </tr>
        </table>

        <!-- LISTE INTERVENTIONS -->
        @if($interventions->count() > 0)
        <div class="section-title">Détail des interventions</div>
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Date planifiée</th>
                    <th>Durée</th>
                    <th>Coût (FCFA)</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($interventions as $inv)
                @php
                    $badgeClass = match($inv->statut) {
                        'terminee'  => 'badge-green',
                        'en_cours'  => 'badge-orange',
                        'planifiee' => 'badge-blue',
                        'annulee'   => 'badge-red',
                        default     => 'badge-blue',
                    };
                @endphp
                <tr>
                    <td><strong>{{ $inv->reference }}</strong></td>
                    <td>{{ $inv->client->raison_sociale ?? '—' }}</td>
                    <td>{{ ucfirst($inv->type_intervention) }}</td>
                    <td>{{ \Carbon\Carbon::parse($inv->date_planifiee)->format('d/m/Y') }}</td>
                    <td>
                        {{ $inv->duree_minutes
                            ? floor($inv->duree_minutes/60).'h '.($inv->duree_minutes%60).'m'
                            : '—' }}
                    </td>
                    <td>{{ $inv->cout ? number_format($inv->cout, 0, ',', ' ') : '—' }}</td>
                    <td>
                        <span class="badge {{ $badgeClass }}">
                            {{ strtoupper(str_replace('_', ' ', $inv->statut)) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="background:#f5f7fa; border-radius:8px; padding:16px; text-align:center; color:#6B84AA; font-size:12px; margin-top:8px;">
            Aucune intervention sur cette période.
        </div>
        @endif

    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div>FGCL SARL · Douala, Cameroun · Document confidentiel</div>
        <div>{{ $technicien->user->prenom }} {{ $technicien->user->nom }} · Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

</body>
</html>
