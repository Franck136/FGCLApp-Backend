<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Intervention — {{ $intervention->reference }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a2e; }

        .header {
            background: linear-gradient(135deg, #0F1E35, #1D6FA4);
            color: #fff; padding: 28px 36px;
            display: flex; justify-content: space-between; align-items: flex-start;
        }
        .header-logo  { font-size: 22px; font-weight: 900; }
        .header-sub   { font-size: 11px; opacity: 0.7; margin-top: 3px; }
        .header-title {
            background: #EF4444; color: #fff;
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

        .info-grid   { display: table; width: 100%; }
        .info-row    { display: table-row; }
        .info-label  {
            display: table-cell; width: 35%;
            color: #6B84AA; font-size: 11px; font-weight: 600;
            text-transform: uppercase; padding: 5px 0; vertical-align: top;
        }
        .info-value  { display: table-cell; font-size: 12px; padding: 5px 0; }

        .badge { padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; }
        .badge-green  { background: #d1fae5; color: #065f46; }
        .badge-blue   { background: #dbeafe; color: #1e40af; }
        .badge-orange { background: #fef3c7; color: #92400e; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }

        .alert-box {
            border-radius: 8px; padding: 12px 16px;
            margin: 16px 0; font-size: 12px;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #0F1E35; color: #fff; }
        thead th { padding: 8px 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; text-align: left; }
        tbody tr:nth-child(even) { background: #f5f7fa; }
        tbody td { padding: 7px 10px; font-size: 11px; border-bottom: 1px solid #e8ecf2; }

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
            <div class="header-title">Rapport d'Intervention</div>
        </div>
        <div class="header-right">
            <div>Généré le {{ now()->format('d/m/Y à H:i') }}</div>
            <div style="margin-top:4px; font-size:13px; font-weight:700; color:#F0A500;">
                {{ $intervention->reference }}
            </div>
        </div>
    </div>

    <div class="content">

        <!-- STATUT & PRIORITÉ -->
        <div style="display:flex; gap:10px; margin-bottom:8px; margin-top:4px;">
            @php
                $statutClass = match($intervention->statut) {
                    'planifiee'  => 'badge-blue',
                    'en_cours'   => 'badge-orange',
                    'terminee'   => 'badge-green',
                    'annulee'    => 'badge-red',
                    default      => 'badge-blue',
                };
                $prioriteClass = match($intervention->priorite) {
                    'haute'   => 'badge-red',
                    'normale' => 'badge-blue',
                    'basse'   => 'badge-green',
                    default   => 'badge-blue',
                };
            @endphp
            <span class="badge {{ $statutClass }}">
                STATUT : {{ strtoupper(str_replace('_', ' ', $intervention->statut)) }}
            </span>
            <span class="badge {{ $prioriteClass }}">
                PRIORITÉ : {{ strtoupper($intervention->priorite) }}
            </span>
        </div>

        <!-- INFORMATIONS GÉNÉRALES -->
        <div class="section-title">Informations générales</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Référence</div>
                <div class="info-value"><strong>{{ $intervention->reference }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Type</div>
                <div class="info-value">{{ ucfirst($intervention->type_intervention) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date planifiée</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($intervention->date_planifiee)->format('d/m/Y à H:i') }}</div>
            </div>
            @if($intervention->date_debut_reelle)
            <div class="info-row">
                <div class="info-label">Début réel</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($intervention->date_debut_reelle)->format('d/m/Y à H:i') }}</div>
            </div>
            @endif
            @if($intervention->date_fin_reelle)
            <div class="info-row">
                <div class="info-label">Fin réelle</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($intervention->date_fin_reelle)->format('d/m/Y à H:i') }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Durée effective</div>
                <div class="info-value">
                    {{ $intervention->duree_minutes
                        ? floor($intervention->duree_minutes/60).'h '.($intervention->duree_minutes%60).'min'
                        : '—' }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Coût</div>
                <div class="info-value">
                    {{ $intervention->cout
                        ? number_format($intervention->cout, 0, ',', ' ').' FCFA'
                        : '—' }}
                </div>
            </div>
        </div>

        <!-- CLIENT -->
        <div class="section-title">Client</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Raison sociale</div>
                <div class="info-value"><strong>{{ $intervention->client->raison_sociale }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Adresse</div>
                <div class="info-value">{{ $intervention->client->adresse }}, {{ $intervention->client->ville }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone</div>
                <div class="info-value">{{ $intervention->client->telephone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Contact</div>
                <div class="info-value">{{ $intervention->client->nom_responsable }}</div>
            </div>
        </div>

        <!-- CONTRAT LIÉ -->
        @if($intervention->contrat)
        <div class="section-title">Contrat associé</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Référence</div>
                <div class="info-value"><strong>{{ $intervention->contrat->reference }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Type</div>
                <div class="info-value">{{ str_replace('_', ' ', $intervention->contrat->type_contrat) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Validité</div>
                <div class="info-value">
                    {{ \Carbon\Carbon::parse($intervention->contrat->date_debut)->format('d/m/Y') }}
                    au
                    {{ \Carbon\Carbon::parse($intervention->contrat->date_fin)->format('d/m/Y') }}
                </div>
            </div>
        </div>
        @endif

        <!-- TECHNICIENS -->
        @if($intervention->techniciens->count() > 0)
        <div class="section-title">Technicien(s) assigné(s)</div>
        <table>
            <thead>
                <tr>
                    <th>Nom & Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                </tr>
            </thead>
            <tbody>
                @foreach($intervention->techniciens as $tech)
                <tr>
                    <td><strong>{{ $tech->prenom }} {{ $tech->nom }}</strong></td>
                    <td>{{ $tech->email }}</td>
                    <td>{{ $tech->telephone ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- CRÉATEUR -->
        <div class="section-title">Créée par</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom</div>
                <div class="info-value">{{ $intervention->createur->prenom }} {{ $intervention->createur->nom }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Rôle</div>
                <div class="info-value">{{ ucfirst($intervention->createur->role) }}</div>
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div>FGCL SARL · Douala, Cameroun · Document confidentiel</div>
        <div>{{ $intervention->reference }} · Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

</body>
</html>
