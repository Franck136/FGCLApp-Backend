<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Global — Contrats</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a2e; }

        .header {
            background: linear-gradient(135deg, #0F1E35, #A855F7);
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

        .stat-box {
            display: inline-block; background: #f0f4f8;
            border: 1px solid #dde3ed; border-radius: 8px;
            padding: 12px 20px; text-align: center;
            margin-right: 10px; margin-bottom: 10px;
            min-width: 100px;
        }
        .stat-value { font-size: 24px; font-weight: 900; color: #1D6FA4; }
        .stat-label { font-size: 10px; color: #6B84AA; text-transform: uppercase; margin-top: 3px; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #0F1E35; color: #fff; }
        thead th { padding: 8px 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; text-align: left; }
        tbody tr:nth-child(even) { background: #f5f7fa; }
        tbody td { padding: 7px 10px; font-size: 11px; border-bottom: 1px solid #e8ecf2; }

        .badge { padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; }
        .badge-actif    { background: #d1fae5; color: #065f46; }
        .badge-expire   { background: #fee2e2; color: #991b1b; }
        .badge-suspendu { background: #fef3c7; color: #92400e; }
        .badge-resilie  { background: #ede9fe; color: #5b21b6; }

        .alert-row td { background: #fff7ed !important; }

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
            <div class="header-title">Rapport Global — Contrats</div>
        </div>
        <div class="header-right">
            <div>Généré le {{ now()->format('d/m/Y à H:i') }}</div>
            <div style="margin-top:4px; font-size:12px;">
                Total : {{ $contrats->count() }} contrat(s)
            </div>
        </div>
    </div>

    <div class="content">

        <!-- STATISTIQUES -->
        <div class="section-title">Résumé</div>
        @php
            $actifs    = $contrats->where('statut', 'actif')->count();
            $expires   = $contrats->where('statut', 'expire')->count();
            $suspendus = $contrats->where('statut', 'suspendu')->count();
            $resilies  = $contrats->where('statut', 'resilie')->count();
            $expireJ30 = $contrats->filter(function($c) {
                $jours = now()->diffInDays(\Carbon\Carbon::parse($c->date_fin), false);
                return $jours >= 0 && $jours <= 30 && $c->statut === 'actif';
            })->count();
        @endphp

        <div>
            <div class="stat-box">
                <div class="stat-value">{{ $contrats->count() }}</div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color:#22C55E;">{{ $actifs }}</div>
                <div class="stat-label">Actifs</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color:#EF4444;">{{ $expires }}</div>
                <div class="stat-label">Expirés</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color:#F59E0B;">{{ $suspendus }}</div>
                <div class="stat-label">Suspendus</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color:#A855F7;">{{ $resilies }}</div>
                <div class="stat-label">Résiliés</div>
            </div>
            @if($expireJ30 > 0)
            <div class="stat-box" style="background:#fff7ed; border-color:#F59E0B;">
                <div class="stat-value" style="color:#F59E0B;">{{ $expireJ30 }}</div>
                <div class="stat-label">Expirent J-30</div>
            </div>
            @endif
        </div>

        <!-- LISTE CONTRATS -->
        <div class="section-title">Liste des contrats</div>
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Signature</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Durée</th>
                    <th>Renou. auto</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contrats as $contrat)
                @php
                    $jours = now()->diffInDays(\Carbon\Carbon::parse($contrat->date_fin), false);
                    $isAlert = $jours >= 0 && $jours <= 30 && $contrat->statut === 'actif';
                @endphp
                <tr class="{{ $isAlert ? 'alert-row' : '' }}">
                    <td><strong>{{ $contrat->reference }}</strong></td>
                    <td>{{ $contrat->client->raison_sociale ?? '—' }}</td>
                    <td>{{ str_replace('_', ' ', $contrat->type_contrat) }}</td>
                    <td>{{ \Carbon\Carbon::parse($contrat->date_signature)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') }}
                        @if($isAlert)
                            <span style="color:#F59E0B; font-weight:700;"> (J-{{ $jours }})</span>
                        @endif
                    </td>
                    <td>{{ $contrat->duree_mois }} mois</td>
                    <td>{{ $contrat->renouvellement_auto ? 'Oui' : 'Non' }}</td>
                    <td>
                        <span class="badge badge-{{ $contrat->statut }}">
                            {{ strtoupper($contrat->statut) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($expireJ30 > 0)
        <div style="margin-top:14px; background:#fff7ed; border:1px solid #F59E0B; border-radius:8px; padding:10px 14px; font-size:11px; color:#92400e;">
            ⚠ Les lignes surlignées correspondent aux contrats expirant dans les 30 prochains jours.
        </div>
        @endif

    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div>FGCL SARL · Douala, Cameroun · Document confidentiel</div>
        <div>Rapport Contrats · Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

</body>
</html>
